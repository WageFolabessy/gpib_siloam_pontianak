function checkBrowserSupport() {
    var supportMessage = "";
    // Cek speechSynthesis dan SpeechRecognition (atau webkitSpeechRecognition)
    if (
        window.speechSynthesis &&
        (window.SpeechRecognition || window.webkitSpeechRecognition)
    ) {
        supportMessage =
            "Browser Anda mendukung fitur Speech Synthesis dan Speech Recognition.";
    } else {
        supportMessage =
            "Browser Anda TIDAK mendukung fitur Speech Synthesis dan/atau Speech Recognition. Beberapa fitur mungkin tidak berfungsi.";
    }
    document.getElementById("browserSupport").innerText = supportMessage;
}

document.addEventListener("DOMContentLoaded", function () {
    checkBrowserSupport();
});

// Deklarasikan variabel global speechEnabled (default aktif)
window.speechEnabled = false;

// Fungsi untuk mengubah tampilan tombol sesuai status
function updateToggleButton() {
    const btn = document.getElementById("btnToggleSpeech");
    if (window.speechEnabled) {
        btn.classList.remove("btn-success");
        btn.classList.add("btn-danger");
        btn.innerText = "Nonaktifkan Speech";
    } else {
        btn.classList.remove("btn-danger");
        btn.classList.add("btn-success");
        btn.innerText = "Aktifkan Speech";
    }
}

// Inisialisasi tampilan tombol saat halaman dimuat
updateToggleButton();

// Event listener untuk tombol toggle
document
    .getElementById("btnToggleSpeech")
    .addEventListener("click", function () {
        window.speechEnabled = !window.speechEnabled;
        if (!window.speechEnabled) {
            window.speechSynthesis.cancel();
            alert("Speech synthesis dinonaktifkan.");
        } else {
            alert("Speech synthesis diaktifkan.");
        }
        updateToggleButton();
    });

const SpeechRecognition =
    window.SpeechRecognition || window.webkitSpeechRecognition;
if (SpeechRecognition) {
    const recognition = new SpeechRecognition();
    // Set pengenalan suara secara kontinu (opsional) dan bahasa Indonesia
    recognition.continuous = true;
    recognition.lang = "id-ID";

    // Mulai mendengarkan
    recognition.start();

    recognition.onresult = function (event) {
        // Ambil hasil pengenalan suara
        const transcript = event.results[event.results.length - 1][0].transcript
            .trim()
            .toLowerCase();
        console.log("Recognized: ", transcript);

        // Contoh perintah: "buka halaman jadwal ibadah"
        if (transcript.includes("buka halaman jadwal ibadah")) {
            window.location.href = "/jadwal-ibadah";
        }
        // Anda dapat menambahkan perintah lain, misalnya:
        else if (transcript.includes("buka halaman beranda")) {
            window.location.href = "/";
        } else if (transcript.includes("buka halaman renungan")) {
            window.location.href = "/renungan";
        } else if (transcript.includes("buka halaman info")) {
            window.location.href = "/info";
        }
    };

    // recognition.onerror = function(event) {
    //     console.error("Speech recognition error:", event.error);
    // };

    // Jika perlu, restart recognizer setelah selesai
    recognition.onend = function () {
        recognition.start();
    };
} else {
    console.log("Browser tidak mendukung Speech Recognition API.");
}
