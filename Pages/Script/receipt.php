<?php
/*
 * Printable receipt page for order details.
 * Loads system settings via get_system.php and fetches order data via get_orders.php.
 * Displays formatted receipt with customer info, item breakdown, and payment calculations.
 * Includes interactive payment controls for discount, advance, delivery, and cash inputs.
 * Used as the final order receipt/print view in the POS system.
*/
include './../Script/get_system.php';
$orderId = $_GET['order_id'] ?? '000';

$receiptTitle = $settings['receipt_title'] ?? "Lalenz Online Shop";
$receiptSubtitle = $settings['receipt_subtitle'] ?? "";
$receiptAddress = $settings['receipt_address'] ?? "";
$receiptFooter = $settings['receipt_footer'] ?? "Thank you!";

$receiptWidth = $settings['receipt_width'] ?? "58mm";

$currencyData = json_decode(
  file_get_contents($_SERVER['DOCUMENT_ROOT'] . $systemFolder . "/currencies.json"),
  true
);

$systemCurrency = $settings['currency_code'] ?? "PHP";

$currencySymbol = $currencyData[$systemCurrency]['symbol']
  ?? $currencyData[$systemCurrency]['code']
  ?? "₱";
?>

<!DOCTYPE html>
<html oncontextmenu="return false;" class="select-none" lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $receiptTitle; ?> | Receipt #<?php echo $orderId; ?></title>
<link rel="icon" type="image/x-icon" href="<?php echo $systemLogo; ?>">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.tailwindcss.com/"></script>

<style>

@page {
    size: <?php echo $receiptWidth; ?> auto;
    margin: 0;
}

.receipt {
    width: <?php echo $receiptWidth; ?>;
    padding: 4mm;
    font-family: monospace;
    font-size: 13px; /* increased */
    line-height: 1.2; /* more breathing room */
}

.center { text-align:center; }
.right { text-align:right; }

.text-black { font-weight: 900; }

table { width:100%; border-collapse:collapse; }
td { padding:1px 0; }

hr { border-top:1px dotted #000; margin:4px 0; }

@media print {
    input, button { display:none !important; }
    .no-print { display:none; !!important; }
}

</style>

</head>

<body>

<div class="receipt">

<!-- HEADER -->
<div class="center">
    <div>
        <h1 style="font-weight: 900;"><?php echo $receiptTitle; ?></h1>
        <h2 style="font-weight: 700;"><?php echo $receiptSubtitle; ?></h2>
    </div>
    <small style="font-weight: 700;"><?php echo nl2br($receiptAddress); ?></small><br><br>

    <span style="font-weight: 800;">ORDER SLIP</span><br><br>

    <div class="text-left" style="margin-bottom: 10.5px; margin-top: 1px;">
        Name: <span id="customer-name"></span><br>
        Address: <span id="customer-address"></span><br>
        Payment: <span id="payment-method"></span>
    </div>
    <div id="scheduled-section" style="display:none;">
        <hr>

        <div style="padding:4px 0;">
            <strong>** SCHEDULED ORDER **</strong><br>
            <span id="scheduled-datetime"></span>
        </div>

        <hr>
    </div>
</div>
<hr>

<table id="items-table"></table>

<hr>

<table>

<tr id="originalTotal" class="hidden">
    <td>SUBTOTAL</td>
    <td class="right" id="original-total"></td>
</tr>

<tr id="discount-row" class="hidden">
    <td>DISCOUNT</td>
    <td class="right" id="discount-amount"></td>
</tr>

<tr id="credit-row" class="hidden">
    <td>ADVANCE</td>
    <td class="right" id="credit-amount"></td>
</tr>

<tr id="delivery-row" class="hidden">
    <td>DELIVERY</td>
    <td class="right" id="delivery-amount"></td>
</tr>

<tr>
    <td><b>TOTAL</b></td>
    <td class="right" id="total"></td>
</tr>

<tr id="cash-row" class="hidden">
    <td>CASH</td>
    <td class="right" id="cash-display"></td>
</tr>

<tr id="change-row" class="hidden">
    <td>CHANGE</td>
    <td class="right" id="change"></td>
</tr>

</table>

<hr>

<div class="center">
    <div class="flex flex-row justify-between">
        <span style="font-weight: 700;" id="order-date"></span>
        <span style="font-weight: 800;" id="order-id" class="ml-auto">#</span><br><br>
    </div>
    <?php echo nl2br($receiptFooter); ?>
</div>

</div>

<div class="no-print max-w-md mx-auto mt-4">

  <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 space-y-4">

    <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
      💳 Payment Controls
    </h2>

    <!-- GRID -->
    <div class="grid grid-cols-2 gap-3">

      <!-- DISCOUNT -->
      <div>
        <label class="text-[10px] font-semibold text-gray-500 uppercase">Discount</label>
        <input id="discount-input" type="number" min="0" step="0.01" disabled
          class="w-full mt-1 px-3 py-2 text-sm border rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed"
          placeholder="0.00">
      </div>

      <!-- ADVANCE -->
      <div>
        <label class="text-[10px] font-semibold text-gray-500 uppercase">Advance</label>
        <input id="credit-input" type="number" min="0" step="0.01" disabled
          class="w-full mt-1 px-3 py-2 text-sm border rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed"
          placeholder="0.00">
      </div>

      <!-- DELIVERY -->
      <div>
        <label class="text-[10px] font-semibold text-gray-500 uppercase">Delivery Fee</label>
        <input id="delivery-input" type="number" min="0" step="0.01" disabled
          class="w-full mt-1 px-3 py-2 text-sm border rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed"
          placeholder="0.00">
      </div>

      <!-- CASH -->
      <div>
        <label class="text-[10px] font-semibold text-gray-500 uppercase">Cash</label>
        <input id="cash-input" type="number" min="0" step="0.01"
          class="w-full mt-1 px-3 py-2 text-sm border rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none"
          placeholder="0.00">
      </div>

    </div>

    <!-- ACTION BUTTONS -->
    <div class="grid grid-cols-2 gap-3 mt-4">

    <button onclick="window.print()"
        class="bg-green-500 hover:bg-green-600 text-white text-sm py-2 rounded-xl font-bold shadow">
        PRINT
    </button>

    <button onclick="window.close()"
        class="bg-red-500 hover:bg-red-600 text-white text-sm py-2 rounded-xl font-bold shadow">
        CLOSE
    </button>

    </div>

  </div>

</div>

<script>

let menuMap = {};

$(async function () {

    const currencySymbol = "<?php echo $currencySymbol; ?>";

    const format = n =>
        currencySymbol + (parseFloat(n || 0)).toFixed(2);

    let originalTotal = 0;

    /* =========================================
       LOAD ITEMS FROM DATABASE API
    ========================================= */
    try {

        const itemsRes = await fetch(
            '<?php echo $systemFolder; ?>/Pages/Script/api/items/get.php?t=<?php echo time(); ?>'
        );

        const itemsData = await itemsRes.json();

        (itemsData.items || []).forEach(item => {
            menuMap[item.id] = {
                name: item.name,
                price: parseFloat(item.price || 0)
            };
        });

    } catch (err) {
        console.error("Failed loading menu items:", err);
    }

    /* =========================================
       LOAD ORDER
    ========================================= */
    try {

        const response = await fetch(
            './get_orders.php?t=<?php echo time(); ?>'
        );

        const orders = await response.json();

        const orderId = new URLSearchParams(location.search).get('order_id');

        const order = orders.find(o => o.id == orderId);

        if (!order) {
            alert("Order not found");
            return;
        }

        $('#order-id').text("#" + order.id);

        const orderDateTime =
            order.order_datetime ||
            (
                (order.order_date || "") + " " +
                (order.order_time || "")
            );

        $('#order-date').text(
            moment(orderDateTime).format('l LT')
        );

        $('#customer-name').text(order.customer_name);
        $('#customer-address').text(order.customer_address);
        $('#payment-method').text(order.payment_method || "Cash");
        
        const scheduledDatetime = order.scheduled_datetime || "";
        console.log('Order Receipt Info');
        console.log(order);

        if (order.status.toLowerCase() !== "delivered" && order.is_scheduled == true && scheduledDatetime !== null) {

            $('#scheduled-datetime').text(
                scheduledDatetime
                    ? scheduledDatetime
                    : '-'
            );

            $('#scheduled-section').show();

        } else {

            $('#scheduled-section').hide();

        }

        const savedDiscount = parseFloat(order.discount || 0) || 0;
        const savedDelivery = parseFloat(order.delivery_fee || 0) || 0;
        const savedAdvance = parseFloat(order.advance_payment || 0) || 0;
        const savedSubtotal = order.subtotal !== undefined && order.subtotal !== null
            ? parseFloat(order.subtotal)
            : null;

        const items = JSON.parse(order.order_items || "[]");

        $('#items-table').empty();

        let total = 0;

        items.forEach(item => {

            const qty = parseFloat(item.qty || 1);

            const dbItem = menuMap[item.id] || {};

            const itemName = dbItem.name || item.name || "Unknown Item";

            /*
             * OLD orders may only have amnt
             * NEW orders may have price
             */

            let lineTotal = 0;
            let unitPrice = 0;

            if (item.price) {

                unitPrice = parseFloat(item.price);
                lineTotal = unitPrice * qty;

            } else {

                lineTotal = parseFloat(item.amnt || 0);
                unitPrice = qty > 0 ? lineTotal / qty : 0;
            }

            total += lineTotal;

            $('#items-table').append(`
                <tr>
                    <td>
                        ${itemName}<br>
                        ${qty} x ${format(unitPrice)}
                    </td>

                    <td class="right">
                        ${format(lineTotal)}
                    </td>
                </tr>
            `);

        });

        originalTotal = savedSubtotal && !isNaN(savedSubtotal) ? savedSubtotal : total;

        $('#discount-input').val(savedDiscount.toFixed(2));
        $('#delivery-input').val(savedDelivery.toFixed(2));
        $('#credit-input').val(savedAdvance.toFixed(2));

        updateTotal();

    } catch (err) {

        console.error(err);

        alert("Failed to load order.");

    }

    /* =========================================
       TOTAL LOGIC
    ========================================= */
    function updateTotal() {

        const discount = parseFloat($('#discount-input').val()) || 0;
        const credit = parseFloat($('#credit-input').val()) || 0;
        const delivery = parseFloat($('#delivery-input').val()) || 0;
        const cash = parseFloat($('#cash-input').val()) || 0;

        const finalTotal =
            Math.max(
                originalTotal - discount + delivery - credit,
                0
            );

        const change = cash - finalTotal;

        $('#original-total').text(format(originalTotal));

        $('#discount-amount').text("-" + format(discount));

        $('#credit-amount').text("-" + format(credit));

        $('#delivery-amount').text(format(delivery));

        $('#total').text(format(finalTotal));

        $('#cash-display').text(format(cash));

        $('#change').text(
            format(cash >= finalTotal ? change : 0)
        );

        $('#originalTotal').toggle(
            discount > 0 ||
            credit > 0 ||
            delivery > 0
        );

        $('#discount-row').toggle(discount > 0);

        $('#credit-row').toggle(credit > 0);

        $('#delivery-row').toggle(delivery > 0);

        $('#cash-row').toggle(cash > 0);

        $('#change-row').toggle(
            cash > 0 &&
            cash >= finalTotal
        );
    }

    $('#discount-input,#credit-input,#delivery-input,#cash-input')
        .on('input', updateTotal);

});

/* =========================================
   QUICK CASH
========================================= */
$('.quick-cash').on('click', function () {

    const val = parseFloat($(this).data('val')) || 0;

    $('#cash-input')
        .val(val)
        .trigger('input');

});

</script>

</body>
</html>