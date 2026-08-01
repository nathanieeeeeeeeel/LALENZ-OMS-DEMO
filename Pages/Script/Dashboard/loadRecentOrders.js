async function loadRecentOrders(page = 1) {

    // 1. Load system config (currency code, etc.)
    const systemRes = await fetch(`./../Script/get_system.php`);
    const systemData = await systemRes.json();

    const systemCurrency = systemData.currency_code || "PHP";

    // 2. Load currencies.json (single source of truth)
    const currencyRes = await fetch('./../../currencies.json');
    const currencyData = await currencyRes.json();

    const currency = currencyData[systemCurrency];

    // 3. Currency display fallback
    const currencySymbol =
        currency?.symbol ||
        currency?.code ||
        systemCurrency ||
        "₱";

    // Grab filters
    const date = document.getElementById("filterDate").value;
    const status = document.getElementById("filterStatus").value;
    const paymentMethod = document.getElementById("filterByPaymentMethod").value;
    const desktopSearch = document.getElementById("searchCustomer");
    const mobileSearch = document.getElementById("searchCustomerMobile");
    const searchValue = (desktopSearch?.value.trim() || mobileSearch?.value.trim() || "");
    const searchQuery = searchValue.toLowerCase();
    const sortBy = document.getElementById("sortBy").value;
    const tbody = document.getElementById("orders-tbody");
    const emptyMessage = searchQuery ? "No matching orders found." : "No orders found.";

    tbody.innerHTML = `
    <tr>
        <td colspan="4" class="py-10 text-center text-gray-400 italic">
            <div class="flex flex-col items-center gap-3">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-300 animate-spin"></i>
                Loading orders...
            </div>
        </td>
    </tr>
    `;

    try {
        const res = await fetch(`./../../Pages/Script/get_orders.php?date=${encodeURIComponent(date)}&status=${encodeURIComponent(status)}&payment_method=${encodeURIComponent(paymentMethod)}`);
        let orders = await res.json();

        // Apply search filter
        if (searchQuery) {
            orders = orders.filter(order =>
                (order.customer_name || "").toLowerCase().includes(searchQuery)
            );
        }

        if (!Array.isArray(orders) || orders.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="4" class="py-10 text-center text-gray-400 italic">
                    <div class="flex flex-col items-center gap-3">
                        <i class="fas fa-box text-4xl text-gray-300"></i>
                        No ${searchQuery.length ? `matching orders` : "orders"} found.
                    </div>
                </td>
            </tr>
            `;
            const mobileList = document.getElementById("orders-mobile-list");
            if (mobileList) mobileList.innerHTML = "";

            ["prevBtn", "nextBtn", "firstPageBtn", "lastPageBtn"]
                .forEach(id => document.getElementById(id).disabled = true);

            ["currentPageNum", "totalPagesNum", "totalItems", "startItem", "endItem"]
                .forEach(id => document.getElementById(id).textContent = "0");

            renderPageNumbers([], 10, 1);
            return;
        }

        // Sorting
        orders = applySorting(orders, sortBy);

        const rowsPerPage = 10;
        const totalPages = Math.ceil(orders.length / rowsPerPage);

        page = Math.max(1, Math.min(page, totalPages));

        const startIndex = (page - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, orders.length);

        const paginatedOrders = orders.slice(startIndex, endIndex);

        const rowsHtml = paginatedOrders.map(order => {
            const items = order.order_items ? JSON.parse(order.order_items) : [];
            const maxItems = 3;
            const visibleItems = items.slice(0, maxItems);

            const itemSummary = visibleItems.map(i => {
                const name = i.name || (i.id && menuMap[i.id]) || `Item #${i.id || "Unknown"}`;
                return `
                    <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-semibold mr-1.5 mb-1.5 border border-gray-200/50 dark:border-gray-700/50 whitespace-nowrap">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold mr-1">${i.qty}×</span> ${name}
                    </span>
                `;
            }).join("");

            const remaining = items.length - maxItems;
            const summaryHTML = itemSummary + (
                remaining > 0
                    ? `
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50 text-xs font-semibold mb-1.5 whitespace-nowrap">
                            +${remaining} more...
                        </span>
                    `
                    : ""
            );

            const statusDisplayName = getStatusDisplayName(order.status);
            const statusClass = getStatusClasses(order.status);

            return `
                <tr id="order-row-${order.id}" data-order-id="${order.id}" data-order-date="${order.order_datetime}" onclick="openOrderSlip('${order.id}', this)"
                    class="group cursor-pointer transition-all duration-200
                    bg-white dark:bg-gray-900/20
                    hover:bg-gray-300/20 dark:hover:bg-gray-800/20
                    border-b border-gray-300/50 dark:border-gray-700">

                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[11px] text-gray-400 font-mono font-bold">
                                #${String(order.id).padStart(3, "0")}
                            </span>
                            <div class="text-[15px] font-semibold text-gray-900 dark:text-white group-hover:text-emerald-600 transition-colors">
                                ${order.customer_name}
                            </div>
                            <div class="text-[11px] text-gray-400 mt-0.5">
                                ${order.is_scheduled == 1
                                    ? `<span class="text-purple-500 font-medium">Scheduled</span> • ${order.scheduled_datetime}`
                                    : `${order.order_datetime}`
                                }
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex flex-wrap items-center max-w-[340px]">
                            ${summaryHTML || '<span class="text-xs text-gray-400 italic">No items</span>'}
                        </div>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <span class="status px-3 py-1 rounded-lg text-[11px] font-semibold border ${statusClass}">
                            ${statusDisplayName}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-right">
                        <div class="text-[15px] font-semibold text-gray-900 dark:text-white tracking-tight" data-grand-total="${order.grand_total}">
                            ${currencySymbol} ${parseFloat(order.grand_total).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </div>
                    </td>
                </tr>
            `;
        }).join("");

        const cardsHtml = paginatedOrders.map(order => {

    const items = order.order_items ? JSON.parse(order.order_items) : [];

    const maxItems = 4;

    const itemSummary = (() => {

        if (!items.length) {
            return `<span class="text-gray-400 text-sm">No items</span>`;
        }

        const badges = items
            .slice(0, maxItems)
            .map(i => {

                const name =
                    i.name ||
                    (i.id && menuMap[i.id]) ||
                    `Item #${i.id || "Unknown"}`;

                return `
                    <span class="inline-flex items-center rounded-full bg-gray-200 dark:bg-gray-800 px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-200">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold mr-1">${i.qty}×</span> ${name}
                    </span>
                `;
            })
            .join("");

        const remaining = items.length - maxItems;

        return badges + (
            remaining > 0
                ? `
                    <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/40 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                        +${remaining} more...
                    </span>
                `
                : ""
        );

    })();

    const statusDisplayName = getStatusDisplayName(order.status);
    const statusClass = getStatusClasses(order.status);

    return `
        <div id="order-card-${order.id}"
            onclick="openOrderSlip('${order.id}', this)"
            class="group cursor-pointer rounded-[2rem] border border-white/10 dark:border-gray-700/10 bg-white dark:bg-gray-900/80 p-5 shadow-lg shadow-gray-200/20 dark:shadow-black/20 transition hover:-translate-y-1 backdrop-blur-xl">

            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-emerald-500">
                        #${String(order.id).padStart(3, "0")}
                    </p>

                    <p class="mt-3 text-base font-black text-gray-900 dark:text-white uppercase">
                        ${order.customer_name}
                    </p>

                    <p class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                        ${
                            order.is_scheduled == 1
                                ? `<span class="text-purple-500 font-medium">Scheduled</span> • ${order.scheduled_date} ${order.scheduled_time}`
                                : `${order.order_datetime}`
                        }
                    </p>
                </div>

                <div class="text-right">

                    <div class="lg:text-2xl md:text-2xl sm:text-md text-sm font-black text-gray-900 dark:text-white">
                        ${currencySymbol}
                        ${parseFloat(order.grand_total).toLocaleString("en-US", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}
                    </div>

                    <span class="status mt-2 inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-semibold ${statusClass}">
                        ${statusDisplayName}
                    </span>

                </div>

            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                ${itemSummary}
            </div>

        </div>
    `;

}).join("");

        tbody.innerHTML = rowsHtml;
        tbody.querySelectorAll("tr[data-order-id]").forEach(row => {
            row.addEventListener("contextmenu", e => {
                showShortcut(e, row);
            });
        });

        const mobileList = document.getElementById("orders-mobile-list");
        if (mobileList) mobileList.innerHTML = cardsHtml;

        // Pagination UI
        document.getElementById("currentPageNum").textContent = page;
        document.getElementById("totalPagesNum").textContent = totalPages;
        document.getElementById("totalItems").textContent = orders.length;
        document.getElementById("startItem").textContent = startIndex + 1;
        document.getElementById("endItem").textContent = endIndex;

        document.getElementById("firstPageBtn").disabled = page === 1;
        document.getElementById("prevBtn").disabled = page === 1;
        document.getElementById("nextBtn").disabled = page === totalPages;
        document.getElementById("lastPageBtn").disabled = page === totalPages;

        document.getElementById("prevBtn").onclick = () => loadRecentOrders(Math.max(1, page - 1));
        document.getElementById("nextBtn").onclick = () => loadRecentOrders(Math.min(totalPages, page + 1));
        document.getElementById("firstPageBtn").onclick = () => loadRecentOrders(1);
        document.getElementById("lastPageBtn").onclick = () => loadRecentOrders(totalPages);

        renderPageNumbers(orders, rowsPerPage, page);

    } catch (e) {
        console.error(e);
        tbody.innerHTML = `
        <tr>
            <td colspan="4" class="py-10 text-center text-red-400">
                Error connecting to order database.
            </td>
        </tr>
        `;
    }
}