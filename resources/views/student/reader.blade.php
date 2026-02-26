<x-app-layout>
    <style>
        .night-mode {
            background-color: #1a202c !important;
            color: #e2e8f0 !important;
        }

        .night-mode .bg-white {
            background-color: #2d3748 !important;
            color: #e2e8f0 !important;
        }

        .night-mode .bg-gray-50 {
            background-color: #1a202c !important;
            border-bottom-color: #4a5568 !important;
        }

        .night-mode .bg-gray-100,
        .night-mode .bg-gray-200 {
            background-color: #171923 !important;
        }

        .night-mode #pdf-container canvas {
            filter: brightness(0.8) contrast(1.2);
        }

        /* Ensure touch targets are large enough */
        .btn-touch {
            min-width: 44px;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <div id="reader-wrapper" class="flex flex-col h-screen overflow-hidden transition-colors duration-300">
        <!-- Header -->
        <header
            class="bg-white shadow py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center z-30 sticky top-0 border-b">
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}" class="btn-touch text-gray-600 hover:text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-lg text-gray-800 leading-tight truncate max-w-[150px] md:max-w-md">
                    {{ $book->title }}
                </h2>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Read Aloud Button -->
                <button id="tts-toggle"
                    class="btn-touch bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition"
                    title="Read Aloud">
                    <svg id="play-icon" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                            clip-rule="evenodd" />
                    </svg>
                    <svg id="pause-icon" class="w-6 h-6 hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <button id="tts-settings-toggle" class="btn-touch bg-gray-100 rounded-full hover:bg-gray-200 transition"
                    title="Voice Settings">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </button>

                <button id="night-mode-toggle" class="btn-touch bg-gray-100 rounded-full hover:bg-gray-200 transition"
                    title="Toggle Night Mode">
                    <svg id="sun-icon" class="w-6 h-6 text-yellow-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z">
                        </path>
                    </svg>
                    <svg id="moon-icon" class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </button>
                <div class="bg-blue-600 text-white px-3 py-1 rounded text-sm font-bold min-w-[60px] text-center">
                    <span id="page-num">{{ $progress->current_page }}</span> / <span id="page-count">0</span>
                </div>
            </div>
        </header>

        <!-- TTS Settings Modal -->
        <div id="tts-settings-modal"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Reading Voice Settings</h3>
                    <button id="close-tts-settings" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Voice</label>
                        <select id="voice-select"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <!-- Voices populated by JS -->
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reading Speed</label>
                        <input type="range" id="rate-range" min="0.5" max="2" step="0.1" value="1" class="w-full">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Slower</span>
                            <span id="rate-value">1.0x</span>
                            <span>Faster</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button id="save-tts-settings"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Done
                    </button>
                </div>
            </div>
        </div>

        <!-- PDF Container -->
        <main class="flex-1 overflow-y-auto bg-gray-200 p-2 sm:p-4 scroll-smooth" id="pdf-container">
            <div id="pages-wrapper" class="max-w-4xl mx-auto flex flex-col items-center space-y-4">
                <!-- Canvases will be injected here -->
            </div>

            <!-- Loading Spinner -->
            <div id="loading" class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-90 z-50">
                <div class="flex flex-col items-center">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-600 mb-4"></div>
                    <p class="text-blue-600 font-bold animate-pulse">Loading Book...</p>
                </div>
            </div>
        </main>
    </div>

    <!-- PDF.js scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <script>
        const url = "{{ route('books.stream', $book->id) }}";
        const bookId = {{ $book->id }};
        const initialPage = {{ $progress->current_page }};

        let pdfDoc = null;
        let lastPingedPage = initialPage;
        const scale = window.innerWidth < 768 ? 1.0 : 1.5;

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

        // TTS Manager Class
        class TTSManager {
            constructor() {
                this.synth = window.speechSynthesis;
                this.utterance = new SpeechSynthesisUtterance();
                this.isPlaying = false;
                this.currentPage = 1;
                this.sentences = [];
                this.currentSentenceIndex = 0;
                this.voices = [];
                this.selectedVoiceURI = localStorage.getItem('tts-voice-uri');
                this.rate = parseFloat(localStorage.getItem('tts-rate')) || 1.0;

                this.initVoices();
                this.setupEventListeners();
            }

            initVoices() {
                const loadVoices = () => {
                    this.voices = this.synth.getVoices();
                    this.populateVoiceSelect();
                };
                loadVoices();
                if (this.synth.onvoiceschanged !== undefined) {
                    this.synth.onvoiceschanged = loadVoices;
                }
            }

            populateVoiceSelect() {
                const select = document.getElementById('voice-select');
                if (!select) return;

                select.innerHTML = '';
                this.voices.forEach(voice => {
                    if (voice.lang.includes('en')) { // Filter for English by default for this library
                        const option = document.createElement('option');
                        option.textContent = `${voice.name} (${voice.lang})`;
                        option.value = voice.voiceURI;
                        if (voice.voiceURI === this.selectedVoiceURI) option.selected = true;
                        select.appendChild(option);
                    }
                });
            }

            setupEventListeners() {
                this.utterance.onend = () => {
                    if (this.isPlaying) {
                        this.currentSentenceIndex++;
                        if (this.currentSentenceIndex < this.sentences.length) {
                            this.speakSentence();
                        } else {
                            this.goToNextPage();
                        }
                    }
                };

                this.utterance.onerror = (event) => {
                    console.error('TTS Error:', event);
                    this.stop();
                };
            }

            async extractTextFromPage(pageNum) {
                const page = await pdfDoc.getPage(pageNum);
                const textContent = await page.getTextContent();
                const viewport = page.getViewport({ scale: 1 });

                // Heuristic filtering
                const items = textContent.items
                    .map(item => {
                        const tx = pdfjsLib.Util.transform(viewport.transform, item.transform);
                        return {
                            text: item.str.trim(),
                            y: tx[5] / viewport.height // normalized Y coordinate
                        };
                    })
                    .filter(item => {
                        if (!item.text) return false;

                        // 1. Filter headers (top 7%) and footers (bottom 7%)
                        if (item.y < 0.07 || item.y > 0.93) {
                            // Only filter if it looks like a page number or repetitive header
                            if (/^\d+$/.test(item.text) || item.text.toLowerCase().includes('page')) {
                                return false;
                            }
                        }

                        // 2. Filter standalone numbers (likely page numbers)
                        if (/^\d+$/.test(item.text)) return false;

                        return true;
                    });

                const rawText = items.map(i => i.text).join(' ');

                // Split into sentences
                this.sentences = rawText.match(/[^.!?]+[.!?]+/g) || [rawText];
                this.currentSentenceIndex = 0;
                this.currentPage = pageNum;

                return this.sentences.length > 0;
            }

            speakSentence() {
                if (!this.sentences[this.currentSentenceIndex]) return;

                this.utterance.text = this.sentences[this.currentSentenceIndex];

                // Set voice
                const voice = this.voices.find(v => v.voiceURI === this.selectedVoiceURI);
                if (voice) this.utterance.voice = voice;

                this.utterance.rate = this.rate;

                this.synth.speak(this.utterance);
            }

            async start(pageNum) {
                if (this.isPlaying && this.currentPage === pageNum) {
                    this.synth.resume();
                    return;
                }

                this.stop();
                const hasText = await this.extractTextFromPage(pageNum);
                if (hasText) {
                    this.isPlaying = true;
                    this.speakSentence();
                    this.updateUI();
                } else {
                    console.warn('No text found on page', pageNum);
                    this.goToNextPage();
                }
            }

            pause() {
                this.isPlaying = false;
                this.synth.pause();
                this.updateUI();
            }

            stop() {
                this.isPlaying = false;
                this.synth.cancel();
                this.updateUI();
            }

            async goToNextPage() {
                if (this.currentPage < pdfDoc.numPages) {
                    const nextPage = this.currentPage + 1;
                    const nextCanvas = document.getElementById(`page-${nextPage}`);
                    if (nextCanvas) {
                        nextCanvas.scrollIntoView({ behavior: 'smooth' });
                        // Small delay to let scroll happen
                        setTimeout(() => this.start(nextPage), 800);
                    }
                } else {
                    this.stop();
                }
            }

            updateUI() {
                const playIcon = document.getElementById('play-icon');
                const pauseIcon = document.getElementById('pause-icon');
                if (this.isPlaying) {
                    playIcon.classList.add('hidden');
                    pauseIcon.classList.remove('hidden');
                } else {
                    playIcon.classList.remove('hidden');
                    pauseIcon.classList.add('hidden');
                }
            }

            setVoice(uri) {
                this.selectedVoiceURI = uri;
                localStorage.setItem('tts-voice-uri', uri);
            }

            setRate(rate) {
                this.rate = rate;
                localStorage.setItem('tts-rate', rate);
                if (this.isPlaying) {
                    this.stop();
                    this.start(this.currentPage);
                }
            }
        }

        const tts = new TTSManager();

        // UI Handlers
        document.getElementById('tts-toggle').addEventListener('click', () => {
            if (tts.isPlaying) {
                tts.pause();
            } else {
                const currentPage = parseInt(document.getElementById('page-num').textContent);
                tts.start(currentPage);
            }
        });

        const settingsModal = document.getElementById('tts-settings-modal');
        document.getElementById('tts-settings-toggle').addEventListener('click', () => {
            settingsModal.classList.remove('hidden');
        });

        document.getElementById('close-tts-settings').addEventListener('click', () => {
            settingsModal.classList.add('hidden');
        });

        document.getElementById('save-tts-settings').addEventListener('click', () => {
            const voiceSelect = document.getElementById('voice-select');
            const rateRange = document.getElementById('rate-range');

            tts.setVoice(voiceSelect.value);
            tts.setRate(parseFloat(rateRange.value));

            settingsModal.classList.add('hidden');
        });

        document.getElementById('rate-range').addEventListener('input', (e) => {
            document.getElementById('rate-value').textContent = `${e.target.value}x`;
        });

        // Night Mode Logic
        const toggleBtn = document.getElementById('night-mode-toggle');
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');
        const wrapper = document.getElementById('reader-wrapper');

        function toggleNightMode() {
            const isNight = wrapper.classList.toggle('night-mode');
            sunIcon.classList.toggle('hidden', !isNight);
            moonIcon.classList.toggle('hidden', isNight);
            localStorage.setItem('night-mode', isNight ? 'true' : 'false');
        }

        if (localStorage.getItem('night-mode') === 'true') {
            toggleNightMode();
        }

        toggleBtn.addEventListener('click', toggleNightMode);

        // Render Page Function
        async function renderPage(num) {
            const page = await pdfDoc.getPage(num);
            const viewport = page.getViewport({ scale });

            const canvas = document.createElement('canvas');
            canvas.id = `page-${num}`;
            canvas.className = 'shadow-xl bg-white mb-4 max-w-full';
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            canvas.dataset.page = num;

            document.getElementById('pages-wrapper').appendChild(canvas);

            const renderCtx = {
                canvasContext: canvas.getContext('2d'),
                viewport
            };

            await page.render(renderCtx).promise;
            return canvas;
        }

        // Initialize Observer for scroll-tracking
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && entry.intersectionRatio > 0.5) {
                    const pageNum = parseInt(entry.target.dataset.page);
                    document.getElementById('page-num').textContent = pageNum;

                    if (pageNum !== lastPingedPage) {
                        trackProgress(pageNum);
                        lastPingedPage = pageNum;
                    }
                }
            });
        }, { threshold: 0.6 });

        async function trackProgress(pageNumber) {
            try {
                await axios.post(`/books/${bookId}/progress`, { page: pageNumber });
                console.log('Progress saved:', pageNumber);
            } catch (err) {
                console.error('Progress sync failed:', err);
            }
        }

        // Load Document
        async function init() {
            try {
                pdfDoc = await pdfjsLib.getDocument(url).promise;
                document.getElementById('page-count').textContent = pdfDoc.numPages;

                // Render all pages (lazy-ish or just sequentially)
                for (let i = 1; i <= pdfDoc.numPages; i++) {
                    const canvas = await renderPage(i);
                    observer.observe(canvas);
                }

                document.getElementById('loading').classList.add('hidden');

                // Scroll to initial page if not 1
                if (initialPage > 1) {
                    setTimeout(() => {
                        const targetCanvas = document.getElementById(`page-${initialPage}`);
                        if (targetCanvas) targetCanvas.scrollIntoView();
                    }, 500);
                }

            } catch (err) {
                console.error('PDF Init Error:', err);
                let message = 'Oops! We couldn\'t load the PDF. Check your internet connection.';
                if (err.response && err.response.data && err.response.data.error) {
                    message = err.response.data.error;
                }
                alert(message);
            }
        }

        init();
    </script>

</x-app-layout>