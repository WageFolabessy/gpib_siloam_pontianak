@extends('../components/main')

@section('title')
    - Jadwal Ibadah
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
    <div id="autoSpeak">
        <div class="container mt-4">
            <div class="card speakable">
                <div class="card-header bg-chocolate text-white">
                    <h5 class="card-title text-center">Jadwal Ibadah Minggu</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center table-striped">
                            <caption>Jadwal Ibadah Minggu</caption>
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Keterangan</th>
                                    <th scope="col">Jam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jadwalIbadah as $jadwal)
                                    @if ($jadwal->kategori == 'Ibadah Minggu')
                                        <tr>
                                            <td>{{ $jadwal->keterangan }}</td>
                                            <td>{{ $jadwal->jam }} WIB</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4">
            <div class="card speakable">
                <div class="card-header bg-chocolate text-white">
                    <h5 class="card-title text-center">Jadwal Ibadah Pelkat</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center table-striped">
                            <caption>Jadwal Ibadah Pelkat</caption>
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Keterangan</th>
                                    <th scope="col">Hari</th>
                                    <th scope="col">Jam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jadwalIbadah as $jadwal)
                                    @if ($jadwal->kategori == 'Ibadah Pelkat')
                                        <tr>
                                            <td>{{ $jadwal->keterangan }}</td>
                                            <td>{{ $jadwal->hari }}</td>
                                            <td>{{ $jadwal->jam }} WIB</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Fungsi bantu untuk memisahkan teks menjadi kalimat tanpa memecah bagian angka seperti 07.00
        function splitSentences(text) {
            let regex = /(.+?(?:(?<!\d)[.!?](?!\d)|$))/g;
            let sentences = [];
            let match;
            while ((match = regex.exec(text)) !== null) {
                if (match[1].trim()) {
                    sentences.push(match[1].trim());
                }
            }
            return sentences;
        }

        // Fungsi untuk membungkus teks dalam elemen target (p, li, serta elemen table: td, th, caption)
        function wrapSpeakableText() {
            const containers = document.querySelectorAll('.speakable');
            containers.forEach(container => {
                // Elemen paragraf
                let paragraphs = container.querySelectorAll('p');
                paragraphs.forEach(p => {
                    let text = p.innerText.trim();
                    if (text.length > 0) {
                        let sentences = splitSentences(text);
                        p.innerHTML = sentences.map(s => `<span class="sentence">${s} </span>`).join('');
                    }
                });
                // Elemen list item
                let listItems = container.querySelectorAll('li');
                listItems.forEach(li => {
                    let text = li.innerText.trim();
                    if (text.length > 0) {
                        let sentences = splitSentences(text);
                        li.innerHTML = sentences.map(s => `<span class="sentence">${s} </span>`).join('');
                    }
                });
                // Elemen table: td, th, caption
                let tableElements = container.querySelectorAll('td, th, caption');
                tableElements.forEach(el => {
                    let text = el.innerText.trim();
                    if (text.length > 0) {
                        let sentences = splitSentences(text);
                        el.innerHTML = sentences.map(s => `<span class="sentence">${s} </span>`).join('');
                    }
                });
            });
        }

        // Fungsi untuk melakukan speech synthesis dengan highlight.
        // Ubah juga pola waktu "07.00-09.00 WIB" menjadi "pukul 7 sampai pukul 9 WIB"
        function speakText(originalText, container) {
            // Cek status global speechEnabled
            if (!window.speechEnabled) return;

            const synth = window.speechSynthesis;
            let voices = synth.getVoices();
            // Pilih voice bahasa Indonesia (utamakan suara perempuan jika tersedia)
            let indoVoices = voices.filter(voice => voice.lang.toLowerCase().includes('id'));
            let selectedVoice = indoVoices.find(voice => voice.name.toLowerCase().includes('female')) ||
                (indoVoices.length ? indoVoices[0] : voices[0]);

            // Ambil semua elemen kalimat yang sudah dibungkus
            let sentenceEls = container ? container.querySelectorAll('.sentence') : document.querySelectorAll('.sentence');

            let computedSentences = [];
            sentenceEls.forEach(function(el) {
                let sentenceText = el.innerText.trim();
                // Ubah format waktu "07.00-09.00 WIB" menjadi "pukul 7 sampai pukul 9 WIB"
                sentenceText = sentenceText.replace(/(\d{2})\.00-(\d{2})\.00\s*WIB/gi, function(match, startHour,
                    endHour) {
                    return "pukul " + parseInt(startHour, 10) + " sampai pukul " + parseInt(endHour, 10) +
                        " WIB";
                });
                computedSentences.push(sentenceText);
            });

            let computedUtteranceText = computedSentences.length ? computedSentences.join(" ") : originalText;

            let utterance = new SpeechSynthesisUtterance(computedUtteranceText);
            utterance.voice = selectedVoice;
            utterance.lang = "id-ID";
            utterance.rate = 1.5;

            // Saat mulai, highlight kalimat pertama
            utterance.onstart = function() {
                if (sentenceEls.length > 0) {
                    sentenceEls.forEach(el => el.classList.remove('highlight'));
                    sentenceEls[0].classList.add('highlight');
                }
            };

            // Atur highlight berdasarkan posisi karakter
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

            // Hapus highlight setelah selesai
            utterance.onend = function() {
                if (sentenceEls.length > 0) {
                    sentenceEls.forEach(el => el.classList.remove('highlight'));
                }
            };

            synth.speak(utterance);
        }

        function initSpeechFeatures() {
            const synth = window.speechSynthesis;
            // Bungkus teks dalam elemen 'speakable'
            wrapSpeakableText();

            // Baca otomatis konten di dalam #autoSpeak
            let autoElement = document.getElementById("autoSpeak");
            if (autoElement) {
                let text = autoElement.innerText;
                speakText(text, autoElement);
            }

            // Tambahkan event listener agar teks dibacakan ulang saat mouse masuk (mouseenter)
            let speakableElements = document.querySelectorAll('.speakable');
            speakableElements.forEach(function(el) {
                el.addEventListener('mouseenter', function() {
                    if (!window.speechEnabled) return;
                    document.querySelectorAll('.sentence').forEach(el => el.classList.remove('highlight'));
                    synth.cancel();
                    let text = el.innerText;
                    speakText(text, el);
                });
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            if (window.speechSynthesis.getVoices().length !== 0) {
                initSpeechFeatures();
            } else {
                window.speechSynthesis.onvoiceschanged = initSpeechFeatures;
            }
        });
    </script>
@endsection
