/*
 * Receipt Print Handler (Admin Dashboard)
 *
 * Opens a dedicated receipt window for printing an order slip.
 *
 * Function:
 * - printReceipt(orderId)
 *   Opens receipt.php in a new centered popup window
 *   with fixed dimensions for consistent print layout.
 *
 * Features:
 * - Centers popup relative to current browser window
 * - Disables resizing for consistent receipt formatting
 * - Enables scrollbars for smaller screens
 * - Passes order_id via query string to receipt generator
 *
 * Used in Pages/admin/dashboard.php for printing order receipts
 * from the order management interface.
 */
function printReceipt(orderId) {
  // 1. Hide the context menu
  if (!document.getElementById("contextMenu").classList.contains("hidden")) {
      document.getElementById("contextMenu").classList.add("hidden");
  };
  if (!orderId) return;

  const width = 800;
  const height = 1000;
  const left = window.screenX + (window.innerWidth - width) / 2;
  const top = window.screenY + (window.innerHeight - height) / 2;

  window.open(
    `./../../Pages/Script/receipt.php?order_id=${orderId}`,
    "_blank",
    `width=${width},height=${height},top=${top},left=${left},resizable=no,scrollbars=yes`
  );
}