async function loadSystemInfo() {
    $.get(window.systemInfoUrl, function (data) {
        const systemInfo = data;

        console.log("System Info:", systemInfo);

        if (data) {
            document.getElementById("navSystemName").innerText =
                systemInfo?.system_name || "LALENZ";

            Array.from(document.getElementsByClassName("systemCurrency"))
                .forEach((element) => {
                    element.innerText = systemInfo.currency_code || "₱";
                });
        }
    });
}


window.onload = async () => {
    await loadSystemInfo();

    const today = new Date().toLocaleDateString("en-CA");
    const thirtyDaysAgo = new Date(
        Date.now() - 30 * 24 * 60 * 60 * 1000
    ).toISOString().split("T")[0];

    document.getElementById("chartStartDate").value = thirtyDaysAgo;
    document.getElementById("chartEndDate").value = today;
    document.getElementById("filterDate").value = today;

    await loadMenuItems();
    await loadStats();
    await loadRecentOrders();

    // Build initial order snapshot
    const res = await fetch("../../Pages/Script/get_orders.php");
    const orders = await res.json();

    orders.forEach((order) => {
        lastOrderSnapshot[order.id] = order.status;
    });

    setInterval(checkOrderUpdates, 3000);
};
