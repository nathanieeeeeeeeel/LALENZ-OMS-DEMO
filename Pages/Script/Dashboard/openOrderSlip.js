// Load menu items
fetch('./../../Pages/Script/api/items/get.php?t=' + Date.now())
    .then(res => res.json())
    .then(data => {
        data.items.forEach(item => {
            menuMap[item.id] = item.name;
        });
    })
    .catch(err => console.error("Failed to load items:", err));

async function openOrderSlip(orderId, thisElement) {
    try {
        // ===============================
        // 0. HIDE THE CONTEXT MENU UPON CLICK
        // ===============================
        await document.getElementById("contextMenu")
        .classList.add("hidden");
        // ===============================
        // 1. SYSTEM + CURRENCY DATA
        // ===============================
        const [systemRes, currencyRes] = await Promise.all([
            fetch(`./../../Pages/Script/get_system.php`),
            fetch(`./../../currencies.json`)
        ]);

        const systemInfo = await systemRes.json();
        const currencyData = await currencyRes.json();

        const systemCurrency = systemInfo.currency_code || "PHP";
        // NOTE: statusLabels is now defined in Functions.js and available globally

        const currencySymbol =
            currencyData?.[systemCurrency]?.symbol ||
            currencyData?.[systemCurrency]?.code ||
            systemCurrency ||
            "₱";

        // ===============================
        // 2. FETCH ORDER DETAILS (ONLY ONCE)
        // ===============================
        const dataset = thisElement?.dataset || {};

        const res = await fetch(
            `./../../Pages/Script/get_order_details.php?id=${dataset.id || orderId}`
        );

        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);

        const data = await res.json();

        console.log("Order details:", data);

        // ===============================
        // 3. BASIC INFO
        // ===============================
        document.getElementById("modalCustName").textContent =
            data.customer_name || "Unknown Customer";

        document.getElementById("modalCustID").textContent =
            `Order ID: #${String(data.id).padStart(3, "0")}`;

        document.getElementById("modalCustID").dataset.orderId = data.id;

        document.getElementById("orderDate").innerHTML = data.is_scheduled
            ? `Order Date: ${data.order_datetime}<br>Scheduled for: ${data.scheduled_datetime}`
            : `Order Date: ${data.order_datetime}`;

        document.getElementById("modalCustPhone").textContent =
            data.customer_phone || "--";

        document.getElementById("modalCustAddr").textContent =
            data.customer_address || "--";

        // ===============================
        // 4. TOTALS
        // ===============================
        const setMoney = (id, value, sign = "") => {
            document.getElementById(id).textContent =
                `${sign}${currencySymbol}${parseFloat(value || 0)
                    .toLocaleString("en-PH", { minimumFractionDigits: 2 })}`;
        };

        setMoney("modalSubtotal", data.subtotal);

        if (parseFloat(data.discount) > 0) {
            setMoney("modalDiscount", data.discount, "- ");
            document.getElementById("modalDiscountRow").classList.remove("hidden");
        } else {
            setMoney("modalDiscount", 0, "- ");
            document.getElementById("modalDiscountRow").classList.add("hidden");
        }

        if (parseFloat(data.delivery_fee) > 0) {
            setMoney("modalDelivery", data.delivery_fee);
            document.getElementById("modalDeliveryRow").classList.remove("hidden");
        } else {
            setMoney("modalDelivery", 0);
            document.getElementById("modalDeliveryRow").classList.add("hidden");
        }

        if (parseFloat(data.advance_payment) > 0) {
            setMoney("modalAdvance", data.advance_payment, "- ");
            document.getElementById("modalAdvanceRow").classList.remove("hidden");
        } else {
            setMoney("modalAdvance", 0, "- ");
            document.getElementById("modalAdvanceRow").classList.add("hidden");
        }

        setMoney("modalTotal", data.grand_total);

        // ===============================
        // 5. NOTE + PAYMENT
        // ===============================
        document.getElementById("modalNote").textContent =
            (data.order_note && data.order_note.trim() !== "" &&
                data.order_note !== "No special instructions.")
                ? data.order_note
                : "--";

        document.getElementById("modalPayment").textContent =
            data.payment_method || "N/A";

        // ===============================
        // 6. ITEMS
        // ===============================
        const historyModal = document.getElementById("modalOrderHistory");

        let parsedItems = [];
        try {
            parsedItems = typeof data.order_items === "string"
                ? JSON.parse(data.order_items)
                : data.order_items || [];
        } catch {
            parsedItems = [];
        }

        historyModal.dataset.items = JSON.stringify(parsedItems);
        historyModal.dataset.payment = data.payment_method || "";
        historyModal.dataset.isScheduled = data.is_scheduled;
        historyModal.dataset.scheduled_datetime = data.scheduled_datetime || "";

        if (parsedItems.length > 0) {

            const gridClass =
                parsedItems.length === 1 ? "grid-cols-1" :
                parsedItems.length === 2 ? "grid-cols-2" :
                "grid-cols-3";

            $("#modalOrderHistory")
                .removeClass()
                .addClass(`grid lg:${gridClass} md:${gridClass} sm:grid-cols-1 gap-2`);

            historyModal.innerHTML = parsedItems.map((item, index) => {

                const qty = parseInt(item.qty) || 1;

                const totalRowAmount = item.amnt
                    ? parseFloat(item.amnt)
                    : parseFloat(item.price || 0) * qty;

                const unitPrice = totalRowAmount / qty;

                const displayName =
                    item.name ||
                    menuMap[item.id] ||
                    `Item #${item.id || "Unknown"}`;

                return `
                    <div class="order-item flex justify-between items-center p-4 bg-gray-100 dark:bg-gray-800 rounded-2xl">
                        <div>
                            <p class="text-xs font-bold text-gray-400">Item #${index + 1}</p>
                            <p class="font-bold">${displayName}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-emerald-600">
                                ${currencySymbol}${totalRowAmount.toFixed(2)}
                            </p>
                            <span class="text-xs text-gray-400" data-qty="${qty}" data-unit-price="${unitPrice.toFixed(2)}">
                                (${currencySymbol}${unitPrice.toFixed(2)} × ${qty})
                            </span>
                        </div>
                    </div>
                `;
            }).join("");

        } else {
            historyModal.innerHTML = `<p class="text-center text-gray-400">No items</p>`;
        }

        // ===============================
        // 7. OPEN MODAL (AFTER DATA READY)
        // ===============================
        const modal = document.getElementById("customerModal");

        modal.classList.remove("hidden");
        document.documentElement.classList.add("overflow-hidden");

        document.querySelectorAll(".statusDropdown").forEach(el => {
            el.dataset.orderId = orderId;
            el.selectedIndex = 0; // Reset dropdown to default
        });

        // Store full data ONCE (no second API call)
        Object.entries(data).forEach(([key, value]) => {
            modal.dataset[key] = value ?? "";
        });

    } catch (e) {
        console.error("Error opening modal:", e);
        alert("Could not load order details.");
    }
}