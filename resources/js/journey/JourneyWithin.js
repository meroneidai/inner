import * as THREE from 'three';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { AmbientAudio } from './AmbientAudio';
import { HEART_GAP, JOURNEY_WORLDS, TUNNEL_LEAD } from './worlds';

gsap.registerPlugin(ScrollTrigger);

const PATH_END = TUNNEL_LEAD + (JOURNEY_WORLDS.length - 1) * HEART_GAP + 8;

const particleVertex = /* glsl */ `
attribute float aSize;
attribute float aSeed;
attribute float aAlong;
varying float vAlong;
varying float vSeed;
uniform float uTime;
uniform float uPixelRatio;
uniform vec2 uMouse;
uniform float uPush;

void main() {
  vAlong = aAlong;
  vSeed = aSeed;
  vec3 pos = position;

  // Soft organic drift
  pos.x += sin(uTime * 0.35 + aSeed * 6.28) * 0.12;
  pos.y += cos(uTime * 0.28 + aSeed * 4.1) * 0.1;

  vec4 mv = modelViewMatrix * vec4(pos, 1.0);
  vec4 clip = projectionMatrix * mv;
  vec2 ndc = clip.xy / clip.w;

  // Fernhollow-like screen-space push away from cursor
  vec2 delta = ndc - uMouse;
  float dist = length(delta) + 0.001;
  float influence = uPush * smoothstep(0.55, 0.0, dist);
  ndc += normalize(delta) * influence * 0.22;
  clip.xy = ndc * clip.w;

  float nearGlow = 1.0 + influence * 2.4;
  gl_PointSize = aSize * uPixelRatio * (180.0 / -mv.z) * nearGlow;
  gl_PointSize = clamp(gl_PointSize, 1.0, 48.0);
  gl_Position = clip;
}
`;

const particleFragment = /* glsl */ `
precision mediump float;
varying float vAlong;
varying float vSeed;
uniform vec3 uColorA;
uniform vec3 uColorB;
uniform float uBrightness;

void main() {
  vec2 uv = gl_PointCoord - 0.5;
  float d = length(uv);
  if (d > 0.5) discard;

  float core = smoothstep(0.5, 0.08, d);
  float halo = smoothstep(0.5, 0.2, d) * 0.45;
  float alpha = (core + halo) * (0.55 + 0.45 * uBrightness);

  float mixT = smoothstep(0.0, 1.0, vAlong + sin(vSeed * 12.0) * 0.04);
  vec3 col = mix(uColorA, uColorB, mixT);
  col += core * 0.35;

  gl_FragColor = vec4(col, alpha);
}
`;

export class JourneyWithin {
    /**
     * @param {HTMLElement} root
     */
    constructor(root) {
        this.root = root;
        this.canvas = root.querySelector('[data-journey-canvas]');
        this.viewport = root.querySelector('[data-journey-sticky]') || root;
        this.overlay = root.querySelector('[data-journey-overlay]');
        this.worldLabel = root.querySelector('[data-journey-world]');
        this.worldLine = root.querySelector('[data-journey-line]');
        this.progressEl = root.querySelector('[data-journey-progress]');
        this.scrollHint = root.querySelector('[data-journey-hint]');
        this.fallback = root.querySelector('[data-journey-fallback]');
        this.loader = root.querySelector('[data-journey-loader]');
        this.dots = [...root.querySelectorAll('[data-journey-dot]')];
        this.exploreBtns = [...root.querySelectorAll('[data-journey-explore]')];

        this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        this.isMobile = window.matchMedia('(max-width: 768px)').matches;
        this.locale = document.documentElement.lang?.startsWith('ar') ? 'ar' : 'en';

        this.pointer = { x: 0, y: 0, tx: 0, ty: 0 };
        this.progress = 0;
        this.smoothProgress = 0;
        this.worldIndex = 0;
        this.raf = 0;
        this.disposed = false;
        this.idleBoost = 0;
        this.lastScrollAt = performance.now();
        this.clock = new THREE.Clock();

        this.onResize = this.onResize.bind(this);
        this.onPointer = this.onPointer.bind(this);
        this.tick = this.tick.bind(this);
        this.onExplore = this.onExplore.bind(this);
        this.onDotClick = this.onDotClick.bind(this);
        this.onUserScroll = this.onUserScroll.bind(this);
    }

    async init() {
        if (!this.canvas || this.reducedMotion || !this.supportsWebGL()) {
            this.showFallback();
            this.bindUiOnly();
            this.hideLoader();
            return;
        }

        try {
            this.setupRenderer();
            this.setupScene();
            this.buildParticleField();
            this.buildHearts();
            this.buildDust();
            this.setupScroll();
            this.bindEvents();
            this.audio = new AmbientAudio({
                button: this.root.querySelector('[data-journey-audio]'),
            });
            this.tick();
            this.root.classList.add('is-ready');
            this.fallback?.setAttribute('hidden', '');
            this.hideLoader();
            window.__innerPathJourney = this;
        } catch (error) {
            console.error('JourneyWithin failed to start', error);
            this.showFallback();
            this.bindUiOnly();
            this.hideLoader();
        }
    }

    hideLoader() {
        this.loader?.classList.add('is-gone');
    }

    bindUiOnly() {
        this.exploreBtns.forEach((btn) => btn.addEventListener('click', this.onExplore));
        this.dots.forEach((dot) => dot.addEventListener('click', this.onDotClick));
    }

    supportsWebGL() {
        try {
            const canvas = document.createElement('canvas');
            return !!(canvas.getContext('webgl2') || canvas.getContext('webgl'));
        } catch {
            return false;
        }
    }

    showFallback() {
        this.root.classList.add('is-fallback');
        this.canvas?.setAttribute('hidden', '');
        this.fallback?.removeAttribute('hidden');
    }

    setupRenderer() {
        this.renderer = new THREE.WebGLRenderer({
            canvas: this.canvas,
            antialias: false,
            alpha: false,
            powerPreference: 'high-performance',
        });
        this.renderer.setClearColor(0x050505, 1);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, this.isMobile ? 1.15 : 1.5));
        const { width, height } = this.viewportSize();
        this.renderer.setSize(width, height, false);
        this.renderer.outputColorSpace = THREE.SRGBColorSpace;
        this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.renderer.toneMappingExposure = 1.08;
    }

    viewportSize() {
        return {
            width: Math.max(this.viewport.clientWidth || window.innerWidth, 1),
            height: Math.max(this.viewport.clientHeight || window.innerHeight, 1),
        };
    }

    setupScene() {
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(JOURNEY_WORLDS[0].fog);
        this.scene.fog = new THREE.FogExp2(JOURNEY_WORLDS[0].fog, 0.034);

        const { width, height } = this.viewportSize();
        this.camera = new THREE.PerspectiveCamera(55, width / height, 0.1, 160);
        this.camera.position.set(0, 0.15, 3.2);

        this.ambient = new THREE.AmbientLight(0xffffff, 0.35);
        this.keyLight = new THREE.PointLight(JOURNEY_WORLDS[0].accent, 14, 50);
        this.keyLight.position.set(0, 0.5, 2);
        this.scene.add(this.ambient, this.keyLight);
    }

    /**
     * Continuous cosmos of glowing motes along the inward path.
     */
    buildParticleField() {
        const count = this.isMobile ? 900 : 2600;
        const positions = new Float32Array(count * 3);
        const sizes = new Float32Array(count);
        const seeds = new Float32Array(count);
        const along = new Float32Array(count);

        for (let i = 0; i < count; i += 1) {
            const t = Math.random();
            const z = -t * PATH_END;
            const radius = 0.55 + Math.random() * 3.8;
            const angle = Math.random() * Math.PI * 2;
            // denser near the path axis (tunnel feel)
            const r = radius * Math.pow(Math.random(), 0.55);
            positions[i * 3] = Math.cos(angle) * r;
            positions[i * 3 + 1] = Math.sin(angle) * r * 0.72;
            positions[i * 3 + 2] = z;
            sizes[i] = 0.6 + Math.random() * 2.4;
            seeds[i] = Math.random();
            along[i] = t;
        }

        const geo = new THREE.BufferGeometry();
        geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        geo.setAttribute('aSize', new THREE.BufferAttribute(sizes, 1));
        geo.setAttribute('aSeed', new THREE.BufferAttribute(seeds, 1));
        geo.setAttribute('aAlong', new THREE.BufferAttribute(along, 1));

        const mat = new THREE.ShaderMaterial({
            vertexShader: particleVertex,
            fragmentShader: particleFragment,
            transparent: true,
            depthWrite: false,
            blending: THREE.AdditiveBlending,
            uniforms: {
                uTime: { value: 0 },
                uPixelRatio: { value: this.renderer.getPixelRatio() },
                uMouse: { value: new THREE.Vector2(0, 0) },
                uPush: { value: this.isMobile ? 0.35 : 0.7 },
                uColorA: { value: new THREE.Color(JOURNEY_WORLDS[0].accent) },
                uColorB: { value: new THREE.Color(JOURNEY_WORLDS[1].accent) },
                uBrightness: { value: 0.85 },
            },
        });

        this.field = new THREE.Points(geo, mat);
        this.fieldMat = mat;
        this.scene.add(this.field);
    }

    /**
     * Symbolic heart of each world + portal ring — camera flies through them.
     */
    buildHearts() {
        this.hearts = JOURNEY_WORLDS.map((world, index) => {
            const z = -(TUNNEL_LEAD + index * HEART_GAP);
            const group = new THREE.Group();
            group.position.z = z;

            const portal = new THREE.Mesh(
                new THREE.TorusGeometry(2.15, 0.035, 10, 64),
                new THREE.MeshBasicMaterial({
                    color: world.accent,
                    transparent: true,
                    opacity: 0.55,
                    depthWrite: false,
                }),
            );
            group.add(portal);

            const inner = new THREE.Mesh(
                new THREE.RingGeometry(1.55, 1.7, 48),
                new THREE.MeshBasicMaterial({
                    color: world.glow,
                    transparent: true,
                    opacity: 0.22,
                    side: THREE.DoubleSide,
                    depthWrite: false,
                }),
            );
            group.add(inner);

            const emblem = this.createEmblem(world, index);
            if (emblem) {
                group.add(emblem);
            }

            // Soft radial glow sprite (canvas texture, tiny)
            const glow = new THREE.Sprite(
                new THREE.SpriteMaterial({
                    map: this.getGlowMap(),
                    color: world.glow,
                    transparent: true,
                    opacity: 0.35,
                    depthWrite: false,
                    blending: THREE.AdditiveBlending,
                }),
            );
            glow.scale.set(6.5, 6.5, 1);
            glow.position.z = -0.4;
            group.add(glow);

            this.scene.add(group);
            return { world, index, group, portal, inner, emblem, glow, z };
        });
    }

    getGlowMap() {
        if (this._glowMap) {
            return this._glowMap;
        }
        const size = 64;
        const c = document.createElement('canvas');
        c.width = size;
        c.height = size;
        const ctx = c.getContext('2d');
        const g = ctx.createRadialGradient(32, 32, 0, 32, 32, 32);
        g.addColorStop(0, 'rgba(255,255,255,0.9)');
        g.addColorStop(0.35, 'rgba(255,255,255,0.35)');
        g.addColorStop(1, 'rgba(255,255,255,0)');
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, size, size);
        this._glowMap = new THREE.CanvasTexture(c);
        return this._glowMap;
    }

    createEmblem(world, index) {
        const mat = new THREE.MeshBasicMaterial({
            color: world.accent,
            transparent: true,
            opacity: 0.55,
            wireframe: world.id === 'diamond' || world.id === 'ice',
            depthWrite: false,
        });

        if (world.id === 'chest') {
            const g = new THREE.Group();
            for (let i = 0; i < 5; i += 1) {
                const ring = new THREE.Mesh(
                    new THREE.TorusGeometry(0.35 + i * 0.18, 0.012, 6, 40),
                    mat.clone(),
                );
                ring.rotation.x = Math.PI / 2;
                g.add(ring);
            }
            return g;
        }
        if (world.id === 'desert') {
            const g = new THREE.Group();
            for (let i = 0; i < 4; i += 1) {
                const wave = new THREE.Mesh(
                    new THREE.PlaneGeometry(3.2, 0.35, 24, 1),
                    new THREE.MeshBasicMaterial({
                        color: world.accent,
                        transparent: true,
                        opacity: 0.28,
                        side: THREE.DoubleSide,
                        depthWrite: false,
                    }),
                );
                const pos = wave.geometry.attributes.position;
                for (let p = 0; p < pos.count; p += 1) {
                    const x = pos.getX(p);
                    pos.setZ(p, Math.sin(x * 2.2 + i) * 0.18);
                }
                pos.needsUpdate = true;
                wave.position.set(0, -0.8 + i * 0.35, 0.2);
                g.add(wave);
            }
            return g;
        }
        if (world.id === 'ice') {
            const g = new THREE.Group();
            for (let i = 0; i < 7; i += 1) {
                const shard = new THREE.Mesh(new THREE.ConeGeometry(0.07, 0.7 + (i % 3) * 0.2, 4), mat);
                shard.position.set((i - 3) * 0.28, Math.sin(i) * 0.25, 0.3);
                shard.rotation.z = (i - 3) * 0.15;
                g.add(shard);
            }
            return g;
        }
        if (world.id === 'forest') {
            const g = new THREE.Group();
            const trunk = new THREE.Mesh(
                new THREE.CylinderGeometry(0.04, 0.08, 1.4, 5),
                new THREE.MeshBasicMaterial({ color: world.mid, transparent: true, opacity: 0.7 }),
            );
            trunk.position.y = -0.2;
            g.add(trunk);
            for (let i = 0; i < 8; i += 1) {
                const orb = new THREE.Mesh(
                    new THREE.SphereGeometry(0.06 + Math.random() * 0.05, 8, 8),
                    new THREE.MeshBasicMaterial({
                        color: world.glow,
                        transparent: true,
                        opacity: 0.7,
                    }),
                );
                const a = (i / 8) * Math.PI * 2;
                orb.position.set(Math.cos(a) * 0.55, 0.2 + Math.sin(i) * 0.4, Math.sin(a) * 0.55);
                g.add(orb);
            }
            return g;
        }
        if (world.id === 'ocean') {
            const g = new THREE.Group();
            const bell = new THREE.Mesh(
                new THREE.SphereGeometry(0.45, 16, 12, 0, Math.PI * 2, 0, Math.PI * 0.55),
                new THREE.MeshBasicMaterial({
                    color: world.accent,
                    transparent: true,
                    opacity: 0.35,
                    side: THREE.DoubleSide,
                    depthWrite: false,
                }),
            );
            bell.position.y = 0.35;
            g.add(bell);
            for (let i = 0; i < 6; i += 1) {
                const tent = new THREE.Mesh(
                    new THREE.CylinderGeometry(0.01, 0.025, 0.9, 4),
                    new THREE.MeshBasicMaterial({ color: world.glow, transparent: true, opacity: 0.4 }),
                );
                tent.position.set((i - 2.5) * 0.12, -0.25, 0.2);
                g.add(tent);
            }
            return g;
        }
        if (world.id === 'diamond') {
            return new THREE.Mesh(new THREE.OctahedronGeometry(0.7, 0), mat);
        }
        // peace
        const disc = new THREE.Mesh(
            new THREE.CircleGeometry(0.85, 48),
            new THREE.MeshBasicMaterial({
                color: world.glow,
                transparent: true,
                opacity: 0.3,
                side: THREE.DoubleSide,
                depthWrite: false,
            }),
        );
        return disc;
    }

    buildDust() {
        const count = this.isMobile ? 80 : 180;
        const positions = new Float32Array(count * 3);
        for (let i = 0; i < count; i += 1) {
            positions[i * 3] = (Math.random() - 0.5) * 10;
            positions[i * 3 + 1] = (Math.random() - 0.5) * 6;
            positions[i * 3 + 2] = -Math.random() * PATH_END;
        }
        const geo = new THREE.BufferGeometry();
        geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        this.dust = new THREE.Points(
            geo,
            new THREE.PointsMaterial({
                color: 0xffffff,
                size: 0.04,
                transparent: true,
                opacity: 0.25,
                depthWrite: false,
                blending: THREE.AdditiveBlending,
            }),
        );
        this.scene.add(this.dust);
    }

    setupScroll() {
        const track = this.root.querySelector('[data-journey-track]');
        if (!track) {
            return;
        }

        this.scrollTrigger = ScrollTrigger.create({
            trigger: track,
            start: 'top top',
            end: 'bottom bottom',
            scrub: 1.55,
            onUpdate: (self) => {
                this.progress = self.progress;
                this.lastScrollAt = performance.now();
                this.updateWorldCopy(self.progress);
            },
        });
    }

    scrollToProgress(progress, duration = 2) {
        const track = this.root.querySelector('[data-journey-track]');
        if (!track) {
            return;
        }
        const rect = track.getBoundingClientRect();
        const absoluteTop = window.scrollY + rect.top;
        const travel = Math.max(track.offsetHeight - window.innerHeight, 1);
        const target = absoluteTop + travel * THREE.MathUtils.clamp(progress, 0, 1);
        const proxy = { y: window.scrollY };
        gsap.to(proxy, {
            y: target,
            duration,
            ease: 'power3.inOut',
            overwrite: true,
            onUpdate: () => window.scrollTo(0, proxy.y),
        });
    }

    scrollToWorld(index) {
        const progress = (index + 0.15) / JOURNEY_WORLDS.length;
        this.scrollToProgress(progress, 2.2);
    }

    onExplore(event) {
        event.preventDefault();
        this.scrollToWorld(1);
    }

    onDotClick(event) {
        event.preventDefault();
        const index = Number(event.currentTarget.dataset.journeyDot);
        if (Number.isFinite(index)) {
            this.scrollToWorld(index);
        }
    }

    onUserScroll() {
        this.lastScrollAt = performance.now();
    }

    worldBlend(progress) {
        const scaled = progress * (JOURNEY_WORLDS.length - 1);
        const i = Math.min(JOURNEY_WORLDS.length - 2, Math.floor(scaled));
        const mix = scaled - i;
        const s = mix * mix * (3 - 2 * mix);
        return { i, mix: s, a: JOURNEY_WORLDS[i], b: JOURNEY_WORLDS[i + 1] };
    }

    updateWorldCopy(progress) {
        const labelIndex = Math.min(
            JOURNEY_WORLDS.length - 1,
            Math.floor(progress * JOURNEY_WORLDS.length - 1e-6),
        );
        if (labelIndex !== this.worldIndex) {
            this.worldIndex = labelIndex;
            const world = JOURNEY_WORLDS[labelIndex];
            const title = this.locale === 'ar' ? world.titleAr : world.titleEn;
            const line = this.locale === 'ar' ? world.lineAr : world.lineEn;

            if (this.worldLabel) {
                gsap.fromTo(this.worldLabel, { opacity: 0, y: 8 }, {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    ease: 'power2.out',
                    onStart: () => {
                        this.worldLabel.textContent = title;
                    },
                });
            }
            if (this.worldLine) {
                gsap.fromTo(this.worldLine, { opacity: 0 }, {
                    opacity: 1,
                    duration: 0.7,
                    ease: 'power2.out',
                    onStart: () => {
                        this.worldLine.textContent = line;
                    },
                });
            }
            this.root.dataset.world = world.id;
            this.syncDots(labelIndex);
        }

        if (this.progressEl) {
            this.progressEl.style.width = `${Math.round(progress * 100)}%`;
        }
        if (this.scrollHint && progress > 0.03) {
            this.scrollHint.classList.add('is-hidden');
        }
        if (this.overlay) {
            const introFade = Math.min(1, progress / 0.09);
            this.overlay.style.setProperty('--journey-intro-opacity', String(1 - introFade * 0.9));
            this.overlay.style.setProperty('--journey-world-opacity', String(Math.min(1, Math.max(0, (progress - 0.04) / 0.1))));
        }
    }

    syncDots(activeIndex) {
        this.dots.forEach((dot) => {
            const index = Number(dot.dataset.journeyDot);
            const active = index === activeIndex;
            dot.classList.toggle('is-active', active);
            if (active) {
                dot.setAttribute('aria-current', 'true');
            } else {
                dot.removeAttribute('aria-current');
            }
        });
    }

    bindEvents() {
        window.addEventListener('resize', this.onResize, { passive: true });
        window.addEventListener('pointermove', this.onPointer, { passive: true });
        window.addEventListener('wheel', this.onUserScroll, { passive: true });
        window.addEventListener('touchmove', this.onUserScroll, { passive: true });
        this.exploreBtns.forEach((btn) => btn.addEventListener('click', this.onExplore));
        this.dots.forEach((dot) => dot.addEventListener('click', this.onDotClick));
    }

    onResize() {
        if (!this.renderer || !this.camera) {
            return;
        }
        const { width, height } = this.viewportSize();
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height, false);
        if (this.fieldMat) {
            this.fieldMat.uniforms.uPixelRatio.value = this.renderer.getPixelRatio();
        }
    }

    onPointer(event) {
        this.pointer.tx = (event.clientX / window.innerWidth) * 2 - 1;
        this.pointer.ty = -((event.clientY / window.innerHeight) * 2 - 1);
    }

    cameraZForProgress(p) {
        const eased = p * p * (3 - 2 * p);
        return THREE.MathUtils.lerp(3.2, -PATH_END + 6, eased);
    }

    applyAtmosphere(progress) {
        const { i, mix, a, b } = this.worldBlend(progress);
        const fog = new THREE.Color(a.fog).lerp(new THREE.Color(b.fog), mix);
        const accent = new THREE.Color(a.accent).lerp(new THREE.Color(b.accent), mix);
        const glow = new THREE.Color(a.glow).lerp(new THREE.Color(b.glow), mix);

        this.scene.fog.color.copy(fog);
        this.scene.background.copy(fog);
        this.keyLight.color.copy(accent);
        this.keyLight.intensity = 10 + Math.sin(this.clock.elapsedTime * 1.4) * 2;

        if (this.fieldMat) {
            this.fieldMat.uniforms.uColorA.value.copy(accent);
            this.fieldMat.uniforms.uColorB.value.copy(glow);
            this.fieldMat.uniforms.uBrightness.value = a.bright || b.bright ? 1.15 : 0.85;
        }
        if (this.dust) {
            this.dust.material.color.copy(glow);
        }

        const bright = (a.bright ? 1 : 0) * (1 - mix) + (b.bright ? 1 : 0) * mix;
        this.renderer.toneMappingExposure = 1.05 + bright * 0.28;
        this.scene.fog.density = 0.036 - bright * 0.016;

        this.audio?.setBlend(i, Math.min(i + 1, JOURNEY_WORLDS.length - 1), mix);
    }

    tick() {
        if (this.disposed) {
            return;
        }
        this.raf = requestAnimationFrame(this.tick);
        const t = this.clock.getElapsedTime();
        const dt = Math.min(this.clock.getDelta(), 0.05);

        // Idle forward drift (Fernhollow-like wander) when scroll is quiet
        if (performance.now() - this.lastScrollAt > 2200 && this.smoothProgress < 0.985) {
            this.idleBoost = Math.min(1, this.idleBoost + dt * 0.35);
            this.progress = Math.min(1, this.progress + dt * 0.008 * this.idleBoost);
            this.updateWorldCopy(this.progress);
        } else {
            this.idleBoost = Math.max(0, this.idleBoost - dt);
        }

        this.smoothProgress += (this.progress - this.smoothProgress) * 0.055;
        this.pointer.x += (this.pointer.tx - this.pointer.x) * 0.06;
        this.pointer.y += (this.pointer.ty - this.pointer.y) * 0.06;

        const targetZ = this.cameraZForProgress(this.smoothProgress);
        this.camera.position.z += (targetZ - this.camera.position.z) * 0.09;
        this.camera.position.x += (this.pointer.x * 0.35 - this.camera.position.x) * 0.07;
        this.camera.position.y += (0.12 + this.pointer.y * 0.2 - this.camera.position.y) * 0.07;
        this.camera.lookAt(
            this.pointer.x * 0.4,
            0.05 + this.pointer.y * 0.15,
            this.camera.position.z - 10,
        );

        this.applyAtmosphere(this.smoothProgress);
        this.keyLight.position.z = this.camera.position.z - 1.2;

        if (this.fieldMat) {
            this.fieldMat.uniforms.uTime.value = t;
            this.fieldMat.uniforms.uMouse.value.set(this.pointer.x, this.pointer.y);
        }

        // Hearts: breathe, and dissolve as we pass through their core
        this.hearts?.forEach((heart) => {
            const dist = heart.z - this.camera.position.z;
            const approaching = dist < 0 && dist > -18;
            const through = THREE.MathUtils.clamp(1 - Math.abs(dist + 4) / 10, 0, 1);
            const visibility = approaching || through > 0
                ? THREE.MathUtils.clamp(1 - Math.abs(dist + 2) / 14, 0.08, 1)
                : THREE.MathUtils.clamp(1 - Math.abs(dist) / 20, 0, 0.35);

            heart.portal.material.opacity = 0.2 + visibility * 0.45;
            heart.inner.material.opacity = 0.08 + visibility * 0.25;
            heart.portal.rotation.z = t * 0.15;
            heart.inner.rotation.z = -t * 0.1;
            heart.glow.material.opacity = 0.12 + through * 0.45;
            heart.glow.scale.setScalar(5.5 + through * 3 + Math.sin(t * 1.5) * 0.2);
            heart.group.position.y = Math.sin(t * 0.4 + heart.index) * 0.06;

            if (heart.emblem) {
                heart.emblem.rotation.y = t * 0.25;
                heart.emblem.rotation.x = Math.sin(t * 0.35 + heart.index) * 0.2;
                heart.emblem.traverse((obj) => {
                    if (obj.material && obj.material.opacity !== undefined) {
                        obj.material.opacity = 0.25 + visibility * 0.45;
                    }
                });
            }
        });

        // Dust drifts with world motion profile
        if (this.dust) {
            const motion = JOURNEY_WORLDS[this.worldIndex]?.motion;
            const pos = this.dust.geometry.attributes.position;
            for (let i = 0; i < pos.count; i += 1) {
                let x = pos.getX(i);
                let y = pos.getY(i);
                let z = pos.getZ(i);
                if (motion === 'drift') {
                    x += 0.01;
                } else if (motion === 'rise' || motion === 'embers') {
                    y += 0.008;
                } else if (motion === 'float') {
                    y += Math.sin(t + i) * 0.003;
                } else {
                    z += 0.008;
                }
                if (x > 5) {
                    x = -5;
                }
                if (y > 3) {
                    y = -3;
                }
                if (z > this.camera.position.z + 3) {
                    z = this.camera.position.z - PATH_END;
                }
                pos.setXYZ(i, x, y, z);
            }
            pos.needsUpdate = true;
        }

        this.renderer.render(this.scene, this.camera);
    }

    dispose() {
        this.disposed = true;
        cancelAnimationFrame(this.raf);
        window.removeEventListener('resize', this.onResize);
        window.removeEventListener('pointermove', this.onPointer);
        window.removeEventListener('wheel', this.onUserScroll);
        window.removeEventListener('touchmove', this.onUserScroll);
        this.exploreBtns.forEach((btn) => btn.removeEventListener('click', this.onExplore));
        this.dots.forEach((dot) => dot.removeEventListener('click', this.onDotClick));
        this.scrollTrigger?.kill();
        this.audio?.dispose();
        this._glowMap?.dispose();
        this.renderer?.dispose();
        this.scene?.traverse((obj) => {
            if (obj.geometry) {
                obj.geometry.dispose();
            }
            if (obj.material) {
                if (Array.isArray(obj.material)) {
                    obj.material.forEach((m) => m.dispose());
                } else {
                    obj.material.map?.dispose();
                    obj.material.dispose();
                }
            }
        });
    }
}
