@extends('../components/main')
@section('content')
    <!-- COVER -->
    <header id="autoSpeak" class="text-center speakable">
        <h1>- GPIB SILOAM PONTIANAK -</h1>
        <blockquote class="blockquote">
            <p class="mt-3">
                “Dan orang akan datang dari Timur dan Barat dan dari Utara dan Selatan
                dan mereka duduk makan di dalam Kerajaan Allah”
                <br>
                Lukas 13:29
            </p>
        </blockquote>
        <a href="{{ route('info') }}" class="btn btn-get-started px-4 mt-4">Sejarah</a>
    </header>
    <!-- Akhir COVER -->

    <!-- RENUNGAN -->
    @if (!empty($renungan))
        <div class="container mt-4">
            <h1 class="text-center mb-4">Renungan Terbaru</h1>
            <div class="row justify-content-start">
                @foreach ($renungan as $data)
                    <!-- Card -->
                    <div class="col-md-4 mb-4 speakable">
                        <div class="card" style="height:100%">
                            <img src="{{ asset('storage/thumbnails/') . '/' . $data->thumbnail }}" class="card-img-top"
                                alt="Thumbnail" style="height:100%">
                            <div class="card-body">
                                <h5 class="card-title">{{ $data->judul }}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">Bacaan: {{ $data->alkitab }}</h6>
                                <a href="{{ route('detail-renungan', $data->slug) }}"
                                    class="btn btn-get-started w-100">Baca</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    <!-- AKHIR RENUNGAN -->
@endsection

@section('script')
    <script>
        // Fungsi untuk mengkonversi format bacaan Alkitab,
        // misal "Yohanes 3:1" menjadi "Yohanes pasal 3 ayat 1"
        function fixBibleCitation(text) {
            return text.replace(/(\b\w+\b)\s(\d+):(\d+)(?:-(\d+))?/g, function(match, book, chapter, verseStart, verseEnd) {
                if (verseEnd) {
                    return book + ' pasal ' + chapter + ' ayat ' + verseStart + ' sampai ' + verseEnd;
                }
                return book + ' pasal ' + chapter + ' ayat ' + verseStart;
            });
        }

        // Fungsi untuk memilih voice bahasa Indonesia (coba pilih suara perempuan jika ada)
        function getSelectedVoice(synth) {
            let voices = synth.getVoices();
            let indoVoices = voices.filter(voice => voice.lang.toLowerCase().includes('id'));
            if (indoVoices.length > 0) {
                let femaleVoice = indoVoices.find(voice => voice.name.toLowerCase().includes('female'));
                return femaleVoice ? femaleVoice : indoVoices[0];
            }
            return voices[0];
        }

        // Fungsi untuk melakukan speech synthesis pada teks yang diberikan
        function speakText(text) {
            // Cek apakah fitur speech diaktifkan
            if (!window.speechEnabled) return;

            // Perbaiki format bacaan Alkitab
            text = fixBibleCitation(text);

            const synth = window.speechSynthesis;
            // Dapatkan voice yang sesuai
            let selectedVoice = getSelectedVoice(synth);

            if (text.includes("GPIB")) {
                text = text.replace(/GPIB/g, '%%GPIB%%');
                let parts = text.split('%%GPIB%%');
                parts.forEach((part, index) => {
                    if (part.trim().length > 0) {
                        let utt = new SpeechSynthesisUtterance(part);
                        utt.voice = selectedVoice;
                        utt.lang = "id-ID";
                        utt.rate = 1.5;
                        synth.speak(utt);
                    }
                    if (index < parts.length - 1) {
                        let uttGPIB = new SpeechSynthesisUtterance("G, P, I, B");
                        uttGPIB.voice = selectedVoice;
                        uttGPIB.lang = "id-ID";
                        uttGPIB.rate = 2.0;
                        synth.speak(uttGPIB);
                    }
                });
            } else {
                let utterance = new SpeechSynthesisUtterance(text);
                utterance.voice = selectedVoice;
                utterance.lang = "id-ID";
                utterance.rate = 1.5;
                synth.speak(utterance);
            }
        }

        function initSpeechFeatures() {
            // Speech otomatis pada elemen dengan id "autoSpeak"
            let autoElement = document.getElementById("autoSpeak");
            if (autoElement) {
                let text = autoElement.innerText;
                speakText(text);
            }

            // Tambahkan event listener untuk elemen dengan class 'speakable'
            let speakableElements = document.querySelectorAll('.speakable');
            speakableElements.forEach(function(el) {
                el.addEventListener('mouseenter', function() {
                    if (!window.speechEnabled) return;
                    window.speechSynthesis.cancel();
                    let text = el.innerText;
                    speakText(text);
                });
            });
        }

        // Inisialisasi speech synthesis saat DOM sudah siap
        document.addEventListener("DOMContentLoaded", function() {
            if (window.speechSynthesis.getVoices().length !== 0) {
                initSpeechFeatures();
            } else {
                window.speechSynthesis.onvoiceschanged = initSpeechFeatures;
            }
        });
    </script>
@endsection
