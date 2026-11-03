document.addEventListener("DOMContentLoaded", function () {

    var MAX_AVATAR = 3 * 1024 * 1024;
    var MAX_COVER  = 5 * 1024 * 1024;

    function wirePreview(inputId, imgId, maxBytes, label, onShow) {

        var fileInput = document.getElementById(inputId);
        var img       = document.getElementById(imgId);

        if (!fileInput || !img) {
            return;
        }

        fileInput.addEventListener("change", function () {

            var file = fileInput.files && fileInput.files[0];

            if (!file) {
                return;
            }

            if (["image/jpeg", "image/png", "image/webp"].indexOf(file.type) === -1) {
                FTDP.toast(label + ": only JPG, PNG or WEBP images are allowed.", "error");
                fileInput.value = "";
                return;
            }

            if (file.size > maxBytes) {
                FTDP.toast(label + " is too large (max " + Math.round(maxBytes / 1048576) + " MB).", "error");
                fileInput.value = "";
                return;
            }

            img.src = URL.createObjectURL(file);
            img.hidden = false;

            if (onShow) {
                onShow();
            }
        });
    }

    wirePreview("profile_picture", "avatarImg", MAX_AVATAR, "Profile photo", function () {

        var initial = document.getElementById("avatarInitial");

        if (initial) {
            initial.hidden = true;
        }
    });

    wirePreview("cover_picture", "coverImg", MAX_COVER, "Cover photo");

    var bio      = document.getElementById("bio");
    var bioCount = document.getElementById("bioCount");

    function updateBioCount() {
        if (bio && bioCount) {
            bioCount.textContent = bio.value.length;
        }
    }

    if (bio) {
        bio.addEventListener("input", updateBioCount);
        updateBioCount();
    }

    var MAX_SPECS  = 15;

    var box        = document.getElementById("specBox");
    var chipsEl    = document.getElementById("specChips");
    var specInput  = document.getElementById("specInput");
    var specValue  = document.getElementById("specValue");

    var specs = [];

    if (specValue && specValue.value.trim() !== "") {

        specValue.value.split(",").forEach(function (name) {

            name = name.trim();

            if (name) {
                specs.push(name);
            }
        });
    }

    function syncSpecs() {

        specValue.value = specs.join(",");

        chipsEl.innerHTML = "";

        specs.forEach(function (name, index) {

            var chip = document.createElement("span");
            var text = document.createElement("span");
            var del  = document.createElement("button");

            chip.className = "tag-chip";

            text.textContent = name;

            del.type = "button";
            del.textContent = "✕";
            del.setAttribute("aria-label", "Remove " + name);

            del.addEventListener("click", function () {
                specs.splice(index, 1);
                syncSpecs();
                specInput.focus();
            });

            chip.appendChild(text);
            chip.appendChild(del);

            chipsEl.appendChild(chip);
        });
    }

    function addSpec(raw) {

        var name = raw.replace(/,/g, " ").replace(/\s+/g, " ").trim();

        if (!name) {
            return;
        }

        var exists = specs.some(function (s) {
            return s.toLowerCase() === name.toLowerCase();
        });

        if (exists) {
            return;
        }

        if (specs.length >= MAX_SPECS) {
            FTDP.toast("You can add up to " + MAX_SPECS + " specializations.", "error");
            return;
        }

        specs.push(name);
        syncSpecs();
    }

    if (box && specInput) {

        syncSpecs();

        box.addEventListener("click", function () {
            specInput.focus();
        });

        specInput.addEventListener("keydown", function (event) {

            if (event.key === "Enter" || event.key === ",") {

                event.preventDefault();

                addSpec(specInput.value);
                specInput.value = "";

            } else if (event.key === "Backspace" && specInput.value === "" && specs.length > 0) {

                specs.pop();
                syncSpecs();
            }
        });

        specInput.addEventListener("change", function () {

            if (specInput.value.trim() !== "") {
                addSpec(specInput.value);
                specInput.value = "";
            }
        });

        document.getElementById("detailsForm").addEventListener("submit", function () {

            if (specInput.value.trim() !== "") {
                addSpec(specInput.value);
                specInput.value = "";
            }
        });
    }

    var newPw     = document.getElementById("new_password");
    var confirmPw = document.getElementById("confirm_password");
    var hint      = document.getElementById("pwMatchHint");
    var showPw    = document.getElementById("showPasswords");

    function checkMatch() {

        if (!confirmPw || !hint) {
            return true;
        }

        if (confirmPw.value === "") {
            hint.textContent = "";
            hint.className = "hint";
            return true;
        }

        var ok = newPw.value === confirmPw.value;

        hint.textContent = ok ? "Passwords match." : "Passwords don't match yet.";
        hint.className = "hint " + (ok ? "good" : "bad");

        return ok;
    }

    if (newPw && confirmPw) {

        newPw.addEventListener("input", checkMatch);
        confirmPw.addEventListener("input", checkMatch);

        document.getElementById("passwordForm").addEventListener("submit", function (event) {

            if (!checkMatch()) {
                event.preventDefault();
                confirmPw.focus();
            }
        });
    }

    if (showPw) {

        showPw.addEventListener("change", function () {

            ["current_password", "new_password", "confirm_password"].forEach(function (id) {

                var field = document.getElementById(id);

                if (field) {
                    field.type = showPw.checked ? "text" : "password";
                }
            });
        });
    }

    var firstError = document.querySelector(".profile-section .comm-alert.error");

    if (firstError) {
        firstError.scrollIntoView({ block: "center" });
    }
});
