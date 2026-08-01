// âœ… Attach event listeners dynamically
document.addEventListener("input", (e) => {
    if (e.target.classList.contains("item-qty")) {
        onEditItemQtyChange(e.target);
    }
});

document.addEventListener("change", (e) => {
    if (e.target.classList.contains("item-select")) {
        onEditItemChange(e.target);
    }
});

document.addEventListener("keyup", async (e) => {
    if (e.key === "Escape") {
        if ((document.getElementById("customerModal").classList.contains("hidden") == false && document.getElementById("editOrderModal").classList.contains("hidden") == true)) {
        await closeModal();
        } else {
        await closeEditOrderModal();
        };
    }
});