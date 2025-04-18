document.addEventListener("DOMContentLoaded", function () {
    const globalFunctionsAvailable =
        typeof window.speakText === "function" &&
        typeof window.addToSpeechQueue === "function" &&
        typeof window.startSpeechQueue === "function" &&
        typeof window.stopSpeechQueue === "function";

    if (!globalFunctionsAvailable) {
        console.error("[Jadwal Ibadah] Fungsi TTS global tidak ditemukan...");
        return;
    }

    let jadwalSpeechInitialized = false;

    function initJadwalSpeechFeatures() {
        if (jadwalSpeechInitialized) return;
        jadwalSpeechInitialized = true;

        let autoReadTexts = [];
        const pageTitleElement = document.querySelector(
            ".container > h2.display-6.speakable"
        );
        const speakableCardsAuto = document.querySelectorAll(".speakable.card");
        if (pageTitleElement) {
            const titleText = (
                pageTitleElement.innerText || pageTitleElement.textContent
            ).trim();
            if (titleText) {
                autoReadTexts.push(titleText + ".");
            }
        }
        speakableCardsAuto.forEach((card) => {
            const titleElement = card.querySelector(".card-title");
            const table = card.querySelector("table");
            if (titleElement) {
                const cardTitleText = (
                    titleElement.innerText || titleElement.textContent
                ).trim();
                if (cardTitleText) {
                    autoReadTexts.push(cardTitleText + ".");
                }
            }
            if (table) {
                const rows = table.querySelectorAll("tbody tr");
                let hasData = false;
                rows.forEach((row) => {
                    const cells = row.querySelectorAll("td");
                    if (
                        cells.length === 1 &&
                        cells[0].hasAttribute("colspan")
                    ) {
                        autoReadTexts.push(
                            (
                                cells[0].innerText || cells[0].textContent
                            ).trim() + "."
                        );
                        hasData = true;
                    } else if (cells.length > 0) {
                        hasData = true;
                        let rowData = [];
                        if (cells.length === 2) {
                            rowData.push(
                                (
                                    cells[0].innerText || cells[0].textContent
                                ).trim()
                            );
                            rowData.push(
                                `${(
                                    cells[1].innerText || cells[1].textContent
                                ).trim()}`
                            );
                        } else if (cells.length === 3) {
                            rowData.push(
                                (
                                    cells[0].innerText || cells[0].textContent
                                ).trim()
                            );
                            rowData.push(
                                `hari ${(
                                    cells[1].innerText || cells[1].textContent
                                ).trim()}`
                            );
                            rowData.push(
                                `${(
                                    cells[2].innerText || cells[2].textContent
                                ).trim()}`
                            );
                        } else {
                            cells.forEach((td) =>
                                rowData.push(
                                    (td.innerText || td.textContent).trim()
                                )
                            );
                        }
                        autoReadTexts.push(rowData.join(", ") + ".");
                    }
                });
                if (!hasData && rows.length === 0) {
                    autoReadTexts.push("Tidak ada jadwal tersedia.");
                }
            }
        });
        if (autoReadTexts.length > 0) {
            window.addToSpeechQueue(autoReadTexts);
        }
        if (window.speechEnabled && autoReadTexts.length > 0) {
            setTimeout(() => {
                window.startSpeechQueue();
            }, 500);
        }

        const speakableElementsClick = document.querySelectorAll(".speakable");
        speakableElementsClick.forEach(function (element) {
            element.style.cursor = "pointer";
            element.addEventListener("click", function (event) {
                const clickedElementContainer = this;
                if (
                    event.target.closest("a, button") ||
                    !window.speechEnabled
                ) {
                    return;
                }
                window.stopSpeechQueue();

                let textToSpeak = "";
                const titleH2 = clickedElementContainer.matches("h2.speakable")
                    ? clickedElementContainer
                    : null;
                const card = clickedElementContainer.matches(".card.speakable")
                    ? clickedElementContainer
                    : null;

                if (titleH2) {
                    textToSpeak = (
                        titleH2.innerText || titleH2.textContent
                    ).trim();
                } else if (card) {
                    const title = card.querySelector(".card-title");
                    const table = card.querySelector("table");
                    let tableContentText = "";
                    let hasData = false;

                    if (title) {
                        textToSpeak =
                            (title.innerText || title.textContent).trim() +
                            ". ";
                    }

                    if (table) {
                        const rows = table.querySelectorAll("tbody tr");
                        rows.forEach((row) => {
                            const cells = row.querySelectorAll("td");
                            if (
                                cells.length === 1 &&
                                cells[0].hasAttribute("colspan")
                            ) {
                                tableContentText +=
                                    (
                                        cells[0].innerText ||
                                        cells[0].textContent
                                    ).trim() + ". ";
                                hasData = true;
                            } else if (cells.length > 0) {
                                hasData = true;
                                let rowData = [];
                                if (cells.length === 2) {
                                    rowData.push(
                                        (
                                            cells[0].innerText ||
                                            cells[0].textContent
                                        ).trim()
                                    );
                                    rowData.push(
                                        `${(
                                            cells[1].innerText ||
                                            cells[1].textContent
                                        ).trim()}`
                                    ); 
                                } else if (cells.length === 3) {
                                    rowData.push(
                                        (
                                            cells[0].innerText ||
                                            cells[0].textContent
                                        ).trim()
                                    );
                                    rowData.push(
                                        `hari ${(
                                            cells[1].innerText ||
                                            cells[1].textContent
                                        ).trim()}`
                                    );
                                    rowData.push(
                                        `${(
                                            cells[2].innerText ||
                                            cells[2].textContent
                                        ).trim()}`
                                    );
                                } else {
                                    // Fallback
                                    cells.forEach((td) => {
                                        rowData.push(
                                            (
                                                td.innerText || td.textContent
                                            ).trim()
                                        );
                                    });
                                }
                                tableContentText += rowData.join(", ") + ". ";
                            }
                        });
                        if (!hasData && rows.length === 0) {
                            tableContentText =
                                "Tidak ada jadwal tersedia dalam tabel ini. ";
                        }
                    }
                    textToSpeak += tableContentText;
                }

                if (textToSpeak.trim()) {
                    window.speakText(textToSpeak.trim());
                } else {
                    console.warn(
                        "[Jadwal Ibadah] Tidak ada teks relevan ditemukan pada elemen:",
                        clickedElementContainer
                    );
                }
            });
        });
    }

    function runInitWhenReady() {
        if (
            typeof window.speakText === "function" &&
            !jadwalSpeechInitialized
        ) {
            initJadwalSpeechFeatures();
        } else if (jadwalSpeechInitialized) {
        } else {
            console.error("Fungsi TTS global tidak tersedia saat init.");
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
