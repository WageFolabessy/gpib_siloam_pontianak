@extends('../components/main')
@section('title')
    - Info
@endsection
@section('style')
    <style>
        .highlight {
            background-color: #ffeb3b;
            /* Warna kuning terang */
            transition: background-color 0.3s ease;
        }
    </style>
@endsection

@section('content')
    <!-- Konten Info tanpa mengubah struktur tampilan -->
    <div id="autoSpeak">
        <!-- Informasi Sejarah -->
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0">
                        <div class="card-body text-center speakable">
                            <h4 class="card-title">Sejarah</h4>
                            <p class="card-text">
                                "SILOAM" adalah nama Jemaat yang ditetapkan berdasarkan Surat
                                Penjerahan, tertanggal Pontianak 29 September 1963 dari Panitia
                                Pembangunan Ibadat Pontianak yang disebut Panitia 9 menyerahkan
                                kepada Madjelis Sinode GPIB Djakarta yang menjaksikan Madjelis
                                Geredja GPIB Pontianak, sesuai data histories bernama De
                                Protestansche Gemeente te Pontianak atau Jemaat Protestan di
                                Pontianak menjadi GPIB Jemaat "SILOAM" Pontianak. Perlu kiranya
                                diketahui bahwa pada masa peralihan dari De Indishe Kerk (DIK)
                                kepada GEREJA PROTESTAN di INDONESIA bagian BARAT pada tahun
                                1948 terakhir kali dikuatkan dengan Surat Keputusan Menteri
                                Dalam Negeri RI No. SK. 22//DDA/1969/D/13, tanggal Jakarta
                                20-3-1978.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Visi, Misi -->
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0">
                        <div class="card-body text-center speakable">
                            <h4 class="card-title">Visi</h4>
                            <p class="card-text">
                                Gereja GPIB Jemaat SILOAM Pontianak memiliki visi untuk menjadi
                                Gereja yang mewujudkan damai sejahtera Allah bagi seluruh
                                ciptaan-Nya, serta berperan aktif dalam pelayanan sosial,
                                pendidikan, dan lingkungan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 mt-4">
                    <div class="card border-0">
                        <div class="card-body text-justify speakable">
                            <h4 class="card-title text-center">Misi</h4>
                            <ol>
                                <li>
                                    Menjadi Gereja yang terus menerus diperbaharui dengan bertolak
                                    dari firman Allah yang terwujud dalam perilaku warga gereja,
                                    baik dalam Persekutuan maupun dalam hidup bermasyarakat.
                                </li>
                                <li>
                                    Menjadi Gereja yang hadir sebagai contoh kehidupan, yang
                                    terwujud melalui inisiatif dan partisipasi dalam
                                    kesetiakawanan sosial serta kerukunan dalam Masyarakat, dengan
                                    berbasis pada perilaku kehidupan keluarga yang kuat dan
                                    sejahtera.
                                </li>
                                <li>
                                    Menjadi Gereja yang membangun keutuhan ciptaan yang terwujud
                                    melalui perhatian terhadap lingkungan hidup, semangat keesaan
                                    dan semangat persatuan dan kesatuan warga Gereja sebagai warga
                                    masyarakat.
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Pendeta dan Wilayah Pelayanan -->
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- Info Pendeta -->
                    <div class="card mb-4">
                        <div class="card-body speakable">
                            <h4 class="card-title">Pendeta yang Melayani Saat Ini</h4>
                            <ul>
                                @foreach ($pengurus as $item)
                                    @if ($item->kategori == 'Ketua Majelis Jemaat')
                                        <li>
                                            <strong>Ketua Majelis Jemaat:</strong> {{ $item->nama }}
                                        </li>
                                    @elseif ($item->kategori == 'Pendeta Jemaat')
                                        <li>
                                            <strong>Pendeta Jemaat:</strong> {{ $item->nama }}
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <!-- Wilayah Pelayanan -->
                    <div class="card">
                        <div class="card-body speakable">
                            <h4 class="card-title">Wilayah Pelayanan</h4>
                            <ul>
                                <li>GPIB Jemaat "Siloam" Pontianak, Kota Pontianak</li>
                                <li>Bajem Tuah Petara Sintang, Kabupaten Sintang</li>
                                <li>Pos Pelkes Ekklesia Nanga Silat, Kabupaten Kapuas Hulu</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Fungsi untuk membungkus teks di dalam elemen target (paragraf dan list item)
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
                // Proses elemen list item
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

        // Fungsi untuk melakukan speech synthesis dengan highlight pada kalimat yang sedang dibacakan.
        function speakText(text, container) {
            // Cek apakah fitur speech diaktifkan secara global
            if (!window.speechEnabled) return;

            const synth = window.speechSynthesis;
            let voices = synth.getVoices();
            // Pilih voice bahasa Indonesia (utamakan suara perempuan jika tersedia)
            let indoVoices = voices.filter(voice => voice.lang.toLowerCase().includes('id'));
            let selectedVoice = indoVoices.find(voice => voice.name.toLowerCase().includes('female')) ||
                (indoVoices.length ? indoVoices[0] : voices[0]);

            let utterance = new SpeechSynthesisUtterance(text);
            utterance.voice = selectedVoice;
            utterance.lang = "id-ID";
            utterance.rate = 1.5;

            // Ambil kalimat-kalimat dari container (jika ada)
            let sentenceEls = container ? container.querySelectorAll('.sentence') : document.querySelectorAll('.sentence');

            // Fungsi untuk menentukan indeks kalimat berdasarkan posisi karakter yang sedang dibacakan.
            function getCurrentSentenceIndex(charIndex, sentenceEls) {
                let cumulativeLength = 0;
                for (let i = 0; i < sentenceEls.length; i++) {
                    cumulativeLength += sentenceEls[i].innerText.trim().length;
                    if (charIndex < cumulativeLength) {
                        return i;
                    }
                }
                return sentenceEls.length - 1;
            }

            // Event onstart: highlight kalimat pertama
            utterance.onstart = function() {
                if (sentenceEls.length > 0) {
                    sentenceEls.forEach(el => el.classList.remove('highlight'));
                    sentenceEls[0].classList.add('highlight');
                }
            };

            utterance.onboundary = function(event) {
                if (sentenceEls.length > 0) {
                    let index = getCurrentSentenceIndex(event.charIndex, sentenceEls);
                    sentenceEls.forEach(el => el.classList.remove('highlight'));
                    if (sentenceEls[index]) {
                        sentenceEls[index].classList.add('highlight');
                    }
                }
            };

            utterance.onend = function() {
                if (sentenceEls.length > 0) {
                    sentenceEls.forEach(el => el.classList.remove('highlight'));
                }
            };

            synth.speak(utterance);
        }

        function initSpeechFeatures() {
            // Bungkus teks dalam elemen 'speakable'
            wrapSpeakableText();

            // Baca otomatis konten di dalam #autoSpeak
            let autoElement = document.getElementById("autoSpeak");
            if (autoElement) {
                let text = autoElement.innerText;
                speakText(text, autoElement);
            }

            // Tambahkan event listener untuk setiap elemen dengan kelas 'speakable'
            let speakableElements = document.querySelectorAll('.speakable');
            speakableElements.forEach(function(el) {
                el.addEventListener('mouseenter', function() {
                    if (!window.speechEnabled) return;
                    // Hapus highlight dari semua kalimat di halaman
                    document.querySelectorAll('.sentence').forEach(el => el.classList.remove('highlight'));
                    // Batalkan pembacaan yang sedang berlangsung
                    window.speechSynthesis.cancel();
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
