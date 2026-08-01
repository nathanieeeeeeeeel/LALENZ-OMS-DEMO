/*
 * Order Status & Action Handler (Admin)
 *
 * Handles confirmation flow and execution for order actions
 * inside the admin dashboard.
 *
 * Functions:
 *
 * - showStatusConfirmation(message)
 *   Displays a modal confirmation dialog and returns a Promise
 *   that resolves to true (confirm) or false (cancel).
 *
 * - orderSlipActions(option, orderId)
 *   Routes admin actions for a specific order:
 *     • delete → triggers delete confirmation then removes order
 *     • delivered / out / pending / cancelled / scheduled → 
 *       shows status change confirmation then updates order status
 *
 * Features:
 * - Uses centralized confirmation modal UI
 * - Maps status keys to readable labels
 * - Prevents accidental destructive actions via async confirmation
 *
 * Used in Pages/admin/dashboard.php for order management actions
 * triggered from the order modal (customerModal).
*/
async function showStatusConfirmation(message) {
    return new Promise((resolve) => {
        const modal = document.getElementById("statusConfirmModal");
        const text = document.getElementById("statusConfirmText");
        const confirmBtn = document.getElementById("confirmStatusBtn");
        const cancelBtn = document.getElementById("cancelStatusBtn");

        text.textContent = message;
        modal.classList.remove("hidden");
        modal.classList.add("flex");

        const cleanup = () => {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            confirmBtn.onclick = null;
            cancelBtn.onclick = null;
        };

        confirmBtn.onclick = () => {
            cleanup();
            resolve(true);
        };

        cancelBtn.onclick = () => {
            cleanup();
            resolve(false);
        };
    });
};
async function orderSlipActions(option, orderId) {
    const id = orderId;
    console.log(`[DEBUG]: Action: ${option} on Order ID: ${id}`);

    const statusMap = {
        delivered: "Delivered",
        out: "Out For Delivery",
        pending: "Pending",
        cancelled: "Cancelled",
        scheduled: "Scheduled"
    };

    if (option === "delete") {
        // ===============================
        // HIDE THE CONTEXT MENU UPON CLICK
        // ===============================
        await document.getElementById("contextMenu")
        .classList.add("hidden");

        closeModal();
        const confirmed = await showDeleteConfirmation(id);
        if (!confirmed) return;
        await updateOrderStatus(id, "delete");

    } else if (
        option === "delivered" ||
        option.toLowerCase() === "out" ||
        option === "pending" ||
        option === "cancelled" ||
        option === "scheduled"
    ) {
        // const modal = document.getElementById("customerModal");
        // modal.classList.add("hidden");
        const statusText = statusMap[option.toLowerCase()];

        const confirmed = await showStatusConfirmation(
            `Change status to ${statusText} for Order ID ${id}?`
        );

        if (!confirmed) return;

        await updateOrderStatus(id, option);
    }
}
