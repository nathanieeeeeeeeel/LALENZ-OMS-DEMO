const statusStyles = {
    "Pending": "bg-orange-500/10 text-orange-500 border-orange-500/20",
    "Out For Delivery": "bg-blue-500/10 text-blue-500 border-blue-500/20",
    "Completed": "bg-emerald-500/10 text-emerald-500 border-emerald-500/20",
    "Cancelled": "bg-red-500/10 text-red-500 border-red-500/20",
    "Scheduled": "bg-purple-500/10 text-purple-500 border-purple-500/20",
    "Preparing": "bg-yellow-500/10 text-yellow-500 border-yellow-500/20",
    "Ready": "bg-green-500/10 text-green-500 border-green-500/20",
    "Serving": "bg-green-500/10 text-green-500 border-green-500/20",
};

const statusNameMap = {
    "pending": "Pending",
    "out_for_delivery": "Out For Delivery",
    "preparing": "Preparing",
    "ready": "Ready",
    "completed": "Completed",
    "cancelled": "Cancelled",
    "scheduled": "Scheduled",
    "serving": "Serving",
};

/**
 * Normalize status values to snake_case lowercase for consistent lookup
 */
function normalizeStatus(status) {
    const raw = String(status || "").trim();
    if (!raw) return "";

    return raw
        .toLowerCase()
        .trim()
        .replace(/\s+/g, "_")
        .replace(/ready\s*for\s*pickup/g, "ready")
        .replace(/^out$/i, "out_for_delivery")
        .replace(/^delivered$/i, "completed")
        .replace(/^done$/i, "completed")
        .replace(/^out_for_delivery$/i, "out_for_delivery")
        .replace(/^completed$/i, "completed");
}

/**
 * Get display name for a status
 */
function getStatusDisplayName(status) {
    const normalized = normalizeStatus(status);
    return statusNameMap[normalized] || status;
}

/**
 * Get CSS classes for a status
 */
function getStatusClasses(status) {
    const normalized = normalizeStatus(status);
    const displayName = getStatusDisplayName(status);
    return statusStyles[displayName] || statusStyles[normalized] || statusStyles[status] || "bg-gray-100/10 text-gray-600 border-gray-200/20";
}

async function showDeleteConfirmation(orderId) {
    const modal = document.getElementById("deleteModal");
    const modalOrderIdDisplay = document.getElementById("modalOrderId");
    const confirmBtn = document.getElementById("confirmDelete");
    const cancelBtn = document.getElementById("cancelDelete");
    const html = document.querySelector("html");

    // Prevent background scrolling
    html.classList.add("overflow-hidden");

    // Set the order ID in the modal for display
    modalOrderIdDisplay.textContent = `#${orderId.toString().padStart(3, "0")}`;

    // Show modal
    modal.classList.remove("hidden");

    return new Promise((resolve) => {
        confirmBtn.onclick = () => {
            modal.classList.add("hidden");
            resolve(true);
        };
        cancelBtn.onclick = () => {
            modal.classList.add("hidden");
            html.classList.remove("overflow-hidden");
            resolve(false);
        };
    });
}
// Helper function to communicate with your PHP backend
async function updateOrderStatus(id, action) {

    const cleanId = id.toString().replace("LNZ-", "").trim();
    
    try {
        if (action === "delete") {
            
            const response = await fetch(`./../../Pages/Script/update_order_status.php`, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${cleanId}&action=delete`,
            });

            const result = await response.json();
            if (!result.success) {
                toastr.error("Error: " + result.message);
            } else {
                toastr.success(result.message);
                loadStats(); // Refresh stats
                const row = document.getElementById(`order-row-${cleanId}`);
                if (row) {
                    row.remove();
                    loadRecentOrders(1); // Refresh the order list to reflect deletion
                };
            };
            document.getElementById("deleteModal")?.classList.add("hidden");
            document.documentElement.classList.remove("overflow-hidden");
            return;

        };
        console.log(`Updating order ${cleanId} to action: ${action}`);

        const response = await fetch("./../../Pages/Script/update_order_status.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${cleanId}&action=${action}`,
        });

        const result = await response.json();

        if (!result.success) {
            alert("Error: " + result.message);
            return;
        }

        toastr.success(result.message);
        loadStats(); // Refresh stats

        const updateStatusElement = (container) => {
            const statusEl = container?.querySelector(".status");
            if (!statusEl) return;

            const displayName = getStatusDisplayName(action);
            const classes = getStatusClasses(action);
            statusEl.className = "status px-3 py-1 rounded-lg text-[11px] font-semibold border";
            statusEl.classList.add(...classes.split(" "));
            statusEl.innerText = displayName;
        };

        const row = document.getElementById(`order-row-${cleanId}`);
        updateStatusElement(row);

        const card = document.getElementById(`order-card-${cleanId}`);
        updateStatusElement(card);

        document.getElementById("customerModal")?.classList.add("hidden");
        document.documentElement.classList.remove("overflow-hidden");

    } catch (error) {
        console.error("Request failed:", error);
        toastr.error("Network error. Please try again.");
    } finally {
        const dropdowns = document.querySelectorAll(".statusDropdown");
        dropdowns.forEach((dropdown) => {
            dropdown.selectedIndex = 0;
        });

        // 1. Hide the context menu
        document.getElementById("contextMenu")
                .classList.add("hidden");
    };
}

function resolveItemName(item) {
    if (item.name) return item.name;

    if (item.id) {
        const match = MENU_ITEMS.find((m) => m.id == item.id);
        if (match) return match.name;
    }

    return `Item #${item.id || "?"}`;
}

async function openEditOrderModal(dataset = {}) {

    try {
        let { orderId } = dataset;

        const fetchOrder = await fetch(`./../../Pages/Script/get_order_details.php?id=${orderId}`);
        const orderData = await fetchOrder.json();
        let {
            advance_payment,
            customer_name,
            customer_address,
            delivery_fee,
            discount,
            grand_total,
            id,
            is_scheduled,
            order_datetime,
            order_items,
            order_note,
            payment_method,
            scheduled_date,
            scheduled_datetime,
            status,
            subtotal,
            success 
        } = orderData;

        console.log("Opening edit modal with dataset:", orderData);

        const modal = document.getElementById("modalOrderHistory");

        // Load menu items if not already loaded
        if (!MENU_ITEMS.length) {
            await loadMenuItems();
        }

        // Populate form fields with data from dataset
        document.getElementById("editOrderId").value = id || "";
        document.getElementById("editCustomerName").value = customer_name || "";
        document.getElementById("editCustomerAddress").value = customer_address || "";
        document.getElementById("editOrderNote").value = order_note || "";

        // Handle payment method
        const payment = payment_method || "";
        const select = document.getElementById("editPaymentMethod");
        const otherInput = document.getElementById("editOtherPayment");

        const predefined = ["Cash", "GCash", "PayMaya", "Bank Transfer"];
        if (predefined.includes(payment)) {
            select.value = payment;
            otherInput.classList.add("hidden");
            otherInput.value = "";
        } else if (payment) {
            select.value = "Others";
            otherInput.classList.remove("hidden");
            otherInput.value = payment;
        } else {
            select.value = "";
            otherInput.classList.add("hidden");
            otherInput.value = "";
        }

        // Handle scheduled order status
        const isScheduled = JSON.parse(is_scheduled);
        const scheduledDateTime = scheduled_datetime || "";

        const checkbox = document.getElementById("editIsScheduled");
        const fields = document.getElementById("editScheduleFields");
        const dateInput = document.getElementById("editScheduledDate");
        const timeInput = document.getElementById("editScheduledTime");

        if (isScheduled && scheduledDateTime) {
            checkbox.checked = true;
            fields.classList.remove("hidden");
            dateInput.value = scheduledDateTime.split(" ")[0];
            timeInput.value = scheduledDateTime.split(" ")[1];
        } else {
            checkbox.checked = false;
            fields.classList.add("hidden");
            dateInput.value = "";
            timeInput.value = "";
        }

        // Load existing total breakdown values into the edit modal
        document.getElementById("editDiscount").value = parseFloat(discount || 0).toFixed(2);
        document.getElementById("editDelivery").value = parseFloat(delivery_fee || 0).toFixed(2);
        document.getElementById("editAdvance").value = parseFloat(advance_payment || 0).toFixed(2);

        // ITEMS - Load and display items
        const container = document.getElementById("editItemsContainer");
        container.innerHTML = "";

        let orderItems = [];
        try {
            orderItems = order_items ? JSON.parse(order_items) : [];
        } catch (e) {
            console.error("Edit parse error:", e);
            orderItems = [];
        }

        // Add each item row to the modal
        await Promise.all(
            orderItems.map((item) =>
                addEditItemRow({
                    id: item.id,
                    qty: Number(item.qty) || 1,
                    amnt: item.amnt,
                })
            )
        );

        recalculateEditTotal();
        document.getElementById("editOrderModal").classList.remove("hidden");
    } catch(err) {
        console.error("Error while opening edit order modal", err.stack);
    } finally {
        
        // 1. Hide the context menu
        if (!document.getElementById("contextMenu").classList.contains("hidden")) {
            document.getElementById("contextMenu").classList.add("hidden");
        };
        // 2. Disable scrolling
        if (!document.documentElement.classList.contains("hidden")) {
            document.documentElement.classList.add("overflow-hidden");
        };
    };
}

async function addEditItemRow(data = {}) {
    const currencyRes = await fetch('./../../currencies.json');
    const currencyData = await currencyRes.json();
    const systemCurrency = await fetch(`./../Script/get_system.php`);
    const systemData = await systemCurrency.json();

    const currencySymbol = currencyData[systemData.currency_code]?.symbol || systemData.currency_code || "₱"; // Default to ₱ if not set

    const container = document.getElementById("editItemsContainer");

    const row = document.createElement("div");
    row.className = "flex w-auto gap-2 mb-2 items-center";

    row.innerHTML = `
        <select class="editItem border-none p-1 w-full flex-1 bg-gray-100 rounded text-black dark:bg-gray-700 dark:text-white w-6 text-xs">
            <option value="" disabled selected>Select Item</option>
            ${window.MENU_ITEMS.map(
              (m) =>
                `<option value="${m.id}" ${m?.stock <= 0 ? "disabled" : ""} data-price="${m.price}" data-stock="${m.stock}">
                    ${m.name} — ${currencySymbol}${m.price.toFixed(2)}
                    ${m?.stock <= 0 ? " (Out of Stock)" : ""}
                </option>`
            ).join("")}
        </select>

        <div class="flex items-center gap-1">
            <button type="button" class="decrementQty bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-black dark:text-white px-2 py-1 rounded text-xs">-</button>
            <input
                type="number"
                class="editQty text-center border-none w-10 p-1 bg-gray-100 rounded text-black dark:bg-gray-700 dark:text-white text-xs"
                value="${data.qty || 1}"
                min="1"
            >
            <button type="button" class="incrementQty bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-black dark:text-white px-2 py-1 rounded text-xs">+</button>
        </div>

        <input type="number" class="editAmount border-none w-20 p-1 bg-gray-100 rounded text-black dark:bg-gray-700 dark:text-white text-xs" value="${data.amnt || 0}" readonly>

        <button type="button"
            class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded removeRowBtn">
            ✕
        </button>
    `;

    container.appendChild(row);

    const select = row.querySelector(".editItem");
    const qtyInput = row.querySelector(".editQty");
    const amountInput = row.querySelector(".editAmount");
    const removeBtn = row.querySelector(".removeRowBtn");
    const decrementBtn = row.querySelector(".decrementQty");
    const incrementBtn = row.querySelector(".incrementQty");

    // Disable number input spinners
    qtyInput.style.webkitAppearance = 'none';
    qtyInput.style.mozAppearance = 'textfield';
    qtyInput.style.appearance = 'none';

    // PRESELECT ITEM
    if (data.id) select.value = data.id;

    function updateRow() {
        const selected = select.selectedOptions[0];
        if (!selected) return;

        const price = Number(selected.dataset.price);
        const stock = Number(selected.dataset.stock || 0);

        let qty = Number(qtyInput.value);

        // clamp quantity to stock
        if (qty > stock) {
            toastr.warning(`Only ${stock} units available.`);
            qty = stock;
            qtyInput.value = stock;
        }

        const lineTotal = price * qty;
        amountInput.value = lineTotal;

        recalculateEditTotal();
    }

    select.addEventListener("change", updateRow);
    select.addEventListener("change", () => {
    const stock = Number(select.selectedOptions[0].dataset.stock || 0);

    if (stock <= 0) {
        qtyInput.value = 0;
        amountInput.value = 0;
        toastr.error("Item is out of stock");
    } else {
        qtyInput.value = 1;
    }

    updateRow();
});
    qtyInput.addEventListener("input", updateRow);

    // INCREMENT/DECREMENT QUANTITY
    decrementBtn.addEventListener("click", () => {
        const currentQty = Number(qtyInput.value);
        if (currentQty > 1) {
            qtyInput.value = currentQty - 1;
            updateRow();
        }
    });

    incrementBtn.addEventListener("click", () => {
        const currentQty = Number(qtyInput.value);
        qtyInput.value = currentQty + 1;
        updateRow();
    });

    // REMOVE ITEM
    removeBtn.addEventListener("click", () => {
        row.remove();
        recalculateEditTotal();
    });
}

function recalculateEditTotal() {
    let subtotal = 0;

    // Loop through all items in the container and calculate their total
    document.querySelectorAll("#editItemsContainer .editAmount").forEach((el) => {
        const val = parseFloat(el.value) || 0;
        subtotal += val;
    });

    const subtotalInput = document.getElementById("editSubtotal");
    const discountInput = document.getElementById("editDiscount");
    const deliveryFeeInput = document.getElementById("editDelivery");
    const advanceInput = document.getElementById("editAdvance");
    const totalInput = document.getElementById("editGrandTotal");

    const discount = parseFloat(discountInput?.value || 0) || 0;
    const delivery = parseFloat(deliveryFeeInput?.value || 0) || 0;
    const advance = parseFloat(advanceInput?.value || 0) || 0;

    const grandTotal = subtotal - discount + delivery - advance;

    if (subtotalInput) {
        subtotalInput.value = subtotal.toFixed(2);
    }

    if (totalInput) {
        totalInput.value = grandTotal.toFixed(2);
    }
}

function closeEditOrderModal() {
    document.getElementById("editOrderModal").classList.add("hidden");
    
    // Clear all form fields
    document.getElementById("editOrderId").value = "";
    document.getElementById("editCustomerName").value = "";
    document.getElementById("editCustomerAddress").value = "";
    document.getElementById("editOrderNote").value = "";
    document.getElementById("editPaymentMethod").value = "";
    document.getElementById("editOtherPayment").value = "";
    document.getElementById("editOtherPayment").classList.add("hidden");
    document.getElementById("editIsScheduled").checked = false;
    document.getElementById("editScheduleFields").classList.add("hidden");
    document.getElementById("editScheduledDate").value = "";
    document.getElementById("editScheduledTime").value = "";
    document.getElementById("editDiscount").value = "0.00";
    document.getElementById("editDelivery").value = "0.00";
    document.getElementById("editAdvance").value = "0.00";
    document.getElementById("editSubtotal").value = "0.00";
    document.getElementById("editGrandTotal").value = "0.00";
    document.getElementById("editItemsContainer").innerHTML = "";
    
    const customerModal = document.getElementById("customerModal");
    const editOrderModal = document.getElementById("editOrderModal");
    const deleteModal = document.getElementById("deleteModal");
    const statusConfirmModal = document.getElementById("statusConfirmModal");

    const isAnyOtherModalOpen =
        (customerModal && !customerModal.classList.contains("hidden")) ||
        (deleteModal && !deleteModal.classList.contains("hidden")) ||
        (statusConfirmModal && !statusConfirmModal.classList.contains("hidden"));

    if (!isAnyOtherModalOpen) {
        const html = document.querySelector("html");
        html.classList.remove("overflow-hidden");
    }
}

function closeModal() {
    document.getElementById("customerModal").classList.add("hidden");
    
    const html = document.querySelector("html");
    html.classList.remove("overflow-hidden");
}

// Recalculate edit order totals when adjustment fields change
document.addEventListener("input", (event) => {
    const target = event.target;
    if (!target || !target.id) return;

    const adjustmentFields = ["editDiscount", "editDelivery", "editAdvance"];
    if (adjustmentFields.includes(target.id)) {
        recalculateEditTotal();
    }
});

function capitalizeName(name) {
    return name
        .split(" ")
        .map((word) =>
            word
            .split("-")
            .map((part) => part.charAt(0).toUpperCase() + part.slice(1).toLowerCase())
            .join("-"),
        )
        .join(" ");
}

// Sorting function
function applySorting(orders, sortBy) {
    const [field, direction] = sortBy.split("-");
    const isAsc = direction === "asc";

    return [...orders].sort((a, b) => {
        let aVal, bVal;

        switch (field) {

            case "date":
            case "datetime":
                aVal = new Date(a.order_datetime || 0);
                bVal = new Date(b.order_datetime || 0);
                break;

            case "id":
                aVal = Number(a.id);
                bVal = Number(b.id);
                break;

            case "customer":
            case "name":
                aVal = (a.customer_name || "").toLowerCase();
                bVal = (b.customer_name || "").toLowerCase();
                break;

            case "total":
                aVal = Number(a.grand_total);
                bVal = Number(b.grand_total);
                break;
            
            case "status":
                aVal = a.status || "";
                bVal = b.status || "";
                break;

            case "items":
                try {
                    aVal = a.order_items ? JSON.parse(a.order_items).length : 0;
                    bVal = b.order_items ? JSON.parse(b.order_items).length : 0;
                } catch {
                    aVal = 0;
                    bVal = 0;
                }
                break;

            default:
                return 0;
        }

        // Normalize invalid dates
        if (aVal instanceof Date && isNaN(aVal)) aVal = new Date(0);
        if (bVal instanceof Date && isNaN(bVal)) bVal = new Date(0);

        // Compare
        if (aVal instanceof Date && bVal instanceof Date) {
            return isAsc ? aVal - bVal : bVal - aVal;
        }

        if (typeof aVal === "number" && typeof bVal === "number") {
            return isAsc ? aVal - bVal : bVal - aVal;
        }

        return isAsc
            ? String(aVal).localeCompare(String(bVal))
            : String(bVal).localeCompare(String(aVal));
    });
}

function renderPageNumbers(totalOrders, rowsPerPage, currentPage) {
    const totalPages = Math.ceil(totalOrders.length / rowsPerPage);
    const container = document.getElementById("paginationButtons");
    const nextBtn = document.getElementById("nextBtn");

    // Remove old numbered buttons
    [...container.querySelectorAll(".pageNumberBtn")].forEach((btn) => btn.remove());

    // Show max 5 pages at a time
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);
    if (currentPage <= 2) endPage = Math.min(5, totalPages);
    if (currentPage >= totalPages - 1) startPage = Math.max(1, totalPages - 4);

    for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        btn.className = `px-3 py-1 rounded pageNumberBtn ${i === currentPage ? "bg-blue-500 text-white enabled:hover:bg-blue-600 dark:bg-white dark:text-black" : "bg-gray-200 text-black enabled:hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:enabled:hover:bg-gray-600"}`;
        if (i !== currentPage) {
            btn.onclick = () => loadRecentOrders(i);
        } else {
            btn.disabled = true; // disable current page button
            btn.classList.add("cursor-not-allowed", "opacity-50"); // optional styling
        }
        container.insertBefore(btn, nextBtn);
    }
}

// Update your Apply Filter button logic
function applyAllFilters() {
    loadRecentOrders(); // Updates the Table
    loadStats(); // Updates the Financial Cards
}

// When quantity changes
function onEditItemQtyChange(input) {
    // alert("Quantity changed! Recalculating totals..."); // Debugging alert
    const row = input.closest(".grid");
    const qty = Number(input.value) || 0;
    const price = Number(row.querySelector(".item-price").value) || 0;

    // ✅ Compute row subtotal
    row.querySelector(".item-subtotal").value = (qty * price).toFixed(2);

    // Update grand total
    recalculateEditTotal();
}

// When dropdown changes
function onEditItemChange(select) {
    const row = select.closest(".grid");
    const menuId = Number(select.value);

    const menuItem = window.MENU_ITEMS.find((i) => i.id === menuId);
    if (!menuItem) return;

    // Update price
    row.querySelector(".item-price").value = menuItem.price;

    // ✅ Compute row subtotal
    const qty = Number(row.querySelector(".item-qty").value) || 0;
    row.querySelector(".item-subtotal").value = (qty * menuItem.price).toFixed(2);

    // Update grand total
    recalculateEditTotal();
}

async function saveEditedOrder() {
    const orderId = document.getElementById("editOrderId").value;

    const items = [...document.querySelectorAll("#editItemsContainer > div")]
        .map((row) => {
            const select = row.querySelector(".editItem");
            const qtyInput = row.querySelector(".editQty");
            const amountInput = row.querySelector(".editAmount");

            if (!select || !qtyInput || !amountInput) return null;

            const itemId = parseInt(select.value);
            const qty = parseInt(qtyInput.value);
            const amnt = parseFloat(amountInput.value);

            if (!itemId || !qty || !amnt) return null;

            const menuItem = window.MENU_ITEMS.find((m) => m.id === itemId);
            console.log(`[DEBUG]: Item ID: ${itemId}, Qty: ${qty}, Amount: ${amnt}, Menu Item:`, menuItem);

            if (qty > menuItem.stock) {
                toastr.warning(`Only ${menuItem.filter(m => m.id === itemId)[0].stock} unit(s) of "${menuItem.filter(m => m.id === itemId)[0].name}" available. Please adjust quantity.`);
                throw new Error(`Stock limit exceeded for item ID ${itemId}`);
            }

            return {
                id: itemId,
                name: menuItem ? menuItem.name : `Item #${itemId}`,
                qty: qty,
                price: amnt / qty,
            };
        })
        .filter(Boolean);

    if (items.length === 0) {
        toastr.error("Please add at least one item to the order.");
        return;
    }

    // SCHEDULE DATA
    const isScheduled = document.getElementById("editIsScheduled").checked ? 1 : 0;
    const scheduledDate = document.getElementById("editScheduledDate").value.trim();
    const scheduledTime = document.getElementById("editScheduledTime").value.trim();

    if (isScheduled && !scheduledDate) {
        toastr.warning("Please select scheduled date.");
        document.getElementById("editScheduledDate").focus();
        return;
    }

    if (isScheduled && !scheduledTime) {
        toastr.warning("Please select scheduled time.");
        document.getElementById("editScheduledTime").focus();
        return;
    }

    // PAYMENT METHOD
    const paymentSelect = document.getElementById("editPaymentMethod");
    const otherPaymentInput = document.getElementById("editOtherPayment");
    let payment_method = paymentSelect.value;
    if (payment_method === "Others") {
        payment_method = otherPaymentInput.value.trim() || "N/A";
    }

    // CALCULATE TOTAL BREAKDOWN
    const discount = parseFloat(document.getElementById("editDiscount").value || 0) || 0;
    const delivery_fee = parseFloat(document.getElementById("editDelivery").value || 0) || 0;
    const advance_payment = parseFloat(document.getElementById("editAdvance").value || 0) || 0;
    const itemTotal = items.reduce((sum, item) => sum + item.qty * item.price, 0);
    const grandTotal = Math.max(0, itemTotal - discount + delivery_fee - advance_payment);

    const payload = {
        order_id: orderId,
        customer: document.getElementById("editCustomerName").value.trim(),
        address: document.getElementById("editCustomerAddress").value.trim(),
        notes: document.getElementById("editOrderNote").value.trim(),
        items: items,
        original_items: JSON.parse(document.getElementById("modalOrderHistory").dataset.items || "[]"),
        discount: discount.toFixed(2),
        delivery_fee: delivery_fee.toFixed(2),
        advance_payment: advance_payment.toFixed(2),
        total: grandTotal.toFixed(2),
        is_scheduled: isScheduled,
        scheduled_date: scheduledDate || null,
        scheduled_time: scheduledTime || null,
        payment_method, // ✅ now included!
    };

    try {
        const res = await fetch("./../../Pages/Script/update_order.php?t=" + Date.now(), {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload),
        });

        const text = await res.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            console.error("Server returned non-JSON:", text);
            throw new Error("Server Error: Check PHP Response");
        }

        if (result.status === "success") {
            toastr.success(`Order #${orderId} updated successfully.`);
            closeEditOrderModal();
            if (typeof closeModal === "function") closeModal();
            loadRecentOrders();
            if (typeof loadStats === "function") loadStats();
        } else {
            toastr.error(result.message || "Update failed");
        }
    } catch (err) {
        console.error(err);
        toastr.error("Server error while saving order");
    }
}

function toggleExpenseModal() {
    document.getElementById("expenseModal").classList.toggle("hidden");
}

function toggleEditScheduleFields() {
    const checkbox = document.getElementById("editIsScheduled");
    const fields = document.getElementById("editScheduleFields");

    if (checkbox.checked) {
        fields.classList.remove("hidden");
    } else {
        fields.classList.add("hidden");
    }
}