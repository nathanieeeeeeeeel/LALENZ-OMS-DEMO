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
    <title><?php echo htmlspecialchars($systemName); ?> - Sales Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <script>
      (function () {
        const theme = localStorage.getItem("theme");
        if (theme === "dark") document.documentElement.classList.add("dark");
      })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = { darkMode: "class" };
    </script>

    <link rel="icon" type="image/png" href="<?= $logo; ?>?v=<?= time(); ?>" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  </head>

  <body class="bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 transition-colors duration-300">
    <?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/navbar.php'; ?>

    <main class="max-w-6xl mx-auto px-6 py-10">
      <h1 class="text-4xl font-black mb-6">
        Yearly Sales
        <span class="text-emerald-600 underline decoration-orange-400">Report</span>
      </h1>

      <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow mb-10">
        <h2 class="text-lg font-black mb-4">
          Monthly Breakdown for
          <span id="chartYear" class="text-emerald-600"></span>
        </h2>
        <div class="w-full h-[250px] md:h-[350px]">
          <canvas id="salesChart"></canvas>
        </div>
      </div>

      <div class="grid md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow">
          <p class="text-sm text-gray-400">Total Sales</p>
          <h2 id="totalSales" class="text-3xl font-black text-emerald-600"><?php echo htmlspecialchars($currencySymbol); ?>0.000.00</h2>
        </div>
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow">
          <p class="text-sm text-gray-400">Total Expenses</p>
          <h2 id="totalExpenses" class="text-3xl font-black text-red-500"><?php echo htmlspecialchars($currencySymbol); ?>0.00</h2>
        </div>
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow">
          <p class="text-sm text-gray-400">Net Profit</p>
          <h2 id="netAmount" class="text-3xl font-black"><?php echo htmlspecialchars($currencySymbol); ?>0.00</h2>
        </div>
      </div>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <!-- YEARLY EXPORT -->
        <div class="flex flex-wrap gap-2">
          <span class="text-sm font-bold text-gray-500 dark:text-gray-400 w-full">Yearly Export</span>

          <button onclick="exportCSV('yearly')" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700">CSV</button>

          <button onclick="exportExcel('yearly')" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700">Excel</button>

          <button onclick="exportPDF('yearly')" class="px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700">PDF</button>
        </div>

        <!-- MONTHLY EXPORT -->
        <div class="flex flex-wrap gap-2">
          <span class="text-sm font-bold text-gray-500 dark:text-gray-400 w-full">Monthly Export</span>

          <button onclick="exportCSV('monthly')" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700">CSV</button>

          <button onclick="exportExcel('monthly')" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700">Excel</button>

          <button onclick="exportPDF('monthly')" class="px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700">PDF</button>
        </div>
      </div>
      <!-- ================= MONTHLY DETAIL SECTION ================= -->

      <div class="mt-16">
        <h2 class="text-3xl font-black mb-4">
          Monthly Detailed
          <span class="text-emerald-600 underline decoration-orange-400">Report</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-3 mb-6">
          <select onchange="loadMonthlyDetailReport()" id="monthSelect" class="w-full border rounded-xl px-3 py-2 text-sm bg-white dark:bg-gray-800 dark:border-gray-700">
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
          </select>

          <select onchange="loadMonthlyDetailReport()" id="monthYearSelect" class="w-full border rounded-xl px-3 py-2 text-sm bg-white dark:bg-gray-800 dark:border-gray-700">
            <?php $currentYear = date('Y'); for ($y = $currentYear; $y >= $currentYear - 5; $y--) { echo "
            <option value=\"$y\">$y</option>
            "; } ?>
          </select>

          <!-- <button onclick="loadMonthlyDetailReport()" class="w-full md:w-auto px-4 py-2 text-sm font-black rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">Load Month</button> -->
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow">
            <p class="text-sm text-gray-400">Grand Sales</p>
            <h2 id="monthGrandSales" class="text-3xl font-black text-emerald-600">₱0.00</h2>
          </div>
          <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow">
            <p class="text-sm text-gray-400">Grand Expenses</p>
            <h2 id="monthGrandExpenses" class="text-3xl font-black text-red-500">₱0.00</h2>
          </div>
          <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow">
            <p class="text-sm text-gray-400">Grand Net</p>
            <h2 id="monthGrandNet" class="text-3xl font-black">₱0.00</h2>
          </div>
        </div>

        <div class="bg-white/20 dark:bg-gray-900/20 border border-white/10 dark:border-gray-700/10 backdrop-blur-xl rounded-3xl shadow-lg overflow-hidden">
          <div class="w-full overflow-x-auto hidden md:block">
            <table class="w-full min-w-[500px] text-left text-xs md:text-sm">
              <thead class="bg-gray-100/80 dark:bg-gray-900/60">
                <tr>
                  <th class="px-3 py-2 md:px-4 md:py-3">Date</th>
                  <th class="px-4 py-3 text-right">Sales</th>
                  <th class="px-4 py-3 text-right">Expenses</th>
                  <th class="px-4 py-3 text-right">Net</th>
                </tr>
              </thead>
              <tbody id="monthlyDetailBody"></tbody>
            </table>
          </div>
          <div id="monthlyDetailCards" class="space-y-4 p-4 md:hidden"></div>
        </div>
      </div>
      <?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/footer.php'; ?>
    </main>
    <!-- ✅ EXCEL -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style/dist/xlsx.bundle.js"></script>

    <!-- ✅ PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/navbar.js?v=<?= time(); ?>"></script>
    a
    <script>
      // ================= GLOBAL STORAGE =================
      let monthlyData = [];
      let yearlyData = [];

      // ================= CSV EXPORT =================
      window.exportCSV = async function (type = "monthly") {

        const data = type === "yearly"
          ? yearlyData
          : monthlyData;

        if (!data || !data.length) {
          alert("No data to export");
          return;
        }

        const currency =
          "<?php echo htmlspecialchars($currencySymbol); ?>";

        const systemName =
          "<?php echo addslashes($settings['system_name'] ?? 'Lalenz Foodies'); ?>";

        const reportTitle =
          type === "monthly"
            ? `Monthly Sales Report (${document.getElementById("monthSelect").selectedOptions[0].text} ${document.getElementById("monthYearSelect").value})`
            : `Yearly Sales Report (${document.getElementById("monthYearSelect").value})`;

        const generatedAt = new Date().toLocaleString();

        // ================= CSV ESCAPE =================
        const escapeCSV = (value) => {

          if (value === null || value === undefined)
            return "";

          const str = String(value);

          return /[",\n]/.test(str)
            ? `"${str.replace(/"/g, '""')}"`
            : str;

        };

        // ================= CURRENCY FORMAT =================
        const money = (value) =>
          `${currency}${Number(value || 0).toLocaleString("en-US", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          })}`;

        let totalSales = 0;
        let totalExpenses = 0;
        let totalNet = 0;

        data.forEach(row => {
          totalSales += Number(row.sales || 0);
          totalExpenses += Number(row.expenses || 0);
          totalNet += Number(row.net || 0);
        });

        const rows = [];

        // ==========================================
        // BRANDING
        // ==========================================

        rows.push([systemName]);
        rows.push(["Order Management System"]);
        rows.push([reportTitle]);
        rows.push([`Generated: ${generatedAt}`]);

        rows.push([]);
        rows.push([]);

        // ==========================================
        // SUMMARY
        // ==========================================

        rows.push(["SUMMARY"]);
        rows.push(["Metric", "Amount"]);

        rows.push([
          "Total Sales",
          money(totalSales)
        ]);

        rows.push([
          "Total Expenses",
          money(totalExpenses)
        ]);

        rows.push([
          "Net Profit",
          money(totalNet)
        ]);

        rows.push([]);
        rows.push([]);

        // ==========================================
        // DETAILED REPORT
        // ==========================================

        rows.push(["DETAILED REPORT"]);

        rows.push(
          type === "monthly"
            ? ["Date", "Sales", "Expenses", "Net"]
            : ["Month", "Sales", "Expenses", "Net"]
        );

        data.forEach((r) => {

          const sales = Number(r.sales || 0);
          const expenses = Number(r.expenses || 0);
          const net = Number(r.net || 0);

          rows.push(
            type === "monthly"
              ? [
                  r.date,
                  money(sales),
                  money(expenses),
                  money(net)
                ]
              : [
                  r.month_name,
                  money(sales),
                  money(expenses),
                  money(net)
                ]
          );

        });

        // ==========================================
        // GRAND TOTAL
        // ==========================================

        rows.push([]);

        rows.push([
          "TOTAL",
          money(totalSales),
          money(totalExpenses),
          money(totalNet)
        ]);

        rows.push([]);
        rows.push([
          `Generated by ${systemName}`
        ]);

        // ==========================================
        // BUILD CSV
        // ==========================================

        const csvContent = rows
          .map(row => row.map(escapeCSV).join(","))
          .join("\n");

        // UTF-8 BOM for Excel compatibility
        const BOM = "\uFEFF";

        downloadFile(
          BOM + csvContent,
          getFileName(type, "csv"),
          "text/csv;charset=utf-8;"
        );

      };

      // ================= EXCEL EXPORT =================
      window.exportExcel = async function(type = "monthly") {

        const data = type === "yearly" ? yearlyData : monthlyData;
        const currency = <?php echo json_encode($currencySymbol); ?>;

        if (!data || !data.length) {
          alert("No data to export");
          return;
        }

        const systemName =
          <?php echo json_encode($settings['system_name'] ?? 'Lalenz Foodies'); ?>;

        const reportTitle =
          type === "monthly"
            ? `Monthly Sales Report (${document.getElementById("monthSelect").selectedOptions[0].text} ${document.getElementById("monthYearSelect").value})`
            : `Yearly Sales Report (${document.getElementById("monthYearSelect").value})`;

        const generatedAt = new Date().toLocaleString();

        let totalSales = 0;
        let totalExpenses = 0;
        let totalNet = 0;

        data.forEach(row => {
          totalSales += Number(row.sales || 0);
          totalExpenses += Number(row.expenses || 0);
          totalNet += Number(row.net || 0);
        });

        // =====================================
        // DETAIL SHEET
        // =====================================

        const rows = [
          [systemName],
          ["Order Management System"],
          [reportTitle],
          [`Generated: ${generatedAt}`],
          [],
          ["SUMMARY"],
          ["Total Sales", totalSales],
          ["Total Expenses", totalExpenses],
          ["Net Profit", totalNet],
          [],
          [],
          ["DETAILED REPORT"],
          type === "monthly"
            ? ["Date", "Sales", "Expenses", "Net"]
            : ["Month", "Sales", "Expenses", "Net"]
        ];

        data.forEach(r => {

          rows.push(
            type === "monthly"
              ? [
                  r.date,
                  Number(r.sales || 0),
                  Number(r.expenses || 0),
                  Number(r.net || 0)
                ]
              : [
                  r.month_name,
                  Number(r.sales || 0),
                  Number(r.expenses || 0),
                  Number(r.net || 0)
                ]
          );

        });

        rows.push([]);

        rows.push([
          "TOTAL",
          totalSales,
          totalExpenses,
          totalNet
        ]);

        const workbook = XLSX.utils.book_new();
        const worksheet = XLSX.utils.aoa_to_sheet(rows);

        // =====================================
        // MERGES
        // =====================================

        worksheet["!merges"] = [
          { s: { r: 0, c: 0 }, e: { r: 0, c: 3 } },
          { s: { r: 1, c: 0 }, e: { r: 1, c: 3 } },
          { s: { r: 2, c: 0 }, e: { r: 2, c: 3 } },
          { s: { r: 3, c: 0 }, e: { r: 3, c: 3 } },
          { s: { r: 5, c: 0 }, e: { r: 5, c: 3 } },
          { s: { r: 11, c: 0 }, e: { r: 11, c: 3 } }
        ];

        // =====================================
        // ROW HEIGHTS
        // =====================================

        worksheet["!rows"] = [
          { hpt: 42 },
          { hpt: 24 },
          { hpt: 28 },
          { hpt: 20 }
        ];

        // =====================================
        // COLUMN WIDTHS
        // =====================================

        worksheet["!cols"] = [
          { wch: 25 },
          { wch: 20 },
          { wch: 20 },
          { wch: 20 }
        ];

        // =====================================
        // STYLES
        // =====================================

        const titleStyle = {
          font: {
            bold: true,
            sz: 24,
            color: { rgb: "059669" }
          },
          alignment: {
            horizontal: "center",
            vertical: "center"
          }
        };

        const subtitleStyle = {
          font: {
            bold: true,
            sz: 14
          },
          alignment: {
            horizontal: "center"
          }
        };

        const sectionStyle = {
          font: {
            bold: true,
            sz: 13,
            color: { rgb: "FFFFFF" }
          },
          fill: {
            fgColor: { rgb: "059669" }
          },
          alignment: {
            horizontal: "center"
          }
        };

        const tableHeaderStyle = {
          font: {
            bold: true,
            color: { rgb: "FFFFFF" }
          },
          fill: {
            fgColor: { rgb: "2563EB" }
          },
          alignment: {
            horizontal: "center"
          }
        };

        if (worksheet["A1"]) worksheet["A1"].s = titleStyle;
        if (worksheet["A2"]) worksheet["A2"].s = subtitleStyle;
        if (worksheet["A3"]) worksheet["A3"].s = subtitleStyle;

        if (worksheet["A6"]) worksheet["A6"].s = sectionStyle;
        if (worksheet["A12"]) worksheet["A12"].s = sectionStyle;

        ["A13", "B13", "C13", "D13"].forEach(cell => {
          if (worksheet[cell]) {
            worksheet[cell].s = tableHeaderStyle;
          }
        });

        // =====================================
        // CURRENCY FORMAT
        // =====================================

        const range = XLSX.utils.decode_range(worksheet["!ref"]);

        for (let R = 0; R <= range.e.r; R++) {

          for (let C = 1; C <= 3; C++) {

            const ref = XLSX.utils.encode_cell({
              r: R,
              c: C
            });

            const cell = worksheet[ref];

            if (
              cell &&
              typeof cell.v === "number"
            ) {
              cell.z = `"${currency}" #,##0.00`;
            }
          }
        }

        // =====================================
        // SUMMARY SHEET
        // =====================================

        const summarySheet = XLSX.utils.aoa_to_sheet([
          [systemName],
          ["Order Management System"],
          [],
          [reportTitle],
          [],
          ["Metric", "Amount"],
          ["Total Sales", totalSales],
          ["Total Expenses", totalExpenses],
          ["Net Profit", totalNet],
          [],
          [`Generated: ${generatedAt}`]
        ]);

        summarySheet["!cols"] = [
          { wch: 25 },
          { wch: 20 }
        ];

        summarySheet["!merges"] = [
          { s: { r: 0, c: 0 }, e: { r: 0, c: 1 } },
          { s: { r: 1, c: 0 }, e: { r: 1, c: 1 } },
          { s: { r: 3, c: 0 }, e: { r: 3, c: 1 } }
        ];

        if (summarySheet["A1"]) summarySheet["A1"].s = titleStyle;
        if (summarySheet["A2"]) summarySheet["A2"].s = subtitleStyle;
        if (summarySheet["A4"]) summarySheet["A4"].s = subtitleStyle;

        ["A6", "B6"].forEach(cell => {
          if (summarySheet[cell]) {
            summarySheet[cell].s = tableHeaderStyle;
          }
        });

        ["B7", "B8", "B9"].forEach(cell => {
          if (summarySheet[cell]) {
            summarySheet[cell].z = `"${currency}" #,##0.00`;
          }
        });

        // =====================================
        // WORKBOOK PROPERTIES
        // =====================================

        workbook.Props = {
          Title: reportTitle,
          Subject: "Sales Report",
          Author: systemName,
          Company: systemName,
          CreatedDate: new Date()
        };

        // =====================================
        // APPEND SHEETS
        // =====================================

        XLSX.utils.book_append_sheet(
          workbook,
          summarySheet,
          "Summary"
        );

        XLSX.utils.book_append_sheet(
          workbook,
          worksheet,
          "Detailed Report"
        );

        // =====================================
        // EXPORT
        // =====================================

        XLSX.writeFile(
          workbook,
          getFileName(type, "xlsx")
        );

      };

      // ================= PDF EXPORT =================
      window.exportPDF = async function(type = "monthly") {
        if (!window.jspdf) {
          alert("jsPDF not loaded");
          return;
        }

        let data = type === "yearly" ? yearlyData : monthlyData;
        if (!data.length) {
          alert("No data to export");
          return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // ================= HEADER =================
        const logoUrl = "<?= $logo; ?>";
        const img = await loadImageBase64(logoUrl);

        if (img) {
          doc.addImage(img, "PNG", 14, 10, 25, 25);
        }

        const monthText = document.getElementById("monthSelect")?.selectedOptions[0]?.text || "";
        const yearText = document.getElementById("monthYearSelect")?.value || new Date().getFullYear();

        doc.setFontSize(18);
        doc.text("<?= $settings['system_name'] ?? 'Lalenz Foodies' ?>", 45, 20);

        doc.setFontSize(12);
        doc.text(type === "monthly" ? `Monthly Report - ${monthText} ${yearText}` : `Yearly Report - ${yearText}`, 45, 28);

        // ================= CHART =================
        const chartImg = await createChartImage(data, type);

        let startTableY = 40;

        if (chartImg) {
          const imgProps = doc.getImageProperties(chartImg);
          const pdfWidth = 180;
          const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

          doc.addImage(chartImg, "PNG", 14, 40, pdfWidth, pdfHeight);

          // Move table BELOW chart
          startTableY = 40 + pdfHeight + 10;
        }

        // ================= TOTALS =================
        const totals = data.reduce(
          (acc, row) => {
            acc.sales += Number(row.sales || 0);
            acc.expenses += Number(row.expenses || 0);
            acc.net += Number(row.net || 0);
            return acc;
          },
          { sales: 0, expenses: 0, net: 0 }
        );

        // ================= TABLE =================
        const tableBody = data.map((r) =>
          type === "monthly"
            ? [r.date, formatNum(r.sales), formatNum(r.expenses), formatNum(r.net)]
            : [r.month_name, formatNum(r.sales), formatNum(r.expenses), formatNum(r.net)]
        );

        // Add grand total row
        tableBody.push([
          "TOTAL",
          formatNum(totals.sales),
          formatNum(totals.expenses),
          formatNum(totals.net),
        ]);

        doc.setFontSize(11);
        doc.setFont(undefined, "bold");
        doc.text(`Total Revenue: ${formatNum(totals.sales)}`, 14, startTableY);
        doc.text(`Total Expenses: ${formatNum(totals.expenses)}`, 14, startTableY + 7);
        doc.text(`Net Income: ${formatNum(totals.net)}`, 14, startTableY + 14);

        startTableY += 22;

        if (doc.autoTable) {
          doc.autoTable({
            head: [["Date / Month", "Sales", "Expenses", "Net"]],
            body: tableBody,
            startY: startTableY,
            styles: {
              fontSize: 10,
            },
            headStyles: {
              fillColor: [16, 185, 129],
            },
            alternateRowStyles: {
              fillColor: [240, 240, 240],
            },
            didParseCell: function (data) {
              if (data.row.index === tableBody.length - 1) {
                data.cell.styles.fontStyle = "bold";
                data.cell.styles.fillColor = [220, 252, 231];
              }
            },
          });
        }

        // ================= FOOTER =================
        doc.setFontSize(9);
        doc.text(`Generated on ${new Date().toLocaleString()}`, 14, doc.internal.pageSize.height - 10);

        doc.save(getFileName(type, "pdf"));
      }

      // Helper: create chart image from data and type using Chart.js
      function createChartImage(data, type) {
        return new Promise((resolve) => {
          // Create hidden canvas
          const canvas = document.createElement("canvas");
          canvas.width = 800;
          canvas.height = 400;

          // Extract labels and datasets for chart
          const labels = type === "monthly" ? data.map((r) => r.date) : data.map((r) => r.month_name);
          const salesData = data.map((r) => Number(r.sales));
          const expensesData = data.map((r) => Number(r.expenses));
          const netData = data.map((r) => Number(r.net));

          // Create chart instance
          const ctx = canvas.getContext("2d");
          const chart = new Chart(ctx, {
            type: "line",
            data: {
              labels,
              datasets: [
                {
                  label: "Sales",
                  data: salesData,
                  borderColor: "rgba(16, 185, 129, 1)",
                  backgroundColor: "rgba(16, 185, 129, 0.2)",
                  fill: true,
                  tension: 0.3,
                },
                {
                  label: "Expenses",
                  data: expensesData,
                  borderColor: "rgba(255, 99, 132, 1)",
                  backgroundColor: "rgba(255, 99, 132, 0.2)",
                  fill: true,
                  tension: 0.3,
                },
                {
                  label: "Net",
                  data: netData,
                  borderColor: "rgba(54, 162, 235, 1)",
                  backgroundColor: "rgba(54, 162, 235, 0.2)",
                  fill: true,
                  tension: 0.3,
                },
              ],
            },
            options: {
              responsive: false,
              animation: false,
              plugins: {
                legend: {
                  position: "top",
                  labels: { font: { size: 12 } },
                },
              },
              scales: {
                x: {
                  ticks: {
                    maxRotation: 90,
                    minRotation: 45,
                    autoSkip: true,
                    maxTicksLimit: 10,
                  },
                },
                y: { beginAtZero: true },
              },
            },
          });

          // Wait for chart to render
          setTimeout(() => {
            chart.update();
            resolve(canvas.toDataURL("image/png"));
            chart.destroy();
          }, 300);
        });
      }

      // ================= HELPERS =================
      function loadImageBase64(url) {
        return new Promise((resolve) => {
          const img = new Image();
          img.crossOrigin = "Anonymous";
          img.onload = function () {
            const canvas = document.createElement("canvas");
            canvas.width = this.width;
            canvas.height = this.height;
            canvas.getContext("2d").drawImage(this, 0, 0);
            resolve(canvas.toDataURL("image/png"));
          };
          img.onerror = () => resolve(null);
          img.src = url;
        });
      }

      function downloadFile(content, filename, type) {
        const blob = new Blob([content], { type });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        a.click();
      }

      function getFileName(type, ext) {
        const month = document.getElementById("monthSelect")?.value || "";
        const year = document.getElementById("monthYearSelect")?.value || new Date().getFullYear();
        return type === "monthly" ? `monthly_${year}_${month}.${ext}` : `yearly_${year}.${ext}`;
      }

      function formatNum(num) {
        return `<?php echo $systemCurrency; ?>${Number(num).toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })}`;
      }
    </script>

    <script defer>
      const SYSTEM_CURRENCY = "<?php echo htmlspecialchars($systemCurrency); ?>";
      const CURRENCY_SYMBOL = <?php echo json_encode($currencySymbol); ?>;

      let salesChart = null;

      // ================= YEARLY =================
      async function loadYearlyReport(year = new Date().getFullYear()) {
        try {
          document.getElementById("chartYear").innerText = year;

          const res = await fetch(`./../../Script/get_sales_report.php?year=${year}`);
          const data = await res.json();

          yearlyData = data;

          let sales = 0,
            expenses = 0,
            net = 0;

          data.forEach((r) => {
            sales += Number(r.sales);
            expenses += Number(r.expenses);
            net += Number(r.net);
          });

          updateSummary(sales, expenses, net);
          renderChartDynamic(data);
        } catch (err) {
          console.error(err);
          alert("Failed to load yearly report");
        }
      }

      function formatCurrency(num) {
        return `${CURRENCY_SYMBOL}${parseFloat(num).toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })}`;
      }

      function updateSummary(sales, expenses, net) {
        document.getElementById("totalSales").innerText = formatCurrency(sales);
        document.getElementById("totalExpenses").innerText = formatCurrency(expenses);

        const netEl = document.getElementById("netAmount");
        netEl.innerText = formatCurrency(net);
        netEl.className = "text-3xl font-black " + (net < 0 ? "text-red-600" : "text-emerald-600");
      }

      function renderChartDynamic(data) {
        const ctx = document.getElementById("salesChart").getContext("2d");

        if (salesChart) salesChart.destroy();

        const isDark = document.documentElement.classList.contains("dark");

        const textColor = isDark ? "#e5e7eb" : "#374151";
        const gridColor = isDark ? "rgba(255,255,255,0.08)" : "rgba(0,0,0,0.05)";

        // ✅ Detect screen size
        const isMobile = window.innerWidth < 640;

        salesChart = new Chart(ctx, {
          type: "bar",
          data: {
            labels: data.map((r) => r.month_name),
            datasets: [
              {
                label: "Sales",
                data: data.map((r) => Number(r.sales)),
                backgroundColor: "rgba(16,185,129,0.8)",
                borderRadius: 6,
                maxBarThickness: isMobile ? 14 : 28,
              },
              {
                label: "Expenses",
                data: data.map((r) => Number(r.expenses)),
                backgroundColor: "rgba(239,68,68,0.8)",
                borderRadius: 6,
                maxBarThickness: isMobile ? 14 : 28,
              },
              {
                label: "Net",
                data: data.map((r) => Number(r.net)),
                backgroundColor: "rgba(59,130,246,0.8)",
                borderRadius: 6,
                maxBarThickness: isMobile ? 14 : 28,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,

            interaction: {
              mode: "index",
              intersect: false,
            },

            plugins: {
              legend: {
                position: isMobile ? "bottom" : "top",
                labels: {
                  color: textColor,
                  font: {
                    size: isMobile ? 10 : 12,
                    weight: "bold",
                  },
                  boxWidth: isMobile ? 12 : 18,
                },
              },
              tooltip: {
                callbacks: {
                  label: function (context) {
                    let value = context.raw || 0;
                    return `${context.dataset.label}: ${formatCurrency(value)}`;
                  },
                },
              },
            },

            scales: {
              x: {
                ticks: {
                  color: textColor,
                  maxRotation: isMobile ? 45 : 0,
                  minRotation: isMobile ? 30 : 0,
                  autoSkip: true,
                  maxTicksLimit: isMobile ? 6 : 12,
                },
                grid: {
                  display: false,
                },
              },
              y: {
                beginAtZero: true,
                ticks: {
                  color: textColor,
                  callback: function (value) {
                    return isMobile
                      ? formatCurrency(value).replace(" PHP", "") // shorter on mobile
                      : formatCurrency(value);
                  },
                },
                grid: {
                  color: gridColor,
                },
              },
            },
          },
        });
      }

      // ================= MONTHLY DETAIL =================
      async function loadMonthlyDetailReport() {
        const month = document.getElementById("monthSelect").value;
        const year = document.getElementById("monthYearSelect").value;

        // 🔥 sync yearly chart with selected year
        loadYearlyReport(year);

        const tbody = document.getElementById("monthlyDetailBody");
        const cards = document.getElementById("monthlyDetailCards");

        tbody.innerHTML = `<tr><td colspan="4" class="p-6 text-center">Loading...</td></tr>`;
        if (cards) {
          cards.innerHTML = `
            <div class="rounded-3xl border border-gray-200/70 dark:border-gray-700/70 bg-white/80 dark:bg-gray-950/70 p-5 text-center text-gray-500">
              Loading...
            </div>`;
        }

        try {
          const res = await fetch(`./../../Script/get_monthly_detail.php?month=${month}&year=${year}`);
          const data = await res.json();
          monthlyData = data; // Store for export

          if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-gray-400">No records found</td></tr>`;
            if (cards) {
              cards.innerHTML = `
                <div class="rounded-3xl border border-gray-200/70 dark:border-gray-700/70 bg-white/80 dark:bg-gray-950/70 p-5 text-center text-gray-500">
                  No records found
                </div>`;
            }
            updateMonthlyGrand(0, 0, 0);
            return;
          }

          let grandSales = 0,
            grandExpenses = 0,
            grandNet = 0;
          tbody.innerHTML = "";

          if (cards) {
            cards.innerHTML = "";
          }

          data.forEach((row) => {
            grandSales += Number(row.sales);
            grandExpenses += Number(row.expenses);
            grandNet += Number(row.net);

            const netClass = row.net < 0 ? "text-red-600" : "text-emerald-600";

            tbody.innerHTML += `
        <tr class="border-t dark:border-gray-800 text-xs md:text-sm">
          <td class="p-4 font-bold">${row.date}</td>
          <td class="p-4 text-right text-emerald-600 font-black">${formatCurrency(row.sales)}</td>
          <td class="p-4 text-right text-red-500 font-black">${formatCurrency(row.expenses)}</td>
          <td class="p-4 text-right font-black ${netClass}">${formatCurrency(row.net)}</td>
        </tr>`;

            if (cards) {
              cards.innerHTML += `
              <div class="rounded-[2rem] border border-white/10 dark:border-gray-700/20 bg-white/70 dark:bg-gray-950/70 shadow-lg shadow-black/5 p-4 backdrop-blur-xl">
                <div class="flex items-center justify-between gap-4 mb-3 text-sm text-gray-500 dark:text-gray-400">
                  <span class="font-bold">${row.date}</span>
                  <span class="font-black ${netClass}">${formatCurrency(row.net)}</span>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                  <div class="rounded-3xl bg-gray-50 dark:bg-gray-900 p-3">
                    <p class="text-xs text-gray-400">Sales</p>
                    <p class="font-black text-emerald-600">${formatCurrency(row.sales)}</p>
                  </div>
                  <div class="rounded-3xl bg-gray-50 dark:bg-gray-900 p-3">
                    <p class="text-xs text-gray-400">Expenses</p>
                    <p class="font-black text-red-500">${formatCurrency(row.expenses)}</p>
                  </div>
                </div>
              </div>`;
            }
          });

          updateMonthlyGrand(grandSales, grandExpenses, grandNet);
        } catch (error) {
          tbody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-red-500">Error loading data</td></tr>`;
          updateMonthlyGrand(0, 0, 0);
        }
      }

      function updateMonthlyGrand(sales, expenses, net) {
        document.getElementById("monthGrandSales").innerText = formatCurrency(sales);
        document.getElementById("monthGrandExpenses").innerText = formatCurrency(expenses);

        const netEl = document.getElementById("monthGrandNet");
        netEl.innerText = formatCurrency(net);
        netEl.className = "text-3xl font-black " + (net < 0 ? "text-red-600" : "text-emerald-600");
      }

      // ================= DROPDOWN & THEME =================
      document.addEventListener("DOMContentLoaded", () => {
        const yearSelect = document.getElementById("monthYearSelect");

        // set default year
        const currentYear = new Date().getFullYear();
        yearSelect.value = currentYear;

        // load initial
        loadYearlyReport(currentYear);

        // 🔥 WHEN YEAR CHANGES → UPDATE CHART
        yearSelect.addEventListener("change", () => {
          const selectedYear = yearSelect.value;
          loadYearlyReport(selectedYear);
        });
        loadMonthlyDetailReport();

      });
    </script>
  </body>
</html>
