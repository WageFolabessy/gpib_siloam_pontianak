<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="garisAs" content="projek">
  <title>GPIB SILOAM PONTIANAK @yield('title')</title>
  <link rel="shortcut icon" href="{{ asset('assets/pages/img/logo.png') }}">
  <link rel="stylesheet" href="{{ asset('assets/pages/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/pages/css/style.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .highlight {
      background-color: #ff9;
    }
    #speechPlayer {
      background-color: #f8f9fa;
      box-shadow: 0 2px 4px rgba(0,0,0,.1);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 10px;
      gap: 20px;
    }
    #speechPlayer button {
      background: 0 0;
      border: none;
      font-size: 24px;
      cursor: pointer;
      color: #d2691e;
    }
    #progressBar {
      width: 200px;
      height: 10px;
    }
    .content-wrapper {
      margin-top: 70px;
    }
    .sentence {
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div id="speechPlayer">
    <button id="toggleBtn" title="Play">
      <i class="fas fa-play"></i>
    </button>
    <progress id="progressBar" value="0" max="100"></progress>
  </div>

  <div class="content-wrapper">
    <div class="container mt-4">
      <div class="row">
        <div class="col-12 text-center">
          <a href="{{ route('beranda') }}" class="navbar-brand">
            <img src="{{ asset('assets/pages/img/logo-80.png') }}" alt="GPIB SILOAM PONTIANAK Logo"
                 class="img-fluid mb-3" style="max-width:100px">
            <h2 class="text-uppercase">GPIB SILOAM PONTIANAK</h2>
          </a>
        </div>
      </div>
    </div>

    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-8 bg-white p-4 rounded shadow">
          <!-- Bagian suara (judul & bacaan) -->
          <div id="allText">
            <h1 class="text-center sentence">{{ $renungan->judul }}</h1>
            <h6 class="text-center text-muted mb-4 sentence">{{ $renungan->alkitab }}</h6>
            <h6 class="text-center text-muted mb-4 sentence">{{ $renungan->bacaan_alkitab }}</h6>
          </div>
          
          <!-- Thumbnail -->
          @if ($renungan->thumbnail)
            <div class="text-center mb-4">
              <img src="{{ asset('storage/thumbnails/') . '/' . $renungan->thumbnail }}"
                   class="img-fluid rounded w-100" style="height:400px" alt="Thumbnail">
            </div>
          @endif

          <!-- Bagian isi renungan (dipecah per kalimat) -->
          <div id="isiBacaan">
            <!-- Jika ada beberapa paragraf, masing-masing akan diproses -->
            {!! html_entity_decode($renungan->isi_bacaan) !!}
          </div>

          <hr class="my-4">
          <p class="text-muted">Diupload:<span class="fw-bold">{{ $diupload }}</span></p>
          <hr class="my-4">
          <div class="d-flex justify-content-between mt-4">
            <a href="{{ isset($prevRenungan) ? route('detail-renungan', $prevRenungan->slug) : '#' }}"
               class="btn" style="background-color:#d2691e">
               <i class="fas fa-chevron-left"></i>
            </a>
            <a href="{{ isset($nextRenungan) ? route('detail-renungan', $nextRenungan->slug) : '#' }}"
               class="btn" style="background-color:#d2691e">
               <i class="fas fa-chevron-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('components/footer')

  <script src="{{ asset('assets/pages/js/bootstrap.bundle.min.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if ('speechSynthesis' in window) {
        const synthesis = window.speechSynthesis;
        const toggleBtn = document.getElementById('toggleBtn');
        const progressBar = document.getElementById('progressBar');
        let utterance = null;
        let totalTextLength = 0;
        let isFullReading = false; // flag untuk pembacaan full

        // Pastikan daftar suara termuat (Chrome memuat secara asinkron)
        synthesis.getVoices();

        // Fungsi untuk memperbaiki format bacaan Alkitab
        // Contoh:
        // "Yohanes 3:1"      => "Yohanes pasal 3 ayat 1"
        // "Yohanes 1:1-5"    => "Yohanes pasal 1 ayat 1 sampai 5"
        function fixBibleCitation(text) {
          return text.replace(/(\b\w+\b)\s(\d+):(\d+)(?:-(\d+))?/g, function(match, book, chapter, verseStart, verseEnd) {
            if (verseEnd) {
              return book + ' pasal ' + chapter + ' ayat ' + verseStart + ' sampai ' + verseEnd;
            }
            return book + ' pasal ' + chapter + ' ayat ' + verseStart;
          });
        }

        // Fungsi pembungkusan untuk kontainer #isiBacaan
        function wrapIsiBacaan() {
          const isiBacaanContainer = document.getElementById('isiBacaan');
          if (!isiBacaanContainer) return;
          // Jika terdapat beberapa paragraf, proses masing-masing
          const paragraphs = isiBacaanContainer.querySelectorAll('p');
          if (paragraphs.length > 0) {
            paragraphs.forEach(p => {
              let textContent = p.textContent.trim();
              let sentences = textContent.match(/[^\.!\?]+[\.!\?]+/g);
              if (!sentences) {
                sentences = [textContent];
              }
              p.innerHTML = sentences.map(sentence => `<span class="sentence">${sentence.trim()} </span>`).join('');
            });
          } else {
            let textContent = isiBacaanContainer.textContent.trim();
            let sentences = textContent.match(/[^\.!\?]+[\.!\?]+/g) || [textContent];
            isiBacaanContainer.innerHTML = sentences.map(sentence => `<span class="sentence">${sentence.trim()} </span>`).join('');
          }
        }
        // Proses pembungkusan untuk isi renungan
        wrapIsiBacaan();

        // Fungsi menggabungkan semua teks dari elemen dengan class "sentence"
        function getFullText() {
          const sentences = document.querySelectorAll('.sentence');
          let fullText = "";
          sentences.forEach(el => {
            fullText += el.innerText + " ";
          });
          return fullText.trim();
        }

        // Inisialisasi variabel sentenceEls
        let sentenceEls = document.querySelectorAll('.sentence');

        // Fungsi untuk memilih voice perempuan bahasa Indonesia (jika ada)
        function setFemaleVoice(utt) {
          const voices = synthesis.getVoices();
          const idVoices = voices.filter(voice => voice.lang && voice.lang.indexOf('id-ID') !== -1);
          let femaleVoice = idVoices.find(voice => {
            const lowerName = voice.name.toLowerCase();
            return lowerName.includes('female') || lowerName.includes('wanita');
          });
          utt.voice = femaleVoice || (idVoices.length ? idVoices[0] : null);
        }

        // Fungsi untuk menentukan indeks kalimat berdasarkan event.charIndex
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

        /**
         * Fungsi speakText dengan parameter opsional "sourceElement"
         * Jika sourceElement diberikan (misalnya dari hover), highlight langsung diterapkan ke elemen tersebut.
         * Sebelum membacakan, teks akan diproses agar format bacaan Alkitab diperbaiki.
         */
        function speakText(text, callback, sourceElement) {
          synthesis.cancel();
          // Perbaiki format bacaan Alkitab, misalnya "Yohanes 1:1-5" menjadi "Yohanes pasal 1 ayat 1 sampai 5"
          text = fixBibleCitation(text);
          
          utterance = new SpeechSynthesisUtterance(text);
          utterance.lang = 'id-ID';
          utterance.rate = 1.5;
          setFemaleVoice(utterance);
          totalTextLength = text.length;
          
          utterance.onboundary = function(event) {
            if (totalTextLength > 0) {
              progressBar.value = (event.charIndex / totalTextLength) * 100;
            }
            if (sourceElement) {
              sentenceEls.forEach(el => el.classList.remove('highlight'));
              sourceElement.classList.add('highlight');
            } else {
              const currentSentenceIndex = getCurrentSentenceIndex(event.charIndex, sentenceEls);
              sentenceEls.forEach(el => el.classList.remove('highlight'));
              if (sentenceEls[currentSentenceIndex]) {
                sentenceEls[currentSentenceIndex].classList.add('highlight');
              }
            }
          };

          utterance.onend = function() {
            sentenceEls.forEach(el => el.classList.remove('highlight'));
            progressBar.value = 0;
            toggleBtn.innerHTML = '<i class="fas fa-play"></i>';
            toggleBtn.title = 'Play';
            utterance = null;
            isFullReading = false;
            if (callback) callback();
          };

          synthesis.speak(utterance);
          toggleBtn.innerHTML = '<i class="fas fa-pause"></i>';
          toggleBtn.title = 'Pause';
        }

        // Event listener untuk tombol toggle (Play/Pause full reading)
        toggleBtn.addEventListener('click', () => {
          if (!synthesis.speaking && !synthesis.paused) {
            const fullText = getFullText();
            if (fullText.trim().length === 0) {
              console.log("Tidak ada teks untuk dibacakan.");
              return;
            }
            isFullReading = true;
            speakText(fullText);
          } else {
            if (synthesis.paused) {
              synthesis.resume();
              toggleBtn.innerHTML = '<i class="fas fa-pause"></i>';
              toggleBtn.title = 'Pause';
            } else {
              synthesis.pause();
              toggleBtn.innerHTML = '<i class="fas fa-play"></i>';
              toggleBtn.title = 'Play';
            }
          }
        });

        // Event listener untuk pembacaan per kalimat (hover)
        sentenceEls.forEach((el) => {
          el.addEventListener('mouseenter', () => {
            if (!isFullReading) {
              synthesis.cancel();
              speakText(el.innerText, undefined, el);
            }
          });
        });

        // Jika voice berubah (misalnya di Chrome), perbarui event listener untuk semua .sentence
        window.speechSynthesis.onvoiceschanged = function() {
          sentenceEls = document.querySelectorAll('.sentence');
          sentenceEls.forEach((el) => {
            el.addEventListener('mouseenter', () => {
              if (!isFullReading) {
                synthesis.cancel();
                speakText(el.innerText, undefined, el);
              }
            });
          });
        };

      } else {
        console.log('Browser tidak mendukung Speech Synthesis');
      }
    });
  </script>
</body>
</html>
