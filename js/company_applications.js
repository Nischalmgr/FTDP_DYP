document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".co-status-select").forEach(function (select) {

        select.dataset.previous = select.value;

        select.addEventListener("change", function () {

            var applicationId = select.dataset.applicationId;
            var newStatus     = select.value;
            var oldStatus     = select.dataset.previous;

            select.disabled = true;

            FTDP.post("applicant_action.php", {
                action:         "set_status",
                application_id: applicationId,
                status:         newStatus

            }).then(function (data) {

                if (!data.success) {

                    select.value = oldStatus;

                    FTDP.toast(data.message || "Could not update the status.", "error");

                    return;
                }

                select.dataset.previous = data.status;

                document
                    .querySelectorAll('.status-badge[data-application-id="' + applicationId + '"]')
                    .forEach(function (badge) {

                        badge.className = "status-badge status-" + data.status;
                        badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    });

                if (data.changed) {

                    FTDP.toast(
                        data.status === "pending"
                            ? "Status set to pending."
                            : "Status updated — the designer has been notified."
                    );
                }

            }).catch(function () {

                select.value = oldStatus;

                FTDP.toast("Network problem. Please try again.", "error");

            }).then(function () {

                select.disabled = false;
            });
        });
    });
});
