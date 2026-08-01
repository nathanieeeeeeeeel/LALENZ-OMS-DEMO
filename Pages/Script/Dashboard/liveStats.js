async function loadStats() {
    /*
    * Dashboard statistics loader with date filtering.
    * Fetches sales, expenses, net income, and order count
    * from backend APIs based on selected filter date.
    * Also retrieves system currency settings for formatting.
    * Dynamically updates dashboard UI cards including
    * net profit/loss indicators and styling states.
    * Used in Pages/admin/dashboard.php analytics section.
    */
    const filterDate = document.getElementById("filterDate")?.value.trim();

    try {
        const response = await fetch(`./../Script/get_stats.php?date=${filterDate}`);
        const data = await response.json();
        
        const resp = await fetch(`./../Script/get_orders.php?date=${filterDate}`);
        const ordersData = await resp.json();
        // console.log("ORDERS DATA", ordersData);

        const currencyRes = await fetch('./../../currencies.json');
        const currencyData = await currencyRes.json();
        const systemCurrency = await fetch(`./../Script/get_system.php`);
        const systemData = await systemCurrency.json();

        const currencySymbol = currencyData[systemData.currency_code]?.symbol || systemData.currency_code || "₱"; // Default to ₱ if not set

        if (data.status === "success") {

            const cleanNumber = (v) => {
                if (v === null || v === undefined || v === "") return 0;
                const str = String(v).replace(/,/g, "").trim();
                const num = Number(str);
                return Number.isFinite(num) ? num : 0;
            };

            const salesValue = cleanNumber(data.sales);
            const expensesValue = cleanNumber(data.expenses);
            const netValue = cleanNumber(data.net);

            const formatMoney = (value) => {
                return `${currencySymbol}${value.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            };

            document.getElementById("stat-sales").innerText = formatMoney(salesValue);
            document.getElementById("stat-orders").innerText = ordersData && ordersData.length ? parseFloat(ordersData.length)?.toLocaleString() : 0;
            document.getElementById("stat-expenses").innerText = formatMoney(expensesValue);

            // Update net
            const netElement = document.getElementById("stat-net");
            const netLabel = document.querySelector("#net-card p");
            const netCard = document.getElementById("net-card");
            const netIndicator = document.getElementById("net-indicator");
            if (netElement) {
                netElement.innerText = formatMoney(netValue);
                netElement.classList.remove("text-red-600", "text-emerald-600", "text-blue-600");
            }

            if (netLabel) netLabel.innerHTML = "Net Profit / Loss " + (filterDate ? "<i class=\"fa-solid fa-filter fa-xl\"></i>" : "");

            // reset net-card gradient and hover classes to match other cards
            const netCardBaseClasses = [
                "bg-gradient-to-br",
                "from-red-500/40",
                "from-emerald-500/40",
                "from-blue-500/40",
                "via-transparent",
                "to-transparent",
                "hover:from-red-400/70",
                "hover:from-emerald-400/70",
                "hover:from-blue-400/70",
                "hover:to-red-500/40",
                "hover:to-emerald-500/40",
                "hover:to-blue-500/40"
            ];

            if (netCard) {
                netCard.classList.remove(...netCardBaseClasses);
                netCard.classList.add("bg-gradient-to-br", "via-transparent", "to-transparent", "transition-all", "duration-500");
            }

            const netIndicatorBaseClass = "absolute top-0 left-0 h-1 w-0 group-hover:w-full transition-all duration-500";

            if (netIndicator) {
                if (netValue < 0) {
                    netIndicator.className = `${netIndicatorBaseClass} bg-red-600`;
                    if (netCard) {
                        netCard.classList.add("from-red-500/40", "hover:from-red-400/70", "hover:to-red-500/40");
                    }
                } else if (netValue > 0) {
                    netIndicator.className = `${netIndicatorBaseClass} bg-emerald-500`;
                    if (netCard) {
                        netCard.classList.add("from-emerald-500/40", "hover:from-emerald-400/70", "hover:to-emerald-500/40");
                    }
                } else {
                    netIndicator.className = `${netIndicatorBaseClass} bg-blue-500`;
                    if (netCard) {
                        netCard.classList.add("from-blue-500/40", "hover:from-blue-400/70", "hover:to-blue-500/40");
                    }
                }
            } else if (netCard) {
                if (netValue < 0) netCard.classList.add("from-red-500/40", "hover:from-red-400/70", "hover:to-red-500/40");
                else if (netValue > 0) netCard.classList.add("from-emerald-500/40", "hover:from-emerald-400/70", "hover:to-emerald-500/40");
                else netCard.classList.add("from-blue-500/40", "hover:from-blue-400/70", "hover:to-blue-500/40");
            }
        }
    } catch (e) {
        console.error("Stats Filter Error:", e);
    }

    loadDailyStats();
}
