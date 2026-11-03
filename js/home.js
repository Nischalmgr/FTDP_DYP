document.addEventListener("DOMContentLoaded", function () {

    const mobileMenuBtn =
        document.getElementById("mobileMenuBtn");

    const navMenu =
        document.getElementById("navMenu");

    if (mobileMenuBtn && navMenu) {

        mobileMenuBtn.addEventListener(
            "click",
            function () {

                navMenu.classList.toggle(
                    "mobile-active"
                );

                const icon =
                    mobileMenuBtn.querySelector("i");

                if (
                    navMenu.classList.contains(
                        "mobile-active"
                    )
                ) {

                    icon.classList.remove(
                        "fa-bars"
                    );

                    icon.classList.add(
                        "fa-xmark"
                    );

                } else {

                    icon.classList.remove(
                        "fa-xmark"
                    );

                    icon.classList.add(
                        "fa-bars"
                    );

                }

            }
        );

    }

    const talentSearch =
        document.getElementById("talentSearch");

    if (talentSearch) {

        talentSearch.addEventListener(
            "keydown",
            function (event) {

                if (event.key === "Enter") {

                    event.preventDefault();

                    const searchValue =
                        talentSearch.value.trim();

                    if (searchValue !== "") {

                        console.log(
                            "Searching for:",
                            searchValue
                        );

                    }

                }

            }
        );

    }

    const joinForm =
        document.getElementById("joinForm");

    const emailInput =
        document.getElementById("emailInput");

    const formMessage =
        document.getElementById("formMessage");

    if (
        joinForm &&
        emailInput &&
        formMessage
    ) {

        joinForm.addEventListener(
            "submit",
            function (event) {

                event.preventDefault();

                const email =
                    emailInput.value.trim();

                if (email === "") {

                    formMessage.textContent =
                        "Please enter your email address.";

                    return;

                }

                formMessage.textContent =
                    "Thank you! Welcome to FTDP.";

                emailInput.value = "";

            }
        );

    }

    const heroVideo =
        document.getElementById("heroVideo");

    if (heroVideo) {

        heroVideo.play().catch(
            function () {

                console.log(
                    "Video autoplay is waiting for browser permission."
                );

            }
        );

        heroVideo.addEventListener(
            "ended",
            function () {

                heroVideo.currentTime = 0;

                heroVideo.play();

            }
        );

    }

});
