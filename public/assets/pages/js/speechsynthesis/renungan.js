document.addEventListener("DOMContentLoaded", function () {
    const globalFunctionsAvailable =
        typeof window.speakText === "function" &&
        typeof window.addToSpeechQueue === "function" &&
        typeof window.startSpeechQueue === "function" &&
        typeof window.stopSpeechQueue === "function";

    if (!globalFunctionsAvailable) {
        console.error(
            "[Renungan Page TTS] Fungsi TTS global tidak ditemukan..."
        );
        return;
    }

    let renunganSpeechInitialized = false;

    function initRenunganSpeechFeatures() {
        if (renunganSpeechInitialized) return;
        renunganSpeechInitialized = true;

        let autoReadQueueItems = [];
        const headerElement = document.getElementById(
            "renungan-header-section"
        );
        const initialCards = document.querySelectorAll(
            "#renungan-container .renungan-card-item"
        );

        if (headerElement) {
            const segments = headerElement.querySelectorAll(".tts-segment");
            if (segments.length > 0) {
                let jumbotronText = "";
                segments.forEach(
                    (s) =>
                        (jumbotronText +=
                            (s.innerText || s.textContent).trim() + ". ")
                );
                if (jumbotronText.trim()) {
                    autoReadQueueItems.push(jumbotronText.trim());
                }
            } else {
                // Fallback jika tidak ada span
                let jumbotronText = (
                    headerElement.innerText || headerElement.textContent
                ).trim();
                if (jumbotronText) autoReadQueueItems.push(jumbotronText);
            }
        }

        initialCards.forEach((card, index) => {
            const titleElement = card.querySelector(".card-title");
            const subtitleElement = card.querySelector(".card-subtitle"); // Target h6 langsung
            let cardSummary = "";
            const titleText = titleElement
                ? (titleElement.innerText || titleElement.textContent).trim()
                : "";
            const subtitleText = subtitleElement
                ? (subtitleElement.innerText || subtitleElement.textContent)
                      .replace(/^Bacaan:/i, "")
                      .trim()
                : "";

            if (titleText) {
                cardSummary += `Renungan ${index + 1}: ${titleText}.`;
            } else {
                cardSummary += `Renungan ${index + 1}. `;
            }
            if (subtitleText) {
                cardSummary += ` Bacaan Alkitab: ${subtitleText}.`;
            }

            if (cardSummary.trim().length > 0) {
                autoReadQueueItems.push(cardSummary.trim());
            }
        });

        if (autoReadQueueItems.length > 0) {
            window.addToSpeechQueue(autoReadQueueItems);
        }
        if (window.speechEnabled && autoReadQueueItems.length > 0) {
            setTimeout(() => {
                window.startSpeechQueue();
            }, 500);
        }

        const contentArea = document.querySelector("body");
        if (contentArea) {
            contentArea.addEventListener("click", function (event) {
                const speakableElement = event.target.closest(".speakable");
                if (
                    !speakableElement ||
                    event.target.closest("a, button") ||
                    !window.speechEnabled
                ) {
                    return;
                }
                window.stopSpeechQueue();

                let textToSpeak = "";
                // Ekstrak teks dari semua segmen jika ada, jika tidak, ambil innerText
                const segments =
                    speakableElement.querySelectorAll(".tts-segment");
                if (segments.length === 0) {
                    textToSpeak = (
                        speakableElement.innerText ||
                        speakableElement.textContent
                    ).trim();
                } else {
                    segments.forEach((segment) => {
                        textToSpeak +=
                            (segment.innerText || segment.textContent).trim() +
                            ". ";
                    });
                }

                if (textToSpeak.trim()) {
                    window.speakText(textToSpeak.trim());
                }
            });
        }
    }

    function runInitWhenReady() {
        if (
            typeof window.speakText === "function" &&
            !renunganSpeechInitialized
        ) {
            initRenunganSpeechFeatures();
        }
    }
    if (window.speechSynthesis) {
        const synth = window.speechSynthesis;
        let initTimeout;
        const triggerInit = () => {
            clearTimeout(initTimeout);
            runInitWhenReady();
        };
        if (synth.getVoices().length > 0) {
            runInitWhenReady();
        } else if (synth.onvoiceschanged !== undefined) {
            synth.onvoiceschanged = triggerInit;
            initTimeout = setTimeout(() => {
                triggerInit();
            }, 2500);
        } else {
            setTimeout(triggerInit, 500);
        }
    } else {
        console.warn("Speech Synthesis tidak didukung...");
    }
});
