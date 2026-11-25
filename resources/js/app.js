import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('innerPathFlowHero', (words = [], musicSrc = '/audio/inspiration-ambient.mp3') => ({
    words: Array.isArray(words) && words.length ? words : ['universe'],
    wordIndex: 0,
    word: '',
    soundOn: true,
    music: null,
    wordTimer: null,
    musicSrc,
    unlockBound: null,
    reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,

    init() {
        this.word = this.words[0];
        if (!this.reducedMotion) {
            this.wordTimer = window.setInterval(() => this.cycleWord(), 2800);
        }
        this.$refs.video?.play?.().catch(() => {});
        this.startMusic();
        this.unlockBound = () => this.startMusic();
        window.addEventListener('pointerdown', this.unlockBound, { once: true });
        window.addEventListener('keydown', this.unlockBound, { once: true });
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
        if (this.soundOn) {
            this.pauseMusic();
            this.soundOn = false;
            return;
        }

        this.soundOn = true;
        await this.startMusic();
    },

    async startMusic() {
        if (!this.soundOn) {
            return;
        }

        if (!this.music) {
            this.music = new Audio(this.musicSrc);
            this.music.loop = true;
            this.music.preload = 'auto';
            this.music.volume = 0.55;
        }

        try {
            await this.music.play();
        } catch {
            // Autoplay blocked until a user gesture — unlockBound handles that.
        }
    },

    pauseMusic() {
        try {
            this.music?.pause();
        } catch {
            // ignore
        }
    },

    destroy() {
        if (this.wordTimer) {
            window.clearInterval(this.wordTimer);
        }
        if (this.unlockBound) {
            window.removeEventListener('pointerdown', this.unlockBound);
            window.removeEventListener('keydown', this.unlockBound);
        }
        this.pauseMusic();
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
