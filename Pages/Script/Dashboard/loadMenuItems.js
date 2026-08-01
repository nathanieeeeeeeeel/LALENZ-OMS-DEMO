// Global menu cache (used across dropdowns, edit modal, etc.)
window.MENU_ITEMS = [];

/**
 * Loads menu items from API and stores them in memory.
 * Also triggers dropdown population after successful fetch.
 */
async function loadMenuItems() {
    try {
        // Fetch menu items (cache-busted with timestamp to avoid stale data)
        const res = await fetch('./../../Pages/Script/api/items/get.php?t=' + Date.now());
        const data = await res.json();

        // Validate API response structure
        if (!data || data.status !== "success") {
            throw new Error(data?.message || "Invalid API response");
        }

        // Normalize and sanitize menu items
        MENU_ITEMS = (data.items || [])
            .filter(Boolean) // remove null/undefined entries
            .map(i => ({
                ...i,
                name: (i.name || "").trim(),     // ensure clean string
                price: Number(i.price) || 0      // force numeric price
            }));

        console.log("Menu Loaded:", MENU_ITEMS.length, "items");

        // Auto-populate dropdown AFTER data is ready
        populateDropdown();

    } catch (err) {
        console.error("Menu load failed:", err);

        // Fail-safe fallback to prevent undefined crashes
        MENU_ITEMS = [];
    }
}

/**
 * Populates the menu dropdown (#menuSelect)
 * Uses cached MENU_ITEMS instead of refetching.
 */
function populateDropdown() {
    const dropdown = document.getElementById('menuSelect');

    // Prevent errors if element is not yet in DOM
    if (!dropdown) {
        console.warn('menuSelect not found, skipping populateDropdown');
        return;
    }

    dropdown.innerHTML = '';

    // Handle empty state gracefully
    if (!Array.isArray(MENU_ITEMS) || MENU_ITEMS.length === 0) {
        dropdown.innerHTML = `<option disabled>No items available</option>`;
        return;
    }

    // Sort alphabetically, then by price
    MENU_ITEMS
        .slice() // avoid mutating original array
        .sort((a, b) => a.name.localeCompare(b.name) || a.price - b.price)
        .forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;

            // Display format: "Item Name — ₱123.00"
            option.textContent = `${item.name} — ₱${Number(item.price).toFixed(2)}`;

            dropdown.appendChild(option);
        });
}