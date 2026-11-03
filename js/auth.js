document.addEventListener(
    "DOMContentLoaded",
    function () {

        const passwordButtons =
            document.querySelectorAll(
                ".password-toggle"
            );

        passwordButtons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {

                        const targetId =
                            button.dataset.target;

                        const input =
                            document.getElementById(
                                targetId
                            );

                        const icon =
                            button.querySelector("i");

                        if (!input) {
                            return;
                        }

                        if (
                            input.type ===
                            "password"
                        ) {

                            input.type =
                                "text";

                            icon.classList.remove(
                                "fa-eye"
                            );

                            icon.classList.add(
                                "fa-eye-slash"
                            );

                        } else {

                            input.type =
                                "password";

                            icon.classList.remove(
                                "fa-eye-slash"
                            );

                            icon.classList.add(
                                "fa-eye"
                            );

                        }

                    }
                );

            }
        );

    }
);
