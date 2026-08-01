<?php
// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

// ----------------------------
// Includes
// ----------------------------

require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/init.php';
// ----------------------------
// Item count for quick access
// ----------------------------
$stmt = $pdo->query("SELECT * FROM items"); $itemsCount = $stmt->fetchAll(PDO::FETCH_ASSOC); ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth select-none">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($systemName); ?> - Dashboard</title>
    <script>
      (function () {
        const theme = localStorage.getItem("theme");
        const html = document.documentElement;

        if (theme === "dark") {
          html.classList.add("dark");
        } else {
          html.classList.remove("dark"); // default = light
        }
      })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <link rel="stylesheet" href="./../../src/output.css"/> -->
    <script>
      tailwind.config = {
        darkMode: "class",
      };
    </script>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js" type="text/javascript"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <link rel="icon" type="image/png" href="<?= $logo; ?>?v=<?= time(); ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
      /* Chrome, Safari, Edge */
      input[type="number"]::-webkit-outer-spin-button,
      input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }

      /* Firefox */
      input[type="number"] {
        -moz-appearance: textfield;
      }
    </style>
  </head>
  <body class="bg-gray-100 text-gray-800 dark:bg-gray-950 dark:text-gray-100 font-sans w-auto">
    <!-- Navigation Bar (White Primary) -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/navbar.php'; ?>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-6 py-12">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-black text-gray-900 dark:text-white">Admin Dashboard</h1>
        <div class="Date text-lg font-mono font-medium text-gray-400 italic">--:--:--</div>
      </div>

      <!-- Financial Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16">
        <!-- CARD TEMPLATE -->
        <!-- Total Orders -->
        <div class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-emerald-500/40 via-transparent to-transparent hover:from-emerald-400/70 transition-all duration-500">
          <div class="relative backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 overflow-hidden">
            <!-- Glow Effect -->
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br from-emerald-500/10 via-transparent to-transparent"></div>

            <!-- Animated Top Bar -->
            <div class="absolute top-0 left-0 h-1 w-0 bg-emerald-500 group-hover:w-full transition-all duration-500"></div>

            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em] mb-3">Total Orders</p>

            <div id="stat-orders" class="text-4xl font-black text-gray-900 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">0</div>
          </div>
        </div>

        <!-- Total Sales -->
        <div class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-emerald-500/40 via-transparent to-transparent hover:from-emerald-400/70 transition-all duration-500">
          <div class="relative backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 overflow-hidden">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br from-emerald-500/10 via-transparent to-transparent"></div>
            <div class="absolute top-0 left-0 h-1 w-0 bg-emerald-500 group-hover:w-full transition-all duration-500"></div>

            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em] mb-3">Total Sales</p>

            <div id="stat-sales" class="text-4xl font-black text-gray-900 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300"><?php echo $currencySymbol; ?>0.00</div>
          </div>
        </div>

        <!-- Total Expenses -->
        <div onclick="window.location.href = '<?php echo $systemFolder; ?>/Pages/admin/dashboard/expenses/log.php'" class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-orange-500/40 via-transparent to-transparent hover:from-orange-400/70 hover:cursor-pointer transition-all duration-500">
          <div class="relative backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl hover:shadow-orange-500/10 transition-all duration-500 overflow-hidden">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br from-orange-500/10 via-transparent to-transparent"></div>
            <div class="absolute top-0 left-0 h-1 w-0 bg-orange-500 group-hover:w-full transition-all duration-500"></div>

            <div class="flex items-center justify-between">
              <p class="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] mb-1">Total Expenses</p>

              <a href="<?php echo $systemFolder; ?>/Pages/admin/dashboard/expenses/log.php" class="text-[10px] text-orange-400 hover:text-orange-600 underline transition"> View All Expenses <i class="fas fa-arrow-right group-hover:text-orange-600 transition group-hover:translate-x-1 duration-300"></i> </a>
            </div>

            <div id="stat-expenses" class="mt-2 text-4xl font-black text-gray-900 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300"><?php echo $currencySymbol; ?>0.00</div>
          </div>
        </div>

        <!-- Net Profit / Loss -->
        <div id="net-card" class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-blue-500/40 via-transparent to-transparent hover:from-blue-400/70 transition-all duration-500">
          <div class="relative backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 overflow-hidden">
            <div id="net-indicator" class="absolute top-0 left-0 h-1 w-0 bg-blue-500 group-hover:w-full transition-all duration-500"></div>

            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-3">Net Profit / Loss</p>

            <div id="stat-net" class="text-4xl font-black text-gray-900 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300"><?php echo $currencySymbol; ?>0.00</div>
          </div>
        </div>
      </div>

      <!-- Bar Chart Section -->
      <!-- Sales Analytics Panel -->
      <div class="group relative mb-16 p-[1px] rounded-3xl bg-gradient-to-br from-indigo-500/40 via-transparent to-transparent hover:from-indigo-400/70 transition-all duration-500">
        <div class="relative backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 overflow-hidden">
          <!-- Glow Overlay -->
          <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br from-indigo-500/10 via-transparent to-transparent"></div>

          <!-- Animated Top Bar -->
          <div class="absolute top-0 left-0 h-1 w-0 bg-indigo-500 group-hover:w-full transition-all duration-500"></div>

          <!-- Header -->
          <div class="flex items-center justify-between mb-6">
            <h2 class="lg:text-2xl font-black text-gray-900 dark:text-white tracking-tight"><i class="fa-solid fa-chart-line text-indigo-500 mr-2"></i> Sales Analytics</h2>

            <!-- Range Selector -->
            <div class="relative">
              <select id="salesRange" onchange="loadDailyStats()" class="appearance-none cursor-pointer lg:text-[10px] text-[10px] font-black px-4 py-2 pr-8 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 tracking-widest uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                <option value="24h">Last 24 Hours</option>
                <option value="7d" selected>Last 7 Days</option>
                <option value="15d">Last 15 Days</option>
                <option value="31d">Last 30 Days</option>
              </select>

              <!-- Custom Arrow -->
              <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-indigo-400">
                <i class="fa-solid fa-chevron-down text-[10px]"></i>
              </div>
            </div>
          </div>

          <!-- Subtle Divider -->
          <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-300/40 dark:via-gray-600/40 to-transparent mb-6"></div>

          <!-- Chart Container -->
          <div class="relative h-[340px]">
            <!-- Optional background glow -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-transparent to-transparent pointer-events-none"></div>

            <canvas id="financialChart" class="relative z-10"></canvas>
          </div>
        </div>
      </div>

      <!-- Call to Action -->
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 mb-8">
        <!-- NEW ORDER -->
        <a href="./../admin/order/new.php" class="group relative p-[2px] rounded-2xl bg-gradient-to-br from-emerald-500/40 to-transparent hover:from-emerald-400/70 transition">
          <div class="flex items-center justify-center gap-2 sm:gap-3 backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 border border-white/30 dark:border-gray-700/50 rounded-2xl px-3 py-3 sm:px-5 sm:py-4 lg:px-6 lg:py-5 min-h-[70px] sm:min-h-[80px] shadow-xl hover:shadow-emerald-500/20 transition-all group-hover:-translate-y-1">
            <i class="fas fa-plus text-base sm:text-lg text-emerald-500 group-hover:scale-110 transition"></i>
            <span class="font-bold text-[11px] sm:text-xs md:text-sm tracking-wide whitespace-nowrap"> NEW ORDER </span>
          </div>
        </a>

        <!-- ITEM SUMMARY -->
        <a href="./../admin/dashboard/item-summary.php" class="group relative p-[2px] rounded-2xl bg-gradient-to-br from-blue-500/40 to-transparent hover:from-blue-400/70 transition">
          <div class="flex items-center justify-center gap-2 sm:gap-3 backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 border border-white/30 dark:border-gray-700/50 rounded-2xl px-3 py-3 sm:px-5 sm:py-4 lg:px-6 lg:py-5 min-h-[70px] sm:min-h-[80px] shadow-xl hover:shadow-blue-500/20 transition-all group-hover:-translate-y-1">
            <i class="fas fa-box text-base sm:text-lg text-blue-500 group-hover:scale-110 transition"></i>
            <span class="font-bold text-[11px] sm:text-xs md:text-sm tracking-wide whitespace-nowrap"> ITEM SUMMARY </span>
          </div>
        </a>

        <!-- EXPENSES -->
        <a href="./../admin/dashboard/expenses/log.php" class="group relative p-[2px] rounded-2xl bg-gradient-to-br from-orange-500/40 to-transparent hover:from-orange-400/70 transition">
          <div class="flex items-center justify-center gap-2 sm:gap-3 backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 border border-white/30 dark:border-gray-700/50 rounded-2xl px-3 py-3 sm:px-5 sm:py-4 lg:px-6 lg:py-5 min-h-[70px] sm:min-h-[80px] shadow-xl hover:shadow-orange-500/20 transition-all group-hover:-translate-y-1">
            <i class="fas fa-receipt text-base sm:text-lg text-orange-500 group-hover:scale-110 transition"></i>
            <span class="font-bold text-[11px] sm:text-xs md:text-sm tracking-wide whitespace-nowrap"> EXPENSES </span>
          </div>
        </a>

        <!-- SALES REPORT -->
        <a href="./../admin/dashboard/sales-report.php" class="group relative p-[2px] rounded-2xl bg-gradient-to-br from-indigo-500/40 to-transparent hover:from-indigo-400/70 transition">
          <div class="flex items-center justify-center gap-2 sm:gap-3 backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 border border-white/30 dark:border-gray-700/50 rounded-2xl px-3 py-3 sm:px-5 sm:py-4 lg:px-6 lg:py-5 min-h-[70px] sm:min-h-[80px] shadow-xl hover:shadow-indigo-500/20 transition-all group-hover:-translate-y-1">
            <i class="fas fa-chart-line text-base sm:text-lg text-indigo-500 group-hover:scale-110 transition"></i>
            <span class="font-bold text-[11px] sm:text-xs md:text-sm tracking-wide whitespace-nowrap"> SALES REPORT </span>
          </div>
        </a>

        <!-- ITEMS -->
        <a href="./../admin/dashboard/items.php" class="group relative p-[2px] rounded-2xl bg-gradient-to-br from-purple-500/40 to-transparent hover:from-purple-400/70 transition">
          <div class="flex items-center justify-center gap-2 sm:gap-3 backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 border border-white/30 dark:border-gray-700/50 rounded-2xl px-3 py-3 sm:px-5 sm:py-4 lg:px-6 lg:py-5 min-h-[70px] sm:min-h-[80px] shadow-xl hover:shadow-purple-500/20 transition-all group-hover:-translate-y-1">
            <i class="fas fa-list text-base sm:text-lg text-purple-500 group-hover:scale-110 transition"></i>
            <span class="font-bold text-[11px] sm:text-xs md:text-sm tracking-wide whitespace-nowrap"> ITEMS (<?php echo count($itemsCount); ?>) </span>
          </div>
        </a>
      </div>

      <!-- Filter Section (hidden by default; toggle with filter button) -->
      <div id="filtersPanel" class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-emerald-500/30 to-transparent mb-10">
        <div class="backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 border border-white/30 dark:border-gray-700/50 rounded-3xl p-6 shadow-2xl">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-black tracking-tight"><i class="fa-solid fa-lg fa-sliders"></i> Filters & Controls</h3>
            <!-- <button onclick="applyAllFilters()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-2 rounded-xl font-bold transition shadow-lg">Apply</button> -->
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <!-- Start Date -->
            <div class="flex flex-col hidden">
              <label for="chartStartDate" class="text-[10px] sm:text-xs font-bold uppercase text-gray-500 dark:text-gray-300 mb-1">Start Chart Date</label>
              <input type="date" id="chartStartDate" onchange="loadDailyStats()" class="px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-none focus:ring-2 focus:ring-emerald-500" />
            </div>

            <!-- End Date -->
            <div class="flex flex-col hidden">
              <label for="chartEndDate" class="text-[10px] sm:text-xs font-bold uppercase text-gray-500 dark:text-gray-300 mb-1">End Chart Date</label>
              <input type="date" id="chartEndDate" onchange="loadDailyStats()" class="px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-none focus:ring-2 focus:ring-emerald-500" />
            </div>

            <!-- Payment Method -->
            <div class="flex flex-col">
              <label for="filterByPaymentMethod" class="text-[10px] sm:text-xs font-bold uppercase text-gray-500 dark:text-gray-300 mb-1">Payment Method</label>
              <select id="filterByPaymentMethod" onchange="applyAllFilters()" class="px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-none">
                <option value="">Select Payment</option>
                <option>Cash</option>
                <option>GCash</option>
                <option>PayMaya</option>
                <option>Others</option>
              </select>
            </div>

            <!-- Filter Date -->
            <div class="flex flex-col">
              <label for="filterDate" class="text-[10px] sm:text-xs font-bold uppercase text-gray-500 dark:text-gray-300 mb-1">Filter Date</label>
              <input type="date" id="filterDate" onchange="applyAllFilters()" class="px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 accent-gray-500 border-none" />
            </div>

            <!-- Status -->
            <div class="flex flex-col">
              <label for="filterStatus" class="text-[10px] sm:text-xs font-bold uppercase text-gray-500 dark:text-gray-300 mb-1">Status</label>
              <select id="filterStatus" onchange="applyAllFilters()" class="px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-none">
                <option value="">Select Status</option>
                <option value="Scheduled">Scheduled</option>
                <option value="Pending">Pending</option>
                <option value="Out For Delivery">Out For Delivery</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
                <option value="Preparing">Preparing</option>
                <option value="Ready">Ready</option>
                <option value="Serving">Serving</option>
              </select>
            </div>

            <!-- Sort By -->
            <div class="flex flex-col">
              <label for="sortBy" class="text-[10px] sm:text-xs font-bold uppercase text-gray-500 dark:text-gray-300 mb-1">Sort By</label>
              <select id="sortBy" onchange="applyAllFilters()" class="px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 border-none">
                <option value="datetime-desc">Newest (by date & time)</option>
                <option value="datetime-asc">Oldest (by date & time)</option>
                <option value="id-desc">Order ID (High-Low)</option>
                <option value="id-asc">Order ID (Low-High)</option>
                <option value="items-desc">Items Count (High-Low)</option>
                <option value="items-asc">Items Count (Low-High)</option>
                <option value="total-desc">Total Amount (High-Low)</option>
                <option value="total-asc">Total Amount (Low-High)</option>
                <option value="status-asc">Status (A-Z)</option>
                <option value="status-desc">Status (Z-A)</option>
                <option value="name-asc">Name (A-Z)</option>
                <option value="name-desc">Name (Z-A)</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="relative hidden md:block lg:block overflow-hidden rounded-3xl border border-gray-200/50 dark:border-gray-700/50 bg-white/80 dark:bg-gray-900/60 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.08)]">
        <!-- Header -->
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-200/50 dark:border-gray-700/50">
          <!-- Left Section -->
          <div class="flex w-full justify-between">
            <div>
              <h2 class="text-lg font-bold text-gray-800 dark:text-white">Orders</h2>
              <p class="text-xs text-gray-500 dark:text-gray-400 lg:w-[100%] w-[70%]">Manage and monitor customer orders</p>
            </div>
          </div>

          <!-- Right Section -->
          <div class="flex items-center gap-2">
            <input id="searchCustomer" oninput="loadRecentOrders(1)" placeholder="Search Customer..." class="px-3 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-emerald-500 text-sm w-64" />

            <button id="filterToggleBtn" onclick="toggleFilters()" class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300 text-xs font-semibold flex items-center gap-2 lg:hidden md:hidden" aria-expanded="false" aria-controls="filtersPanel">
              <i class="fa-solid fa-filter"></i>
              <span class="hidden sm:inline">Filters</span>
            </button>
          </div>
        </div>

        <!-- Table Wrapper -->
        <div class="overflow-x-auto max-h-[650px]">
          <table id="order_summary_table" class="w-full">
            <!-- Table Head -->
            <thead class="sticky top-0 z-20 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-b border-gray-200/50 dark:border-gray-700/50">
              <tr class="text-[11px] uppercase tracking-[0.15em] font-bold text-gray-500 dark:text-gray-400">
                <th class="px-6 py-4 text-left">Order</th>

                <th class="px-6 py-4 text-left">Summary</th>

                <th class="px-6 py-4 text-center">Status</th>

                <th class="px-6 py-4 text-right">Total</th>
              </tr>
            </thead>

            <!-- Body -->
            <tbody id="orders-tbody" class="divide-y divide-gray-100 dark:divide-gray-800"></tbody>
          </table>
        </div>
      </div>
      <div id="mobileOrdersHeader" class="md:hidden lg:hidden flex items-center justify-between mb-4 px-2">
        <div class="flex flex-col">
          <h2 class="text-lg font-bold text-gray-800 dark:text-white">Orders</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 lg:w-[100%] w-[70%]">Manage and monitor customer orders</p>
          <div class="mt-2 flex items-center gap-2">
            <input id="searchCustomerMobile" oninput="loadRecentOrders(1)" placeholder="Search Customer..." class="px-3 py-2 rounded-xl bg-white dark:bg-gray-800 border-none focus:ring-2 focus:ring-emerald-500 text-sm w-40" />
            <button id="filterToggleBtnMobile" onclick="toggleFilters()" class="px-3 py-3 rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300 text-xs font-semibold flex items-center gap-2" aria-expanded="false" aria-controls="filtersPanel">
              <i class="fa-solid fa-filter"></i>
              <span class="hidden sm:inline">Filters</span>
            </button>
          </div>
        </div>
      </div>
      <div id="orders-mobile-list" class="md:hidden p-0 space-y-4 w-full"></div>

      <!-- Pagination Controls -->
      <div class="mt-4 rounded-2xl border border-gray-200/60 dark:border-gray-700/60 bg-white/80 dark:bg-gray-900/40 backdrop-blur-xl shadow-lg p-3 sm:p-4">
        <!-- Buttons -->
        <div id="paginationButtons" class="flex flex-wrap justify-center sm:justify-between lg:justify-center gap-2">
          <button id="firstPageBtn" class="px-3 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 enabled:hover:bg-gray-200 dark:enabled:hover:bg-gray-700 transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed"><< <span class="hidden sm:inline">First</span></button>

          <button id="prevBtn" class="px-3 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 enabled:hover:bg-gray-200 dark:enabled:hover:bg-gray-700 transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed">< <span class="hidden sm:inline">Previous</span></button>

          <!-- page numbers injected here -->

          <button id="nextBtn" class="px-3 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 enabled:hover:bg-gray-200 dark:enabled:hover:bg-gray-700 transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed"><span class="hidden sm:inline">Next</span> ></button>

          <button id="lastPageBtn" class="px-3 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 enabled:hover:bg-gray-200 dark:enabled:hover:bg-gray-700 transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed"><span class="hidden sm:inline">Last</span> >></button>
        </div>

        <!-- Divider -->
        <div class="my-3 h-px bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-700 to-transparent"></div>

        <!-- Info -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs sm:text-sm">
          <div class="text-gray-500 dark:text-gray-400 text-center sm:text-left">
            Displaying
            <span id="startItem" class="font-bold text-gray-800 dark:text-white">0</span>
            -
            <span id="endItem" class="font-bold text-gray-800 dark:text-white">0</span>
            of
            <span id="totalItems" class="font-bold text-gray-800 dark:text-white">0</span>
            order(s)
          </div>

          <div class="text-gray-500 dark:text-gray-400 text-center sm:text-right">
            Page
            <span id="currentPageNum" class="font-bold text-indigo-500">0</span>
            of
            <span id="totalPagesNum" class="font-bold text-indigo-500">0</span>
          </div>
        </div>
      </div>

      <?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/footer.php'; ?>
    </main>

    <!-- Expense Modal -->
    <div id="expenseModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white w-full max-w-md rounded-3xl p-8 shadow-2xl">
        <h2 class="text-2xl font-black text-gray-900 mb-6">Record Expense</h2>
        <div class="space-y-4">
          <input type="text" id="expDesc" placeholder="Description (e.g. Gas, Ingredients)" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500" />
          <input type="number" id="expAmount" placeholder="Amount (<?php echo $currencySymbol; ?>)" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500" />
          <div class="flex gap-3">
            <button onclick="toggleExpenseModal()" class="flex-1 py-3 font-bold text-gray-400">Cancel</button>
            <button onclick="saveExpense()" class="flex-1 bg-orange-500 text-white py-3 rounded-xl font-black hover:bg-orange-600">Save Expense</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Customer Details Modal -->
    <div id="customerModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-0 sm:p-4 bg-black/70 backdrop-blur-md">
      <!-- Modal Container -->
      <div class="w-full h-[100dvh] sm:h-auto sm:max-h-[90vh] max-w-md sm:max-w-xl lg:max-w-5xl rounded-none sm:rounded-2xl overflow-hidden shadow-2xl bg-white dark:bg-gray-900 animate-[fadeIn_.2s_ease,scaleIn_.2s_ease] flex flex-col">
        <!-- HEADER -->
        <div class="sticky top-0 z-20 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white px-4 py-3 sm:px-5 sm:py-4">
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
              <h2 id="modalCustName" class="font-black text-base sm:text-lg truncate leading-tight">Customer Name</h2>

              <div class="inline-flex lg:flex-row flex-col justify-start items-start lg:gap-2 text-[10px] sm:text-xs text-white/80 mt-1 truncate">
                <span id="modalCustID" class="truncate">Order ID: #0001</span>
                <span class="hidden lg:block">⋅</span>
                <span id="orderDate" class="truncate">0000-00-00 00:00:00</span>
              </div>
            </div>

            <button onclick="closeModal()" class="flex-shrink-0 h-8 w-8 sm:h-9 sm:w-9 rounded-xl bg-white/10 hover:bg-white/20 transition flex items-center justify-center">
              <i class="fas fa-times text-white"></i>
            </button>
          </div>
        </div>

        <!-- BODY -->
        <div class="flex-1 overflow-y-auto px-3 py-4 sm:px-6 sm:py-6 space-y-4 sm:space-y-6">
          <!-- CONTACT -->
          <div class="grid grid-cols-2 gap-2 sm:gap-3">
            <div class="rounded-xl bg-gray-100/80 dark:bg-gray-800/70 p-3 sm:p-4 border border-gray-200/50 dark:border-gray-700/50">
              <div class="text-[10px] uppercase font-black text-gray-400 tracking-wider">Phone</div>

              <div id="modalCustPhone" class="text-sm sm:text-base font-bold mt-1">--</div>
            </div>

            <div class="rounded-xl bg-gray-100/80 dark:bg-gray-800/70 p-3 sm:p-4 border border-gray-200/50 dark:border-gray-700/50">
              <div class="text-[10px] uppercase font-black text-gray-400 tracking-wider">Address</div>

              <div id="modalCustAddr" class="text-sm sm:text-base font-bold mt-1 truncate">--</div>

              <button id="copyAddressBtn" onclick="copyToClipboard(document.getElementById('modalCustAddr').textContent || '-', 'Address copied!', this)" class="mt-2 px-3 py-1 rounded-full disabled:cursor-not-allowed bg-gray-200 dark:bg-gray-600 enabled:hover:bg-gray-300 dark:enabled:hover:bg-gray-500 text-gray-700 dark:text-gray-300 text-xs font-semibold transition flex items-center gap-1">
                <i class="fa-solid fa-copy text-xs"></i>
                Copy Address
              </button>
            </div>
          </div>

          <!-- HISTORY -->
          <div>
            <h3 class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Order History</h3>

            <div id="modalOrderHistory" class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
              <!-- items -->
            </div>
          </div>

          <!-- TOTAL BREAKDOWN -->
          <div class="p-3 sm:p-5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 space-y-2">
            <div id="modalSubtotalRow" class="flex justify-between text-sm">
              <span class="text-gray-500 dark:text-gray-300">Subtotal</span>
              <span id="modalSubtotal" class="font-bold"><?php echo $currencySymbol; ?>0.00</span>
            </div>

            <div id="modalDiscountRow" class="flex justify-between text-sm">
              <span class="text-gray-500 dark:text-gray-300">Discount</span>
              <span id="modalDiscount" class="font-bold text-red-500">- <?php echo $currencySymbol; ?>0.00</span>
            </div>

            <div id="modalDeliveryRow" class="flex justify-between text-sm">
              <span class="text-gray-500 dark:text-gray-300">Delivery Fee</span>
              <span id="modalDelivery" class="font-bold text-gray-700 dark:text-gray-300"><?php echo $currencySymbol; ?>0.00</span>
            </div>

            <div id="modalAdvanceRow" class="flex justify-between text-sm">
              <span class="text-gray-500 dark:text-gray-300">Advance Payment</span>
              <span id="modalAdvance" class="font-bold text-blue-500">- <?php echo $currencySymbol; ?>0.00</span>
            </div>

            <hr class="border-gray-300 dark:border-gray-700" />

            <div class="flex justify-between items-center">
              <span class="text-sm font-black text-emerald-500"> TOTAL </span>

              <span id="modalTotal" class="text-lg sm:text-xl font-black text-emerald-500"> <?php echo $currencySymbol; ?>0.00 </span>
            </div>
          </div>

          <!-- NOTE + PAYMENT -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <div class="p-3 sm:p-4 rounded-xl bg-gray-100/80 dark:bg-gray-800/70 border border-gray-200/50 dark:border-gray-700/50">
              <p class="text-[10px] sm:text-xs font-black uppercase text-gray-400 mb-1">Note</p>

              <p id="modalNote" class="text-sm sm:text-base">--</p>
            </div>

            <div class="p-3 sm:p-4 rounded-xl bg-gray-100/80 dark:bg-gray-800/70 border border-gray-200/50 dark:border-gray-700/50">
              <p class="text-[10px] sm:text-xs font-black uppercase text-gray-400 mb-1">Payment</p>

              <p id="modalPayment" class="font-bold text-sm sm:text-base text-blue-500">--</p>
            </div>
          </div>
        </div>

        <!-- FOOTER -->
        <div class="sticky bottom-0 bg-white/95 dark:bg-gray-900/95 border-t dark:border-gray-700 px-3 py-3 sm:px-5 sm:py-4">
          <!-- MOBILE LAYOUT -->
          <div class="flex flex-col gap-2 lg:hidden">
            <!-- ROW 1: DELETE -->
            <button onclick="orderSlipActions('delete', document.getElementById('modalCustID').dataset.orderId)" class="w-full px-3 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold flex items-center justify-center gap-2">
              <i class="fas fa-trash-alt"></i>
              Delete
            </button>

            <!-- ROW 2: EDIT + PRINT -->
            <div class="grid grid-cols-2 gap-2">
              <button onclick="openEditOrderModal({ orderId: document.getElementById('modalCustID').dataset.orderId })" class="w-full px-3 py-2 rounded-lg bg-yellow-500 text-white text-xs font-bold">Edit</button>

              <button onclick="printReceipt(document.getElementById('modalCustID').dataset.orderId)" class="w-full px-3 py-2 rounded-lg bg-blue-600 text-white text-xs font-bold">Print</button>
            </div>

            <!-- ROW 3: DOWNLOAD + STATUS -->
            <div class="grid grid-cols-2 gap-2">
              <button onclick="downloadReceipt(document.getElementById('customerModal').dataset.orderId, '<?php echo htmlspecialchars($currencySymbol); ?>')" class="w-full px-3 py-2 rounded-lg bg-indigo-600 text-white text-xs font-bold">Download</button>

              <select onchange="updateOrderStatus(document.getElementById('modalCustID').dataset.orderId, this.value)" class="statusDropdown w-full px-3 py-2 rounded-lg text-xs font-bold bg-emerald-600 text-white dark:bg-emerald-500">
                <option disabled selected>Update</option>
                <option value="pending">Pending</option>
                <option value="preparing">Preparing</option>
                <option value="ready">Ready</option>
                <option value="serving">Serving</option>
                <option value="scheduled">Scheduled</option>
                <option value="out">Out For Delivery</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>

          <!-- DESKTOP LAYOUT -->
          <div class="hidden lg:flex items-center justify-between gap-3">
            <!-- LEFT: DELETE -->
            <button onclick="orderSlipActions('delete', document.getElementById('modalCustID').dataset.orderId)" class="px-3 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-bold flex items-center gap-2">
              <i class="fas fa-trash-alt"></i>
              Delete
            </button>

            <!-- RIGHT ACTIONS -->
            <div class="flex items-center gap-2 ml-auto">
              <button onclick="openEditOrderModal({ orderId: document.getElementById('modalCustID').dataset.orderId })" class="px-3 py-2 rounded-lg bg-yellow-500 text-white text-sm font-bold">Edit</button>

              <button onclick="printReceipt(document.getElementById('modalCustID').dataset.orderId)" class="px-3 py-2 rounded-lg bg-blue-600 text-white text-sm font-bold">Print</button>

              <button onclick="downloadReceipt(document.getElementById('customerModal').dataset.id, '<?php echo htmlspecialchars($currencySymbol); ?>')" class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm font-bold">Download</button>

              <select onchange="updateOrderStatus(document.getElementById('modalCustID').dataset.orderId, this.value)" class="statusDropdown px-3 py-2 rounded-lg text-sm font-bold bg-emerald-600 text-white dark:bg-emerald-500">
                <option value="" selected disabled>Update Status</option>
                <option value="pending">Pending</option>
                <option value="preparing">Preparing</option>
                <option value="ready">Ready</option>
                <option value="scheduled">Scheduled</option>
                <option value="out_for_delivery">Out For Delivery</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Order Modal -->
    <div id="editOrderModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/70 backdrop-blur-lg">
      <div class="w-full max-w-3xl rounded-[2.5rem] overflow-hidden shadow-2xl bg-white/[90%] dark:bg-gray-900/70 backdrop-blur-xl border border-white/30 dark:border-gray-700/50 animate-[fadeIn_.2s_ease,scaleIn_.2s_ease]">
        <!-- HEADER -->
        <div class="sticky top-0 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white p-6 flex justify-between items-center">
          <h2 class="text-2xl font-black tracking-tight">Edit Order</h2>
          <button onclick="closeEditOrderModal()" class="text-white/70 hover:text-white text-2xl transition hover:rotate-90"><i class="fas fa-times"></i></button>
        </div>

        <!-- BODY -->
        <div class="p-6 space-y-6 max-h-[65vh] overflow-y-auto">
          <input type="hidden" id="editOrderId" />

          <!-- CUSTOMER -->
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase">Customer</label>
              <input id="editCustomerName" class="w-full mt-1 rounded-xl px-4 py-2 bg-gray-100/70 dark:bg-gray-800/70 border border-transparent focus:border-emerald-500 outline-none" />
            </div>

            <div>
              <label class="text-xs font-bold text-gray-400 uppercase">Address</label>
              <input id="editCustomerAddress" class="w-full mt-1 rounded-xl px-4 py-2 bg-gray-100/70 dark:bg-gray-800/70 border border-transparent focus:border-emerald-500 outline-none" />
            </div>
          </div>

          <!-- NOTE -->
          <div>
            <label class="text-xs font-bold text-gray-400 uppercase">Order Note</label>
            <textarea id="editOrderNote" class="w-full mt-1 rounded-xl px-4 py-2 bg-gray-100/70 dark:bg-gray-800/70 border border-transparent focus:border-emerald-500 outline-none"></textarea>
          </div>

          <!-- PAYMENT -->
          <div>
            <label class="text-xs font-bold text-gray-400 uppercase">Payment</label>

            <select id="editPaymentMethod" onchange="handleEditPaymentChange()" class="w-full mt-1 rounded-xl px-4 py-2 bg-gray-100/70 dark:bg-gray-800 border border-transparent focus:border-emerald-500 outline-none">
              <option disabled value="">Select Payment Method</option>
              <option>Cash</option>
              <option>GCash</option>
              <option>PayMaya</option>
              <option>Bank Transfer</option>
              <option>Others</option>
            </select>

            <input id="editOtherPayment" placeholder="Specify..." class="hidden mt-2 w-full rounded-xl px-4 py-2 bg-gray-100/70 dark:bg-gray-800/70" />
          </div>

          <!-- SCHEDULE -->
          <div class="border-t pt-4 space-y-3">
            <label class="flex items-center gap-2 text-xs font-bold uppercase text-gray-500">
              <input type="checkbox" id="editIsScheduled" class="accent-emerald-600" onchange="toggleEditScheduleFields()" />
              Scheduled Order
            </label>

            <div id="editScheduleFields" class="hidden grid grid-cols-2 gap-3">
              <input type="date" id="editScheduledDate" class="rounded-xl px-4 py-2 bg-gray-100/70 dark:bg-gray-800/70" />
              <input type="time" id="editScheduledTime" class="rounded-xl px-4 py-2 bg-gray-100/70 dark:bg-gray-800/70" />
            </div>
          </div>

          <!-- ITEMS -->
          <div>
            <div class="flex justify-between items-center mb-2">
              <span class="text-xs font-bold text-gray-400 uppercase">Items</span>
              <button onclick="addEditItemRow()" class="text-xs font-black text-emerald-500 hover:text-emerald-600">+ Add</button>
            </div>

            <div id="editItemsContainer" class="space-y-2"></div>
          </div>

          <!-- TOTAL BREAKDOWN -->
          <div class="space-y-2">
            <div class="flex justify-between items-center text-xs">
              <span class="text-gray-400">Subtotal</span>
              <div class="flex items-center gap-1">
                <span> <?php echo htmlspecialchars($currencySymbol) . " " . htmlspecialchars($systemCurrency); ?> </span>
                <input id="editSubtotal" type="number" step="0.01" value="0.00" readonly class="w-20 bg-transparent text-right border-none outline-none" />
              </div>
            </div>

            <div class="flex justify-between items-center text-xs">
              <span class="text-gray-400">Discount</span>
              <div class="flex items-center gap-1 text-red-500">
                <span> <?php echo htmlspecialchars($currencySymbol) . " " . htmlspecialchars($systemCurrency); ?> </span>
                <input id="editDiscount" type="number" step="0.01" value="0.00" class="w-20 bg-transparent text-right border-none outline-none text-red-500" />
              </div>
            </div>

            <div class="flex justify-between items-center text-xs">
              <span class="text-gray-400">Delivery Fee</span>
              <div class="flex items-center gap-1">
                <span> <?php echo htmlspecialchars($currencySymbol) . " " . htmlspecialchars($systemCurrency); ?> </span>
                <input id="editDelivery" type="number" step="0.01" value="0.00" class="w-20 bg-transparent text-right border-none outline-none" />
              </div>
            </div>

            <div class="flex justify-between items-center text-xs">
              <span class="text-gray-400">Advance Payment</span>
              <div class="flex items-center gap-1 text-blue-500">
                <span> <?php echo htmlspecialchars($currencySymbol) . " " . htmlspecialchars($systemCurrency); ?> </span>
                <input id="editAdvance" type="number" step="0.01" value="0.00" class="w-20 bg-transparent text-right border-none outline-none text-blue-500" />
              </div>
            </div>

            <hr class="border-gray-300 dark:border-gray-700" />

            <label class="text-xs font-bold text-gray-400 uppercase"> Grand Total </label>

            <div class="relative">
              <span id="editGrandTotalCurrency" class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"> <?php echo htmlspecialchars($currencySymbol) . " " . htmlspecialchars($systemCurrency); ?> </span>

              <input id="editGrandTotal" class="w-full mt-1 rounded-xl px-4 py-2 bg-gray-100/70 dark:bg-gray-800/70 font-black text-emerald-500" readonly />
            </div>
          </div>
        </div>

        <!-- FOOTER -->
        <div class="sticky bottom-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur border-t p-5 flex justify-end gap-3">
          <button onclick="closeEditOrderModal()" class="px-4 py-2 font-bold text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-200">Cancel</button>

          <button onclick="saveEditedOrder()" class="px-6 py-2 rounded-xl bg-emerald-600 text-white font-black hover:bg-emerald-700 transition">Save Changes</button>
        </div>
      </div>
    </div>
    <!-- Delete Confirmation Modal -->

    <div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-sm w-full p-6 shadow-2xl scale-95 transition-transform">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
          </div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">Delete Order?</h3>
          <p class="text-sm text-gray-500 dark:text-white mt-2">
            Are you sure you want to PERMANENTLY delete order
            <span id="modalOrderId" class="font-bold text-gray-800 dark:text-gray-400"></span>? This action cannot be undone.
          </p>
        </div>
        <div class="mt-6 flex gap-3">
          <button id="cancelDelete" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors">Cancel</button>
          <button id="confirmDelete" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors">Delete</button>
        </div>
      </div>
    </div>
    <div id="statusConfirmModal" class="fixed inset-0 z-[999] hidden items-center justify-center bg-black/50 overflow-hidden backdrop-blur-sm p-4">
      <div class="w-full max-w-md rounded-lg bg-white dark:bg-gray-900 p-6 shadow-lg">
        <h2 class="mb-4 text-lg font-semibold">Confirm Status Change</h2>

        <p id="statusConfirmText" class="mb-6 text-gray-700 dark:text-gray-300"></p>

        <div class="flex justify-end gap-3">
          <button id="cancelStatusBtn" class="rounded bg-gray-200 dark:bg-gray-500 px-4 py-2 hover:bg-gray-300 dark:hover:bg-gray-300">Cancel</button>
          <button id="confirmStatusBtn" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Confirm</button>
        </div>
      </div>
    </div>

    <!-- Right Click Context Menu -->
    <div id="contextMenu" class="fixed hidden z-[9999] w-64 overflow-visible rounded-xl border border-gray-200 bg-white py-2 shadow-2xl dark:border-gray-700 dark:bg-gray-900">
      <!-- Edit -->
      <button onclick="openEditOrderModal({ orderId: document.getElementById('contextMenu').dataset.orderId })" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition hover:bg-emerald-50 hover:text-emerald-600 dark:text-gray-200 dark:hover:bg-emerald-900/20 dark:hover:text-emerald-400">
        <i class="fa-solid fa-pen-to-square w-5 text-center"></i>
        <span>Edit Order</span>
      </button>

      <!-- Download -->
      <button onclick="downloadReceipt(document.getElementById('contextMenu').dataset.orderId, '<?php echo htmlspecialchars($currencySymbol); ?>')" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
        <i class="fa-solid fa-file-arrow-down w-5 text-center"></i>
        <span>Download E-Order Slip</span>
      </button>

      <!-- Print -->
      <button onclick="printReceipt(document.getElementById('contextMenu').dataset.orderId)" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
        <i class="fa-solid fa-print w-5 text-center"></i>
        <span>Print Receipt</span>
      </button>

      <div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>

      <!-- Update Status -->
      <div class="relative status-menu w-full">
        <button class="flex w-full items-center justify-between gap-3 px-4 py-2.5 text-sm text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
          <span class="flex items-center gap-3">
            <i class="fa-solid fa-arrows-rotate w-5 text-center"></i>
            Update Status
          </span>

          <i class="fa-solid fa-chevron-right text-xs"></i>
        </button>

        <!-- Click submenu -->
        <div id="statusSubmenu" class="absolute left-full top-0 ml-1 hidden w-48 rounded-xl border border-gray-200 bg-white py-2 shadow-xl dark:border-gray-700 dark:bg-gray-900">
          <button onclick="updateOrderStatus(document.getElementById('contextMenu').dataset.orderId, 'pending')" class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">Pending</button>

          <button onclick="updateOrderStatus(document.getElementById('contextMenu').dataset.orderId, 'preparing')" class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">Preparing</button>

          <button onclick="updateOrderStatus(document.getElementById('contextMenu').dataset.orderId, 'ready')" class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">Ready</button>

          <button onclick="updateOrderStatus(document.getElementById('contextMenu').dataset.orderId, 'scheduled')" class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">Scheduled</button>

          <button onclick="updateOrderStatus(document.getElementById('contextMenu').dataset.orderId, 'out')" class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">Out For Delivery</button>

          <button onclick="updateOrderStatus(document.getElementById('contextMenu').dataset.orderId, 'completed')" class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">Completed</button>

          <button onclick="updateOrderStatus(document.getElementById('contextMenu').dataset.orderId, 'cancelled')" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Cancelled</button>
        </div>
      </div>

      <div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>

      <!-- Open -->
      <button onclick="openOrderSlip(document.getElementById('contextMenu').dataset.orderId)" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
        <i class="fa-solid fa-up-right-from-square w-5 text-center"></i>
        <span>Open Order</span>
      </button>
      <div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>

      <!-- Delete -->
      <button onclick="orderSlipActions('delete', document.getElementById('contextMenu').dataset.orderId)" class="flex w-full items-center gap-3 rounded-lg px-4 py-2.5 text-sm text-red-600 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300">
        <i class="fa-solid fa-trash-can w-5 text-center"></i>

        <span class="font-medium"> Delete Order </span>
      </button>
    </div>

    <audio id="notificationSound" src="<?php echo $systemFolder; ?>/Assets/sounds/dashboard notification.mp3" preload="auto"></audio>

    <script>
      document.addEventListener("click", (event) => {
        const menu = document.getElementById("contextMenu");
        const statusMenu = event.target.closest(".status-menu");

        if (statusMenu) {
          return;
        }

        if (!menu.contains(event.target)) {
          menu.classList.add("hidden");

          const submenu = document.getElementById("statusSubmenu");
          submenu.classList.add("hidden");
        }
      });

      let activeRow = null;
      let submenuHideTimer;

      /*
          Update Status hover handling
      */

      const menu = document.getElementById("contextMenu");
      const submenu = document.getElementById("statusSubmenu");
      const statusMenu = document.querySelector(".status-menu");

      menu.addEventListener("click", (event) => {
        // If clicking Update Status, allow submenu toggle
        if (statusMenu.contains(event.target)) {
          return;
        }

        // Any other click inside context menu hides submenu
        submenu.classList.add("hidden");
      });

      statusMenu.addEventListener("click", (event) => {
        event.stopPropagation();

        submenu.classList.toggle("hidden");
      });

    </script>

    <script>
      window.menuMap = {};
    </script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/printReceipt.js?v=<?php echo time(); ?>" defer></script>
    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/liveStats.js?v=<?php echo time(); ?>" defer></script>
    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/loadMenuItems.js?v=<?php echo time(); ?>"></script>
    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/orderSlipActions.js?v=<?php echo time(); ?>" defer></script>
    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/Functions.js?v=<?php echo time(); ?>" defer></script>
    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/loadRecentOrders.js?v=<?php echo time(); ?>"></script>
    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/contextMenu.js?v=<?php echo time(); ?>"></script>
    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/updateLiveTime.js?v=<?php echo time(); ?>"></script>
    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/loadDailyStats.js?v=<?php echo time(); ?>" defer></script>
    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/downloadReceipt.js?v=<?php echo time(); ?>" defer></script>
    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/navbar.js?v=<?php echo time(); ?>" defer></script>
    <!-- Production -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script>
        window.systemInfoUrl = "<?php echo $systemFolder; ?>/Pages/Script/get_system.php";
    </script>

    <script src="<?php echo $systemFolder ?>/Pages/Script/Dashboard/init.js"></script>

    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/openOrderSlip.js?v=<?php echo time(); ?>" defer></script>
    <script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/editEvents.js?v=<?php echo time(); ?>" defer></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/toastrConfig.js"></script>
    <script src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/utils.js"></script>

    <script src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/liveOrderWatcher.js?v=<?php echo time(); ?>"></script>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const deleteModal = document.getElementById("deleteModal");
        const customerModal = document.getElementById("customerModal");

        const editOrderModal = document.getElementById("editOrderModal");
        const statusConfirmModal = document.getElementById("statusConfirmModal");

        function restoreScrollIfNoModal() {
          const openModals = ["customerModal", "editOrderModal", "deleteModal", "statusConfirmModal"];
          const anyOpen = openModals.some((id) => {
            const modal = document.getElementById(id);
            return modal && !modal.classList.contains("hidden");
          });
          if (!anyOpen) {
            document.documentElement.classList.remove("overflow-hidden");
          }
        }

        const modalOverlayClosers = [
          {
            modal: customerModal,
            close: () => {
              if (typeof closeModal === "function") closeModal();
            },
          },
          {
            modal: editOrderModal,
            close: () => {
              if (typeof closeEditOrderModal === "function") closeEditOrderModal();
            },
          },
          {
            modal: deleteModal,
            close: () => {
              deleteModal.classList.add("hidden");
              restoreScrollIfNoModal();
            },
          },
          {
            modal: statusConfirmModal,
            close: () => {
              statusConfirmModal.classList.add("hidden");
              restoreScrollIfNoModal();
            },
          },
        ];

        modalOverlayClosers.forEach(({ modal, close }) => {
          if (!modal) return;
          modal.addEventListener("click", (e) => {
            if (e.target === modal) {
              close();
            }
          });
        });
      });
    </script>
    <!-- Scripts -->
  </body>
</html>
