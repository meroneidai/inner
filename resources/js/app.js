import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('innerPathFlowHero', (words = []) => ({
    words: Array.isArray(words) && words.length ? words : ['universe'],
    wordIndex: 0,
    word: '',
    // Start false so the icon shows "play" when browsers block autoplay.
    soundOn: false,
    wordTimer: null,
    unlockBound: null,
    reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,

    init() {
        this.word = this.words[0];

        if (!this.reducedMotion) {
            this.wordTimer = window.setInterval(() => this.cycleWord(), 2800);
        }

        const audio = this.$refs.music;
        if (audio) {
            audio.loop = true;
            audio.volume = 0.7;
            audio.addEventListener('play', () => {
                this.soundOn = true;
            });
            audio.addEventListener('pause', () => {
                // Only mark off if we intentionally paused / autoplay never started.
                if (audio.currentTime === 0 || audio.ended) {
                    this.soundOn = false;
                }
            });
        }

        this.ensureVideoPlaying();
        this.tryAutoplayMusic();

        this.unlockBound = (event) => {
            if (event?.target?.closest?.('.ip-flow-sound')) {
                return;
            }

            this.ensureVideoPlaying();
            this.enableMusicFromGesture();
            this.clearUnlockListeners();
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

    clearUnlockListeners() {
        if (!this.unlockBound) {
            return;
        }

        window.removeEventListener('pointerdown', this.unlockBound);
        window.removeEventListener('touchstart', this.unlockBound);
        window.removeEventListener('keydown', this.unlockBound);
        this.unlockBound = null;
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

    async tryAutoplayMusic() {
        const audio = this.$refs.music;
        if (!audio) {
            return;
        }

        try {
            await audio.play();
            this.soundOn = true;
            this.clearUnlockListeners();
        } catch {
            this.soundOn = false;
        }
    },

    async enableMusicFromGesture() {
        this.soundOn = true;
        await this.startMusic();
    },

    async toggleSound() {
        const audio = this.$refs.music;
        const playing = Boolean(audio && !audio.paused);

        if (playing) {
            this.pauseMusic();
            this.soundOn = false;
            return;
        }

        this.clearUnlockListeners();
        await this.enableMusicFromGesture();
    },

    async startMusic() {
        const audio = this.$refs.music;
        if (!audio) {
            return false;
        }

        audio.loop = true;
        audio.volume = 0.7;

        try {
            await audio.play();
            this.soundOn = true;
            return true;
        } catch {
            this.soundOn = false;
            return false;
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
        this.clearUnlockListeners();
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
