tippy("[data-tippy-content]", {
    allowHTML: true,
    animation: "shift-away",
    theme: "light-border",
    delay: [300, 0],
});
document.addEventListener("DOMContentLoaded", () => {
    // Grab elements
    const adminBtn = document.getElementById("adminMenuButton");
    const adminDropdown = document.getElementById("adminDropdown");
    const themeBtn = document.getElementById("themeToggleBtn");
    const themeDropdown = document.getElementById("themeDropdown");
    // Utility to toggle dropdowns
    function toggleDropdown(target, other) {
        if (!target) return;
        target.addEventListener("click", (e) => {
        e.stopPropagation(); // prevent window click from closing immediately
        target.classList.toggle("active"); // optional, if you want a button active state
        if (target === adminBtn) {
            adminDropdown.classList.toggle("hidden");
        } else if (target === themeBtn) {
            themeDropdown.classList.toggle("hidden");
        }
        // Hide the other dropdown
        if (other) other.classList.add("hidden");
        });
    }

    toggleDropdown(adminBtn, themeDropdown);
    toggleDropdown(themeBtn, adminDropdown);

    // Click outside closes all dropdowns
    window.addEventListener("click", () => {
        if (adminDropdown) adminDropdown.classList.add("hidden");
        if (themeDropdown) themeDropdown.classList.add("hidden");
    });

    window.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        if (deleteModal && !deleteModal.classList.contains("hidden")) {
            deleteModal.classList.add("hidden");
        }

        if (adminDropdown) adminDropdown.classList.add("hidden");
        if (themeDropdown) themeDropdown.classList.add("hidden");
    }

    // Shift + D
    if (e.shiftKey && e.key.toLowerCase() === "d") {
        if (
            document.activeElement &&
            (
                document.activeElement.tagName === "INPUT" ||
                document.activeElement.tagName === "TEXTAREA" ||
                document.activeElement.isContentEditable
            )
        ) return;

        const isDark = document.documentElement.classList.contains("dark");

        setTheme(isDark ? "light" : "dark");
    }
});

    // Load saved theme
    const savedTheme = localStorage.getItem("theme");

    if (savedTheme === "dark") {
        document.documentElement.classList.add("dark");
        document.getElementById("themeDarkIcon").classList.add("fa-moon");
        document.getElementById("themeDarkIcon").classList.remove("fa-sun");
    } else {
        document.documentElement.classList.remove("dark");
        document.getElementById("themeDarkIcon").classList.add("fa-sun");
        document.getElementById("themeDarkIcon").classList.remove("fa-moon");
    }
    
    // =========================
    // THEME INIT
    // =========================
    applyTheme(savedTheme === "dark" ? "dark" : "light");

    function applyTheme(theme) {
        const html = document.documentElement;
        const icon = document.getElementById("themeDarkIcon");
        if (!icon) return;

        if (theme === "dark") {
            html.classList.add("dark");
            icon.classList.replace("fa-sun", "fa-moon");
        } else {
            html.classList.remove("dark");
            icon.classList.replace("fa-moon", "fa-sun");
        }
    }
});

function setTheme(theme) {
    const html = document.documentElement;
    const isCurrentlyDark = html.classList.contains("dark");

    // 1. EXIT if they click "dark" while already in dark mode (or vice versa)
    if ((isCurrentlyDark && theme === "dark") || (!isCurrentlyDark && theme === "light")) {
        console.log("Theme already set. Ignoring spam.");
        return;
    }

    const icon = document.getElementById("themeDarkIcon");

    // 2. Apply the UI changes
    if (theme === "dark") {
        html.classList.add("dark");
        icon?.classList.replace("fa-sun", "fa-moon");
        localStorage.setItem("theme", "dark");
    } else {
        html.classList.remove("dark");
        icon?.classList.replace("fa-moon", "fa-sun");
        localStorage.setItem("theme", "light");
    }

    // 3. Only run the expensive logic ONCE per change
    if (typeof loadDailyStats === "function") {
        loadDailyStats();
    }
    if (typeof loadYearlyReport === "function") {
        loadYearlyReport();
    }

    if (typeof renderChartDynamic === "function") {
        renderChartDynamic();
    }
}