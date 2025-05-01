document.addEventListener("DOMContentLoaded", function () {
  // Initialize tooltips for truncated names
  var tooltipTriggerList = [].slice.call(document.querySelectorAll("[title]"));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Auto-focus name field on home page
  if (document.getElementById("name")) {
    document.getElementById("name").focus();
  }

  // Responsive table adjustments
  function adjustTable() {
    const tables = document.querySelectorAll(".table-responsive");
    tables.forEach((table) => {
      if (window.innerWidth < 768) {
        table.classList.add("table-responsive-sm");
      } else {
        table.classList.remove("table-responsive-sm");
      }
    });
  }

  // Run on load and resize
  adjustTable();
  window.addEventListener("resize", adjustTable);

  // Enhanced delete confirmation
  document.querySelectorAll(".delete-btn").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const memberName =
        this.closest("tr").querySelector("td:nth-child(2)").textContent;
      if (
        confirm(
          `Are you sure you want to delete "${memberName.trim()}" from attendance?`
        )
      ) {
        const id = this.getAttribute("data-id");
        fetch("process.php?action=delete&id=" + id)
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              this.closest("tr").remove();
              // Update count badge
              const badge = document.querySelector(".badge");
              badge.textContent = parseInt(badge.textContent) - 1 + " members";

              // Show toast notification
              showToast(
                "Success",
                "Attendance record deleted successfully",
                "success"
              );
            } else {
              showToast("Error", data.message, "danger");
            }
          })
          .catch((error) => {
            showToast("Error", "Network error occurred", "danger");
          });
      }
    });
  });

  // Toast notification function
  function showToast(title, message, type) {
    const toastContainer =
      document.getElementById("toast-container") || createToastContainer();
    const toastId = "toast-" + Date.now();

    const toastHTML = `
            <div id="${toastId}" class="toast show align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong>: ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

    toastContainer.insertAdjacentHTML("beforeend", toastHTML);

    // Auto-remove toast after 5 seconds
    setTimeout(() => {
      const toastElement = document.getElementById(toastId);
      if (toastElement) {
        toastElement.remove();
      }
    }, 5000);
  }

  function createToastContainer() {
    const container = document.createElement("div");
    container.id = "toast-container";
    container.className = "position-fixed bottom-0 end-0 p-3";
    container.style.zIndex = "11";
    document.body.appendChild(container);
    return container;
  }
});
