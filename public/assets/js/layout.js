/**
 * Layout & Theme Management
 * Seamless Light/Dark Mode Persistence via localStorage and sessionStorage
 */
(function () {
    "use strict";

    try {
        var savedTheme = localStorage.getItem("data-bs-theme") || sessionStorage.getItem("data-bs-theme");
        
        if (!savedTheme) {
            savedTheme = document.documentElement.getAttribute("data-bs-theme") || "light";
        }

        var topbarTheme = (savedTheme === "dark") ? "dark" : "light";

        document.documentElement.setAttribute("data-bs-theme", savedTheme);
        document.documentElement.setAttribute("data-layout-mode", savedTheme);
        document.documentElement.setAttribute("data-topbar", topbarTheme);

        sessionStorage.setItem("data-bs-theme", savedTheme);
        sessionStorage.setItem("data-layout-mode", savedTheme);
        sessionStorage.setItem("data-topbar", topbarTheme);

        localStorage.setItem("data-bs-theme", savedTheme);
        localStorage.setItem("data-layout-mode", savedTheme);
        localStorage.setItem("data-topbar", topbarTheme);

        var attributes = [
            "data-layout",
            "data-sidebar-size",
            "data-layout-width",
            "data-sidebar",
            "data-sidebar-image",
            "data-layout-direction",
            "data-layout-position",
            "data-layout-style",
            "data-preloader",
            "data-body-image",
            "data-theme",
            "data-theme-colors"
        ];

        attributes.forEach(function (attr) {
            var val = sessionStorage.getItem(attr) || localStorage.getItem(attr);
            if (val) {
                document.documentElement.setAttribute(attr, val);
            }
        });
    } catch (e) {
        console.error("Theme initialization error:", e);
    }
})();