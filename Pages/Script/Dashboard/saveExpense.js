/*
 * Expense Creation Handler (Admin Dashboard)
 *
 * Handles saving of new expense records from the dashboard modal form.
 *
 * Function:
 * - saveExpense()
 *   Collects expense input values and sends them to save_expense.php
 *   via a JSON POST request.
 *
 * Flow:
 * - Reads expense description and amount from form inputs
 * - Validates required fields before submission
 * - Sends data to backend expense API (save_expense.php)
 * - On success:
 *     • Clears input fields
 *     • Closes expense modal
 *     • Refreshes dashboard stats via loadStats()
 *
 * Notes:
 * - Uses current date as default reference (frontend-side)
 * - Relies on backend for final validation and storage
 *
 * Used in Pages/admin/dashboard.php for adding new expense entries
 * directly from the admin financial dashboard.
*/
async function saveExpense() {
    const date = new Date().toISOString().split("T")[0];
    const desc = document.getElementById("expDesc").value;
    const amount = document.getElementById("expAmount").value;

    if (!desc || !amount) return alert("Please fill all fields");
    const url = `./../../Pages/Script/save_expense.php`;

    const response = await fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            description: desc,
            amount: amount
        }),
    });

    const result = await response.json();
    if (result.status === "success") {
        document.getElementById("expDesc").value = "";
        document.getElementById("expAmount").value = "";
        toggleExpenseModal();
        loadStats(); // Refresh dashboard financial cards
    }
}