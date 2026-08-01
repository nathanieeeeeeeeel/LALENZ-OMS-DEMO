<?php
// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']);
$systemFolder = "/" . $parts[1];
// e.g., /LALENZ_ORDER_SYSTEM
// ----------------------------
// Includes
// ----------------------------
require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/init.php';
// Fallback values in case $settings is not set or doesn't contain the expected keys
$receiptTitle = $settings["receipt_title"] ?? "Lalenz Foodies";
$receiptSubtitle = $settings["receipt_subtitle"] ?? "Registered as: LALENZ ONLINE SHOP";
$receiptAddress = $settings["receipt_address"] ?? "";
$receiptFooter = $settings["receipt_footer"] ?? "Thank you for your purchase!";
?>
<!-- =============================
HTML STRUCTURE
============================= -->
<!DOCTYPE html>
<html lang="en" class="scroll-smooth select-none">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php
echo htmlspecialchars($systemName);
?> - Settings</title>
<script>
(function () {
  const theme = localStorage.getItem("theme");
  document.documentElement.classList.toggle("dark", theme === "dark");
}
)();
</script>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  darkMode: "class"
};
</script>
<link rel="icon" type="image/png" href="<?= $logo;
?>?v=<?= time();
?>" />
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js" type="text/javascript"></script>
<link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<!-- =============================
BODY
============================= -->
<body class="bg-gray-100 text-gray-800 dark:bg-gray-950 dark:text-gray-100 font-sans w-auto">
<?php
include $_SERVER["DOCUMENT_ROOT"] .
$systemFolder .
"/Pages/Partials/navbar.php";
?>
<!-- =============================
MAIN CONTENT
============================= -->
<main class="max-w-6xl mx-auto px-6 py-14">
<!-- HEADER -->
<div class="mb-14 relative bg-gray-50 dark:bg-gray-800 p-8 rounded-tr-3xl rounded-bl-3xl shadow-md lg:w-[40%] md:w-[60%] sm:w-[100%] w-[100%] overflow-hidden">
<!-- Gradient top border -->
<div class="absolute top-0 left-0 h-1 w-[100%] rounded-tr-3xl"
style="background: linear-gradient(to bottom, #10b981, #06b6d4);
"></div>
<h1 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-3 relative z-10">
<i class="fa-solid fa-gears text-emerald-500"></i>
System Settings
</h1>
<p class="text-gray-400 mt-3 text-sm relative z-10">
Manage system behavior, backups, and data safely
</p>
</div>
<div class="overflow-x-auto">
<div class="flex min-w-max border-b border-gray-300 dark:border-gray-700 mb-6">
<button class="tab-btn pb-2 font-semibold border-b-2 border-emerald-500 text-emerald-500 flex-shrink-0 px-4" data-tab="all">
Show all
</button>
<button class="tab-btn pb-2 font-semibold border-b-2 border-transparent hover:border-gray-400 text-gray-600 dark:text-gray-300 flex-shrink-0 px-4" data-tab="security">
Security
</button>
<button class="tab-btn pb-2 font-semibold border-b-2 border-transparent hover:border-gray-400 text-gray-600 dark:text-gray-300 flex-shrink-0 px-4" data-tab="system-preferences">
System Preferences
</button>
<button class="tab-btn pb-2 font-semibold border-b-2 border-transparent hover:border-gray-400 text-gray-600 dark:text-gray-300 flex-shrink-0 px-4" data-tab="receipt">
Receipts
</button>
<button class="tab-btn pb-2 font-semibold border-b-2 border-transparent hover:border-gray-400 text-gray-600 dark:text-gray-300 flex-shrink-0 px-4" data-tab="backup">
Backup
</button>
<button class="tab-btn pb-2 font-semibold border-b-2 border-transparent hover:border-gray-400 text-gray-600 dark:text-gray-300 flex-shrink-0 px-4" data-tab="dangerzone">
Danger Zone
</button>
</div>
</div>
<!-- GRID -->
<div class="grid lg:grid-cols-1 gap-10">
<!-- SYSTEM PREFERENCES -->
<?php
include $_SERVER["DOCUMENT_ROOT"] . $systemFolder . "/Pages/admin/settings/system-pref.php";
?>
<!-- SECURITY TEMPLATE -->
<?php
include $_SERVER["DOCUMENT_ROOT"] . $systemFolder . "/Pages/admin/settings/security.php";
?>
<!-- RECEIPT TEMPLATE -->
<?php
include $_SERVER["DOCUMENT_ROOT"] . $systemFolder . "/Pages/admin/settings/receipt.php";
?>
<!-- BACKUP -->
<?php
include $_SERVER["DOCUMENT_ROOT"] . $systemFolder . "/Pages/admin/settings/backup.php";
?>
</div>
<!-- DANGER ZONE -->
<div class="tab-section relative group p-8 rounded-[2rem]
bg-red-50/80 dark:bg-red-900/20
border border-red-300 dark:border-red-700
shadow-xl shadow-black/5
backdrop-blur-md overflow-hidden
transition-all duration-300
hover:-translate-y-1 hover:shadow-2xl mt-10"
data-section="dangerzone">
<!-- Glow accents -->
<div class="absolute -top-24 -right-24 w-52 h-52 bg-red-500/10 blur-3xl rounded-full"></div>
<div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-red-500 via-orange-500 to-amber-500"></div>
<h2 class="text-lg font-bold mb-6 text-red-600 flex items-center gap-2">
⚠️ Danger Zone
</h2>
<div class="grid md:grid-cols-2 gap-6">
<button
onclick="<?= $isSuperAdminLoggedIn ? 'confirmClearData(event)' : 'return false;
' ?>"
<?= $isSuperAdminLoggedIn ? '' : 'disabled' ?>
class="w-full py-3 rounded-xl font-bold text-white
bg-orange-500 hover:bg-orange-600
transition
disabled:opacity-50
disabled:cursor-not-allowed
disabled:hover:bg-orange-500">
Clear Orders
</button>
<button
onclick="<?= $isSuperAdminLoggedIn ? 'confirmClearExpenses(event)' : 'return false;
' ?>"
<?= $isSuperAdminLoggedIn ? '' : 'disabled' ?>
class="w-full py-3 rounded-xl font-bold text-white
bg-yellow-500 hover:bg-yellow-600
transition
disabled:opacity-50
disabled:cursor-not-allowed
disabled:hover:bg-yellow-500">
Clear Expenses
</button>
<button
onclick="<?= $isSuperAdminLoggedIn ? 'wipeAllData()' : 'return false;
' ?>"
<?= $isSuperAdminLoggedIn ? '' : 'disabled' ?>
class="md:col-span-2 w-full py-3 rounded-xl font-bold text-white
bg-red-600 hover:bg-red-700
transition
disabled:opacity-50
disabled:cursor-not-allowed
disabled:hover:bg-red-600">
Wipe All Data
</button>
</div>
</div>
<!-- ============================= UNSAVED CHANGES BAR ============================= -->
<div id="unsavedBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 translate-y-20 opacity-0 pointer-events-none flex justify-between items-center gap-6 w-[90%] max-w-xl bg-white text-black dark:bg-gray-800 dark:text-white px-6 py-3 rounded-2xl shadow-2xl z-50 transition-all duration-[0.3s] ease-out">
<span class="text-sm font-semibold">
⚠️ You have unsaved changes
</span>
<div id="buttonContainer" class="flex gap-3">
<button id="cancelChanges" onclick="cancelChanges()" class="bg-gray-200 dark:bg-gray-700 px-4 py-1.5 rounded-lg text-sm hover:opacity-80 transition">
Cancel
</button>
<button onclick="saveSettings(this)" id="saveChanges" class="bg-emerald-600 text-white px-4 py-1.5 rounded-lg text-sm font-bold hover:bg-emerald-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
Save
</button>
</div>
</div>
<!-- ============================= CONFIRM MODAL ============================= -->
<div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6">
<div class="flex items-center gap-2 mb-4">
<i class='fas fa-exclamation-triangle fa-xl text-red-500'></i>
<h2 id="confirmTitle" class="text-xl font-semibold">Confirm Action</h2>
</div>
<p id="confirmMessage" class="text-sm text-gray-600 dark:text-gray-300">
Are you sure?
</p>
<div class="flex justify-end gap-3 mt-6">
<button id="confirmCancel" class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:opacity-80">
Cancel
</button>
<button id="confirmOk" class="px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600">
Confirm
</button>
</div>
</div>
</div>
<?php
include $_SERVER["DOCUMENT_ROOT"] . $systemFolder . "/Pages/Partials/footer.php";
?>
</main>
<!-- =============================
SCRIPTS
============================= -->
<!-- Production -->
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="<?php
echo $systemFolder;
?>/Pages/Script/Dashboard/navbar.js?v=<?= time();
?>"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  // Toastr configuration
  toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  };
  // ===============================
  // 1️⃣ Elements
  // ===============================
  const logoInput     = document.getElementById("systemLogoInput");
  const logoPreview   = document.getElementById("logoPreview");
  const removeLogoBtn = document.getElementById("removeLogoBtn");
  const unsavedBar    = document.getElementById("unsavedBar");
  const saveBtn       = document.getElementById("saveChanges");
  const inputs        = document.querySelectorAll("input:not(#sqlFileInput), textarea, select");
  const importForm    = document.getElementById("importForm");
  const importBtn     = document.getElementById("importBtn");
  const importStatus  = document.getElementById("importStatus");
  // ===============================
  // 2️⃣ State
  // ===============================
  let originalValues = {
  };
  let currentLogoSrc = logoPreview.src;
  // currently saved logo
  // ===============================
  // 3️⃣ Capture Initial Values
  // ===============================
  function captureOriginals() {
    inputs.forEach(i => {
      if (!i.name) return;
      if (i.type === "checkbox" || i.type === "radio") originalValues[i.name] = i.checked;
      else originalValues[i.name] = i.value;
    }
  );
}
captureOriginals();
// ===============================
// 4️⃣ Detect Changes
// ===============================
function checkChanges() {
  const formChanged = Array.from(inputs).some(i => {
    // Ignore inputs inside the changePasswordForm
    if (i.closest('#changePasswordForm')) return false;
    if (!i.name) return false;
    if (i.type === "checkbox" || i.type === "radio") return i.checked !== originalValues[i.name];
    if (i.type === "file") return i.files.length > 0;
    return i.value !== originalValues[i.name];
  }
);
const logoChanged = logoInput?.files.length > 0;
const hasChanges = formChanged || logoChanged;
if (unsavedBar) {
  unsavedBar.classList.toggle("translate-y-0", hasChanges);
  unsavedBar.classList.toggle("opacity-100", hasChanges);
  unsavedBar.classList.toggle("pointer-events-auto", hasChanges);
  unsavedBar.classList.toggle("translate-y-20", !hasChanges);
  unsavedBar.classList.toggle("opacity-0", !hasChanges);
  unsavedBar.classList.toggle("pointer-events-none", !hasChanges);
}
return hasChanges;
}
// Attach input/change listeners to all inputs
inputs.forEach(i => {
  i.addEventListener("input", checkChanges);
  i.addEventListener("change", checkChanges);
}
);
// ===============================
// 5️⃣ Import Database Handling
// ===============================
if (importForm) {
  importForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (!importForm.sql_file || !importForm.sql_file.files.length) {
      toastr.warning("Please choose a .sql file before importing.");
      return;
    }
    importBtn.disabled = true;
    importBtn.innerText = "Importing...";
    importStatus.textContent = "Importing database, please wait...";
    try {
      const formData = new FormData(importForm);
      const res = await fetch(importForm.action, {
        method: "POST",
        body: formData,
      }
    );
    const text = await res.text();
    if (!res.ok || !text.toLowerCase().startsWith("success")) {
      const message = text.trim() || "Import failed.";
      throw new Error(message);
    }
    importStatus.textContent = "Database imported successfully.";
    toastr.success("Database imported successfully.");
    importForm.reset();
    if (typeof toggleXButton === "function") toggleXButton();
  }
  catch (err) {
    console.error(err);
    importStatus.textContent = "Import failed. Check the console for details.";
    toastr.error(err.message || "Import failed.");
  }
  finally {
    importBtn.disabled = false;
    importBtn.innerText = "Import Database";
  }
}
);
}
// ===============================
// Export Database Button
// ===============================
window.exportDB = async (btn) => {
  const sleep = (ms) => new Promise(r => setTimeout(r, ms));
  try {
    btn.disabled = true;
    btn.innerHTML = `<i class="fas fa-spinner fa-spin animate-spin mr-2"></i> Exporting...`;
    const res = await fetch("<?php
    echo $systemFolder;
    ?>/Pages/Script/export_db.php", {
      method: 'POST'
    }
  );
  if (!res.ok) throw new Error("Export failed");
  const blob = await res.blob();
  // Create download link
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  // Get filename from headers (optional but nice)
  const disposition = res.headers.get("Content-Disposition");
  let filename = "backup.sql";
  if (disposition && disposition.includes("filename=")) {
    filename = disposition.split("filename=")[1].replace(/"/g, "");
  }
  await sleep(1000);
  // 1 second delay
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  a.remove();
  toastr.success("Database exported successfully!");
}
catch (err) {
  console.error(err);
  toastr.error("Export failed.");
}
finally {
  btn.disabled = false;
  btn.textContent = "Export Database";
}
};
// ===============================
// 6️⃣ Logo Preview Handling
// ===============================
logoInput?.addEventListener("change", function() {
  const file = this.files[0];
  if (!file) return;
  const url = URL.createObjectURL(file);
  // Update preview & global logos
  logoPreview.src = url;
  updateGlobalLogos(url);
  checkChanges();
}
);
// ===============================
// 6️⃣ Cancel Changes
// ===============================
window.cancelChanges = function() {
  inputs.forEach(i => {
    if (!i.name || originalValues[i.name] === undefined) return;
    if (i.type === "file") {
      i.value = "";
    }
    else if (i.type === "checkbox" || i.type === "radio") {
      i.checked = originalValues[i.name];
    }
    else {
      i.value = originalValues[i.name];
    }
    // 🔥 Trigger live preview updates
    i.dispatchEvent(new Event("input"));
    i.dispatchEvent(new Event("change"));
  }
);
// Reset logo preview
logoPreview.src = currentLogoSrc;
logoInput.value = "";
updateGlobalLogos(currentLogoSrc);
updateWidth();
checkChanges();
toastr.info("Changes discarded");
};
// ===============================
// 7️⃣ Save Settings
// ===============================
window.saveSettings = async function() {
  const sleep = (ms) => new Promise(r => setTimeout(r, ms));
  const formData = new FormData();
  inputs.forEach(i => {
    if (!i.name || i.id === "sqlFileInput") return;
    if (i.type === "file" && i.files.length) formData.append(i.name, i.files[0]);
    else if (i.type === "checkbox" || i.type === "radio") formData.append(i.name, i.checked ? 1 : 0);
    else formData.append(i.name, i.value);
    console.log(`$ {
      i.name
    }
    : $ {
      i.value
    }
    `);
  }
);
saveBtn.disabled = true;
saveBtn.innerHTML = `<i class="fas fa-spinner fa-spin animate-spin"></i>`;
await sleep(500);
let responseData = null;
try {
  const res = await fetch('<?php
  echo $systemFolder;
  ?>/Pages/Script/save_settings.php', {
    method: 'POST',
    body: formData
  }
);
responseData = await res.json();
if (responseData.status === "success") {
  toastr.success(responseData.message);
  // Update originals and current logo
  captureOriginals();
  if (logoInput.files.length) currentLogoSrc = logoPreview.src;
  logoInput.value = "";
  updateGlobalLogos(currentLogoSrc);
  document.querySelectorAll(".system-logo").forEach(img => img.src = currentLogoSrc);
  $("#navSystemName").text(formData.get("system_name") || "Lalenz Foodies");
  $(document).attr("title", (formData.get("system_name") || "Lalenz Foodies") + " - Settings");
  $("link[rel='icon']").attr("href", currentLogoSrc);
  $("#removeLogoBtn").attr("disabled", currentLogoSrc.endsWith("/Assets/logo.png"));
  checkChanges();
  // hide unsaved bar
  // setTimeout(() => location.reload(), 1000);
}
else {
  toastr.error(responseData.message || "Save failed");
}
}
catch (err) {
  console.error(err);
  toastr.error("Server error. Check Network tab.");
}
finally {
  saveBtn.disabled = false;
  saveBtn.innerText = "Save";
}
};
// ===============================
// 8️⃣ Remove Logo
// ===============================
window.removeLogo = async function() {
  const confirmed = await showConfirm( {
    title: "Remove Logo",
    message: "Are you sure you want to remove the system logo?"
  }
);
if (!confirmed) return;
try {
  const res = await fetch('<?php
  echo $systemFolder;
  ?>/Pages/Script/remove_logo.php', {
    method: 'POST'
  }
);
const data = await res.json();
if (data.status.toLowerCase() === "logo_removed") {
  toastr.success("Logo removed");
  // Reset to default logo
  currentLogoSrc = '<?php
  echo $systemFolder;
  ?>/Assets/logo.png';
  logoPreview.src = currentLogoSrc;
  document.querySelectorAll(".system-logo").forEach(img => img.src = currentLogoSrc);
  document.querySelector("link[rel='icon']").href = currentLogoSrc;
  $("#removeLogoBtn").attr("disabled", true);
  logoInput.value = "";
  updateGlobalLogos(currentLogoSrc);
  checkChanges();
}
else if (data.status.toLowerCase() === "already_default") {
  toastr.info("Logo is already default");
}
else {
  toastr.error(data.message || "Failed to remove logo");
}
}
catch (err) {
  console.error(err);
  toastr.error("Connection error while removing logo");
}
};
function showPasswordConfirm( {
  title, message
}
) {
  return new Promise(resolve => {
    const modal = document.getElementById("confirmModal");
    document.documentElement.classList.add("overflow-hidden");
    const titleEl = document.getElementById("confirmTitle");
    const msgEl = document.getElementById("confirmMessage");
    // Create password input dynamically
    msgEl.innerHTML = `
    <p class="mb-3">$ {
      message
    }
    </p>
    <div class="relative flex items-center">
    <input id="dangerPasswordInput"
    type="password"
    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 pr-16"
    placeholder="Enter password">
    </div>
    `;
    // Create and attach toggle button after HTML is set to avoid Font Awesome processing
    const wrapper = msgEl.querySelector(".relative");
    const toggleBtn = document.createElement("button");
    toggleBtn.id = "togglePasswordBtn";
    toggleBtn.type = "button";
    toggleBtn.className = "absolute right-2 px-2 py-1 text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-semibold transition";
    toggleBtn.title = "Toggle password visibility";
    toggleBtn.textContent = "Show";
    toggleBtn.style.pointerEvents = "auto";
    wrapper.appendChild(toggleBtn);
    const passwordInput = msgEl.querySelector("#dangerPasswordInput");
    toggleBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleBtn.textContent = "Hide";
        toggleBtn.title = "Hide password";
      }
      else {
        passwordInput.type = "password";
        toggleBtn.textContent = "Show";
        toggleBtn.title = "Show password";
      }
    }
  );
  titleEl.innerText = title;
  modal.classList.remove("hidden");
  modal.classList.add("flex");
  const input = document.getElementById("dangerPasswordInput");
  input.focus();
  const btnOk = document.getElementById("confirmOk");
  const btnCancel = document.getElementById("confirmCancel");
  const cleanup = () => {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    msgEl.innerHTML = "Are you sure?";
    btnOk.onclick = null;
    btnCancel.onclick = null;
    document.documentElement.classList.remove("overflow-hidden");
  };
  btnOk.onclick = () => {
    const value = input.value.trim();
    if (value === "") {
      toastr.warning("Password cannot be empty");
      return;
    };
    $.post('<?php
    echo $systemFolder;
    ?>/Pages/Script/verify_password.php', {
      password: value
    }
  )
  .done((data) => {
    if (data.status && data.status.toLowerCase() === "verified") {
      cleanup();
      // toastr.success("Password verified");
      resolve(value);
    }
    else {
      toastr.error(data.message || "Incorrect password");
      console.warn("Password verification failed:", data);
    }
  }
)
.fail(() => {
  toastr.error("Error verifying password");
}
);
};
btnCancel.onclick = () => {
  cleanup();
  resolve(null);
};
}
);
}
// ===============================
// 8️⃣ Danger Zone Actions
// ===============================
async function executeDangerAction(url, successMessage, errorMessage, options = {
}
) {
  try {
    let password = null;
    // =========================
    // 1. Extra protection for wipe all
    // =========================
    if (options.requirePassword) {
      password = await showPasswordConfirm( {
        title: "Confirm Action",
        message: "Enter admin password to continue:",
      }
    );
    if (!password) return;
  };
  // =========================
  // 2. Fetch request
  // =========================
  const res = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    }
    ,
    body: options.requirePassword ? JSON.stringify( {
      password
    }
  ) : null
}
);
const data = await res.json();
if (data.status && data.status.toLowerCase() === 'success') {
  toastr.success(successMessage);
  setTimeout(() => location.reload(), 1000);
}
else {
  toastr.error(data.message || errorMessage);
}
}
catch (err) {
  console.log(err.stack);
  toastr.error(err || errorMessage);
}
}
window.confirmClearData = async function(event) {
  event.preventDefault();
  const confirmed = await showConfirm( {
    title: "Clear Orders Data",
    message: "This will delete ALL orders but keep menu and settings intact."
  }
);
if (!confirmed) return;
await executeDangerAction(
  '<?php
  echo $systemFolder;
  ?>/Pages/Script/clear_orders.php',
  'All order records have been cleared.',
  'Failed to clear orders.', {
    requirePassword: true
  }
);
};
window.confirmClearExpenses = async function(event) {
  event.preventDefault();
  const confirmed = await showConfirm( {
    title: "Clear Expense Data",
    message: "This will delete ALL expense records but keep other data intact."
  }
);
if (!confirmed) return;
await executeDangerAction(
  '<?php
  echo $systemFolder;
  ?>/Pages/Script/clear_expenses.php',
  'All expense records have been cleared.',
  'Failed to clear expenses.', {
    requirePassword: true
  }
);
};
window.wipeAllData = async function() {
  const confirmed = await showConfirm( {
    title: "Wipe All Data",
    message: "This will delete ALL data including orders, menu, and settings. This cannot be undone."
  }
);
if (!confirmed) return;
await executeDangerAction(
  '<?php
  echo $systemFolder;
  ?>/Pages/Script/wipe_data.php',
  'All data has been wiped successfully.',
  'Failed to wipe all data.', {
    requirePassword: true
  }
);
};
window.clearLoginHistory = async function() {
  const confirmed = await showConfirm( {
    title: "Clear Login History",
    message: "This will permanently remove all login activity records."
  }
);
if (!confirmed) return;
const password = await showPasswordConfirm( {
  title: "Confirm Clear Login History",
  message: "Enter admin password to continue:"
}
);
if (!password) return;
try {
  const res = await fetch("./../../Pages/Script/api/security/clear_login_history.php", {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json"
    }
    ,
    body: JSON.stringify( {
      password
    }
  )
}
);
const data = await res.json();
if (data.success) {
  toastr.success("All login history cleared.");
  setTimeout(() => {
    location.reload();
  }
  , 2000);
}
else {
  toastr.error(data.message || "Failed to clear login history.");
}
}
catch (err) {
  console.error(err);
  toastr.error("Server error");
}
}
// ===============================
// 9️⃣ Confirm Modal Utility
// ===============================
function showConfirm( {
  title = "Confirm", message = "Are you sure?"
}
) {
  return new Promise(resolve => {
    const modal     = document.getElementById("confirmModal");
    const titleEl   = document.getElementById("confirmTitle");
    const messageEl = document.getElementById("confirmMessage");
    const btnOk     = document.getElementById("confirmOk");
    const btnCancel = document.getElementById("confirmCancel");
    document.documentElement.classList.add("overflow-hidden");
    titleEl.innerText   = title;
    messageEl.innerText = message;
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    const cleanup = () => {
      modal.classList.add("hidden");
      modal.classList.remove("flex");
      document.documentElement.classList.remove("overflow-hidden");
      btnOk.onclick = null;
      btnCancel.onclick = null;
    };
    btnOk.onclick     = () => {
      cleanup();
      resolve(true);
    };
    btnCancel.onclick = () => {
      cleanup();
      resolve(false);
    };
  }
);
}
// ===============================
// 🔧 Utility: Update all logos globally
// ===============================
function updateGlobalLogos(src) {
  document.querySelectorAll(".system-logo").forEach(img => img.src = src);
  document.getElementById("logoPreview").src = src;
  const favicon = document.querySelector("link[rel='icon']");
  if (favicon) favicon.href = src;
}
}
);
</script>
<script>
// Receipt Width Preview
const widthSelect = document.querySelector('[name="receipt_width"]');
const wrapper = document.getElementById("receiptPreviewWrapper");
const receipt = document.getElementById("receiptPreview");
function updateWidth() {
  const badge = document.getElementById("paperBadge");
  if (widthSelect.value === "58mm") {
    wrapper.style.width = "58mm";
    badge.textContent = "58mm";
  }
  else {
    wrapper.style.width = "80mm";
    badge.textContent = "80mm";
  }
  receipt.style.width = "100%";
}
widthSelect.addEventListener("change", updateWidth);
updateWidth();
</script>
<script>
const tabButtons = document.querySelectorAll('.tab-btn');
const tabSections = document.querySelectorAll('.tab-section');
function showTab(tab) {
  if (tab === 'all') {
    tabSections.forEach(section => section.style.display = '');
  }
  else {
    tabSections.forEach(section => {
      section.dataset.section === tab
      ? section.style.display = ''
      : section.style.display = 'none';
    }
  );
}
// Optional: Update active button styles
tabButtons.forEach(btn => {
  if (btn.dataset.tab === tab) {
    btn.classList.add('border-emerald-500', 'text-emerald-500');
    btn.classList.remove('border-transparent', 'hover:border-gray-400', 'text-gray-600', 'dark:text-gray-300');
  }
  else {
    btn.classList.remove('border-emerald-500', 'text-emerald-500');
    btn.classList.add('border-transparent', 'hover:border-gray-400', 'text-gray-600', 'dark:text-gray-300');
  }
}
);
}
tabButtons.forEach(btn => {
  btn.addEventListener('click', () => showTab(btn.dataset.tab));
}
);
// Initialize: show all by default
showTab('all');
</script>
</body>
</html>