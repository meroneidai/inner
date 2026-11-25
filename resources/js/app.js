import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('innerPathFlowHero', (words = []) => ({
    words: Array.isArray(words) && words.length ? words : ['universe'],
    wordIndex: 0,
    word: '',
    soundOn: true,
    wordTimer: null,
    unlockBound: null,
    reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,

    init() {
        this.word = this.words[0];

        if (!this.reducedMotion) {
            this.wordTimer = window.setInterval(() => this.cycleWord(), 2800);
        }

        this.ensureVideoPlaying();
        this.startMusic();

        this.unlockBound = (event) => {
            // Mute button has its own handler; a global unlock on the same
            // pointerdown would start audio and then the click would mute it.
            if (event?.target?.closest?.('.ip-flow-sound')) {
                return;
            }

            this.ensureVideoPlaying();
            if (this.soundOn) {
                this.startMusic();
            }

            window.removeEventListener('pointerdown', this.unlockBound);
            window.removeEventListener('touchstart', this.unlockBound);
            window.removeEventListener('keydown', this.unlockBound);
            this.unlockBound = null;
        };

        window.addEventListener('pointerdown', this.unlockBound);
        window.addEventListener('touchstart', this.unlockBound, { passive: true });
        window.addEventListener('keydown', this.unlockBound);
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.ensureVideoPlaying();
                if (this.soundOn) {
                    this.startMusic();
                }
            }
        });
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

    ensureVideoPlaying() {
        const video = this.$refs.video;
        if (!video) {
            return;
        }

        video.muted = true;
        video.playsInline = true;
        video.play?.().catch(() => {});
    },

    async toggleSound() {
        const audio = this.$refs.music;
        const playing = audio && !audio.paused;

        // If UI says "on" but autoplay was blocked, first click starts audio.
        if (this.soundOn && !playing) {
            await this.startMusic();
            return;
        }

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

        const audio = this.$refs.music;
        if (!audio) {
            return;
        }

        audio.loop = true;
        audio.volume = 0.55;

        try {
            await audio.play();
        } catch {
            // Autoplay blocked until a gesture — unlockBound / mute button retries.
        }
    },

    pauseMusic() {
        const audio = this.$refs.music;
        if (!audio) {
            return;
        }

        try {
            audio.pause();
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
            window.removeEventListener('touchstart', this.unlockBound);
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
