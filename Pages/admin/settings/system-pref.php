<?php
// expects:
// $isSuperAdminLoggedIn
// $logo
// $systemName
// $systemCurrency
// $currencyData
?>

<div class="tab-section relative group p-8 rounded-[2rem]
  bg-white/80 dark:bg-gray-900/70
  border border-white/30 dark:border-gray-800
  shadow-xl shadow-black/5
  backdrop-blur-md overflow-hidden
  transition-all duration-300
  hover:-translate-y-1 hover:shadow-2xl"
    data-section="system-preferences">

  <!-- glow accents -->
  <div class="absolute -top-24 -right-24 w-52 h-52 bg-emerald-500/10 blur-3xl rounded-full"></div>
  <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-400"></div>

  <h2 class="text-xl font-black mb-6 flex items-center gap-3">
    <span class="flex items-center justify-center w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-500">
      <i class="fa-solid fa-gear"></i>
    </span>
    System Preferences
  </h2>

  <div class="space-y-6">

    <!-- LOGO -->
    <div class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">
      <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">System Logo</label>
      <div class="mt-3 flex items-center gap-4">
        <img id="logoPreview"
             src="<?= $logo; ?>"
             class="system-logo w-16 h-16 object-cover rounded-xl border"
             onerror="this.src='<?php echo $systemFolder; ?>/Assets/logo.png';">
        <input type="file"
               id="systemLogoInput"
               name="system_logo"
               accept=".ico,image/x-icon"
               <?php if (!$isSuperAdminLoggedIn) echo "disabled"; ?>
               class="block text-sm text-gray-500
                      file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                      file:text-sm file:font-semibold file:bg-emerald-500 file:text-white
                      file:transition enabled:file:cursor-pointer
                      hover:file:bg-emerald-600
                      disabled:opacity-60 disabled:cursor-not-allowed
                      disabled:file:bg-gray-400/50 disabled:file:text-gray-200
                      disabled:pointer-events-none">
        <button id="removeLogoBtn"
                type="button"
                onclick="removeLogo()"
                <?php if (!$isSuperAdminLoggedIn || !file_exists($_SERVER['DOCUMENT_ROOT'] . "$systemFolder/Public/uploads/logo.ico")) echo "disabled"; ?>
                class="text-red-500 text-sm enabled:hover:underline disabled:text-gray-400 disabled:cursor-not-allowed">
          Remove
        </button>
      </div>
    </div>

    <!-- SYSTEM NAME -->
    <div class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">
      <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">System Name</label>
      <input type="text"
             name="system_name"
             value="<?= htmlspecialchars($systemName); ?>"
             <?php if (!$isSuperAdminLoggedIn) echo "disabled"; ?>
             class="mt-3 w-full rounded-xl px-4 py-3 bg-gray-200/70 dark:bg-gray-900/60 border border-gray-300 dark:border-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
    </div>

    <!-- CURRENCY -->
    <div class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">
      <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Currency</label>
      <?php
      // Group currencies by optgroup
      $groupedCurrencies = [];
      foreach ($currencyData as $code => $currency) {
          $group = $currency["optgroup"] ?? "Others";
          $groupedCurrencies[$group][$code] = $currency;
      }
      ?>
      <select name="currency_code"
              <?php if (!$isSuperAdminLoggedIn) echo "disabled"; ?>
              class="mt-3 w-full rounded-xl px-4 py-3 bg-gray-200/70 dark:bg-gray-900/60 border border-gray-300 dark:border-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
        <option value="" disabled <?= empty($systemCurrency) ? "selected" : "" ?>>- Select Currency -</option>
        <?php foreach ($groupedCurrencies as $groupName => $currencies): ?>
          <optgroup label="<?= htmlspecialchars($groupName) ?>">
            <?php foreach ($currencies as $code => $currency): ?>
              <option value="<?= htmlspecialchars($code) ?>" <?= $systemCurrency === $code ? "selected" : "" ?>>
                <?= htmlspecialchars($currency["symbol"] ?? "") ?> <?= htmlspecialchars($code) ?> – <?= htmlspecialchars($currency["name"] ?? "") ?>
              </option>
            <?php endforeach; ?>
          </optgroup>
        <?php endforeach; ?>
      </select>
    </div>

  </div>
</div>