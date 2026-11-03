document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".save-job-btn").forEach(function (btn) {

        btn.addEventListener("click", function () {

            var jobId = btn.dataset.jobId;
            var onSavedPage = btn.dataset.removeOnUnsave === "true";

            fetch("../../../backend/designer/save_job_action.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: "job_id=" + encodeURIComponent(jobId),
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (data) {

                    if (!data.success) {
                        return;
                    }

                    if (onSavedPage && !data.saved) {
                        var card = btn.closest(".job-card");
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

    var overlay      = document.getElementById("applyModalOverlay");
    var modalJobId    = document.getElementById("applyModalJobId");
    var modalJobTitle = document.getElementById("applyModalJobTitle");

    document.querySelectorAll(".apply-btn:not(.applied)").forEach(function (btn) {

        btn.addEventListener("click", function () {

            if (!overlay) {
                return;
            }

            modalJobId.value = btn.dataset.jobId;
            modalJobTitle.textContent = btn.dataset.jobTitle;

            overlay.classList.add("active");
        });
    });

    var cancelBtn = document.getElementById("applyModalCancel");
    if (cancelBtn) {
        cancelBtn.addEventListener("click", function () {
            overlay.classList.remove("active");
        });
    }
});
