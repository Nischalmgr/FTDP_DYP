document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".like-btn").forEach(function (btn) {

        btn.addEventListener("click", function () {

            var designId = btn.dataset.designId;

            fetch("../../../backend/company/like_action.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: "design_id=" + encodeURIComponent(designId),
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (data) {

                    if (!data.success) {
                        return;
                    }

                    btn.classList.toggle("liked", data.liked);

                    var countEl = btn.querySelector(".like-count");
                    if (countEl) {
                        countEl.textContent = data.like_count;
                    }
                })
                .catch(function () {
                    alert("Could not update like right now. Try again.");
                });
        });
    });

    document.querySelectorAll(".feed-save-btn").forEach(function (btn) {

        btn.addEventListener("click", function () {

            var designId = btn.dataset.designId;
            var onSavedPage = btn.dataset.removeOnUnsave === "true";

            fetch("../../../backend/company/save_action.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: "design_id=" + encodeURIComponent(designId),
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (data) {

                    if (!data.success) {
                        return;
                    }

                    if (onSavedPage && !data.saved) {
                        var card = btn.closest(".feed-card");
                        if (card) {
                            card.remove();
                        }
                        return;
                    }

                    btn.classList.toggle("saved", data.saved);
                })
                .catch(function () {
                    alert("Could not update save right now. Try again.");
                });
        });
    });
});
