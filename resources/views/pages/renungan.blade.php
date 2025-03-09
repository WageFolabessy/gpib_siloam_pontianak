@extends('../components/main')
@section('title')
    - Renungan
@endsection

@section('style')
    <style>
        .highlight {
            background-color: #ffeb3b;
            transition: background-color 0.3s ease;
        }
    </style>
@endsection

@section('content')
    <!-- Bungkus seluruh konten renungan agar bisa dibacakan secara otomatis -->
    <div id="autoSpeak">
        <div class="container mt-5 speakable">
            <div class="jumbotron text-center">
                <h1 class="display-6">Renungan</h1>
                <p class="lead">
                    Dalam pelukan hangat renungan rohani ini. Semoga setiap kata yang
                    terhampar di halaman ini menjadi sumber kekuatan dan ketenangan bagi
                    hati dan jiwa Anda. Bersama-sama, mari kita renungkan Firman-Nya dan
                    temukan cahaya-Nya yang memandu langkah-langkah kita.
                </p>
                <hr class="my-4" />
                <p>Tuhan memberkati perjalanan rohaniah kita bersama!</p>
                <hr class="my-4" />
            </div>
        </div>

        <!-- RENUNGAN -->
        <div class="container mt-4 speakable">
            <div class="row justify-content-start" id="renungan-container">
                <!-- Data Renungan akan ditampilkan di sini -->
            </div>
            <div class="text-center mt-3">
                <button id="load-more" class="btn btn-get-started">Muat Lebih Banyak</button>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/dashboard/vendor/jquery/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('assets/pages/js/renungan/renungan.js') }}"></script>
    <!-- Speech Synthesis Script -->
    <script>
        // Fungsi pembungkus teks: memecah teks ke dalam <span class="sentence">
        function wrapSpeakableText() {
            const containers = document.querySelectorAll('.speakable');
            containers.forEach(container => {
                // Proses elemen paragraf
                let paragraphs = container.querySelectorAll('p');
                paragraphs.forEach(p => {
                    let text = p.innerText.trim();
                    if (text.length > 0) {
                        let sentences = text.match(/[^\.!\?]+[\.!\?]+/g);
                        if (!sentences) {
                            sentences = [text];
                        }
                        p.innerHTML = sentences.map(s => `<span class="sentence">${s.trim()} </span>`).join(
                            '');
                    }
                });
                // Proses elemen list item (jika ada)
                let listItems = container.querySelectorAll('li');
                listItems.forEach(li => {
                    let text = li.innerText.trim();
                    if (text.length > 0) {
                        let sentences = text.match(/[^\.!\?]+[\.!\?]+/g);
                        if (!sentences) {
                            sentences = [text];
                        }
                        li.innerHTML = sentences.map(s => `<span class="sentence">${s.trim()} </span>`)
                            .join('');
                    }
                });
            });
        }

        // Fungsi untuk melakukan speech synthesis dengan highlight pada kalimat yang sedang dibacakan
        function speakText(originalText, container) {
            // Cek apakah fitur speech diaktifkan secara global
            if (!window.speechEnabled) return;

            const synth = window.speechSynthesis;
            let voices = synth.getVoices();
            // Pilih voice bahasa Indonesia, utamakan suara perempuan jika tersedia.
            let indoVoices = voices.filter(voice => voice.lang.toLowerCase().includes('id'));
            let selectedVoice = indoVoices.find(voice => voice.name.toLowerCase().includes('female')) ||
                (indoVoices.length ? indoVoices[0] : voices[0]);

            // Ambil semua elemen kalimat dalam container.
            let sentenceEls = container ? container.querySelectorAll('.sentence') : document.querySelectorAll('.sentence');

            // Gabungkan teks terbungkus menjadi satu teks utuh.
            let computedSentences = [];
            sentenceEls.forEach(function(el) {
                let sentenceText = el.innerText.trim();
                computedSentences.push(sentenceText);
            });
            let computedUtteranceText = computedSentences.join(" ");

            let utterance = new SpeechSynthesisUtterance(computedUtteranceText);
            utterance.voice = selectedVoice;
            utterance.lang = "id-ID";
            utterance.rate = 1.5;

            // Highlight kalimat pertama saat pembacaan dimulai.
            utterance.onstart = function() {
                if (sentenceEls.length > 0) {
                    sentenceEls.forEach(el => el.classList.remove('highlight'));
                    sentenceEls[0].classList.add('highlight');
                }
            };

            // Update highlight sesuai posisi karakter yang sedang dibacakan.
            utterance.onboundary = function(event) {
                let cumulativeLength = 0;
                let currentSentenceIndex = 0;
                for (let i = 0; i < computedSentences.length; i++) {
                    cumulativeLength += computedSentences[i].length;
                    if (event.charIndex < cumulativeLength) {
                        currentSentenceIndex = i;
                        break;
                    }
                }
                sentenceEls.forEach(el => el.classList.remove('highlight'));
                if (sentenceEls[currentSentenceIndex]) {
                    sentenceEls[currentSentenceIndex].classList.add('highlight');
                }
            };

            // Hapus highlight setelah selesai.
            utterance.onend = function() {
                if (sentenceEls.length > 0) {
                    sentenceEls.forEach(el => el.classList.remove('highlight'));
                }
            };

            synth.speak(utterance);
        }

        // Inisialisasi fitur speech: bungkus teks dan mulai auto-read.
        function initSpeechFeatures() {
            wrapSpeakableText();
            let autoElement = document.getElementById("autoSpeak");
            if (autoElement) {
                let text = autoElement.innerText;
                speakText(text, autoElement);
            }
        }

        // Jika browser sudah siap dengan voices, inisialisasi
        document.addEventListener("DOMContentLoaded", function() {
            if (window.speechSynthesis.getVoices().length !== 0) {
                initSpeechFeatures();
            } else {
                window.speechSynthesis.onvoiceschanged = initSpeechFeatures;
            }
        });

        // Untuk menangani elemen yang dimuat secara dinamis, gunakan event delegation:
        $(document).on('mouseenter', '.speakable', function() {
            if (!window.speechEnabled) return;
            window.speechSynthesis.cancel();
            let text = $(this).text();
            speakText(text, this);
        });

        
    </script>
@endsection
