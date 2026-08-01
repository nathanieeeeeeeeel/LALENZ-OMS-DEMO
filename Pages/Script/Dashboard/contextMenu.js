function showShortcut(event, row) {
    event.preventDefault();

    const menu = document.getElementById("contextMenu");
    const submenu = document.getElementById("statusSubmenu");

    menu.dataset.orderId = row.dataset.orderId;
    menu.dataset.customer = row.dataset.customer;
    menu.dataset.status = row.dataset.status;

    menu.classList.remove("hidden");

    // Hide temporarily so we can measure it
    menu.style.left = "-9999px";
    menu.style.top = "-9999px";

    const menuRect = menu.getBoundingClientRect();

    let x = event.clientX;
    let y = event.clientY;

    // Keep main menu inside viewport
    if (x + menuRect.width > window.innerWidth) {
        x = window.innerWidth - menuRect.width - 8;
    }

    if (y + menuRect.height > window.innerHeight) {
        y = window.innerHeight - menuRect.height - 8;
    }

    // Position the menu FIRST
    menu.style.left = `${x}px`;
    menu.style.top = `${y}px`;

    /*
    Measure submenu
    */
    submenu.classList.remove("hidden");
    submenu.style.visibility = "hidden";

    const submenuRect = submenu.getBoundingClientRect();

    submenu.classList.add("hidden");
    submenu.style.visibility = "";

    /*
    Reset submenu
    */
    submenu.classList.remove("left-full", "right-full", "-ml-1", "-mr-1");

    submenu.style.transform = "";

    /*
    Horizontal flip
    */
    if (x + menuRect.width + submenuRect.width > window.innerWidth) {
        submenu.classList.add("right-full", "-mr-1");
    } else {
        submenu.classList.add("left-full", "-ml-1");
    }

    /*
    Vertical flip
    */
    const updateRow = submenu.parentElement;
    const rowRect = updateRow.getBoundingClientRect();

    const overflow = rowRect.top + submenuRect.height - window.innerHeight;

    if (overflow > 0) {
        submenu.style.transform = `translateY(${-overflow - 8}px)`;
    } else {
        submenu.style.transform = "translateY(0)";
    }
}

function hideContextMenus() {
    const menu = document.getElementById("contextMenu");
    const submenu = document.getElementById("statusSubmenu");

    if (menu) {
        menu.classList.add("hidden");
    }

    if (submenu) {
        submenu.classList.add("hidden");
    }
}


// Window/page scroll
window.addEventListener("scroll", hideContextMenus);

// Capture scroll events from any scrollable element
document.addEventListener("scroll", hideContextMenus, true);