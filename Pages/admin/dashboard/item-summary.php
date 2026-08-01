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
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth select-none">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($systemName); ?> – Item Sales Summary</title>
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
    <script>
      tailwind.config = {
        darkMode: "class", // Enable dark mode with class toggle
      };
    </script>
    <link rel="icon" type="image/png" href="<?= $logo; ?>?v=<?= time(); ?>" />
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js" type="text/javascript"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  </head>

  <body class="bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 font-sans">
    <!-- NAV -->
    <!-- Navigation Bar (White Primary) -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/navbar.php'; ?>

    <!-- MAIN -->
    <!-- MAIN -->
    <main class="max-w-6xl mx-auto px-6 py-10">
      <!-- HEADER -->
      <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-3xl md:text-4xl font-black text-gray-800 dark:text-white">
            Item Sales
            <span class="text-emerald-500">Summary</span>
          </h1>
          <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Aggregated performance per item</p>
        </div>

        <!-- QUICK FILTER -->
        <div class="flex gap-2 flex-wrap">
          <button onclick="quickRange(1)" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">24H</button>
          <button onclick="quickRange(7)" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">7D</button>
          <button onclick="quickRange(30)" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">30D</button>
        </div>
      </div>

      <!-- FILTER -->
      <div class="bg-white/80 dark:bg-gray-900/70 backdrop-blur-xl p-5 rounded-2xl shadow-sm border border-gray-200/70 dark:border-gray-700 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
          <!-- FROM -->
          <div class="flex flex-col">
            <label class="text-xs font-semibold text-gray-400 mb-1">From</label>
            <input type="date" id="fromDate" value="<?= date('Y-m-d'); ?>" class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none w-full" />
          </div>

          <!-- TO -->
          <div class="flex flex-col">
            <label class="text-xs font-semibold text-gray-400 mb-1">To</label>
            <input type="date" id="toDate" value="<?= date('Y-m-d'); ?>" class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none w-full" />
          </div>

          <!-- APPLY -->
          <button onclick="loadItemSummary()" class="w-full px-4 py-2 text-sm font-bold rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-sm">Apply</button>

          <!-- RESET -->
          <button onclick="resetFilter()" class="w-full px-4 py-2 text-sm font-bold rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 transition">Reset</button>
        </div>
      </div>

      <!-- SUMMARY CARDS -->
      <div id="summaryCards" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <!-- Best Seller -->
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
          <p class="text-xs uppercase tracking-widest text-gray-400 font-semibold">Best Seller</p>
          <h3 id="bestSellerName" class="mt-2 text-xl font-black text-emerald-600">--</h3>
          <p id="bestSellerQty" class="text-sm text-gray-500 dark:text-gray-300 mt-1">0 sold</p>
        </div>

        <!-- Total Items Sold -->
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
          <p class="text-xs uppercase tracking-widest text-gray-400 font-semibold">Total Items Sold</p>
          <h3 id="totalSold" class="mt-2 text-2xl font-black">0</h3>
        </div>

        <!-- Revenue -->
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
          <p class="text-xs uppercase tracking-widest text-gray-400 font-semibold">Revenue</p>
          <h3 id="totalRevenue" class="mt-2 text-2xl font-black text-emerald-600"><?= $currencySymbol ?>0.00</h3>
        </div>

        <!-- Average -->
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
          <p class="text-xs uppercase tracking-widest text-gray-400 font-semibold">Average Revenue / Item</p>
          <h3 id="averageRevenue" class="mt-2 text-2xl font-black"><?= $currencySymbol ?>0.00</h3>
        </div>
      </div>

      <!-- TABLE -->
      <div class="bg-white/20 dark:bg-gray-900/20 border border-white/10 dark:border-gray-700/10 backdrop-blur-xl rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto hidden md:block">
          <table class="w-full text-sm">
            <!-- HEADER -->
            <thead class="bg-gray-50/80 dark:bg-gray-900/80 sticky top-0 z-10 backdrop-blur-xl">
              <tr class="text-[11px] uppercase tracking-widest text-gray-400 dark:text-gray-300">
                <div class="bg-white/20 dark:bg-gray-900/20 border border-white/10 dark:border-gray-700/10 backdrop-blur-xl rounded-2xl shadow-lg overflow-hidden">
                  <!-- SORT BAR -->
                  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4">
                    <div>
                      <h2 class="font-black text-lg">Item Performance</h2>

                      <p id="sortLabel" class="text-xs text-gray-400">Sorted by Best Selling</p>
                    </div>

                    <div class="flex items-center gap-2">
                      <label class="text-xs text-gray-400 uppercase tracking-widest"> Sort </label>

                      <select id="sortSelect" onchange="changeSort()" class="rounded-xl px-3 py-2 text-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 outline-none">
                        <option value="qty-desc">Best Selling</option>
                        <option value="revenue-desc">Highest Revenue</option>
                        <option value="qty-asc">Least Sold</option>
                        <option value="revenue-asc">Lowest Revenue</option>
                        <option value="name-asc">Item A-Z</option>
                        <option value="name-desc">Item Z-A</option>
                      </select>
                    </div>
                  </div>

                  <div class="overflow-x-auto hidden md:block">
                    <table class="w-full text-sm">
                      <thead class="bg-gray-50/80 dark:bg-gray-900/80 sticky top-0 z-10 backdrop-blur-xl">
                        <tr class="text-[11px] uppercase tracking-widest text-gray-400 dark:text-gray-300">
                          <th class="p-4 text-left">Item</th>

                          <th class="p-4 text-center">Sold</th>

                          <th class="p-4 text-right">Revenue</th>

                          <th class="p-4 text-center">Share</th>
                        </tr>
                      </thead>

                      <tbody id="itemSummaryBody" class="divide-y divide-gray-100 dark:divide-gray-800"></tbody>
                    </table>
                  </div>
                </div>
              </tr>
            </thead>

            <!-- BODY -->
            <tbody id="itemSummaryBody" class="divide-y divide-gray-100 dark:divide-gray-800"></tbody>
          </table>
        </div>
        <div id="itemSummaryCards" class="md:hidden space-y-4 p-4"></div>
      </div>
      <!-- PAGINATION -->
      <div id="pagination" class="flex justify-center items-center mt-4 gap-2"></div>

      <?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/footer.php'; ?>
    </main>

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

    <!-- Production -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/navbar.js?v=<?= time(); ?>"></script>
    <script>
      const deleteModal = document.getElementById("deleteModal");

      function updateSortIndicators() {
        const headers = {
          name: document.getElementById("sort-name"),
          qty: document.getElementById("sort-qty"),
          revenue: document.getElementById("sort-revenue"),
        };

        // Reset all headers
        Object.entries(headers).forEach(([key, el]) => {
          const label = {
            name: "Item",
            qty: "Sold",
            revenue: "Revenue",
          };

          el.innerHTML = label[key];
          el.classList.remove("text-emerald-600", "dark:text-emerald-400", "font-bold");
        });

        // Highlight active header
        const active = headers[currentSort.field];

        if (!active) return;

        active.classList.add("text-emerald-600", "dark:text-emerald-400", "font-bold");

        active.innerHTML += currentSort.direction === "asc" ? ' <i class="fas fa-arrow-up text-xs ml-1"></i>' : ' <i class="fas fa-arrow-down text-xs ml-1"></i>';
      }
      /* -------------------------
        DATE FILTERS & SORTING
      -------------------------*/
      function getTodayDate() {
        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, "0");
        const dd = String(now.getDate()).padStart(2, "0");
        return `${yyyy}-${mm}-${dd}`;
      }

      function getDateRange() {
        const fromInput = document.getElementById("fromDate").value;
        const toInput = document.getElementById("toDate").value;
        const today = getTodayDate();

        const from = fromInput || today;
        const to = toInput || from;

        return { from, to };
      }

      function quickRange(days) {
        const today = new Date();
        const from = new Date();
        from.setDate(today.getDate() - days);

        document.getElementById("fromDate").value = from.toISOString().split("T")[0];
        document.getElementById("toDate").value = today.toISOString().split("T")[0];

        loadItemSummary();
      }

      function resetSummaryKpis() {
        document.getElementById("bestSellerName").textContent = "--";
        document.getElementById("bestSellerQty").textContent = "0 sold";
        document.getElementById("totalSold").textContent = "0";
        document.getElementById("totalRevenue").textContent = `<?= htmlspecialchars($currencySymbol) ?>0.00`;
        document.getElementById("averageRevenue").textContent = `<?= htmlspecialchars($currencySymbol) ?>0.00`;
      }

      function setSummaryLoadingState() {
        document.getElementById("bestSellerName").textContent = "Loading...";
        document.getElementById("bestSellerQty").textContent = "--";
        document.getElementById("totalSold").textContent = "--";
        document.getElementById("totalRevenue").textContent = `<?= htmlspecialchars($currencySymbol) ?>--`;
        document.getElementById("averageRevenue").textContent = `<?= htmlspecialchars($currencySymbol) ?>--`;
      }

      function resetFilter() {
        document.getElementById("fromDate").value = getTodayDate();
        document.getElementById("toDate").value = getTodayDate();
        document.getElementById("sortSelect").value = "qty-desc";
        currentSort = { field: "qty", direction: "desc" };
        document.getElementById("sortLabel").textContent = "Sorted by Best Selling";
        setSummaryLoadingState();
        loadItemSummary();
      }

      let currentSort = {
        field: "qty",
        direction: "desc",
      };

      function changeSort() {
        const value = document.getElementById("sortSelect").value;

        const [field, direction] = value.split("-");

        currentSort.field = field;
        currentSort.direction = direction;

        const labels = {
          "qty-desc": "Best Selling",
          "qty-asc": "Least Sold",

          "revenue-desc": "Highest Revenue",
          "revenue-asc": "Lowest Revenue",

          "name-asc": "Item A-Z",
          "name-desc": "Item Z-A",
        };

        document.getElementById("sortLabel").textContent = "Sorted by " + labels[value];

        currentPage = 1;

        loadItemSummary();
      }

      /* -------------------------
        MENU MAP
      -------------------------*/
      let menuMap = {};
      let nameMap = {};

      async function loadMenu() {
        try {
          const res = await fetch("<?php echo $systemFolder; ?>/Pages/Script/api/items/get.php?v=" + Date.now());
          const data = await res.json();
          data.items.forEach((item) => {
            menuMap[String(item.id)] = item.name;
            nameMap[item.name.toLowerCase()] = item.name;
          });
        } catch (err) {
          console.error("API load failed:", err);
        }
      }

      /* -------------------------
        GET ITEM NAME
      -------------------------*/
      function getItemName(item) {
        if (item.id && menuMap[item.id]) return menuMap[item.id];
        if (item.name) return item.name;
        if (item.display_name && nameMap[item.display_name.toLowerCase()]) {
          return nameMap[item.display_name.toLowerCase()];
        }
        return `Unknown (₱${item.revenue || 0})`;
      }
      /* -------------------------
        PAGINATION VARIABLES
      -------------------------*/
      let currentPage = 1;
      const pageSize = 10; // Number of items per page

      /* -------------------------
        LOAD ITEM SUMMARY (with pagination)
      -------------------------*/
      let summaryRequestToken = 0;

      async function loadItemSummary() {
        const requestId = ++summaryRequestToken;
        setSummaryLoadingState();

        const { from, to } = getDateRange();
        const tbody = document.getElementById("itemSummaryBody");
        const cards = document.getElementById("itemSummaryCards");
        tbody.innerHTML = "";
        if (cards) cards.innerHTML = "";

        try {
          const res = await fetch(`<?php echo $systemFolder; ?>/Pages/Script/get_item_summary.php?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`);
          const data = await res.json();

          if (requestId !== summaryRequestToken) return;

          if (!Array.isArray(data) || !data.length) {
            resetSummaryKpis();

            const emptyRow = `
              <tr>
                <td colspan="4" class="p-8 text-center text-gray-400 italic dark:text-gray-500">
                  No data available
                </td>
              </tr>`;
            tbody.innerHTML = emptyRow;
            if (cards) {
              cards.innerHTML = `
                <div class="rounded-[2rem] border border-white/10 dark:border-gray-700/20 bg-white/80 dark:bg-gray-950/70 shadow-lg p-5 text-center text-gray-500">
                  No data available
                </div>`;
            }
            document.getElementById("pagination").innerHTML = "";
            return;
          }

          const totalRevenue = data.reduce((sum, i) => sum + (i.revenue || 0), 0);

          const totalSold = data.reduce((sum, i) => sum + Number(i.qty || 0), 0);

          const averageRevenue = totalSold > 0 ? totalRevenue / totalSold : 0;

          // ---------- SUMMARY (always based on quantity sold) ----------
          const bestSeller = [...data].sort((a, b) => Number(b.qty) - Number(a.qty))[0];

          // ---------- TABLE SORT ----------

          data.sort((a, b) => {
            let A;
            let B;

            if (currentSort.field === "name") {
              A = getItemName(a).toLowerCase();
              B = getItemName(b).toLowerCase();
            } else {
              A = Number(a[currentSort.field] || 0);
              B = Number(b[currentSort.field] || 0);
            }

            if (A < B) return currentSort.direction === "asc" ? -1 : 1;

            if (A > B) return currentSort.direction === "asc" ? 1 : -1;

            return 0;
          });

          document.getElementById("bestSellerName").textContent = data.length ? getItemName(bestSeller) : "--";

          document.getElementById("bestSellerQty").textContent = data.length ? `${bestSeller.qty} sold` : "0 sold";

          document.getElementById("totalSold").textContent = data.length ? totalSold.toLocaleString() : "0";

          document.getElementById("totalRevenue").textContent = data.length
            ? `<?php echo htmlspecialchars($currencySymbol); ?>${totalRevenue.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })}`
            : `<?php echo htmlspecialchars($currencySymbol); ?>0.00`;

          document.getElementById("averageRevenue").textContent = data.length
            ? `<?php echo htmlspecialchars($currencySymbol); ?>${averageRevenue.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })}`
            : `<?php echo htmlspecialchars($currencySymbol); ?>0.00`;

          // PAGINATION
          const totalPages = Math.ceil(data.length / pageSize);
          if (currentPage > totalPages) currentPage = totalPages;

          const startIndex = (currentPage - 1) * pageSize;
          const pageData = data.slice(startIndex, startIndex + pageSize);

          const topIds = data.slice(0, 3).map((i) => String(i.id));

          // RENDER PAGE DATA
          pageData.forEach((item, index) => {
            const rank = startIndex + index + 1;
            let name = getItemName(item);

            // const medal = topIds.indexOf(String(item.id));
            let medal = -1;

            if ((currentSort.field === "qty" && currentSort.direction === "desc") || (currentSort.field === "revenue" && currentSort.direction === "desc")) {
              medal = topIds.indexOf(String(item.id));
            }

            if (medal === 0) name = "🥇 " + name;
            else if (medal === 1) name = "🥈 " + name;
            else if (medal === 2) name = "🥉 " + name;

            const percent = totalRevenue ? ((item.revenue / totalRevenue) * 100).toFixed(1) : 0;
            const revenueText = `<?php echo htmlspecialchars($currencySymbol); ?>${parseFloat(item.revenue || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

            tbody.innerHTML += `
              <tr class="border-t-2 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/10">
                <td class="p-4 border-b-[1px] dark:border-gray-600 font-bold">${name}</td>
                <td class="p-4 border-b-[1px] dark:border-gray-600 text-center font-black">${item.qty}</td>
                <td class="p-4 border-b-[1px] dark:border-gray-600 text-right font-black text-emerald-600 dark:text-emerald-400">
                  ${revenueText}
                </td>
                <td class="p-4 border-b-[1px] dark:border-gray-600 text-center text-xs font-bold">${percent}%</td>
              </tr>`;

            if (cards) {
              cards.innerHTML += `

                <div class="
                rounded-[2rem]
                border
                border-gray-200
                dark:border-gray-700
                bg-white
                dark:bg-gray-900
                shadow-lg
                p-5">


                <div class="flex justify-between items-center mb-3">


                <span class="
                text-xs
                font-bold
                px-3
                py-1
                rounded-full
                bg-emerald-100
                text-emerald-700
                dark:bg-emerald-900
                dark:text-emerald-300">

                #${rank}

                </span>


                <span class="text-sm font-bold text-gray-500">
                ${percent}%
                </span>


                </div>



                <div class="flex justify-between">


                <div>

                <p class="text-xs text-gray-400 uppercase">
                Item
                </p>


                <p class="font-black">
                ${name}
                </p>


                </div>


                </div>



                <div class="grid grid-cols-2 gap-3 mt-4">


                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-4">

                <p class="text-xs text-gray-400">
                Sold
                </p>

                <p class="font-black text-center">
                ${item.qty}
                </p>

                </div>



                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-4">

                <p class="text-xs text-gray-400">
                Revenue
                </p>

                <p class="font-black text-right text-emerald-600">
                ${revenueText}
                </p>

                </div>


                </div>


                </div>

                `;
            }
          });

          renderPagination(totalPages);
        } catch (e) {
          console.error(e);
          tbody.innerHTML = `
            <tr>
              <td colspan="4" class="p-8 text-center text-red-500 italic dark:text-red-400">
                Error loading data
              </td>
            </tr>`;
        }
      }

      /* -------------------------
        RENDER PAGINATION
      -------------------------*/
      function renderPagination(totalPages) {
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";

        if (totalPages <= 1) return; // No need for pagination

        // Previous button
        const prevBtn = document.createElement("button");
        prevBtn.textContent = "«";
        prevBtn.disabled = currentPage === 1; // disable if first page
        prevBtn.className = `px-3 py-1 rounded ${prevBtn.disabled ? "bg-gray-300 dark:bg-gray-700/25 cursor-not-allowed" : "bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700"}`;
        prevBtn.onclick = (e) => {
          e.preventDefault();
          if (currentPage > 1) {
            currentPage--;
            loadItemSummary();
          }
        };
        pagination.appendChild(prevBtn);

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
          const pageBtn = document.createElement("button");
          pageBtn.textContent = i;

          if (i === currentPage) {
            // Current page: active & disabled
            pageBtn.disabled = true;
            pageBtn.className = "px-3 py-1 rounded bg-emerald-600 text-white cursor-not-allowed";
          } else {
            pageBtn.disabled = false;
            pageBtn.className = "px-3 py-1 rounded bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700";
            pageBtn.onclick = (e) => {
              e.preventDefault();
              currentPage = i;
              loadItemSummary();
            };
          }

          pagination.appendChild(pageBtn);
        }

        // Next button
        const nextBtn = document.createElement("button");
        nextBtn.textContent = "»";
        nextBtn.disabled = currentPage === totalPages; // disable if last page
        nextBtn.className = `px-3 py-1 rounded ${nextBtn.disabled ? "bg-gray-300 dark:bg-gray-700/25 cursor-not-allowed" : "bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700"}`;
        nextBtn.onclick = (e) => {
          e.preventDefault();
          if (currentPage < totalPages) {
            currentPage++;
            loadItemSummary();
          }
        };
        pagination.appendChild(nextBtn);
      }

      /* -------------------------
        PAGE INIT
      -------------------------*/
      document.addEventListener("DOMContentLoaded", async () => {
        await loadMenu();
        loadItemSummary();

        // Setup dropdowns & theme
        setupDropdowns();

        // Initialize tooltips (requires tippy.js)
        if (typeof tippy !== "undefined") tippy("[data-tippy-content]");
      });
    </script>
  </body>
</html>
