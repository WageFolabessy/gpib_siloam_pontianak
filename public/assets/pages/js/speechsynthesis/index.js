(function () {
    "use strict";

    const synthesisAvailable =
        "speechSynthesis" in window && "SpeechSynthesisUtterance" in window;
    const synth = synthesisAvailable ? window.speechSynthesis : null;
    let voices = [];
    window.speechEnabled = true;

    let utteranceQueue = [];
    let isSpeakingQueue = false;
    let currentUtterance = null;

    const supportEl = document.getElementById("browserSupport");
    const toggleBtn = document.getElementById("btnToggleSpeech");

    function checkBrowserSupport() {
        if (!supportEl) return;
        let supportText = "";
        if (synthesisAvailable) {
            supportText +=
                '<i class="fas fa-check-circle text-success me-1"></i>TTS Didukung. ';
        } else {
            supportText +=
                '<i class="fas fa-times-circle text-danger me-1"></i>TTS Tidak Didukung. ';
            window.speechEnabled = false;
            if (toggleBtn) toggleBtn.disabled = true;
        }
        supportEl.innerHTML = supportText;
    }

    function loadVoices() {
        if (!synthesisAvailable) return;
        try {
            voices = synth
                .getVoices()
                .filter(
                    (voice) =>
                        voice.lang && voice.lang.toLowerCase().startsWith("id")
                );
        } catch (e) {
            console.error("Error getting voices:", e);
            voices = [];
        }
    }

    function getSelectedVoice() {
        if (!synthesisAvailable) return null;
        if (voices.length === 0) loadVoices();
        if (voices.length === 0) {
            let allVoices = synth.getVoices();
            return (
                allVoices.find((voice) =>
                    voice.lang.toLowerCase().startsWith("en")
                ) ||
                allVoices[0] ||
                null
            );
        }
        let femaleVoice = voices.find(
            (voice) =>
                voice.name.toLowerCase().includes("female") ||
                (voice.gender && voice.gender.toLowerCase() === "female")
        );
        return femaleVoice || voices[0] || null;
    }

    function fixBibleCitation(text) {
        if (!text) return "";
        try {
            return text.replace(
                /(\b[A-Za-z]+(?:\s[A-Za-z]+){0,2})\s?(\d{1,3})\s?[:.]\s?(\d{1,3})(?:\s?[-–—]\s?(\d{1,3}))?/g,
                function (match, book, chapter, verseStart, verseEnd) {
                    book = book.replace(/<[^>]*>/g, "").trim();
                    let result =
                        book +
                        " pasal " +
                        parseInt(chapter, 10) +
                        " ayat " +
                        parseInt(verseStart, 10);
                    if (verseEnd) {
                        result += " sampai " + parseInt(verseEnd, 10);
                    }
                    return result;
                }
            );
        } catch (e) {
            console.error("Error fixing Bible citation:", e);
            return text;
        }
    }

    function preprocessText(text) {
        if (!text) return "";
        try {
            text = fixBibleCitation(text);
            text = text.replace(/GPIB/gi, "G P I B");
            text = text.replace(/(?:https?|ftp):\/\/[\n\S]+/g, "");
            text = text.replace(/&copy;/gi, "hak cipta");
            text = text.replace(/&reg;/gi, "merek terdaftar");
            text = text.replace(/([.?!;])\s+/g, "$1 ");
            text = text.replace(/\s+/g, " ").trim();
            return text;
        } catch (e) {
            console.error("Error preprocessing text:", e);
            return text.replace(/\s+/g, " ").trim();
        }
    }

    window.speakText = function (textToSpeak) {
        if (
            !synthesisAvailable ||
            !window.speechEnabled ||
            !textToSpeak ||
            typeof textToSpeak !== "string"
        ) {
            stopSpeechQueue();
            if (synth && synth.speaking) {
                try {
                    synth.cancel();
                } catch (e) {}
            }
            return;
        }
        stopSpeechQueue();  

        const processedText = preprocessText(textToSpeak);
        if (!processedText) return;

        const utterance = new SpeechSynthesisUtterance(processedText);
        const selectedVoice = getSelectedVoice();

        if (selectedVoice) {
            utterance.voice = selectedVoice;
            utterance.lang = selectedVoice.lang;
        } else {
            utterance.lang = "id-ID";
        }
        utterance.rate = 1.1;
        utterance.pitch = 1;
        utterance.onerror = function (event) {
            console.error("SpeechSynthesisUtterance.onerror", event.error);
            showFeedback(`Error Text-to-Speech: ${event.error}`, "error");
            isSpeakingQueue = false;
        };

        setTimeout(() => {
            try {
                if (synth) synth.speak(utterance);
            } catch (e) {
                console.error("Error calling synth.speak:", e);
                showFeedback("Gagal memulai pembacaan teks.", "error");
            }
        }, 150);
    };

    function processSpeechQueue() {
        if (
            !synthesisAvailable ||
            !window.speechEnabled ||
            utteranceQueue.length === 0
        ) {
            isSpeakingQueue = false;
            currentUtterance = null;
            return;
        }

        isSpeakingQueue = true;
        const textToSpeak = utteranceQueue.shift();
        const processedText = preprocessText(textToSpeak);

        if (!processedText) {
            setTimeout(processSpeechQueue, 50);
            return;
        }

        currentUtterance = new SpeechSynthesisUtterance(processedText);
        const selectedVoice = getSelectedVoice();

        if (selectedVoice) {
            currentUtterance.voice = selectedVoice;
            currentUtterance.lang = selectedVoice.lang;
        } else {
            currentUtterance.lang = "id-ID";
        }
        currentUtterance.rate = 1.1;
        currentUtterance.pitch = 1;

        currentUtterance.onend = function () {
            currentUtterance = null;
            setTimeout(processSpeechQueue, 200);
        };

        currentUtterance.onerror = function (event) {
            console.error(
                "SpeechSynthesisUtterance.onerror (Queue)",
                event.error
            );
            showFeedback(`Error saat membaca antrian: ${event.error}`, "error");
            currentUtterance = null;
            stopSpeechQueue();
        };

        setTimeout(() => {
            try {
                if (synth) synth.speak(currentUtterance);
            } catch (e) {
                console.error("Error calling synth.speak from queue:", e);
                showFeedback(
                    "Gagal memulai pembacaan teks dari antrian.",
                    "error"
                );
                stopSpeechQueue();
            }
        }, 50);
    }

    window.addToSpeechQueue = function (text) {
        if (typeof text === "string" && text.trim().length > 0) {
            utteranceQueue.push(text.trim());
        } else if (Array.isArray(text)) {
            text.forEach((item) => {
                if (typeof item === "string" && item.trim().length > 0) {
                    utteranceQueue.push(item.trim());
                }
            });
        }
    };

    window.startSpeechQueue = function () {
        if (!synthesisAvailable || !window.speechEnabled) return;
        if (synth && synth.speaking) {
            try {
                synth.cancel();
            } catch (e) {}
        }
        if (currentUtterance) {
            currentUtterance.onend = null;
        }

        if (!isSpeakingQueue && utteranceQueue.length > 0) {
            processSpeechQueue();
        }
    };

    window.stopSpeechQueue = function () {
        utteranceQueue = [];
        isSpeakingQueue = false;
        if (currentUtterance) {
            currentUtterance.onend = null;
            currentUtterance = null;
        }
        if (synth && synth.speaking) {
            try {
                synth.cancel();
            } catch (e) {}
        }
    };

    function updateToggleButton(button, enabled) {
        if (!button) return;
        if (enabled) {
            button.classList.remove("btn-danger");
            button.classList.add("btn-success");
            button.title = "Nonaktifkan Pembaca Teks";
            button.innerHTML = '<i class="fas fa-volume-up fa-fw"></i>';
        } else {
            button.classList.remove("btn-success");
            button.classList.add("btn-danger");
            button.title = "Aktifkan Pembaca Teks";
            button.innerHTML = '<i class="fas fa-volume-mute fa-fw"></i>';
        }
    }

    function initializeGlobalSpeech() {
        checkBrowserSupport();

        if (synthesisAvailable) {
            loadVoices();
            if (synth.onvoiceschanged !== undefined) {
                synth.onvoiceschanged = loadVoices;
            }

            if (toggleBtn) {
                updateToggleButton(toggleBtn, window.speechEnabled);
                toggleBtn.addEventListener("click", function () {
                    window.speechEnabled = !window.speechEnabled;
                    updateToggleButton(this, window.speechEnabled);
                    if (!window.speechEnabled) {
                        stopSpeechQueue();
                    }
                });
            } else {
                console.warn(
                    "#btnToggleSpeech element not found. TTS defaults to enabled state."
                );
            }
        } else {
            window.speechEnabled = false;
            if (toggleBtn) updateToggleButton(toggleBtn, false);
        }
    }

    function showFeedback(message, type = "info") {
        console.log(`Feedback (${type}): ${message}`);
    }

    document.addEventListener("DOMContentLoaded", initializeGlobalSpeech);
})();
