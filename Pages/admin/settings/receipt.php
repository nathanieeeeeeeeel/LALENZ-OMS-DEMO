<?php
// ----------------------
// Fallbacks for receipt settings
// ----------------------
$systemName      = $settings["system_name"] ?? "Lalenz Online Shop";
$systemCurrency  = strtoupper(trim($settings["currency_code"] ?? "PHP"));
$receiptTitle    = $settings["receipt_title"] ?? "Lalenz Foodies";
$receiptSubtitle = $settings["receipt_subtitle"] ?? "Registered as: LALENZ ONLINE SHOP";
$receiptAddress  = $settings["receipt_address"] ?? "";
$receiptFooter   = $settings["receipt_footer"] ?? "Thank you for your purchase!";
$receiptWidth    = $settings["receipt_width"] ?? "58mm";
?>

<div class="tab-section relative group p-8 rounded-[2rem]
    bg-white/80 dark:bg-gray-900/70
    border border-white/30 dark:border-gray-800
    shadow-xl shadow-black/5
    backdrop-blur-md overflow-hidden
    transition-all duration-300
    hover:-translate-y-1 hover:shadow-2xl"
    data-section="receipt">

  <!-- Glow accents -->
  <div class="absolute -top-24 -right-24 w-52 h-52 bg-emerald-500/10 blur-3xl rounded-full"></div>
  <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-400"></div>

  <h2 class="text-xl font-black mb-6 flex items-center gap-3">
    <span class="flex items-center justify-center w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-500">
      🧾
    </span>
    Receipt Template
  </h2>

  <div class="grid lg:grid-cols-2 gap-8 items-start">
    <!-- MOBILE PREVIEW BUTTON -->
    <div class="lg:hidden mb-4">

      <button
        type="button"
        id="openReceiptPreview"
        class="w-full py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold shadow-lg transition">

        🧾 Preview Receipt

      </button>

    </div>
    <form method="POST" class="space-y-6">

      <!-- BUSINESS NAME -->
      <div class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">
        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Business Name</label>
        <input type="text" name="receipt_title"
              value="<?= htmlspecialchars($receiptTitle) ?>"
              <?= !$isSuperAdminLoggedIn ? "disabled" : "" ?>
              class="mt-3 w-full rounded-xl px-4 py-3 bg-gray-200/70 dark:bg-gray-900/60 border border-gray-300 dark:border-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
      </div>

      <!-- SUBTITLE -->
      <div class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">
        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Subtitle</label>
        <input type="text" name="receipt_subtitle"
              value="<?= htmlspecialchars($receiptSubtitle) ?>"
              <?= !$isSuperAdminLoggedIn ? "disabled" : "" ?>
              class="mt-3 w-full rounded-xl px-4 py-3 bg-gray-200/70 dark:bg-gray-900/60 border border-gray-300 dark:border-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
      </div>

      <!-- BUSINESS ADDRESS -->
      <div class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">
        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Business Address</label>
        <textarea name="receipt_address" rows="3"
                  <?= !$isSuperAdminLoggedIn ? "disabled" : "" ?>
                  class="mt-3 w-full rounded-xl px-4 py-3 bg-gray-200/70 dark:bg-gray-900/60 border border-gray-300 dark:border-gray-700 resize-none disabled:opacity-50 disabled:cursor-not-allowed"><?= htmlspecialchars($receiptAddress) ?></textarea>
      </div>

      <!-- FOOTER -->
      <div class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">
        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Footer</label>
        <textarea name="receipt_footer" rows="3" placeholder="Enter footer text (supports multiple lines)"
                  <?= !$isSuperAdminLoggedIn ? "disabled" : "" ?>
                  class="mt-3 w-full rounded-xl px-4 py-3 bg-gray-200/70 dark:bg-gray-900/60 border border-gray-300 dark:border-gray-700 text-sm resize-none focus:ring-2 focus:ring-orange-400 disabled:opacity-50 disabled:cursor-not-allowed"><?= htmlspecialchars($receiptFooter) ?></textarea>
        <span class="text-xs text-gray-500 italic mt-1 flex items-center gap-1">
          <i class="fa-solid fa-info-circle text-lg"></i> Note: This affects both digital and printed order slips.
        </span>
      </div>

      <!-- RECEIPT PAPER WIDTH -->
      <div class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">
        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Receipt Paper Width</label>
        <select name="receipt_width"
                <?= !$isSuperAdminLoggedIn ? "disabled" : "" ?>
                class="mt-3 w-full rounded-xl px-4 py-3 bg-gray-200/70 dark:bg-gray-900/60 border border-gray-300 dark:border-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
          <option value="58mm" <?= $receiptWidth === '58mm' ? 'selected' : '' ?>>57/58mm (Small POS Printer)</option>
          <option value="80mm" <?= $receiptWidth === '80mm' ? 'selected' : '' ?>>80mm (Standard Thermal Printer)</option>
        </select>
      </div>

    </form>
    <!-- RIGHT: PREVIEW -->
    <div class="hidden lg:block sticky top-24">

      <div class="bg-gray-200 dark:bg-gray-950 rounded-3xl p-6 border border-gray-300 dark:border-gray-700">

        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-bold uppercase tracking-widest text-gray-500">
            Live Preview
          </h3>

          <span id="paperBadge"
            class="text-[10px] px-2 py-1 rounded-full bg-emerald-500 text-white font-bold">
            58mm
          </span>
        </div>

        <!-- THERMAL PAPER -->
        <div id="receiptPreviewWrapper"
          class="mx-auto flex justify-center transition-all duration-300">

          <div id="receiptPreview" style="width: <?= $receiptWidth === '58mm' ? '58mm' : '80mm' ?>;"
              class="bg-white text-black shadow-2xl p-[4mm] font-mono text-[13px] leading-tight">

            <!-- HEADER -->
            <div class="text-center">

              <h1 id="previewTitle"
                  class="font-black text-[16px] leading-tight">
                <?= htmlspecialchars($receiptTitle) ?>
              </h1>

              <div id="previewSubtitle"
                  class="font-bold text-[11px] mt-1">
                <?= htmlspecialchars($receiptSubtitle) ?>
              </div>

              <div id="previewAddress"
                  class="whitespace-pre-line text-[11px] mt-1">
                <?= htmlspecialchars($receiptAddress) ?>
              </div>

              <div class="mt-3 font-bold">
                ORDER SLIP
              </div>

              <div class="mt-2 text-left text-[11px]">
                Name: John Doe<br>
                Address: 1234 Main Street, Anytown, USA<br>
                Payment: Cash
              </div>
            </div>

            <!-- DIVIDER -->
            <div class="border-t border-dotted border-black my-2"></div>

            <!-- ITEMS -->
            <table class="w-full text-[12px]">

              <tr>
                <td>
                  Burger<br>
                  2 x <?php echo $currencySymbol; ?>120.00
                </td>
                <td class="text-right align-top">
                  <?php echo $currencySymbol; ?>240.00
                </td>
              </tr>

              <tr>
                <td class="pt-2">
                  Fries<br>
                  1 x <?php echo $currencySymbol; ?>80.00
                </td>
                <td class="text-right align-top pt-2">
                  <?php echo $currencySymbol; ?>80.00
                </td>
              </tr>

              <tr>
                <td class="pt-2">
                  Coke<br>
                  1 x <?php echo $currencySymbol; ?>45.00
                </td>
                <td class="text-right align-top pt-2">
                  <?php echo $currencySymbol; ?>45.00
                </td>
              </tr>

            </table>

            <!-- DIVIDER -->
            <div class="border-t border-dotted border-black my-2"></div>

            <!-- TOTALS -->
            <table class="w-full text-[12px]">

              <tr>
                <td>SUBTOTAL</td>
                <td class="text-right"><?php echo $currencySymbol; ?>365.00</td>
              </tr>

              <tr>
                <td>DELIVERY</td>
                <td class="text-right"><?php echo $currencySymbol; ?>50.00</td>
              </tr>

              <tr>
                <td class="font-black">TOTAL</td>
                <td class="text-right"><?php echo $currencySymbol; ?>415.00</td>
              </tr>

              <tr>
                <td>CASH</td>
                <td class="text-right"><?php echo $currencySymbol; ?>500.00</td>
              </tr>

              <tr>
                <td>CHANGE</td>
                <td class="text-right"><?php echo $currencySymbol; ?>85.00</td>
              </tr>

            </table>

            <!-- DIVIDER -->
            <div class="border-t border-dotted border-black my-2"></div>

            <!-- FOOTER -->
            <div class="text-center text-[11px]">

              <div class="flex justify-between mb-2">
                <span class="font-black"><?php echo date("n/j/Y g:i A"); ?></span>
                <span class="font-black">#0001</span>
              </div>

              <div id="previewFooter"
                  class="whitespace-pre-line">
                <?= htmlspecialchars($receiptFooter) ?>
              </div>

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MOBILE RECEIPT PREVIEW MODAL -->
<div id="receiptPreviewModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">

  <div class="relative w-full max-w-md bg-gray-200 dark:bg-gray-950 rounded-3xl border border-gray-300 dark:border-gray-700 shadow-2xl overflow-hidden">

    <!-- HEADER -->
    <div class="flex items-center justify-between p-4 border-b border-gray-300 dark:border-gray-700">

      <h3 class="text-sm font-bold uppercase tracking-widest text-gray-500">
        Receipt Preview
      </h3>

      <button id="closeReceiptPreview"
        class="w-9 h-9 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold transition">
        ✕
      </button>

    </div>

    <!-- CONTENT -->
    <div class="overflow-auto max-h-[80vh] p-4">

      <div class="flex justify-center">

        <div id="mobileReceiptPreviewContainer"></div>

      </div>

    </div>

  </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

  const bindings = [
    ["receipt_title", "previewTitle"],
    ["receipt_subtitle", "previewSubtitle"],
    ["receipt_address", "previewAddress"],
    ["receipt_footer", "previewFooter"]
  ];

  bindings.forEach(([inputName, previewId]) => {

    const input = document.querySelector(`[name="${inputName}"]`);
    const preview = document.getElementById(previewId);

    if (!input || !preview) return;

    const update = () => {
      preview.textContent = input.value || "---";
    };

    input.addEventListener("input", update);

    update();
  });

  // Receipt Width Preview
  const widthSelect = document.querySelector('[name="receipt_width"]');
  const wrapper = document.getElementById("receiptPreviewWrapper");

  const receipt = document.getElementById("receiptPreview");

  function updateWidth() {

    const badge = document.getElementById("paperBadge");

    if (widthSelect.value === "58mm") {

      receipt.style.width = "58mm";
      badge.textContent = "58mm";

    } else {

      receipt.style.width = "80mm";
      badge.textContent = "80mm";
    }

    receipt.style.width = "100%";
  }

  widthSelect.addEventListener("change", updateWidth);

  updateWidth();

  /* =========================
    MOBILE RECEIPT PREVIEW
  ========================= */

  const openBtn = document.getElementById("openReceiptPreview");

  const closeBtn = document.getElementById("closeReceiptPreview");

  const modal = document.getElementById("receiptPreviewModal");

  const desktopReceipt = document.getElementById("receiptPreview");

  const mobileContainer =
    document.getElementById("mobileReceiptPreviewContainer");

  function openPreview() {

    mobileContainer.innerHTML = "";

    const clone = desktopReceipt.cloneNode(true);

    clone.id = "mobileReceiptClone";

    /* APPLY RECEIPT WIDTH */
    if (widthSelect.value === "58mm") {

      clone.style.width = "58mm";

    } else {

      clone.style.width = "80mm";
    }

    clone.style.maxWidth = "100%";

    mobileContainer.appendChild(clone);

    modal.classList.remove("hidden");

    modal.classList.add("flex");

    document.documentElement.classList.add("overflow-hidden");
  }

  function closePreview() {

    modal.classList.add("hidden");

    modal.classList.remove("flex");

    document.documentElement.classList.remove("overflow-hidden");
  }

  openBtn?.addEventListener("click", openPreview);

  closeBtn?.addEventListener("click", closePreview);

  modal?.addEventListener("click", (e) => {

    if (e.target === modal) {

      closePreview();
    }

  });
});


</script>