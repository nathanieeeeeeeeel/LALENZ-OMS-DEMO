<?php

// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

// ----------------------------
// Includes
// ----------------------------

require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/init.php'
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth select-none">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($systemName); ?> - Expenses Log</title>
    <link rel="icon" type="image/png" href="<?= $logo; ?>?v=<?= time(); ?>" />
    <!-- 1. JQUERY MUST BE FIRST -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"></script>

    <!-- 2. TOASTR SECOND -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>

    <!-- 3. TAILWIND & OTHERS LAST -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

    <script>
      tailwind.config = { darkMode: "class" };
      (function () {
        const theme = localStorage.getItem("theme") || "dark";
        if (theme === "dark") document.documentElement.classList.add("dark");
      })();
    </script>
  </head>

  <body class="bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 font-sans transition-colors duration-300">
    <?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/navbar.php'; ?>

    <main class="max-w-6xl mx-auto px-6 py-12">
      <!-- Header Section -->
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-6xl font-black dark:text-gray-100 mb-4 tracking-tighter">Expenses Overview</h1>
        <div id="liveClock" class="text-lg font-mono font-bold text-gray-400 italic tracking-widest uppercase">Loading Time...</div>
      </div>

      <!-- Financial Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
        <!-- Total Gross Sales -->
        <div class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-emerald-500/40 via-transparent to-transparent hover:from-emerald-400/70 transition-all duration-500">
          <div class="relative backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 overflow-hidden">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br from-emerald-500/10 via-transparent to-transparent"></div>
            <div class="absolute top-0 left-0 h-1 w-0 bg-emerald-500 group-hover:w-full transition-all duration-500"></div>
            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em] mb-3">Total Gross Sales</p>
            <div id="stat-sales" class="text-4xl font-black text-gray-900 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">₱0.00</div>
          </div>
        </div>

        <!-- Total Expenses -->
        <div class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-orange-500/40 via-transparent to-transparent hover:from-orange-400/70 transition-all duration-500">
          <div class="relative backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl hover:shadow-orange-500/10 transition-all duration-500 overflow-hidden">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br from-orange-500/10 via-transparent to-transparent"></div>
            <div class="absolute top-0 left-0 h-1 w-0 bg-orange-500 group-hover:w-full transition-all duration-500"></div>
            <p class="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] mb-3">Total Expenses</p>
            <div id="stat-expenses" class="text-4xl font-black text-gray-900 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">₱0.00</div>
          </div>
        </div>

        <!-- Net Profit / Loss -->
        <div id="net-card" class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-blue-500/40 via-transparent to-transparent hover:from-blue-400/70 transition-all duration-500">
          <div class="relative backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 overflow-hidden">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br from-blue-500/10 via-transparent to-transparent"></div>
            <div id="net-indicator" class="absolute top-0 left-0 h-1 w-0 bg-blue-500 group-hover:w-full transition-all duration-500"></div>
            <p id="net-label" class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-3">Net Profit / Loss</p>
            <div id="stat-net" class="text-4xl font-black text-gray-900 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">₱0.00</div>
          </div>
        </div>
      </div>

      <!-- Control Bar -->
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8 bg-white dark:bg-gray-900 p-5 rounded-[2rem] border border-gray-200 dark:border-gray-800 shadow-xl items-end">
        <div>
          <label class="text-[10px] font-black text-gray-400 uppercase ml-3 mb-1 block">Date</label>
          <input
            type="date"
            id="filterDate"
            onchange="
              fetchExpenses();
              loadStats();
            "
            class="w-full bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-none rounded-2xl px-5 py-3 outline-none font-bold"
          />
        </div>
        <div class="md:col-span-2">
          <label class="text-[10px] font-black text-gray-400 uppercase ml-3 mb-1 block">Category</label>
          <select id="filterCategory" onchange="fetchExpenses()" class="w-full bg-gray-100 dark:bg-gray-800 border-none rounded-2xl px-5 py-3 outline-none text-gray-900 dark:text-gray-100 font-bold">
            <option value="">All Categories</option>
            <option value="COGS">Ingredients & Packaging</option>
            <option value="Utilities">Utilities (LPG, Water, Elec)</option>
            <option value="Gasoline">Gasoline</option>
            <option value="Labor">Wages & Staff</option>
            <option value="Rent">Rent & Space</option>
            <option value="General">General / Miscellaneous</option>
          </select>
        </div>
        <div>
          <label class="text-[10px] font-black text-gray-400 uppercase ml-3 mb-1 block">Search</label>
          <input type="search" id="filterSearch" oninput="fetchExpenses()" placeholder="Search title, details, category, or ID" class="w-full bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-none rounded-2xl px-5 py-3 outline-none font-bold" />
        </div>
        <button onclick="openAddExpenseModal()" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl py-4 transition-all shadow-lg uppercase text-xs tracking-widest">+ New Entry</button>
      </div>

      <!-- Table -->
      <div class="bg-white dark:bg-gray-900/50 backdrop-blur-xl rounded-[2.5rem] shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="hidden md:block overflow-x-auto">
          <table class="min-w-full text-left border-separate border-spacing-0">
            <thead>
              <tr class="bg-gray-100 dark:bg-gray-800/80">
                <th class="px-8 py-5 text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em]">Ref ID</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Timestamp</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Title</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Amount</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-center">Class</th>
              </tr>
            </thead>
            <tbody id="expenseTableBody">
              <tr>
                <td colspan="5" class="p-32 text-center text-gray-400 animate-pulse">Syncing...</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div id="expenseTableMobile" class="md:hidden p-4 space-y-4"></div>
      </div>

      <!-- Pagination Controls -->
      <div class="flex justify-center items-center gap-2 mt-8 w-full" id="expensePagination"></div>

      <?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/footer.php'; ?>
    </main>

    <!-- ✅ ADD EXPENSE MODAL -->
    <div id="expenseModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
      <div class="bg-gray-200 dark:bg-gray-900 border border-gray-700 w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="bg-emerald-600 p-8 text-white">
          <h2 class="text-2xl font-black italic uppercase tracking-tighter">Record New Expense</h2>
        </div>
        <div class="p-8 space-y-5">
          <input type="text" id="expTitle" placeholder="Title" class="w-full bg-gray-200 text-green-700 font-bold dark:bg-gray-800/50 dark:text-white border border-gray-700 rounded-2xl px-5 py-3 outline-none" />
          <textarea id="expDetails" rows="2" placeholder="Details (Optional)" class="w-full bg-gray-200 text-green-700 dark:bg-gray-800/50 dark:text-white border border-gray-700 rounded-2xl px-5 py-3 outline-none resize-none"></textarea>
          <div class="grid grid-cols-2 gap-4">
            <input type="number" id="expAmount" placeholder="Amount" class="w-full bg-gray-200 text-green-700 dark:bg-gray-800/50 dark:text-emerald-400 border border-gray-700 rounded-2xl px-5 py-3 outline-none font-bold" />
            <select id="expCategory" class="w-full bg-gray-200 dark:bg-gray-800/50 dark:text-white border border-gray-700 rounded-2xl px-5 py-3 outline-none">
              <option value="COGS">Market/COGS</option>
              <option value="Utilities">Utilities</option>
              <option value="Gasoline">Gasoline</option>
              <option value="Labor">Wages</option>
              <option value="General" selected>General</option>
            </select>
          </div>
        </div>
        <div class="p-8 bg-gray-100 dark:bg-gray-800/30 flex gap-3">
          <button onclick="closeExpenseModal()" class="flex-1 transition ease-in-out duration-[0.2s] bg-neutral-800 text-white hover:bg-neutral-700 rounded-2xl dark:bg-gray-300 dark:text-black dark:hover:text-gray-950 font-black uppercase text-[10px]">Cancel</button>
          <button onclick="saveExpense()" class="flex-2 bg-emerald-600 px-8 py-4 rounded-2xl text-white font-black text-[10px] uppercase tracking-widest">Save Entry</button>
        </div>
      </div>
    </div>

    <!-- ✅ VIEW EXPENSE MODAL -->
    <div id="viewExpenseModal" class="hidden fixed inset-0 bg-black/85 dark:bg-black/60 backdrop-blur-lg z-50 flex items-center justify-center p-4">
      <div class="relative bg-white dark:bg-gray-900 border border-gray-700 w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden p-8 space-y-6">
        <button onclick="closeViewExpense()" class="absolute top-6 right-6 p-2 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-400 dark:hover:text-white hover:text-black rounded-full transition-all duration-200">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <div class="flex lg:flex-row flex-col justify-between items-start pr-8">
          <h3 id="viewTitle" class="lg:text-3xl text-xl font-black text-green-600 dark:text-white italic tracking-tighter uppercase"></h3>
          <span id="viewCategory" class="px-3 py-1 rounded-full text-[9px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase tracking-widest"></span>
        </div>
        <p id="viewDetails" class="text-gray-400 font-medium overflow-y-auto"></p>
        <div class="flex justify-between items-end">
          <div>
            <span class="text-[10px] font-black text-gray-500 uppercase block mb-1 tracking-widest">Total Impact</span>
            <div id="viewAmount" class="text-4xl text-green-600 dark:text-emerald-400 font-black tracking-tighter"></div>
          </div>
          <div id="viewDate" class="text-[11px] font-mono text-gray-500 italic"></div>
        </div>
        <div class="flex gap-4 pt-4">
          <button id="deleteBtn" class="flex-1 bg-red-600/10 border border-red-500/20 text-red-500 py-4 rounded-2xl font-black text-[10px] uppercase hover:bg-red-600 hover:text-white transition-all">Delete Record</button>
          <button onclick="openModifyFromView()" class="flex-2 bg-emerald-600 text-white py-4 px-5 rounded-2xl font-black text-[10px] uppercase shadow-lg shadow-emerald-900/20 hover:bg-emerald-500 transition-all">Modify Entry</button>
        </div>
      </div>
    </div>

    <!-- ✅ MODIFY MODAL (FIXED v1.4.2) -->
    <div id="modifyExpenseModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-6">
      <div id="modifyModalContent" class="bg-gray-200 dark:bg-gray-900 border border-gray-700 w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
        <!-- Header -->
        <div class="p-8 pb-1 flex justify-between bg-emerald-600 items-start">
          <div class="mb-3">
            <h3 class="text-2xl font-black text-white tracking-tighter italic uppercase tracking-widest">Edit Record</h3>
            <div class="flex flex-col items-start mt-1">
              <span id="modifyIDDisplay" class="text-[13px] font-semibold text-gray-300 tracking-widest uppercase"></span>
              <span id="modifyDateDisplay" class="text-[13px] font-medium text-gray-300 tracking-widest uppercase block"></span>
            </div>
          </div>
          <button onclick="closeModifyModal()" class="text-white hover:text-gray-300 text-2xl transition-transform hover:rotate-90">✕</button>
        </div>

        <!-- Form Body -->
        <div class="p-8 space-y-5">
          <input type="hidden" id="modifyExpId" />

          <div class="space-y-1">
            <label class="text-[10px] font-black text-gray-700 dark:text-gray-500 uppercase ml-2 tracking-widest">Title</label>
            <input type="text" id="modifyExpTitle" class="w-full bg-white/50 focus:bg-white text-green-700 dark:bg-gray-800/40 dark:text-white border border-gray-700 rounded-2xl px-5 py-4 outline-none focus:border-emerald-500 font-bold" />
          </div>

          <!-- ✅ NEW: Details Field (Textarea) -->
          <div class="space-y-1">
            <label class="text-[10px] font-black text-gray-700 dark:text-gray-500 uppercase ml-2 tracking-widest">Details (Optional)</label>
            <textarea id="modifyExpDetails" rows="2" class="w-full bg-white/50 focus:bg-white text-green-700 dark:bg-gray-800/40 dark:text-white border border-gray-700 rounded-2xl px-5 py-4 outline-none focus:border-emerald-500 font-bold"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-700 dark:text-gray-500 uppercase ml-2 tracking-widest">Amount (<?= htmlspecialchars($currencySymbol) ?>)</label>
              <input type="number" step="0.01" id="modifyExpAmount" class="w-full bg-white/50 focus:bg-white dark:bg-gray-800/40 border border-gray-700 rounded-2xl px-5 py-4 text-emerald-600 dark:text-emerald-400 font-black outline-none focus:border-emerald-500" />
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-black text-gray-700 dark:text-gray-500 uppercase ml-2 tracking-widest">Category</label>
              <select id="modifyExpCategory" class="w-full bg-white/50 focus:bg-white text-green-700 dark:bg-gray-800/40 dark:text-white border border-gray-700 rounded-2xl px-5 py-4 outline-none focus:border-emerald-500 font-bold cursor-pointer">
                <option value="Gasoline">Gasoline</option>
                <option value="COGS">OpEx</option>
                <option value="Utilities">Utilities</option>
                <option value="Labor">Wages</option>
                <option value="Rent">Rent</option>
                <option value="General">Miscellaneous</option>
              </select>
            </div>
          </div>

          <!-- Hidden Status (Preserves DB 'Paid' status) -->
          <input type="hidden" id="modifyExpStatus" />
        </div>

        <!-- Actions -->
        <div class="p-8 dark:bg-gray-950 flex gap-3">
          <button onclick="closeModifyModal()" class="flex-1 bg-red-600/10 border border-red-500/20 text-red-500 py-4 rounded-2xl font-black text-[10px] uppercase hover:bg-red-600 hover:text-white transition-all">Discard</button>
          <button id="updateSubmitBtn" onclick="saveModifyChanges()" class="flex-2 py-4 px-6 rounded-2xl bg-emerald-600 text-white font-black text-[10px] uppercase shadow-lg shadow-emerald-900/20 hover:bg-emerald-500 transition-all">Apply Changes</button>
        </div>
      </div>
    </div>

    <!-- CONFIRMATION MODAL -->
    <div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-sm w-full">
        <h3 id="confirmTitle" class="text-lg font-bold text-gray-900 dark:text-white mb-2">Confirm</h3>
        <p id="confirmMessage" class="text-gray-700 dark:text-gray-300 mb-4">Are you sure?</p>
        <div class="flex justify-end gap-2">
          <button id="confirmCancelBtn" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg">Cancel</button>
          <button id="confirmOkBtn" class="px-4 py-2 bg-red-500 text-white rounded-lg">Delete</button>
        </div>
      </div>
    </div>

    <!-- Production -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/navbar.js?v=<?= time(); ?>"></script>
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        fetchExpenses();
        loadStats();
      });
      /* ============================================================
      LALENZ ORDER SYSTEM v2.6.2 - FULL MODAL + NAVBAR LOGIC
      Includes: Expense CRUD, Pagination, Stats, Modals, Confirm
      ============================================================ */

      const catName = {
        COGS: "OpEx",
        Gasoline: "Gasoline",
        Utilities: "Utilities",
        Labor: "Wages",
        Rent: "Rent",
        General: "Misc",
      };

      const catMap = {
        Gasoline: "bg-emerald-500/10 text-emerald-400 border-emerald-500/20",
        COGS: "bg-orange-500/10 text-orange-400 border-orange-500/20",
        Utilities: "bg-indigo-500/10 text-indigo-400 border-indigo-500/20",
        Labor: "bg-pink-500/10 text-pink-400 border-pink-500/20",
        Rent: "bg-yellow-500/10 text-yellow-400 border-yellow-500/20",
        General: "bg-gray-500/10 text-gray-400 border-gray-500/20",
      };

      let currentExpense = null;
      let allExpenses = [];
      let expenseCurrentPage = 1;
      let expensePerPage = 10;
      let expenseSearchQuery = "";

      // ============================
      // CONFIRMATION MODAL LOGIC
      // ============================
      let confirmCallback = null;
      function showConfirm({ title, message, onConfirm }) {
        document.getElementById("confirmTitle").innerText = title;
        document.getElementById("confirmMessage").innerText = message;
        document.getElementById("confirmModal").classList.remove("hidden");
        confirmCallback = onConfirm;
      }
      function hideConfirm() {
        document.getElementById("confirmModal").classList.add("hidden");
        confirmCallback = null;
      }
      document.getElementById("confirmCancelBtn").addEventListener("click", hideConfirm);
      document.getElementById("confirmOkBtn").addEventListener("click", () => {
        if (confirmCallback) confirmCallback();
        hideConfirm();
      });
      window.addEventListener("keydown", (e) => {
        if (e.key === "Escape") hideConfirm();
      });

      // ============================
      // LIVE CLOCK
      // ============================
      setInterval(() => {
        const now = new Date();
        const clockEl = document.getElementById("liveClock");
        if (clockEl)
          clockEl.innerHTML = now
            .toLocaleString("en-US", {
              weekday: "long",
              year: "numeric",
              month: "long",
              day: "numeric",
              hour: "2-digit",
              minute: "2-digit",
              second: "2-digit",
              hour12: true,
            })
            .toUpperCase();
      }, 1000);

      // ============================
      // SAVE NEW EXPENSE
      // ============================
      async function saveExpense() {
        const title = document.getElementById("expTitle").value.trim();
        const details = document.getElementById("expDetails").value.trim();
        const amount = parseFloat(document.getElementById("expAmount").value);
        const category = document.getElementById("expCategory").value;
        if (!title || isNaN(amount) || amount <= 0) {
          toastr.error("Please provide a valid title and amount.");
          return;
        }
        try {
          const res = await fetch("<?php echo $systemFolder; ?>/Pages/Script/save_expense.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ title, details, amount, category }),
          });
          const data = await res.json();
          if (data.status === "success") {
            toastr.success("Expense recorded successfully!");
            document.getElementById("expTitle").value = "";
            document.getElementById("expDetails").value = "";
            document.getElementById("expAmount").value = "";
            closeExpenseModal();
            fetchExpenses();
            loadStats();
          } else {
            toastr.error(data.message || "Failed to save expense.");
          }
        } catch (e) {
          console.error("Error:", e);
          toastr.error("An error occurred while saving the expense.");
        }
      }

      // ============================
      // FETCH EXPENSES
      // ============================
      async function fetchExpenses() {
        const date = document.getElementById("filterDate")?.value || "";
        const category = document.getElementById("filterCategory")?.value || "";
        expenseSearchQuery = document.getElementById("filterSearch")?.value.trim().toLowerCase() || "";
        try {
          const url = new URL("<?php echo $systemFolder; ?>/Pages/Script/get_expenses.php", window.location.origin);
          if (date) url.searchParams.append("date", date);
          const res = await fetch(url);
          const expenses = await res.json();
          allExpenses = expenses.filter((e) => {
            const matchesCategory = !category || e.category === category;
            const matchesSearch = !expenseSearchQuery || [String(e.id), e.title, e.details || "", e.category].join(" ").toLowerCase().includes(expenseSearchQuery);
            return matchesCategory && matchesSearch;
          });
          expenseCurrentPage = 1;
          renderExpensesTable();
          renderExpensePagination();
          loadStats();
        } catch (e) {
          console.error("Fetch Error:", e);
        }
      }

      // ============================
      // RENDER TABLE + PAGINATION
      // ============================
      function renderExpensesTable() {
        const tbody = document.getElementById("expenseTableBody");
        const mobileList = document.getElementById("expenseTableMobile");
        if (!tbody || !mobileList) return;
        const start = (expenseCurrentPage - 1) * expensePerPage;
        const paginatedExpenses = allExpenses.slice(start, start + expensePerPage);
        let html = "";
        let mobileHtml = "";

        const sanitizeText = (text) =>
          String(text || "")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
        const truncate = (text, length = 80) => (text.length > length ? `${text.slice(0, length)}...` : text);

        paginatedExpenses.forEach((e) => {
          const safe = JSON.stringify(e).replace(/"/g, "&quot;");
          const formattedAmount = `<?= htmlspecialchars($currencySymbol) ?>${parseFloat(e.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
          const title = sanitizeText(e.title);
          const details = sanitizeText(e.details || "No details available");
          const categoryLabel = catName[e.category] || "Misc";
          const badgeClass = catMap[e.category] || catMap["General"];

          html += `
        <tr data-expense="${safe}" onclick="openExpenseView(this)" class="group hover:bg-emerald-500/[0.03] cursor-pointer border-b border-gray-800/50 transition-all">
            <td class="px-8 py-5 font-mono text-[11px] text-emerald-500/70">#${String(e.id).padStart(4, "0")}</td>
            <td class="px-8 py-5 text-sm text-gray-400">${sanitizeText(e.expense_date)}</td>
            <td class="px-8 py-5 text-sm font-bold text-gray-900 dark:text-white uppercase">${title}</td>
            <td class="px-8 py-5 text-sm font-black text-emerald-400">${formattedAmount}</td>
            <td class="px-8 py-5 text-center"><span class="px-3 py-1 rounded-full text-[9px] font-black border ${badgeClass}">${categoryLabel}</span></td>
        </tr>`;

          mobileHtml += `
        <div data-expense="${safe}" onclick="openExpenseView(this)" class="group cursor-pointer rounded-[2rem] border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/70 p-5 shadow-lg transition hover:-translate-y-1">
          <div class="flex items-start justify-between gap-3">
            <div>
              <p class="text-[10px] font-black uppercase tracking-[0.4em] text-emerald-500">#${String(e.id).padStart(4, "0")}</p>
              <p class="mt-3 text-base font-black text-gray-900 dark:text-white uppercase">${title}</p>
            </div>
            <span class="text-2xl font-black text-emerald-500">${formattedAmount}</span>
          </div>
          <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <span>${sanitizeText(e.expense_date)}</span>
            <span>·</span>
            <span>${sanitizeText(e.expense_time || "")}</span>
          </div>
          <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">${truncate(details, 90)}</p>
          <div class="mt-4 inline-flex items-center rounded-full border px-3 py-1 text-[10px] font-black uppercase tracking-[0.3em] ${badgeClass}">${categoryLabel}</div>
        </div>`;
        });

        if (html === "") {
          html = '<tr><td colspan="5" class="p-20 text-center font-bold text-gray-600 uppercase text-xs">No Records Found</td></tr>';
          mobileHtml = '<div class="p-12 text-center font-bold text-gray-600 dark:text-gray-300 uppercase text-xs">No Records Found</div>';
        }

        tbody.innerHTML = html;
        mobileList.innerHTML = mobileHtml;
      }

      function renderExpensePagination() {
        const paginationEl = document.getElementById("expensePagination");
        if (!paginationEl || allExpenses.length === 0) {
          if (paginationEl) paginationEl.innerHTML = "";
          return;
        }
        const totalPages = Math.ceil(allExpenses.length / expensePerPage);
        let html = `<div class="flex flex-wrap items-center justify-center gap-2">`;
        html += `<button onclick="expenseGoPage(${expenseCurrentPage - 1})" ${expenseCurrentPage === 1 ? "disabled" : ""} class="px-3 py-2 sm:px-4 rounded-lg font-bold transition bg-gray-200 text-gray-800 enabled:hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed">←</button>`;
        const maxVisible = window.innerWidth < 640 ? 3 : 5;
        let start = Math.max(1, expenseCurrentPage - Math.floor(maxVisible / 2));
        let end = start + maxVisible - 1;
        if (end > totalPages) {
          end = totalPages;
          start = Math.max(1, end - maxVisible + 1);
        }
        if (start > 1) {
          html += pageBtn(1);
          if (start > 2) html += `<span class="px-2 text-gray-500">...</span>`;
        }
        for (let i = start; i <= end; i++) html += pageBtn(i);
        if (end < totalPages) {
          if (end < totalPages - 1) html += `<span class="px-2 text-gray-500">...</span>`;
          html += pageBtn(totalPages);
        }
        html += `<button onclick="expenseGoPage(${expenseCurrentPage + 1})" ${expenseCurrentPage === totalPages ? "disabled" : ""} class="px-3 py-2 sm:px-4 rounded-lg font-bold transition bg-gray-200 text-gray-800 enabled:hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed">→</button>`;
        html += `</div>`;
        paginationEl.innerHTML = html;
        function pageBtn(i) {
          return `<button onclick="expenseGoPage(${i})" class="px-3 py-2 rounded-lg font-bold transition ${expenseCurrentPage === i ? "bg-emerald-500 text-white dark:bg-emerald-600" : "bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"}">${i}</button>`;
        }
      }
      function expenseGoPage(page) {
        const totalPages = Math.ceil(allExpenses.length / expensePerPage);
        if (page >= 1 && page <= totalPages) {
          expenseCurrentPage = page;
          renderExpensesTable();
          renderExpensePagination();
        }
      }

      // ============================
      // LOAD STATS
      // ============================
      async function loadStats() {
        const date = document.getElementById("filterDate")?.value || "";
        try {
          const res = await fetch(`<?php echo $systemFolder; ?>/Pages/Script/get_stats.php?date=${date}`);
          const data = await res.json();
          if (data.status === "success") {
            const cleanNumber = (v) => {
              if (v === null || v === undefined || v === "") return 0;
              const str = String(v).replace(/,/g, "").trim();
              const num = Number(str);
              return Number.isFinite(num) ? num : 0;
            };
            const formatMoney = (v) => `<?= htmlspecialchars($currencySymbol) ?>${v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            document.getElementById("stat-sales").innerText = formatMoney(cleanNumber(data.sales));
            document.getElementById("stat-expenses").innerText = formatMoney(cleanNumber(data.expenses));
            document.getElementById("stat-net").innerText = formatMoney(cleanNumber(data.net));
            const netCard = document.getElementById("net-card");
            const netIndicator = document.getElementById("net-indicator");
            const netLabel = document.getElementById("net-label");
            if (cleanNumber(data.net) > 0) {
              netCard.className = "group relative p-[1px] rounded-3xl bg-gradient-to-br from-green-500/40 via-transparent to-transparent hover:from-green-400/70 transition-all duration-500";
              netIndicator.className = "absolute top-0 left-0 h-1 w-0 bg-green-500 group-hover:w-full transition-all duration-500";
              netLabel.innerText = "Net Profit / Loss";
            } else if (cleanNumber(data.net) < 0) {
              netCard.className = "group relative p-[1px] rounded-3xl bg-gradient-to-br from-red-500/40 via-transparent to-transparent hover:from-red-400/70 transition-all duration-500";
              netIndicator.className = "absolute top-0 left-0 h-1 w-0 bg-red-500 group-hover:w-full transition-all duration-500";
              netLabel.innerText = "Net Profit / Loss";
            } else {
              netCard.className = "group relative p-[1px] rounded-3xl bg-gradient-to-br from-blue-500/40 via-transparent to-transparent hover:from-blue-400/70 transition-all duration-500";
              netIndicator.className = "absolute top-0 left-0 h-1 w-0 bg-blue-500 group-hover:w-full transition-all duration-500";
              netLabel.innerText = "Net Profit / Loss";
            }
          }
        } catch (e) {}
      }

      // ============================
      // MODAL SCROLL CONTROL
      // ============================
      function updateScrollLock() {
        const modals = [document.getElementById("expenseModal"), document.getElementById("viewExpenseModal"), document.getElementById("modifyExpenseModal")];
        const anyOpen = modals.some((m) => m && !m.classList.contains("hidden"));
        document.documentElement.classList.toggle("overflow-hidden", anyOpen);
      }

      // ============================
      // ADD MODAL
      // ============================
      function openAddExpenseModal() {
        document.getElementById("expenseModal").classList.remove("hidden");
        updateScrollLock();
      }
      function closeExpenseModal() {
        document.getElementById("expenseModal").classList.add("hidden");
        updateScrollLock();
      }

      // ============================
      // VIEW MODAL
      // ============================
      function openExpenseView(row) {
        const e = JSON.parse(row.getAttribute("data-expense"));
        currentExpense = e;
        document.getElementById("viewTitle").innerText = e.title || "UNTITLED";
        document.getElementById("viewDetails").innerText = e.details?.trim() ? e.details.toUpperCase() : "NO ADDITIONAL DETAILS PROVIDED FOR THIS ENTRY.";
        const el = document.getElementById("viewDetails");
        const isLong = e.details?.length >= 550;

        el.classList.toggle("h-[15rem]", isLong);
        el.classList.toggle("h-auto", !isLong);
        document.getElementById("viewAmount").innerText = `<?= htmlspecialchars($currencySymbol) ?>${parseFloat(e.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
        document.getElementById("viewCategory").innerText = catName[e.category] || "MISC";
        document.getElementById("viewDate").innerText = `${e.expense_date} | ${e.expense_time}`;
        document.getElementById("deleteBtn").onclick = () => deleteExpense(e.id);
        document.getElementById("viewExpenseModal").classList.remove("hidden");
        updateScrollLock();
      }
      function closeViewExpense() {
        document.getElementById("viewExpenseModal").classList.add("hidden");
        updateScrollLock();
      }

      // ============================
      // MODIFY MODAL
      // ============================
      function openModifyFromView() {
        document.getElementById("viewExpenseModal").classList.add("hidden");
        const modifyModal = document.getElementById("modifyExpenseModal");
        const content = document.getElementById("modifyModalContent");
        document.getElementById("modifyExpId").value = currentExpense.id;
        document.getElementById("modifyIDDisplay").innerText = `Expense ID: #${String(currentExpense.id).padStart(3, "0")}`;
        document.getElementById("modifyDateDisplay").innerText = `Expense Date: ${currentExpense.expense_date} ${currentExpense.expense_time || ""}`;
        document.getElementById("modifyExpTitle").value = currentExpense.title;
        document.getElementById("modifyExpDetails").value = currentExpense.details || "";
        document.getElementById("modifyExpAmount").value = currentExpense.amount;
        document.getElementById("modifyExpCategory").value = currentExpense.category;
        document.getElementById("modifyExpStatus").value = currentExpense.status || "Paid";
        modifyModal.classList.remove("hidden");
        content.classList.remove("scale-95", "opacity-0");
        content.classList.add("scale-100", "opacity-100");
        updateScrollLock();
      }
      function closeModifyModal() {
        const modifyModal = document.getElementById("modifyExpenseModal");
        const content = document.getElementById("modifyModalContent");
        content.classList.add("scale-95", "opacity-0");
        modifyModal.classList.add("hidden");
        updateScrollLock();
      }

      // ============================
      // SAVE / UPDATE
      // ============================
      async function saveModifyChanges() {
        const btn = document.getElementById("updateSubmitBtn");
        const payload = {
          id: document.getElementById("modifyExpId").value,
          title: document.getElementById("modifyExpTitle").value,
          details: document.getElementById("modifyExpDetails").value,
          amount: document.getElementById("modifyExpAmount").value,
          category: document.getElementById("modifyExpCategory").value,
          status: document.getElementById("modifyExpStatus").value || "Paid",
        };
        btn.disabled = true;
        const originalText = btn.innerText;
        btn.innerHTML = '<span class="animate-pulse italic uppercase">Syncing...</span>';
        try {
          const res = await fetch("<?php echo $systemFolder; ?>/Pages/Script/update_expense.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload),
          });
          const data = await res.json();
          if (data.status === "success") {
            toastr.success(data.message || `RECORD #${payload.id} UPDATED`);
            closeModifyModal();
            fetchExpenses();
            loadStats();
          } else toastr.error(data.message || "UPDATE FAILED");
        } catch (e) {
          console.error(e);
          toastr.error("SERVER ERROR");
        } finally {
          btn.disabled = false;
          btn.innerText = originalText;
        }
      }

      // ============================
      // DELETE (USING MODAL)
      // ============================
      async function deleteExpense(id) {
        showConfirm({
          title: "Delete Expense",
          message: `Are you sure you want to delete #${id}?`,
          onConfirm: async () => {
            try {
              const res = await fetch(`<?php echo $systemFolder; ?>/Pages/Script/delete_expense.php?id=${id}`);
              const data = await res.json();
              if (data.status === "success") {
                closeViewExpense();
                closeModifyModal();
                fetchExpenses();
                loadStats();
                toastr.info("DELETED");
              } else toastr.error(data.message || "DELETE FAILED");
            } catch (e) {
              toastr.error("DELETE FAILED");
            }
          },
        });
      }

      // ============================
      // ESC SUPPORT
      // ============================
      window.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
          closeViewExpense();
          closeModifyModal();
          closeExpenseModal();
          hideConfirm();
        }
      });
    </script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
  </body>
</html>
