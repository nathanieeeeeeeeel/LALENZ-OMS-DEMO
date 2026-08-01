/*
 * Financial Dashboard Chart Module
 *
 * Handles loading and rendering of the admin financial analytics chart.
 *
 * Responsibilities:
 * - Fetches system currency configuration from backend
 * - Retrieves daily financial stats (sales, expenses, net profit)
 *   within a selectable date range
 * - Renders a responsive Chart.js bar chart
 * - Supports:
 *   • Dark mode styling
 *   • Mobile-responsive layout adjustments
 *   • Currency-aware tooltips and labels
 *
 * Used in admin dashboard analytics section for financial visualization.
 */

let financialChart = null;
let globalCurrencySymbol = "₱"; // fallback

async function loadDailyStats() {
    const range = document.getElementById("salesRange")?.value || "30d";

    try {
        console.log(`Fetching stats for range: ${range}`);

        // Currency
        const systemRes = await fetch("./../../Pages/Script/get_system.php");
        const systemData = await systemRes.json();

        const currencyRes = await fetch("./../../currencies.json");
        const currencyData = await currencyRes.json();

        const currencyCode = systemData.currency_code || "PHP";
        globalCurrencySymbol =
            currencyData[currencyCode]?.symbol || globalCurrencySymbol;

        // Stats
        const response = await fetch(
            `./../../Pages/Script/get_daily_stats.php?range=${encodeURIComponent(range)}`
        );

        const result = await response.json();

        if (result.status === "success") {
            console.log("DATA DAILY STATS:", result.data);
            renderFinancialChart(result.data, range);
        }

    } catch (e) {
        console.error("Daily Stats Error:", e);
    }
}

function renderFinancialChart(dailyData = [], range = "30d") {
    const canvas = document.getElementById("financialChart");
    if (!canvas) return;

    if (financialChart instanceof Chart) {
        financialChart.destroy();
    }

    if (!dailyData.length) return;

    const ctx = canvas.getContext("2d");

    const isMobile = window.innerWidth < 640;
    const isDark = document.documentElement.classList.contains("dark");

    const textColor = isDark ? "#e5e7eb" : "#374151";
    const gridColor = isDark ? "rgba(255,255,255,0.08)" : "rgba(0,0,0,0.05)";

    // ✅ STEP 1: CLEAN DATA (NO MAP NEEDED)
    const cleanData = dailyData.map(d => ({
        date: d.date,
        sales: Number(d.sales) || 0,
        expenses: Number(d.expenses) || 0,
        net: (Number(d.sales) || 0) - (Number(d.expenses) || 0)
    }));

    // ✅ STEP 2: FIX DATE PARSING (IMPORTANT)
    const labels = cleanData.map(d => {

        // 🔥 FIX: make JS understand datetime properly
        const date = new Date(d.date);

        if (range === "24h") {
            return date.toLocaleDateString([], {
                hour: "2-digit",
                minute: "2-digit"
            });
        }

        return date.toLocaleDateString();
    });

    const sales = cleanData.map(d => d.sales);
    const expenses = cleanData.map(d => d.expenses);
    const net = cleanData.map(d => d.net);

    financialChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    label: "Sales",
                    data: sales,
                    backgroundColor: "rgba(16,185,129,0.85)",
                    borderRadius: 6,
                    maxBarThickness: isMobile ? 12 : 26
                },
                {
                    label: "Expenses",
                    data: expenses,
                    backgroundColor: "rgba(249,115,22,0.85)",
                    borderRadius: 6,
                    maxBarThickness: isMobile ? 12 : 26
                },
                {
                    label: "Net Profit",
                    data: net,
                    backgroundColor: net.map(n => n >= 0 ? "#3b82f6" : "#ef4444"),
                    borderRadius: 6,
                    maxBarThickness: isMobile ? 12 : 26
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: textColor }
                },
                tooltip: {
                    mode: "index",
                    intersect: false,
                    callbacks: {
                        label: function (context) {
                            const value = context.parsed.y;
                            const label = context.dataset.label;

                            return `${label}: ${globalCurrencySymbol}${value.toLocaleString("en-US", {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: textColor,
                        maxTicksLimit: range === "24h" ? 12 : 10
                    }
                },
                y: {
                    ticks: {
                        color: textColor,
                        callback: function(value) {
                            return globalCurrencySymbol + Number(value).toLocaleString("en-US", {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    grid: { color: gridColor }
                }
            },
            interaction: {
                mode: "index",
                intersect: false
            }
        }
    });
}