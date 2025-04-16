const SpeechRecognition =
    window.SpeechRecognition || window.webkitSpeechRecognition;
if (SpeechRecognition) {
    const recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.lang = "id-ID";

    recognition.start();

    recognition.onresult = function (event) {
        const transcript = event.results[event.results.length - 1][0].transcript
            .trim()
            .toLowerCase();
        console.log("Recognized: ", transcript);

        if (transcript.includes("buka halaman jadwal ibadah")) {
            window.location.href = "/jadwal-ibadah";
        } else if (transcript.includes("buka halaman beranda")) {
            window.location.href = "/";
        } else if (transcript.includes("buka halaman renungan")) {
            window.location.href = "/renungan";
        } else if (transcript.includes("buka halaman info")) {
            window.location.href = "/info";
        }
    };

    recognition.onend = function () {
        recognition.start();
    };
} else {
    console.log("Browser tidak mendukung Speech Recognition API.");
}