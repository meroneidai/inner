import { JOURNEY_WORLDS } from './worlds';

/**
 * Soft procedural score — muted until toggled; morphs chord + filter per world.
 */
export class AmbientAudio {
    constructor({ button } = {}) {
        this.button = button;
        this.enabled = false;
        this.ctx = null;
        this.master = null;
        this.oscillators = [];
        this.oscGains = [];
        this.filter = null;
        this.noiseGain = null;
        this.worldIndex = 0;

        this.onToggle = this.onToggle.bind(this);
        this.button?.addEventListener('click', this.onToggle);
        this.syncButton();
    }

    async onToggle() {
        if (!this.enabled) {
            await this.start();
            this.enabled = true;
        } else {
            this.stop();
            this.enabled = false;
        }
        this.syncButton();
    }

    syncButton() {
        if (!this.button) {
            return;
        }
        this.button.setAttribute('aria-pressed', this.enabled ? 'true' : 'false');
        this.button.dataset.state = this.enabled ? 'on' : 'off';
        const labelOn = this.button.dataset.labelOn || 'Mute sound';
        const labelOff = this.button.dataset.labelOff || 'Unmute sound';
        this.button.setAttribute('aria-label', this.enabled ? labelOn : labelOff);
        this.button.title = this.enabled ? labelOn : labelOff;
    }

    async start() {
        if (this.ctx) {
            await this.ctx.resume();
            this.master.gain.setTargetAtTime(0.16, this.ctx.currentTime, 0.5);
            return;
        }

        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) {
            return;
        }

        this.ctx = new AudioCtx();
        this.master = this.ctx.createGain();
        this.master.gain.value = 0;
        this.master.connect(this.ctx.destination);

        this.filter = this.ctx.createBiquadFilter();
        this.filter.type = 'lowpass';
        this.filter.frequency.value = 480;
        this.filter.Q.value = 0.8;
        this.filter.connect(this.master);

        const mood = JOURNEY_WORLDS[0].audio;
        mood.freqs.forEach((freq, i) => {
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();
            osc.type = i % 2 === 0 ? 'sine' : 'triangle';
            osc.frequency.value = freq;
            gain.gain.value = i === 0 ? 0.04 : 0.018;
            osc.connect(gain);
            gain.connect(this.filter);
            osc.start();
            this.oscillators.push(osc);
            this.oscGains.push(gain);
        });

        const bufferSize = 2 * this.ctx.sampleRate;
        const noiseBuffer = this.ctx.createBuffer(1, bufferSize, this.ctx.sampleRate);
        const data = noiseBuffer.getChannelData(0);
        let last = 0;
        for (let i = 0; i < bufferSize; i += 1) {
            const white = Math.random() * 2 - 1;
            last = (last + 0.02 * white) / 1.02;
            data[i] = last * 3.5;
        }
        const noise = this.ctx.createBufferSource();
        noise.buffer = noiseBuffer;
        noise.loop = true;
        this.noiseGain = this.ctx.createGain();
        this.noiseGain.gain.value = mood.noise;
        noise.connect(this.noiseGain);
        this.noiseGain.connect(this.filter);
        noise.start();
        this.noise = noise;

        // Slow shimmer LFO
        const lfo = this.ctx.createOscillator();
        const lfoGain = this.ctx.createGain();
        lfo.frequency.value = 0.07;
        lfoGain.gain.value = 40;
        lfo.connect(lfoGain);
        lfoGain.connect(this.filter.frequency);
        lfo.start();
        this.lfo = lfo;

        this.master.gain.setTargetAtTime(0.16, this.ctx.currentTime, 0.8);
        this.setWorld(this.worldIndex);
    }

    setWorld(index) {
        this.worldIndex = index;
        if (!this.ctx || !this.filter) {
            return;
        }
        const mood = JOURNEY_WORLDS[Math.min(index, JOURNEY_WORLDS.length - 1)].audio;
        const now = this.ctx.currentTime;
        this.filter.frequency.setTargetAtTime(mood.filter, now, 1.1);
        mood.freqs.forEach((freq, i) => {
            this.oscillators[i]?.frequency.setTargetAtTime(freq, now, 1.2);
        });
        if (this.noiseGain) {
            this.noiseGain.gain.setTargetAtTime(mood.noise, now, 1.0);
        }
    }

    /**
     * Continuous blend between neighboring world scores.
     */
    setBlend(indexA, indexB, mix) {
        if (!this.ctx || !this.filter) {
            this.worldIndex = indexA;
            return;
        }
        const a = JOURNEY_WORLDS[indexA].audio;
        const b = JOURNEY_WORLDS[indexB].audio;
        const t = mix * mix * (3 - 2 * mix);
        const now = this.ctx.currentTime;
        this.filter.frequency.setTargetAtTime(a.filter + (b.filter - a.filter) * t, now, 0.35);
        for (let i = 0; i < 4; i += 1) {
            const f = a.freqs[i] + (b.freqs[i] - a.freqs[i]) * t;
            this.oscillators[i]?.frequency.setTargetAtTime(f, now, 0.35);
        }
        if (this.noiseGain) {
            this.noiseGain.gain.setTargetAtTime(a.noise + (b.noise - a.noise) * t, now, 0.35);
        }
    }

    stop() {
        if (!this.ctx || !this.master) {
            return;
        }
        this.master.gain.setTargetAtTime(0, this.ctx.currentTime, 0.3);
        window.setTimeout(() => this.ctx?.suspend?.(), 450);
    }

    dispose() {
        this.button?.removeEventListener('click', this.onToggle);
        try {
            this.noise?.stop?.();
            this.lfo?.stop?.();
            this.oscillators.forEach((o) => o.stop());
        } catch {
            // already stopped
        }
        this.ctx?.close?.();
        this.ctx = null;
    }
}
