document.addEventListener("DOMContentLoaded", function () {
    const renunganContainer = document.getElementById("renungan-container");
    const loadMoreButton = document.getElementById("load-more");
    const loadMoreSpinner = document.getElementById("load-more-spinner");
    const loadMoreText = document.getElementById("load-more-text");
    const noMoreMessage = document.getElementById("no-more-renungan");
    const loadMoreContainer = document.getElementById("load-more-container");
    let isLoading = false;

    function loadMoreRenungan() {
        if (!loadMoreButton || !renunganContainer || isLoading) return;

        const baseUrl = loadMoreButton.dataset.baseurl;
        const nextPageUrlString = loadMoreButton.dataset.nextpage;

        if (
            !baseUrl ||
            !nextPageUrlString ||
            nextPageUrlString === "null" ||
            nextPageUrlString === ""
        ) {
            if (loadMoreContainer) loadMoreContainer.style.display = "none";
            if (noMoreMessage) noMoreMessage.style.display = "block";
            console.log("[Renungan Load More] No next page URL found.");
            return;
        }

        let queryString = "";
        try {
            const urlObject = new URL(nextPageUrlString);
            queryString = urlObject.search;
        } catch (e) {
            console.error(
                "[Renungan Load More] Invalid URL format:",
                nextPageUrlString,
                e
            );
            alert("Terjadi kesalahan saat memproses URL halaman berikutnya.");
            return;
        }

        if (!queryString) {
            console.warn(
                "[Renungan Load More] Query string not found in next page URL:",
                nextPageUrlString
            );
            if (loadMoreContainer) loadMoreContainer.style.display = "none";
            if (noMoreMessage) noMoreMessage.style.display = "block";
            return;
        }

        const fetchUrl = baseUrl + queryString;

        isLoading = true;
        loadMoreButton.disabled = true;
        if (loadMoreSpinner) loadMoreSpinner.style.display = "inline-block";
        if (loadMoreText) loadMoreText.textContent = "Memuat...";

        console.log("[Renungan Load More] Fetching:", fetchUrl);

        fetch(fetchUrl, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        })
            .then((response) => {
                const contentType = response.headers.get("content-type");
                console.log(
                    "[Renungan Load More] Response Status:",
                    response.status
                );
                console.log(
                    "[Renungan Load More] Response Content-Type:",
                    contentType
                );

                if (!response.ok) {
                    return response.text().then((text) => {
                        console.error(
                            "[Renungan Load More] Server Error Response Text:",
                            text.substring(0, 500)
                        );
                        try {
                            const errorJson = JSON.parse(text);
                            throw new Error(
                                errorJson.error ||
                                    `Server Error: ${response.status}`
                            );
                        } catch (e) {
                            throw new Error(
                                `Server Error: ${
                                    response.status
                                }. Response not JSON. Preview: ${text.substring(
                                    0,
                                    200
                                )}`
                            );
                        }
                    });
                }

                if (!contentType || !contentType.includes("application/json")) {
                    return response.text().then((text) => {
                        console.error(
                            "[Renungan Load More] Non-JSON Response Text:",
                            text.substring(0, 500)
                        );
                        throw new TypeError(
                            `Server did not return JSON. Content-Type: ${contentType}. Response: ${text.substring(
                                0,
                                200
                            )}`
                        );
                    });
                }

                return response.json();
            })
            .then((data) => {
                console.log("[Renungan Load More] Data received:", data);

                if (data.error) {
                    throw new Error(
                        data.error +
                            (data.details ? ` Details: ${data.details}` : "")
                    );
                }

                if (data.html && renunganContainer) {
                    renunganContainer.insertAdjacentHTML(
                        "beforeend",
                        data.html
                    );
                } else if (data.html === "") {
                    console.log(
                        "[Renungan Load More] Received empty HTML, likely end of data."
                    );
                }

                if (data.nextPageUrl) {
                    loadMoreButton.dataset.nextpage = data.nextPageUrl;
                } else {
                    if (loadMoreContainer)
                        loadMoreContainer.style.display = "none";
                    if (noMoreMessage) noMoreMessage.style.display = "block";
                    loadMoreButton.dataset.nextpage = "";
                    loadMoreButton.disabled = true;
                    console.log("[Renungan Load More] End of data reached.");
                }
            })
            .catch((error) => {
                console.error("[Renungan Load More] Fetch Error:", error);
                alert(`Gagal memuat data renungan: ${error.message}`);
                if (loadMoreContainer) loadMoreContainer.style.display = "none";
            })
            .finally(() => {
                isLoading = false;
                if (loadMoreButton && loadMoreButton.dataset.nextpage) {
                    loadMoreButton.disabled = false;
                }
                if (loadMoreSpinner) loadMoreSpinner.style.display = "none";
                if (loadMoreText)
                    loadMoreText.textContent = "Muat Lebih Banyak";
            });
    }

    if (loadMoreButton && loadMoreButton.dataset.baseurl) {
        loadMoreButton.addEventListener("click", loadMoreRenungan);
    } else if (loadMoreButton) {
        console.warn(
            "[Renungan Load More] Load more button found, but data-baseurl is missing."
        );
        loadMoreButton.disabled = true;
    }
});
