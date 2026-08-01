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
    <title><?php echo htmlspecialchars($systemName); ?> - New Order</title>
    <link rel="icon" href="<?= $systemFolder; ?>/Assets/logo.png" type="image/x-icon" />
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
    <!-- Tailwind Elements CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tw-elements/dist/css/tw-elements.min.css" rel="stylesheet" />
    <script>
      tailwind.config = {
        darkMode: "class", // Enable dark mode with class toggle
      };
    </script>
    <link rel="icon" type="image/png" href="<?= $logo; ?>?v=<?= time(); ?>" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  </head>
  <body class="bg-gray-100 dark:bg-gray-950 text-gray-800 dark:text-gray-100 font-sans transition-colors duration-300">
    <!-- Navigation Bar (White Primary) -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/navbar.php'; ?>

    <main class="max-w-6xl mx-auto px-6 py-12">
      <div class="flex items-center gap-4 mb-8">
        <a href="javascript:history.back()" data-tippy-content="Go Back" class="text-gray-400 dark:text-gray-500 hover:text-emerald-500 text-sm font-bold transition">← BACK</a>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Create <span class="text-orange-500">New Order</span></h1>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-2">
        <!-- Left Side: Form -->
        <div class="lg:col-span-2 space-y-3">
          <!-- Customer Details -->
          <!-- CUSTOMER INFORMATION (TW ELEMENTS UPGRADED) -->
          <!-- CUSTOMER INFORMATION (LAYOUT FIXED VERSION) -->
          <section class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-emerald-500/40 via-transparent to-transparent">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
              <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                  <span class="w-2 h-6 bg-emerald-500 rounded-full"></span>
                  Customer Information
                </h2>

                <button type="button" onclick="openCustomerBook()" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition">
                  <i class="fas fa-book"></i>
                  Customer Book
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- FULL NAME -->
                <div>
                  <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 ml-1"> Full Name </label>
                  <input type="text" id="custName" class="w-full rounded-xl border border-gray-300/60 dark:border-gray-700/60 bg-transparent px-3 py-3 outline-none dark:text-white focus:border-emerald-500 transition" />
                </div>

                <!-- PHONE -->
                <div>
                  <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 ml-1"> Phone (e.g. 0912...) </label>
                  <input type="tel" id="custPhone" class="w-full rounded-xl border border-gray-300/60 dark:border-gray-700/60 bg-transparent px-3 py-3 outline-none dark:text-white focus:border-emerald-500 transition" />
                </div>
              </div>

              <!-- ADDRESS -->
              <div class="mt-5">
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 ml-1"> Complete Delivery Address </label>
                <textarea id="custAddr" rows="3" class="w-full rounded-xl border border-gray-300/60 dark:border-gray-700/60 bg-transparent px-3 py-3 outline-none resize-none dark:text-white focus:border-emerald-500 transition"></textarea>
              </div>

              <!-- NOTES -->
              <div class="mt-5">
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 ml-1"> Special Instructions / Notes </label>
                <input type="text" id="orderNote" class="w-full rounded-xl border border-gray-300/60 dark:border-gray-700/60 bg-transparent px-3 py-3 italic outline-none dark:text-white focus:border-orange-400 transition" />
              </div>

              <!-- PAYMENT -->
              <div class="mt-6">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-2 ml-1 uppercase"> Payment Method </label>

                <!-- TW ELEMENTS SELECT -->
                <div class="relative" data-te-select-wrapper-ref>
                  <select id="paymentMethod" onchange="handlePaymentChange()" data-te-select-init data-te-select-filter="true" data-te-select-placeholder="Select Payment Method" data-te-select-size="lg" class="w-full">
                    <option value="" disabled selected>Select Payment Method</option>
                    <option value="Cash">Cash</option>
                    <option value="GCash">GCash</option>
                    <option value="PayMaya">PayMaya</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Others">Others</option>
                  </select>
                </div>

                <!-- OTHER PAYMENT -->
                <div class="mt-4 hidden" id="otherPaymentWrapper">
                  <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 ml-1"> Specify Other Payment Method </label>
                  <input type="text" id="otherPayment" class="w-full rounded-xl border border-gray-300/60 dark:border-gray-700/60 bg-transparent px-3 py-3 outline-none text-sm dark:text-white" />
                </div>
              </div>
            </div>
          </section>

          <!-- PRICING ADJUSTMENTS -->
          <section class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-blue-500/40 via-transparent to-transparent">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
              <h2 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-6 flex items-center gap-2">
                <span class="w-2 h-6 bg-blue-500 rounded-full"></span>
                Pricing Adjustments
              </h2>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- DISCOUNT -->
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Discount</label>
                  <input type="number" id="discount" value="0" min="0" class="w-full rounded-xl border px-3 py-3 bg-transparent dark:text-white focus:border-blue-500 outline-none" />
                </div>

                <!-- DELIVERY FEE -->
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Delivery Fee</label>
                  <input type="number" id="deliveryFee" value="0" min="0" class="w-full rounded-xl border px-3 py-3 bg-transparent dark:text-white focus:border-blue-500 outline-none" />
                </div>

                <!-- ADVANCE PAYMENT -->
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Advance Payment</label>
                  <input type="number" id="advancePayment" value="0" min="0" class="w-full rounded-xl border px-3 py-3 bg-transparent dark:text-white focus:border-blue-500 outline-none" />
                </div>
              </div>
            </div>
          </section>

          <!-- SCHEDULE ORDER (TW ELEMENTS UPGRADED) -->
          <section class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-purple-500/40 via-transparent to-transparent transition-all duration-500">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
              <h2 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-6 flex items-center gap-2">
                <span class="w-2 h-6 bg-purple-500 rounded-full"></span>
                Scheduled Order (Optional)
              </h2>

              <div class="flex items-center gap-3 mb-6">
                <input type="checkbox" id="isScheduled" class="w-5 h-5 accent-purple-500" onchange="toggleSchedule()" />
                <label for="isScheduled" class="font-semibold text-gray-600 dark:text-gray-300"> Set specific delivery date & time </label>
              </div>

              <div id="scheduleFields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- DATE (NATIVE) -->
                <div class="relative" data-te-datepicker-init data-te-input-wrapper-init>
                  <input type="text" id="scheduleDate" class="peer block w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent px-3 py-2 outline-none focus:border-purple-500 dark:text-white" placeholder="Select a date" />
                </div>

                <!-- TIME (NATIVE) -->
                <div class="relative">
                  <input type="time" id="scheduleTime" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent px-3 py-2 outline-none focus:border-purple-500 dark:text-white" />
                </div>
              </div>
            </div>
          </section>

          <!-- Item Selector -->
          <section class="group relative p-[1px] rounded-3xl bg-gradient-to-br from-orange-500/40 via-transparent to-transparent transition-all duration-500">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
              <div class="absolute inset-0 opacity-0 transition duration-500 bg-gradient-to-br from-orange-500/10 via-transparent to-transparent pointer-events-none"></div>
              <h2 class="text-lg font-bold text-gray-700 dark:text-white mb-6 flex items-center gap-2 relative z-10">
                <span class="w-2 h-6 bg-orange-500 rounded-full"></span> Select Food & Drinks
                <button onclick="loadMenu()" class="ml-auto text-sm text-emerald-600 hover:text-emerald-700">Refresh Menu</button>
              </h2>
              <div class="flex flex-col md:flex-row gap-3 items-end relative z-10">
                <div class="flex-1 w-full">
                  <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Choose Item</label>
                  <select id="menuSelect" data-te-select-init data-te-select-filter="true" data-te-select-placeholder="Select Item" data-te-select-size="lg" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800 dark:text-gray-200 transition">
                    <option value="">Loading items...</option>
                  </select>
                </div>
                <div class="w-full md:w-24">
                  <label class="text-[10px] font-bold text-gray-400 uppercase ml-1"> Quantity </label>

                  <div class="flex items-center h-11 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <!-- Minus -->
                    <button type="button" onclick="changeQty(-1, this)" disabled class="h-full px-3 flex items-center justify-center text-gray-600 dark:text-gray-300 disabled:cursor-not-allowed enabled:hover:bg-gray-200 dark:enabled:hover:bg-gray-700 transition">−</button>

                    <!-- Input -->
                    <input type="number" id="itemQty" value="1" min="1" class="w-full h-full text-center bg-transparent outline-none text-sm text-gray-800 dark:text-gray-200 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />

                    <!-- Plus -->
                    <button type="button" onclick="changeQty(1, this)" class="h-full px-3 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">+</button>
                  </div>
                </div>

                <!-- TOTAL -->
                <div class="w-full md:w-24">
                  <label class="text-[10px] font-bold text-gray-400 uppercase ml-1"> Total </label>

                  <div class="flex items-center h-11 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <input type="text" id="itemTotal" value="0.00" readonly class="w-full h-full text-center bg-transparent outline-none text-sm text-gray-800 dark:text-gray-200" />
                  </div>
                </div>
              </div>
              <button onclick="addItem()" class="w-full mt-2 md:w-auto bg-emerald-600 text-white px-8 py-3 rounded-xl font-black hover:bg-emerald-700 transition shadow-md">ADD</button>
            </div>
          </section>
        </div>

        <!-- Right Side: Sticky Summary -->
        <div class="lg:col-span-1">
          <aside class="sticky top-24 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
              <h3 class="text-xl font-bold text-gray-900 dark:text-white">Order Summary</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Review the order before placing it.</p>
            </div>

            <!-- Cart -->
            <div class="p-6">
              <div id="summaryList" class="space-y-3 max-h-[340px] overflow-y-auto custom-scrollbar">
                <div id="emptyCart" class="py-10 text-center text-gray-500 dark:text-gray-400 italic">Your cart is empty</div>
              </div>

              <!-- Price Breakdown -->
              <div id="breakdown_divider" style="display: none" class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-5 space-y-3">
                <div id="subtotalContainer" style="display: none" class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                  <span>Subtotal</span>
                  <span id="subtotalDisplay"> <?php echo htmlspecialchars($currencySymbol); ?>0.00 </span>
                </div>

                <div id="discountContainer" style="display: none" class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                  <span>Discount</span>
                  <span id="discountDisplay"> <?php echo htmlspecialchars($currencySymbol); ?>0.00 </span>
                </div>

                <div id="dfContainer" style="display: none" class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                  <span>Delivery Fee</span>
                  <span id="deliveryDisplay"> <?php echo htmlspecialchars($currencySymbol); ?>0.00 </span>
                </div>

                <div id="advPaymentContainer" style="display: none" class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                  <span>Advance Payment</span>
                  <span id="advanceDisplay"> <?php echo htmlspecialchars($currencySymbol); ?>0.00 </span>
                </div>
              </div>

              <!-- Total -->
              <div class="mt-6 pt-5 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <span class="text-lg font-semibold text-gray-700 dark:text-gray-200"> Total </span>

                <span id="totalDisplay" class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400"> <?php echo htmlspecialchars($currencySymbol); ?>0.00 </span>
              </div>

              <!-- Buttons -->
              <div class="mt-8 space-y-3">
                <button id="confirmOrderBtn" onclick="confirmOrder()" class="w-full h-12 rounded-xl bg-emerald-600 disabled:cursor-not-allowed disabled:bg-opacity-50 disabled:text-white/50 enabled:hover:bg-emerald-700 text-white font-semibold transition shadow-sm active:scale-[0.98]">Place Order</button>

                <button disabled onclick="downloadReceipt()" class="w-full h-11 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed enabled:hover:bg-gray-100 dark:enabled:hover:bg-gray-700 transition">Download Receipt</button>
              </div>
            </div>
          </aside>
        </div>
      </div>
      <!-- Remove this entirely -->
      <!-- <div id="receiptPreview" style="margin-top: 20px"></div> -->
      <?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/footer.php'; ?>
    </main>
    <!-- Production -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tippy.js@6/dist/tippy-bundle.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/tw-elements.umd.min.js"></script>
    <script src="./../../../Pages/Script/customer_book.js?t=<?= time(); ?>"></script>
    <script>
      let menuItems = [];
      let currentOrder = [];
      let systemCurrency = "<?php echo htmlspecialchars($currencySymbol); ?>"; // Get currency from PHP variable
      // 1. Fetch from JSON with new structure
      async function loadMenu() {
        try {
          const response = await fetch("<?= $systemFolder; ?>/Pages/Script/api/items/get.php");
          const data = await response.json();
          menuItems = data.items;

          const select = document.getElementById("menuSelect");

          select.innerHTML =
            `<option value="" disabled selected>Select Items</option>` +
            menuItems
              .sort((a, b) => a.name.localeCompare(b.name))
              .map((item) => {
                const isOutOfStock = Number(item.stock) <= 0;

                return `
                <option 
                  value="${item.id}" 
                  data-price="${item.price}"
                  ${isOutOfStock ? "disabled" : ""}
                >
                  ${item.name} - ${systemCurrency}${parseFloat(item.price).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                  })}
                  ${isOutOfStock ? " (Out of Stock)" : ""}
                </option>
              `;
              })
              .join("");

          // 🔥 IMPORTANT: destroy + re-init
          const instance = te.Select.getInstance(select);
          if (instance) {
            instance.dispose();
          }

          new te.Select(select);

          document.getElementById("itemTotal").value = "0.00";
          document.getElementById("itemQty").value = 0;
        } catch (err) {
          console.error("Error loading items.json:", err);
        }
      }

      // 1.1 Update item total when selection or quantity changes
      document.getElementById("menuSelect").addEventListener("change", function () {
        const qtyInput = document.getElementById("itemQty");

        const selectedId = parseInt(this.value);
        const product = menuItems.find((i) => i.id === selectedId);

        if (product) {
          qtyInput.value = 1;
          qtyInput.max = product.stock; // 🔥 LIMIT MAX QUANTITY HERE
        } else {
          qtyInput.value = 0;
          qtyInput.max = 1;
        }

        updateItemTotal();
      });
      document.getElementById("itemQty").addEventListener("input", updateItemTotal);

      // 2. Add item to the list
      function addItem() {
        const select = document.getElementById("menuSelect");
        const qtyInput = document.getElementById("itemQty");
        const totalInput = document.getElementById("itemTotal");

        const itemId = parseInt(select.value);
        const qtyRaw = parseInt(qtyInput.value || 1);

        const product = menuItems.find((i) => i.id === itemId);

        if (!product) {
          toastr.warning("Please select a valid item.", "Notice");
          return;
        }

        if (qtyRaw <= 0) {
          toastr.warning("Quantity must be at least 1.", "Notice");
          return;
        }

        // 🔥 SINGLE SOURCE OF TRUTH (clamp here)
        const qty = Math.max(1, Math.min(qtyRaw, product.stock));

        const existing = currentOrder.find((i) => i.id === itemId);
        const currentQty = existing ? existing.qty : 0;

        const totalRequested = currentQty + qty;

        if (totalRequested > product.stock) {
          toastr.error(`Only ${product.stock} available. You already added ${currentQty}.`, "Stock limit reached");
          return;
        }

        if (existing) {
          existing.qty = totalRequested;
        } else {
          currentOrder.push({
            id: product.id,
            name: product.name,
            price: product.price,
            qty: qty,
          });
        }

        updateSummary();
        toastr.success(`${product.name} added to order`, "Added");
        // Reset the selector
        let selectInstance = te.Select.getInstance(select);
        if (selectInstance) {
          selectInstance.dispose();
        }

        select.selectedIndex = 0;

        new te.Select(select);

        qtyInput.value = 1;
        totalInput.value = "0.00";
        totalInput.innerText = `0.00`;
      }

      // 3. Remove item from order
      function removeItem(index) {
        currentOrder.splice(index, 1);
        updateSummary();
      }

      // 4. Update the Summary Display
      function updateSummary() {
        const list = document.getElementById("summaryList");

        const subtotalEl = document.getElementById("subtotalDisplay");
        const discountEl = document.getElementById("discountDisplay");
        const deliveryEl = document.getElementById("deliveryDisplay");
        const advanceEl = document.getElementById("advanceDisplay");
        const totalEl = document.getElementById("totalDisplay");

        const format = (v) =>
          `${systemCurrency}${v.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })}`;

        let subtotal = 0;

        // Render items
        if (currentOrder.length === 0) {
          list.innerHTML = `
            <div class="text-center text-gray-500 italic py-6">
              Your cart is empty
            </div>
          `;
        } else {
          list.innerHTML = currentOrder
            .map((item, index) => {
              subtotal += item.price * item.qty;

              return `
                <div class="flex justify-between items-center bg-gray-100 dark:bg-gray-800 p-3 rounded-xl">

                  <div>
                    <div class="font-bold text-sm">${item.name}</div>
                    <div class="text-xs text-gray-500">
                      ${item.qty} × ${systemCurrency}${parseFloat(item.price).toFixed(2)}
                    </div>
                  </div>

                  <div class="text-right">
                    <div class="font-bold text-emerald-600 dark:text-emerald-400">
                      ${systemCurrency}${(item.price * item.qty).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                      })}
                    </div>

                    <div class="flex items-center gap-2 mt-1">
                      <button onclick="updateQty(${index}, -1)" class="px-2">−</button>
                      <span>${item.qty}</span>
                      <button onclick="updateQty(${index}, 1)" class="px-2">+</button>
                      <button onclick="removeItem(${index})" class="text-red-500 ml-2">×</button>
                    </div>
                  </div>

                </div>
                `;
            })
            .join("");
        }

        const discount = parseFloat(document.getElementById("discount")?.value || 0);
        const delivery = parseFloat(document.getElementById("deliveryFee")?.value || 0);
        const advance = parseFloat(document.getElementById("advancePayment")?.value || 0);

        const total = subtotal - discount + delivery - advance;

        subtotalEl.innerText = format(subtotal);
        discountEl.innerText = format(discount);
        deliveryEl.innerText = format(delivery);
        advanceEl.innerText = format(advance);
        totalEl.innerText = format(total);

        // Containers
        const subtotalContainer = document.getElementById("subtotalContainer");
        const discountContainer = document.getElementById("discountContainer");
        const dfContainer = document.getElementById("dfContainer");
        const advPaymentContainer = document.getElementById("advPaymentContainer");
        const divider = document.getElementById("breakdown_divider");

        // Check if any adjustment exists
        const hasAdjustments = discount > 0 || delivery > 0 || advance > 0;

        // Show subtotal + divider only when adjustments exist
        subtotalContainer.style.display = hasAdjustments ? "flex" : "none";
        divider.style.display = hasAdjustments ? "block" : "none";

        // Individual rows
        discountContainer.style.display = discount > 0 ? "flex" : "none";

        dfContainer.style.display = delivery > 0 ? "flex" : "none";

        advPaymentContainer.style.display = advance > 0 ? "flex" : "none";
      }

      ["discount", "deliveryFee", "advancePayment"].forEach((id) => {
        document.getElementById(id).addEventListener("input", updateSummary);
      });

      function updateQty(index, delta) {
        const item = currentOrder[index];
        if (!item) return;

        const product = menuItems.find((i) => i.id === item.id);
        if (!product) return;

        let newQty = item.qty + delta;

        // 🔥 minimum limit
        if (newQty <= 0) {
          currentOrder.splice(index, 1);
          updateSummary();
          return;
        }

        // 🔥 stock limit
        if (newQty > product.stock) {
          newQty = product.stock;
          toastr.warning(`Only ${product.stock} available in stock`, "Limit reached");
        }

        item.qty = newQty;

        updateSummary();
      }

      async function confirmOrder() {
        // Disable the button immediately to prevent multiple clicks
        const confirmBtn = document.getElementById("confirmOrderBtn");
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = "<i class='fas fa-spinner fa-spin animate-spin'></i> Processing...";

        const name = document.getElementById("custName").value.trim();
        const phone = document.getElementById("custPhone").value.trim();
        const address = document.getElementById("custAddr").value.trim();
        const noteValue = document.getElementById("orderNote").value.trim();
        const subtotal = currentOrder.reduce((sum, i) => sum + Number(i.price) * i.qty, 0);
        const discount = Number(document.getElementById("discount").value || 0);
        const delivery = Number(document.getElementById("deliveryFee").value || 0);
        const advance = Number(document.getElementById("advancePayment").value || 0);

        const total = subtotal - discount + delivery - advance;
        let paymentMethod = document.getElementById("paymentMethod").value;
        const otherPayment = document.getElementById("otherPayment").value.trim();
        let finalPayment = paymentMethod;

        const isScheduled = document.getElementById("isScheduled").checked;
        const rawDate = document.getElementById("scheduleDate").value;
        const scheduleTime = document.getElementById("scheduleTime").value;

        let scheduleDate = null;

        if (rawDate) {
          const parts = rawDate.includes("-")
            ? rawDate.split("-") // YYYY-MM-DD
            : rawDate.split("/"); // fallback

          if (parts[0].length === 4) {
            scheduleDate = rawDate; // already YYYY-MM-DD
          } else {
            const [day, month, year] = parts;
            scheduleDate = `${year}-${month.padStart(2, "0")}-${day.padStart(2, "0")}`;
          }
        }

        // Basic validation
        if (!name) {
          toastr.warning("Customer name is required.", "Notice");

          confirmBtn.disabled = false;
          confirmBtn.innerHTML = "Place Order";
          return;
        }

        if (currentOrder.length === 0) {
          toastr.warning("Please add at least one item.", "Notice");
          confirmBtn.disabled = false;
          confirmBtn.innerHTML = "Place Order";
          return;
        }

        if (!paymentMethod && paymentMethod !== "") {
          // toastr.warning("Please select a payment method.", "Notice");
          // return;
          paymentMethod = "Cash";
        }

        if (paymentMethod === "Others") {
          if (!otherPayment) {
            toastr.warning("Please specify the payment method.", "Notice");
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = "Place Order";
            return;
          }
          finalPayment = otherPayment;
        }

        // Scheduled validation
        if (isScheduled) {
          if (!scheduleDate || !scheduleTime) {
            toastr.warning("Please select delivery date and time.", "Notice");
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = "Place Order";
            return;
          }

          const selectedDateTime = new Date(`${scheduleDate}T${scheduleTime}:00`);
          const now = new Date();

          if (selectedDateTime <= now) {
            toastr.warning("Scheduled time must be in the future.", "Notice");
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = "Place Order";
            return;
          }
        }

        const orderData = {
          customer: name,
          phone,
          address,
          notes: noteValue,
          payment_method: finalPayment,
          total: total,
          items: currentOrder,

          discount: parseFloat(document.getElementById("discount").value || 0),
          delivery_fee: parseFloat(document.getElementById("deliveryFee").value || 0),
          advance_payment: parseFloat(document.getElementById("advancePayment").value || 0),

          is_scheduled: isScheduled ? 1 : 0,
          scheduled_date: isScheduled ? scheduleDate : null,
          scheduled_time: isScheduled ? scheduleTime : null,
        };

        try {
          const response = await fetch("./../../Script/save_order.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(orderData),
          });

          const result = await response.json();

          if (result.status === "success") {
            toastr.success(result.message, "Order Processed");

            setTimeout(() => {
              window.location.href = "./../dashboard.php";
            }, 2000);
          } else {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = "Place Order";
            toastr.error(result.message || "Something went wrong.", "Error");
          }
        } catch (error) {
          confirmBtn.disabled = false;
          confirmBtn.innerHTML = "Place Order";
          console.error("ERROR:", error);
          toastr.error("Database error. Check XAMPP/MySQL.", "Error");
        }
      }

      loadMenu();

      // Fetch the next predicted order ID
      async function getNextOrderId() {
        try {
          const response = await fetch("./../../Script/get_orders.php");
          const orders = await response.json();

          if (orders.length === 0) {
            return 1;
          }

          // Get the highest order ID and add 1
          const maxId = Math.max(...orders.map((o) => parseInt(o.id)));
          return maxId + 1;
        } catch (err) {
          console.error("Error fetching next order ID:", err);
          return "?";
        }
      }

      function updateItemTotal() {
        const select = document.getElementById("menuSelect");
        const qtyInput = document.getElementById("itemQty");
        const totalInput = document.getElementById("itemTotal");

        const selectedId = parseInt(select.value);
        const product = menuItems.find((i) => i.id === selectedId);

        if (!product) {
          totalInput.value = "0.00";
          return;
        }

        // 🔥 ALWAYS clamp here (single source of truth)
        let qty = parseInt(qtyInput.value || 1);

        qty = Math.max(1, Math.min(qty, product.stock));

        const total = qty * product.price;

        totalInput.value = total.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        });
      }

      document.getElementById("itemQty").addEventListener("input", function () {
        const select = document.getElementById("menuSelect");
        const product = menuItems.find((i) => i.id === parseInt(select.value));
        if (!product) return;

        let val = parseInt(this.value || 1);

        // 🔥 clamp FIRST
        val = Math.max(1, Math.min(val, product.stock));

        this.value = val;

        // 🔥 ALWAYS recompute from clamped value
        updateItemTotal();
      });
    </script>

    <style>
      .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
      }
      .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
      }
      .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
      }
    </style>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/navbar.js?v=<?= time(); ?>"></script>
    <script>
      //Toastr.js
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
      // Form functions
      function toggleSchedule() {
        const checkbox = document.getElementById("isScheduled");
        const fields = document.getElementById("scheduleFields");

        if (checkbox.checked) {
          fields.classList.remove("hidden");

          // Prevent selecting past dates
          const today = new Date().toISOString().split("T")[0];
          document.getElementById("scheduleDate").min = today;
        } else {
          fields.classList.add("hidden");
        }
      }

      function changeQty(delta, button) {
        const qtyInput = document.getElementById("itemQty");
        const select = document.getElementById("menuSelect");

        let currentQty = parseInt(qtyInput.value || 1);
        const itemSelected = select.value;

        if (!itemSelected) {
          toastr.warning("Please select an item first.", "Notice");
          return;
        }

        const product = menuItems.find((i) => i.id === parseInt(itemSelected));

        if (!product) {
          toastr.warning("Invalid item selected.", "Notice");
          return;
        }

        // STEP 1: apply delta
        currentQty += delta;

        // STEP 2: clamp value (IMPORTANT)
        const clampedQty = Math.max(1, Math.min(currentQty, product.stock));

        // STEP 3: update input with SAFE value
        qtyInput.value = clampedQty;

        // STEP 4: UI button state
        const container = button.parentElement;
        const minusBtn = container.querySelector('button[onclick*="-1"]');
        if (minusBtn) minusBtn.disabled = clampedQty <= 1;

        // STEP 5: ALWAYS use clamped value for totals
        updateItemTotal(clampedQty, product);

        if (currentQty > product.stock) {
          toastr.warning(`Only ${product.stock} in stock`, "Limit reached");
        }
      }

      function renderReceipt(orderId = null) {
        const customerName = document.getElementById("custName")?.value || "Walk-in Customer";
        const customerAddress = document.getElementById("custAddr")?.value || "N/A";
        const totalAmount = document.getElementById("totalDisplay").innerText;

        const now = new Date();
        const orderDate = now.toISOString().slice(0, 19).replace("T", " "); // YYYY-MM-DD HH:MM:SS

        const orderIdDisplay = orderId ? `#${String(orderId).padStart(3, "0")}` : "#(pending)";

        const itemsParsed = currentOrder.map((item) => ({
          name: item.name,
          qty: item.qty,
          unitPrice: `${systemCurrency}${parseFloat(item.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`,
          amnt: `${systemCurrency}${(item.price * item.qty).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`,
        }));

        // Scheduled text
        let scheduledText = "";
        const isScheduled = document.getElementById("isScheduled")?.checked;
        const rawDate = document.getElementById("scheduleDate")?.value; // For debugging

        let scheduleDate = null;

        if (rawDate) {
          const [month, day, year] = rawDate.split("/");
          scheduleDate = `${year}-${month.padStart(2, "0")}-${day.padStart(2, "0")}`; // Convert MM/DD/YYYY to YYYY-MM-DD
        }
        const scheduleTime = document.getElementById("scheduleTime")?.value;

        if (isScheduled && scheduleDate && scheduleTime) {
          const scheduledDateTime = new Date(`${scheduleDate}T${scheduleTime}`);
          const formattedDate = scheduledDateTime.toISOString().slice(0, 10); // YYYY-MM-DD
          const formattedTime = scheduledDateTime.toTimeString().slice(0, 8); // HH:MM:SS
          scheduledText = `${formattedDate} ${formattedTime}`;
        }

        const logo = new Image();
        logo.src = "./../../../Assets/logo.jpg";
        logo.crossOrigin = "Anonymous";

        logo.onload = () => {
          const DPI = 300;
          const inch = (v) => v * DPI;

          const canvas = document.createElement("canvas");
          canvas.width = inch(3.5);
          canvas.height = inch(6.5);

          const ctx = canvas.getContext("2d");
          ctx.textBaseline = "top";

          // ... all your canvas drawing code stays the same ...

          // Output: immediately download
          const link = document.createElement("a");
          link.download = `Receipt_${orderIdDisplay}.png`;
          link.href = canvas.toDataURL("image/png");
          link.click();
        };
      }

      // Fix download button - fetch order ID first
      async function downloadReceipt() {
        toastr.warning("Receipt download is temporarily disabled until order ID prediction is fully implemented.", "Notice");
        return; // Temporarily disable download
        const nextId = await getNextOrderId();
        renderReceipt(false, nextId); // false = download, pass predicted ID
      }

      // Update preview button to fetch order ID
      const previewButton = document.querySelector('button[onclick="renderReceipt(true)"]');
      if (previewButton) {
        previewButton.onclick = async function () {
          const nextId = await getNextOrderId();
          renderReceipt(true, nextId); // true = preview, pass predicted ID
        };
      }

      function handlePaymentChange() {
        const method = document.getElementById("paymentMethod").value;
        const wrapper = document.getElementById("otherPaymentWrapper");
        const input = document.getElementById("otherPayment");

        if (method === "Others") {
          wrapper.classList.remove("hidden");
        } else {
          wrapper.classList.add("hidden");
          input.value = "";
        }
      }
      document.addEventListener("DOMContentLoaded", () => {
        setTimeout(() => {
          if (window.te && te.initTE) {
            te.initTE({
              Input: true,
            });
          }
        }, 100);
      });
    </script>

    <!-- CUSTOMER BOOK MODAL -->
    <div id="customerBookModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center">
      <div class="w-full max-w-3xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700">
          <h3 class="text-xl font-bold">
            <i class="fas fa-book text-emerald-500 mr-2"></i>
            Customer Book
          </h3>

          <button onclick="closeCustomerBook()" class="text-2xl hover:text-red-500 transition">&times;</button>
        </div>

        <!-- Search -->
        <div class="p-5 border-b dark:border-gray-700">
          <input type="text" id="customerSearch" placeholder="Search customer..." oninput="filterCustomers()" class="w-full rounded-xl border px-4 py-3 bg-transparent outline-none" />
        </div>

        <!-- Customer List -->
        <div id="customerList" class="max-h-[500px] overflow-y-auto divide-y dark:divide-gray-800">
          <!-- AJAX inserts customers here -->
        </div>
      </div>
    </div>
  </body>
</html>
