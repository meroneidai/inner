import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('innerPathFlowHero', (words = []) => ({
    words: Array.isArray(words) && words.length ? words : ['universe'],
    wordIndex: 0,
    word: '',
    soundOn: false,
    music: null,
    ctx: null,
    master: null,
    musicSource: null,
    wordTimer: null,
    fadeTimer: null,
    lastPetalSound: 0,
    // CC0 ambient bed (AERIS MUSIC — Quiet Room), served locally for reliability.
    musicSrc: '/audio/inspiration-ambient.mp3',
    reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,

    init() {
        this.word = this.words[0];
        if (!this.reducedMotion) {
            this.wordTimer = window.setInterval(() => this.cycleWord(), 2800);
        }
        this.$refs.video?.play?.().catch(() => {});
    },

    cycleWord() {
        const el = this.$root.querySelector('.ip-flow-word');
        el?.classList.add('is-swap');
        window.setTimeout(() => {
            this.wordIndex = (this.wordIndex + 1) % this.words.length;
            this.word = this.words[this.wordIndex];
            el?.classList.remove('is-swap');
        }, 280);
    },

    async toggleSound() {
        if (!this.soundOn) {
            await this.startAudio();
            this.soundOn = true;
        } else {
            this.stopAudio();
            this.soundOn = false;
        }
    },

    async ensureAudioGraph() {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) {
            return false;
        }

        if (!this.ctx) {
            this.ctx = new AudioCtx();
            this.master = this.ctx.createGain();
            this.master.gain.value = 1;
            this.master.connect(this.ctx.destination);
        }

        if (this.ctx.state === 'suspended') {
            await this.ctx.resume();
        }

        return true;
    },

    async startAudio() {
        const ok = await this.ensureAudioGraph();
        if (!ok) {
            return;
        }

        if (!this.music) {
            this.music = new Audio(this.musicSrc);
            this.music.loop = true;
            this.music.preload = 'auto';
            this.music.crossOrigin = 'anonymous';
            this.music.volume = 0;

            try {
                this.musicSource = this.ctx.createMediaElementSource(this.music);
                const musicGain = this.ctx.createGain();
                musicGain.gain.value = 0.72;
                this.musicSource.connect(musicGain);
                musicGain.connect(this.master);
                this.musicGain = musicGain;
            } catch {
                // Fallback: element → speakers if MediaElementSource already linked
                this.musicSource = null;
            }
        }

        try {
            await this.music.play();
        } catch (error) {
            console.warn('Ambient music failed to play', error);
            return;
        }

        this.fadeMusic(0.42, 900);
    },

    fadeMusic(target, ms = 700) {
        if (!this.music) {
            return;
        }
        if (this.fadeTimer) {
            window.clearInterval(this.fadeTimer);
        }
        const start = this.music.volume;
        const steps = Math.max(1, Math.round(ms / 40));
        let step = 0;
        this.fadeTimer = window.setInterval(() => {
            step += 1;
            const t = step / steps;
            this.music.volume = start + (target - start) * t;
            if (step >= steps) {
                window.clearInterval(this.fadeTimer);
                this.fadeTimer = null;
                this.music.volume = target;
                if (target <= 0.001) {
                    this.music.pause();
                }
            }
        }, 40);
    },

    /** Soft chime when a petal drifts — layered over the ambient track. */
    onPetalMotion() {
        if (!this.soundOn || !this.ctx || !this.master) {
            return;
        }
        const now = performance.now();
        if (now - this.lastPetalSound < 520) {
            return;
        }
        this.lastPetalSound = now;

        const t = this.ctx.currentTime;
        const osc = this.ctx.createOscillator();
        const gain = this.ctx.createGain();
        const freqs = [523.25, 587.33, 659.25, 698.46, 783.99];
        osc.type = 'sine';
        osc.frequency.value = freqs[Math.floor(Math.random() * freqs.length)];
        gain.gain.value = 0;
        osc.connect(gain);
        gain.connect(this.master);
        gain.gain.setValueAtTime(0, t);
        gain.gain.linearRampToValueAtTime(0.03, t + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, t + 0.7);
        osc.start(t);
        osc.stop(t + 0.75);
    },

    stopAudio() {
        this.fadeMusic(0, 500);
    },

    destroy() {
        if (this.wordTimer) {
            window.clearInterval(this.wordTimer);
        }
        if (this.fadeTimer) {
            window.clearInterval(this.fadeTimer);
        }
        try {
            this.music?.pause();
        } catch {
            // ignore
        }
        this.ctx?.close?.();
    },
}));

Alpine.data('voiceRecorder', () => ({
    recording: false,
    mediaRecorder: null,
    chunks: [],
    audioUrl: null,
    blob: null,

    async toggle() {
        if (this.recording) {
            this.mediaRecorder?.stop();
            this.recording = false;
            return;
        }

        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        this.chunks = [];
        this.mediaRecorder = new MediaRecorder(stream);
        this.mediaRecorder.ondataavailable = (e) => this.chunks.push(e.data);
        this.mediaRecorder.onstop = () => {
            this.blob = new Blob(this.chunks, { type: 'audio/webm' });
            this.audioUrl = URL.createObjectURL(this.blob);
            const input = document.getElementById('audio-file-input');
            if (input) {
                const file = new File([this.blob], 'dream-recording.webm', { type: 'audio/webm' });
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
            }
            stream.getTracks().forEach((t) => t.stop());
        };
        this.mediaRecorder.start();
        this.recording = true;
    },
}));

Alpine.start();
