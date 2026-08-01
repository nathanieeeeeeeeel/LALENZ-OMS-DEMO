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
    <!-- The variable $systemName is already available because you included 'get_system.php' at the top of the file -->
    <title><?php echo htmlspecialchars($systemName); ?> - Home</title>
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

    <!-- <link rel="stylesheet" href="./src/output.css"/> -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: "class",
      };
    </script>

    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js" type="text/javascript"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="<?= $logo; ?>?v=<?= time(); ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  </head>
  <body class="bg-gray-100 text-gray-800 dark:bg-gray-950 dark:text-gray-100 font-sans">
    <!-- Navigation Bar (White Primary) -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/navbar.php'; ?>
    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 md:py-12">
      <?php if ($isAdminLoggedIn): ?>

      <!-- DASHBOARD HEADER -->
      <div class="mb-10">
        <h1 class="text-4xl font-black text-gray-900 dark:text-white">Overview</h1>
        <p class="text-gray-400 mt-2">Real-time overview of today’s operations</p>
      </div>

      <!-- KPI CARDS -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6 mb-12">
        <!-- TODAY SALES -->
        <div class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-emerald-500/40 via-transparent to-transparent hover:from-emerald-400/70 transition-all duration-500">
          <div class="relative backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 overflow-hidden">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br from-emerald-500/10 via-transparent to-transparent"></div>
            <div class="absolute top-0 left-0 h-1 w-0 bg-emerald-500 group-hover:w-full transition-all duration-500"></div>

            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em] mb-3">Today Sales</p>

            <div id="todaySales" class="text-4xl font-black text-gray-900 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300"><?php $symbol = $currencyData[$systemCurrency]['symbol'] ?? $systemCurrency; echo htmlspecialchars($symbol . '' . number_format(0.00, 2)); ?></div>
            <p id="todaySalesTrend"
              class="text-xs mt-2 font-semibold text-gray-400">
              
            </p>
          </div>
        </div>

        <!-- ORDERS TODAY -->
        <div class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-blue-500/40 via-transparent to-transparent hover:from-blue-400/70 transition-all duration-500">
          <div class="relative backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 overflow-hidden">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br from-blue-500/10 via-transparent to-transparent"></div>
            <div class="absolute top-0 left-0 h-1 w-0 bg-blue-500 group-hover:w-full transition-all duration-500"></div>

            <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.3em] mb-3">Orders Today</p>

            <div id="ordersToday" class="text-4xl font-black text-gray-900 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">0</div>
            <p id="ordersTodayTrend" class="text-xs mt-2 font-semibold text-gray-400">
              
            </p>
          </div>
        </div>

        <!-- PENDING -->
        <div class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-orange-500/40 via-transparent to-transparent hover:from-orange-400/70 transition-all duration-500">
          <div class="relative backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl hover:shadow-orange-500/10 transition-all duration-500 overflow-hidden">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br from-orange-500/10 via-transparent to-transparent"></div>
            <div class="absolute top-0 left-0 h-1 w-0 bg-orange-500 group-hover:w-full transition-all duration-500"></div>

            <p class="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] mb-3">Pending</p>

            <div id="pendingOrders" class="text-4xl font-black text-gray-900 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">0</div>
          </div>
        </div>

        <!-- COMPLETED -->
        <div class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-gray-500/40 via-transparent to-transparent hover:from-gray-400/70 transition-all duration-500">
          <div class="relative backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl hover:shadow-gray-500/10 transition-all duration-500 overflow-hidden">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br from-gray-500/10 via-transparent to-transparent"></div>
            <div class="absolute top-0 left-0 h-1 w-0 bg-gray-500 group-hover:w-full transition-all duration-500"></div>

            <p class="text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-[0.3em] mb-3">Completed</p>

            <div id="completedOrders" class="text-4xl font-black text-gray-900 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">0</div>
          </div>
        </div>
      </div>

      <!-- ORDERS CHART -->
      <div class="relative p-[1px] rounded-3xl bg-gradient-to-br from-blue-500/30 via-transparent to-transparent mb-10">
        <div class="backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-6 border border-white/30 dark:border-gray-700/50 shadow-2xl">

          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-black tracking-tight">Performance Overview</h3>
            <select id="chartRange"
            class="px-3 py-1 text-xs font-bold rounded-full bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300 outline-none cursor-pointer">
            <option value="24h">Last 24 Hours</option>
            <option value="7d" selected>Last 7 Days</option>
            <option value="15d">Last 15 Days</option>
            <option value="30d">Last 30 Days</option>
            <option value="60d">Last 60 Days</option>
            <option value="1y">Last 12 Months</option>
            </select>
          </div>

          <div class="relative w-full h-[300px] sm:h-[350px] lg:h-[450px]">
            <canvas class="ordersChart"></canvas>
          </div>

        </div>
      </div>

      <!-- RECENT ORDERS -->
      <div class="relative p-[1px] rounded-3xl bg-gradient-to-br from-emerald-500/30 via-transparent to-transparent">
        <div class="backdrop-blur-xl bg-white/70 dark:bg-gray-900/60 rounded-3xl p-6 border border-white/30 dark:border-gray-700/50 shadow-2xl">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-black tracking-tight">Recent Orders</h3>
            <a href="./Pages/admin/dashboard.php" class="text-sm font-bold text-emerald-500 hover:text-emerald-400 transition"> View all → </a>
          </div>

          <div id="recentOrders" class="space-y-3"></div>
        </div>
      </div>

      <?php else: ?>

      <!-- PUBLIC HERO -->
      <div class="text-center py-20 md:py-28">
        <h1 class="text-4xl md:text-2xl uppercase tracking-widest font-black mb-6 leading-tight">
          <img src="<?= $logo; ?>" alt="Lalenz Logo" class="mx-auto mb-4" width="100" height="100" />
          Order Management System
        </h1>

        <p class="text-gray-400 text-base md:text-xl mb-10 max-w-xl mx-auto">Modern restaurant management system — fast, simple, and reliable.</p>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <a href="<?= $systemFolder ?>/Pages/admin/login.php" class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-8 py-3 rounded-2xl font-black hover:scale-105 transition shadow"> Admin Login </a>

          <a href="#" class="hidden border border-gray-300 dark:border-gray-700 px-8 py-3 rounded-2xl font-bold hover:bg-gray-100 dark:hover:bg-gray-800 transition"> Learn More </a>
        </div>
      </div>

      <?php endif; ?> <?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/footer.php'; ?>
    </main>

    <!-- Production -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= $systemFolder ?>/Pages/Script/Dashboard/navbar.js?v=<?= time() ?>"></script>
    <script>
      toastr.options = {
        closeButton: false,
        debug: false,
        newestOnTop: false,
        progressBar: true,
        positionClass: "toast-bottom-right",
        preventDuplicates: true,
        onclick: null,
        showMethod: "slideUp",
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "5000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
      };
    </script>
    <script>
      /**
       * 1. INITIALIZE GLOBAL DATA FROM PHP
       * We convert PHP arrays/variables into JS constants once at the top.
       */
      const currencyData = <?php echo json_encode($currencyData); ?>;
      const systemCurrency = <?= json_encode($systemCurrency) ?>;
      const activeSymbol = currencyData[systemCurrency]?.symbol || systemCurrency;
      const currencySymbol = <?= json_encode(htmlspecialchars($currencySymbol)); ?>;

      // =========================
      // GLOBAL VARIABLES
      // =========================
      let ordersChart = null;
      let chartData = null;

      // =========================
      // CHART DATA BUILDER
      // =========================

        function buildChart(data, range) {

            const today = new Date();

            let labels = [];
            let orders = [];
            let sales = [];

            // =========================
            // 24 HOURS MODE
            // =========================
            if (range === "24h") {

                const now = new Date();
                const cutoff = new Date(now.getTime() - 24 * 60 * 60 * 1000);

                const recent = data.filter(o => new Date(o.order_datetime) >= cutoff);

                const grouped = {};

                recent.forEach(o => {
                    const d = new Date(o.order_datetime);
                    const hourKey = `${d.toLocaleDateString("en-PH", {
                        month: "short",
                        day: "numeric"
                    })} ${d.toLocaleTimeString("en-PH", {
                        hour: "2-digit",
                        hour12: true
                    })}`;
                    const dateKey = d.toDateString("en-PH", {});

                    if (!grouped[hourKey]) {
                        grouped[hourKey] = { orders: 0, sales: 0 };
                    }

                    grouped[hourKey].orders += 1;
                    grouped[hourKey].sales += parseFloat(o.grand_total || 0);
                });

                labels = Object.keys(grouped);
                orders = labels.map(k => grouped[k].orders);
                sales = labels.map(k => grouped[k].sales);
            }

            // =========================
            // DAILY MODE (7d / 30d)
            // =========================
            else {

                // let daysBack = range === "30d" ? 30 : 7;
                let daysBack;
                if (range == "24h") {
                    daysBack = 1;
                } else if (range == "7d") {
                    daysBack = 7;
                } else if (range == "15d") {
                    daysBack = 15;
                } else if (range == "30d") {
                    daysBack = 30;
                } else if (range == "60d") {
                    daysBack = 60;
                } else if (range === "1y") {

                    const grouped = {};

                    data.forEach(o => {

                        const status = o.status?.toLowerCase();

                        const d = new Date(o.order_datetime);

                        const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;

                        if (!grouped[key]) {
                            grouped[key] = {
                                orders: 0,
                                sales: 0
                            };
                        }

                        grouped[key].orders++;

                        if (status !== "cancelled") {
                            grouped[key].sales += parseFloat(o.grand_total || 0);
                        }
                    });

                    const months = [];

                    const current = new Date();

                    for (let i = 11; i >= 0; i--) {

                        const d = new Date(
                            current.getFullYear(),
                            current.getMonth() - i,
                            1
                        );

                        const key =
                            `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;

                        months.push(key);

                        labels.push(
                            d.toLocaleDateString("en-PH", {
                                month: "short",
                                year: "numeric"
                            })
                        );

                        orders.push(grouped[key]?.orders || 0);
                        sales.push(grouped[key]?.sales || 0);
                    }

                    chartData = { labels, orders, sales };
                    renderChartDynamic();
                    return;
                } else {
                    daysBack = 7; // default
                }

                for (let i = daysBack - 1; i >= 0; i--) {

                    const d = new Date();
                    d.setDate(today.getDate() - i);

                    const dayKey = d.toLocaleDateString("en-CA");

                    labels.push(
                        d.toLocaleDateString("en-PH", { month: 'short', day: 'numeric', year: daysBack > 30 ? 'numeric' : undefined })
                    );

                    const dayOrders = data.filter(o =>
                        o.order_datetime.split(" ")[0] === dayKey
                    );

                    orders.push(dayOrders.length);

                    sales.push(
                        dayOrders
                            .filter(o => o.status?.toLowerCase() !== "cancelled")
                            .reduce((sum, o) => sum + parseFloat(o.grand_total || 0), 0)
                    );
                }
            }

            chartData = { labels, orders, sales };
            renderChartDynamic();
        }
      
      // =========================
      // CHART RENDER FUNCTION
      // =========================
      function renderChartDynamic() {

            if (!chartData) return;

            const ctx = document.querySelector(".ordersChart");
            if (!ctx) return;

            const isDark = document.documentElement.classList.contains("dark");
            const textColor = isDark ? "#e5e7eb" : "#111827";
            const gridColor = isDark ? "rgba(255,255,255,0.08)" : "rgba(0,0,0,0.08)";

            if (ordersChart) ordersChart.destroy();

            ordersChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: "Orders",
                            data: chartData.orders,
                            borderColor: "#3b82f6",
                            backgroundColor: "rgba(59,130,246,0.15)",
                            tension: 0.4,
                            fill: true,
                            pointRadius: 3,
                            yAxisID: 'y'
                        },
                        {
                            label: "Sales",
                            data: chartData.sales,
                            borderColor: "#10b981",
                            backgroundColor: "rgba(16,185,129,0.15)",
                            tension: 0.4,
                            fill: true,
                            pointRadius: 3,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        legend: { labels: { color: textColor } },

                        // ✅ THIS FIXES HOVER DISPLAY
                        tooltip: {
                            mode: "index",
                            intersect: false,
                            callbacks: {
                                label: function (context) {

                                    const value = context.parsed.y;
                                    const label = context.dataset.label;

                                    if (label === "Sales") {
                                        return `${label}: ${activeSymbol}${value.toLocaleString("en-US", {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        })}`;
                                    }

                                    return `${label}: ${value}`;
                                }
                            }
                        }
                    },

                    scales: {
                        x: { ticks: { color: textColor }, grid: { color: gridColor } },

                        y: {
                            ticks: { color: textColor },
                            grid: { color: gridColor }
                        },

                        // ✅ SALES AXIS FORMAT
                        y1: {
                            ticks: {
                                color: textColor,
                                callback: function (value) {
                                    return `${activeSymbol}${value.toLocaleString("en-US", {
                                        minimumFractionDigits: 0
                                    })}`;
                                }
                            },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });
        }

      $(document).ready(function () {

        // =========================
        // SIMPLE TREND FUNCTION
        // =========================
        function calcTrend(today, yesterday) {

            if (today === 0 && yesterday === 0) {
                return { text: "", color: "text-gray-400" };
            }

            if (yesterday === 0 && today > 0) {
                return { text: "▲ New activity today", color: "text-emerald-500" };
            }

            if (today === 0 && yesterday > 0) {
                return { text: "▼ No activity today", color: "text-gray-400" };
            }

            const diff = ((today - yesterday) / yesterday) * 100;
            const sign = diff >= 0 ? "▲" : "▼";

            return {
                text: `${sign} ${Math.abs(diff).toFixed(1)}% vs yesterday`,
                color: diff >= 0 ? "text-emerald-500" : "text-red-500"
            };
        }

        // =========================
        // DATA FETCH
        // =========================
        $.get({
            url: "<?= $systemFolder ?>/Pages/Script/get_orders.php?t=" + Date.now(),
            dataType: "json",

            success: function (data) {

                if (!Array.isArray(data)) {
                    console.error("Expected array, got:", data);
                    return;
                }

                let todaySales = 0;
                let ordersToday = 0;
                let pendingCount = 0;
                let completedCount = 0;

                const today = new Date().toLocaleDateString("en-CA");

                const yesterdayDate = new Date();
                yesterdayDate.setDate(yesterdayDate.getDate() - 1);
                const yesterday = yesterdayDate.toLocaleDateString("en-CA");

                let yesterdaySales = 0;
                let yesterdayOrders = 0;

                data.forEach(order => {
                    const orderDay = order.order_datetime.split(" ")[0];
                    const status = order.status?.toLowerCase();

                    if (status === "cancelled") return;

                    if (orderDay === today) {
                        ordersToday++;
                        todaySales += parseFloat(order.grand_total || 0);

                        if (status === "pending") pendingCount++;
                        if (status === "done" || status === "delivered") completedCount++;
                    }

                    if (orderDay === yesterday) {
                        yesterdayOrders++;
                        yesterdaySales += parseFloat(order.grand_total || 0);
                    }
                });

                // =========================
                // LAST 7 DAYS CHART DATA
                // =========================
                const last7Days = [];

                for (let i = 6; i >= 0; i--) {
                    const d = new Date();
                    d.setDate(d.getDate() - i);
                    last7Days.push(d.toLocaleDateString("en-CA"));
                }

                const ordersByDay = last7Days.map(day =>
                    data.filter(o => o.order_datetime === day).length
                );

                const salesByDay = last7Days.map(day =>
                    data
                        .filter(o => {
                            const d = o.order_datetime;
                            const s = o.status?.toLowerCase();
                            return d === day && s !== "cancelled";
                        })
                        .reduce((sum, o) => sum + parseFloat(o.grand_total || 0), 0)
                );

                // chartData = { last7Days, ordersByDay, salesByDay };
                // renderChart();
                buildChart(data, "7d"); // Build initial chart with last 7 days data

                // ========================
                // CHART RANGE SELECTOR
                // ========================
                const chartRange = document.getElementById("chartRange");

                if (chartRange) {
                    chartRange.addEventListener("change", function () {
                        console.log("range changed:", this.value);
                        buildChart(data, this.value);
                    });
                }

                // =========================
                // UI UPDATES
                // =========================
                $("#todaySales").text(`${activeSymbol}${todaySales.toLocaleString("en-US", { minimumFractionDigits: 2 })}`);

                const salesTrend = calcTrend(todaySales, yesterdaySales);
                $("#todaySalesTrend")
                    .text(salesTrend.text)
                    .removeClass("text-gray-400 text-emerald-500 text-red-500")
                    .addClass(salesTrend.color);

                $("#ordersToday").text(ordersToday);

                const ordersTrend = calcTrend(ordersToday, yesterdayOrders);
                $("#ordersTodayTrend")
                    .text(ordersTrend.text)
                    .removeClass("text-gray-400 text-emerald-500 text-red-500")
                    .addClass(ordersTrend.color);

                $("#pendingOrders").text(pendingCount);
                $("#completedOrders").text(completedCount);

                // =========================
                // RECENT ORDERS
                // =========================
                const recentOrders = data
                    .filter(order => order.order_datetime.split(" ")[0] === today)
                    .sort((a, b) => new Date(b.order_datetime) - new Date(a.order_datetime))
                    .slice(0, 5);

                if (recentOrders.length === 0) {
                    $("#recentOrders").html('<div class="text-center py-4 text-gray-400">No orders placed today.</div>');
                } else {

                    // Helper function to normalize status values
                    function normalizeStatus(status) {
                        return (status || "")
                            .toLowerCase()
                            .trim()
                            .replace(/\s+/g, "_")
                            .replace(/ready\s*for\s*pickup/g, "ready")
                            .replace(/delivered/g, "completed")
                            .replace(/out\s*for\s*delivery/g, "out_for_delivery");
                    }

                    const statusStyles = {
                        "Pending": "bg-orange-500/10 text-orange-500 border-orange-500/20",
                        "Out For Delivery": "bg-blue-500/10 text-blue-500 border-blue-500/20",
                        "Delivered": "bg-emerald-500/10 text-emerald-500 border-emerald-500/20",
                        "Cancelled": "bg-red-500/10 text-red-500 border-red-500/20",
                        "Preparing": "bg-yellow-500/10 text-yellow-500 border-yellow-500/20",
                        "Completed": "bg-emerald-500/10 text-emerald-500 border-emerald-500/20",
                        "Scheduled": "bg-purple-500/10 text-purple-500 border-purple-500/20",
                        "Ready": "bg-green-500/10 text-green-500 border-green-500/20"
                    };

                    const statusName = {
                        "delivered": "Completed",
                        "cancelled": "Cancelled",
                        "out_for_delivery": "Out For Delivery",
                        "pending": "Pending",
                        "scheduled": "Scheduled",
                        "preparing": "Preparing",
                        "completed": "Completed",
                        "ready": "Ready",
                        "done": "Completed"
                    };

                    const getStatusDisplayName = (status) => {
                        const normalized = normalizeStatus(status);
                        return statusName[normalized] || status;
                    };

                    const getStatusClass = (status) => {
                        const displayName = getStatusDisplayName(status);
                        return statusStyles[displayName] || 'bg-gray-100 text-gray-800';
                    };

                    const ordersHtml = recentOrders.map(order => {

                        const totalFormatted = parseFloat(order.grand_total || 0)
                            .toLocaleString("en-US", { minimumFractionDigits: 2 });

                        const statusClass = getStatusClass(order.status);
                        const statusText = getStatusDisplayName(order.status);

                        let itemCount = 1;

                        try {
                            itemCount = JSON.parse(order.order_items || "[]").length || 1;
                        } catch {
                            itemCount = order.total_items || 1;
                        }

                        const orderTime = new Date(order.order_datetime).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        return `
                        <div class="relative p-[1px] rounded-2xl bg-gray-200/40 dark:bg-gray-700/40">

                        <div class="flex items-center justify-between gap-3 p-4 rounded-2xl
                            backdrop-blur-md bg-gray-200 dark:bg-gray-950/75
                            border border-white/30 dark:border-gray-700/50">

                            <!-- LEFT -->
                            <div class="min-w-0">
                            <div class="text-sm font-black text-gray-900 dark:text-gray-100 truncate">
                                ${order.customer_name || 'Guest'}
                            </div>

                            <div class="text-xs text-gray-500 dark:text-gray-300 mt-1">
                                Order #${order.id} · ${itemCount} item${itemCount != 1 ? 's' : ''} · ${orderTime}
                            </div>
                            </div>

                            <!-- RIGHT -->
                            <div class="text-right shrink-0">
                            <div class="text-sm font-black text-gray-900 dark:text-gray-100">
                                ${activeSymbol} ${totalFormatted}
                            </div>

                            <div class="mt-1 text-[10px] font-black uppercase px-3 py-1.5 rounded-full ${statusClass}">
                                ${statusText}
                            </div>
                            </div>

                        </div>

                        </div>
                        `;
                    }).join("");

                    $("#recentOrders").html(ordersHtml);
                }
            },

            error: function () {
                toastr.error("Failed to load dashboard data.");
            }
        });

        });
    </script>
  </body>
</html>