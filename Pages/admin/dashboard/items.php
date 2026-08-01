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
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth select-none">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php
echo htmlspecialchars($settings['system_name'] ?? "Lalenz Foodies");
?> - Items Dashboard</title>
<script>
(function () {
  const theme = localStorage.getItem("theme");
  const html = document.documentElement;
  if (theme === "dark") {
    html.classList.add("dark");
  }
  else {
    html.classList.remove("dark");
    // default = light
  }
}
)();
</script>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  darkMode: "class",
};
</script>
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js" type="text/javascript"></script>
<link rel="icon" type="image/png" href="<?= $logo;
?>?v=<?= time();
?>" />
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw=="
crossorigin="anonymous"
referrerpolicy="no-referrer"
/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" />
<link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/scale.css" />
</head>
<body class="bg-white text-gray-800 dark:bg-gray-950 dark:text-gray-100 font-sans" x-data="itemsDashboard()">
<!-- Navigation Bar (White Primary) -->
<?php
include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/navbar.php';
?>
<div class="max-w-6xl mx-auto p-5 mt-6 mb-16">
<!-- Header + Actions -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
<!-- Title -->
<h1 class="text-3xl font-black tracking-tight">Items Dashboard</h1>
</div>
<!-- Search Bar & Actions Container -->
<div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
<!-- Left Side: Search Input -->
<div class="relative w-full md:max-w-md">
<i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
<input
type="text"
x-model="searchQuery"
placeholder="Search items..."
class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-emerald-500 outline-none transition"
/>
</div>
<!-- Right Side: Action Buttons -->
<div class="flex flex-wrap items-center justify-center md:justify-end gap-2 w-full md:w-auto">
<button
<?= $isSuperAdminLoggedIn !== true ? 'disabled' : '' ?>
data-action="add-item"
@click="openModal('add', null, $event)"
class="px-4 py-2 rounded-xl bg-green-600 text-white font-bold transition shadow-md
enabled:hover:bg-green-700 enabled:hover:-translate-y-0.5
disabled:opacity-50 disabled:cursor-not-allowed"
>
<i class="fas fa-plus"></i>
Add
</button>
<button
<?= $isSuperAdminLoggedIn !== true ? 'disabled' : '' ?>
data-action="export-item"
@click="exportItems()"
class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold transition shadow-md
hover:bg-blue-700 hover:-translate-y-0.5
disabled:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0"
>
Export
</button>
<label
class="px-4 py-2 rounded-xl text-white font-bold transition shadow-md
bg-green-600 hover:bg-green-700 hover:-translate-y-0.5
cursor-pointer
<?= $isSuperAdminLoggedIn !== true ? 'opacity-50 pointer-events-none cursor-not-allowed' : '' ?>"
>
Import
<input
type="file"
accept=".json"
class="hidden"
<?= $isSuperAdminLoggedIn !== true ? 'disabled' : '' ?>
@change="importItems($event)"
/>
</label>
<button
<?= $isSuperAdminLoggedIn !== true ? "disabled" : "" ?>
data-action="clear-items"
@click="clearItems()"
class="px-4 py-2 rounded-xl text-white font-bold transition shadow-md
<?= $isSuperAdminLoggedIn ? "bg-red-600 hover:bg-red-700 hover:-translate-y-0.5" : "enabled:bg-red-600 enabled:hover:bg-red-700 enabled:hover:-translate-y-0.5 disabled:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed" ?>"
>
Clear
</button>
</div>
</div>
<!-- Items Container -->
<div class="lg:w-full mx-auto">
<!-- 1. DESKTOP TABLE (Visible on md and up) -->
<div class="hidden md:block bg-white/80 dark:bg-gray-900/70 backdrop-blur-xl rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-sm">
<thead class="bg-gray-50/80 dark:bg-gray-800/80 backdrop-blur sticky top-0 z-10">
<tr class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-300">
<th @click="sortItems('id')" class="px-6 py-4 cursor-pointer text-left">ID</th>
<th @click="sortItems('name')" class="px-6 py-4 cursor-pointer text-left">Name</th>
<th @click="sortItems('price')" class="px-6 py-4 cursor-pointer text-left">Price</th>
<th @click="sortItems('stock')" class="px-6 py-4 cursor-pointer text-left">Stock</th>
<th @click="sortItems('description')" class="px-6 py-4 cursor-pointer text-left">Description</th>
<th class="px-6 py-4 text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
<!-- Rows -->
<template x-if="paginatedItems().length > 0">
<template x-for="item in paginatedItems()" :key="item.id">
<tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/60 transition">
<td class="px-6 py-4 font-semibold" x-text="item.id"></td>
<td class="px-6 py-4 font-medium" x-text="item.name"></td>
<td class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400"
x-text="currencySymbol + (Number(item.price) || 0).toFixed(2)"></td>
<td class="px-6 py-4">
<span
:class="item.stock <= item.low_stock_threshold
? 'text-red-600 font-bold'
: 'text-emerald-600 font-bold'"
x-text="item.stock ?? 0">
</span>
</td>
<td class="px-6 py-4 text-gray-500 dark:text-gray-400 truncate max-w-[200px]"
x-text="item.description || '-'"></td>
<td class="px-6 py-4 text-right">
<div class="flex justify-end gap-2">
<button
<?= $isSuperAdminLoggedIn !== true ? 'disabled onclick="return false;
"' : '' ?>
@click="openModal('edit', item, $event)"
class="px-3 py-1.5 rounded-lg text-xs font-bold transition
bg-yellow-500 text-white
enabled:hover:bg-yellow-600
disabled:bg-yellow-500 disabled:opacity-50 disabled:cursor-not-allowed"
>
Edit
</button>
<button
<?= $isSuperAdminLoggedIn !== true ? 'disabled onclick="return false;
"' : '' ?>
@click="deleteItem(item.id, $event.currentTarget)"
class="px-3 py-1.5 rounded-lg text-xs font-bold transition
bg-red-600 text-white
enabled:hover:bg-red-700
disabled:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed"
>
Delete
</button>
</div>
</td>
</tr>
</template>
</template>
<!-- EMPTY STATE (search-aware) -->
<tr x-show="paginatedItems().length === 0">
<td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
<div class="flex flex-col items-center gap-2">
<i class="fas fa-search text-2xl opacity-50"></i>
<template x-if="searchQuery">
<span>
No results found for
"<strong x-text="searchQuery"></strong>"
</span>
</template>
<template x-if="!searchQuery">
<span>No items available</span>
</template>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- 2. MOBILE CARD VIEW (Visible on small screens only) -->
<div class="md:hidden space-y-4">
<!-- 2. MOBILE CARD VIEW (Visible on small screens only) -->
<div class="md:hidden space-y-4">
<!-- NO RESULTS STATE -->
<template x-if="paginatedItems().length === 0">
<div class="text-center py-10 text-gray-500 dark:text-gray-400">
<i class="fas fa-box-open text-3xl mb-2"></i>
<p class="font-semibold">
No items found
<span x-show="searchQuery">
for "<span x-text="searchQuery"></span>"
</span>
</p>
</div>
</template>
<!-- ITEMS LIST -->
<template x-for="item in paginatedItems()" :key="item.id">
<div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800">
<div class="flex justify-between items-start mb-3">
<div>
<span class="text-xs font-bold text-gray-400 uppercase tracking-tighter"
x-text="'ID: #' + item.id"></span>
<h3 class="text-lg font-bold text-gray-800 dark:text-white"
x-text="item.name"></h3>
</div>
<div class="text-lg font-black text-emerald-600 dark:text-emerald-400"
x-text="currencySymbol + (Number(item.price) || 0).toFixed(2)">
</div>
<div class="text-sm font-semibold"
:class="item.stock <= item.low_stock_threshold
? 'text-red-500'
: 'text-emerald-500'">
Stock: <span x-text="item.stock ?? 0"></span>
</div>
</div>
<p class="text-sm text-gray-500 dark:text-gray-400 mb-4 italic"
x-text="item.description || 'No description provided.'"></p>
<div class="flex gap-2">
<button
<?= $isSuperAdminLoggedIn !== true ? 'disabled onclick="return false;
"' : '' ?>
@click="openModal('edit', item, $event)"
class="flex-1 py-2.5 rounded-xl bg-yellow-500 text-white font-bold text-sm transition
enabled:hover:bg-yellow-600
disabled:bg-yellow-500 disabled:opacity-50 disabled:cursor-not-allowed"
>
<i class="fas fa-edit mr-1"></i> Edit
</button>
<button
<?= $isSuperAdminLoggedIn !== true ? 'disabled onclick="return false;
"' : '' ?>
@click="deleteItem(item.id, $event.currentTarget)"
class="flex-1 py-2.5 rounded-xl bg-red-600 text-white font-bold text-sm transition
enabled:hover:bg-red-700
disabled:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed"
>
<i class="fas fa-trash mr-1"></i> Delete
</button>
</div>
</div>
</template>
</div>
</div>
</div>
<!-- Pagination -->
<div class="flex flex-wrap justify-center items-center gap-2 mt-6 px-2">
<!-- Prev -->
<button
@click="goToPage(currentPage - 1)"
:disabled="currentPage === 1"
class="px-3 py-2 sm:px-4 rounded-lg bg-gray-200 dark:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed"
>
←
</button>
<!-- Page Numbers -->
<div class="flex flex-wrap justify-center gap-1 max-w-full overflow-x-auto">
<template x-for="page in totalPages()" :key="page">
<button
@click="currentPage !== page && goToPage(page)"
:class="currentPage === page
? 'px-2 sm:px-3 py-1 rounded-lg font-bold bg-emerald-600 text-white cursor-not-allowed'
: 'px-2 sm:px-3 py-1 rounded-lg font-bold bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600'"
>
<span x-text="page"></span>
</button>
</template>
</div>
<!-- Next -->
<button
@click="goToPage(currentPage + 1)"
:disabled="currentPage >= totalPages()"
class="px-3 py-2 sm:px-4 rounded-lg bg-gray-200 dark:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed"
>
→
</button>
</div>
</div>
<!-- Modal -->
<div x-show="modalOpen" x-transition @click.self="closeModal()"
class="fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm z-[99]">
<div class="bg-white dark:bg-gray-900 w-full max-w-md rounded-3xl p-6 shadow-2xl">
<h2 class="text-xl font-black mb-4" x-text="modalTitle"></h2>
<div class="space-y-4">
<!-- Item Name -->
<div>
<label class="text-sm font-semibold text-gray-600 dark:text-gray-300">
Item Name
</label>
<input type="text"
x-model="form.name"
class="w-full mt-1 px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border" />
</div>
<!-- Price -->
<div>
<label class="text-sm font-semibold text-gray-600 dark:text-gray-300">
Price
</label>
<input type="number"
x-model.number="form.price"
class="w-full mt-1 px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border" />
</div>
<!-- Stock -->
<div>
<label class="text-sm font-semibold text-gray-600 dark:text-gray-300">
Stock Quantity
</label>
<input type="number"
x-model.number="form.stock"
class="w-full mt-1 px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border" />
</div>
<!-- Low Stock Threshold -->
<div>
<label class="text-sm font-semibold text-gray-600 dark:text-gray-300">
Low Stock Alert Level
</label>
<input type="number"
x-model.number="form.low_stock_threshold"
class="w-full mt-1 px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border" />
</div>
<!-- Description -->
<div>
<label class="text-sm font-semibold text-gray-600 dark:text-gray-300">
Description
</label>
<textarea
x-model="form.description"
class="w-full mt-1 px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border"></textarea>
</div>
</div>
<div class="flex justify-end gap-2 mt-6">
<button @click="closeModal()"
class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-gray-700">
Cancel
</button>
<button @click="saveItem()"
class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
Save
</button>
</div>
</div>
</div>
<div id="confirmModal" class="hidden fixed inset-0 z-[999] items-center justify-center bg-black/50 backdrop-blur-sm">
<div class="bg-white dark:bg-gray-900 rounded-2xl p-6 w-full max-w-md shadow-2xl">
<h2 id="confirmTitle" class="text-lg font-bold mb-2">Confirm</h2>
<p id="confirmMessage" class="text-gray-600 dark:text-gray-300 mb-6">
Are you sure?
</p>
<div class="flex justify-end gap-3">
<button id="confirmCancel"
class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700">
Cancel
</button>
<button id="confirmOk"
class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
Confirm
</button>
</div>
</div>
</div>
<div id="footer" class="mb-3 -mt-10"><?php
include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/footer.php';
?></div>
<!-- Production -->
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script src="<?php
echo $systemFolder;
?>/Pages/Script/Dashboard/navbar.js?v=<?= time();
?>"></script>
<script>
// Toastr setting
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
function itemsDashboard() {
  return {
    items: [],
    modalOpen: false,
    modalTitle: "",
    form: {
      id: null,
      name: "",
      price: 0,
      description: "",
      stock: 0,
      low_stock_threshold: 5
    }
    ,
    currentAction: "",
    sortKey: "",
    sortAsc: true,
    currentPage: 1,
    itemsPerPage: 10,
    searchQuery: "",
    currencySymbol: "<?php
    echo addslashes($currencySymbol);
    ?>",
    currencyCode: "<?php
    echo addslashes($systemCurrency);
    ?>",
    async exportItems() {
      if (!<?= json_encode($isSuperAdminLoggedIn) ?>) {
        const exportButton = document.querySelector("button[data-action='export-item']");
        if (exportButton) {
          exportButton.disabled = true;
          exportButton.classList.add("opacity-50", "cursor-not-allowed");
        }
        toastr.error("You do not have permission to perform this action.");
        return;
      }
      const res = await fetch(`<?php
      echo $systemFolder;
      ?>/Pages/Script/api/items/export.php`);
      const data = await res.json();
      if (!data.items) {
        toastr.error("Export failed");
        return;
      }
      const blob = new Blob([JSON.stringify(data, null, 2)], {
        type: "application/json"
      }
    );
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "items_export.json";
    a.click();
    URL.revokeObjectURL(url);
  }
  ,
  filteredItems() {
    if (!this.searchQuery) return this.items;
    const q = this.searchQuery.toLowerCase();
    return this.items.filter((item) =>
    String(item.id).includes(q) ||
    (item.name && item.name.toLowerCase().includes(q)) ||
    (item.description && item.description.toLowerCase().includes(q))
  );
}
,
init() {
  this.loadItems();
  this.$watch("searchQuery", () => {
    this.currentPage = 1;
  }
);
// ESC key handler
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && this.modalOpen) {
    this.closeModal();
  }
}
);
}
,
loadItems() {
  fetch(`<?php
  echo $systemFolder;
  ?>/Pages/Script/api/items/get.php?v=` + Date.now())
  .then(res => res.json())
  .then(data => {
    this.items = data.items || [];
  }
)
.catch(err => console.error(err));
}
,
paginatedItems() {
  const filtered = this.filteredItems();
  const start = (this.currentPage - 1) * this.itemsPerPage;
  return filtered.slice(start, start + this.itemsPerPage);
}
,
totalPages() {
  return Math.ceil(this.filteredItems().length / this.itemsPerPage);
}
,
goToPage(page) {
  if (page >= 1 && page <= this.totalPages()) {
    this.currentPage = page;
  }
}
,
openModal(action, item = null, event = null) {
  // 🔐 Permission check (client-side UX guard)
  if (!<?= json_encode($isSuperAdminLoggedIn) ?>) {
    // try to get clicked button safely
    let button = event?.currentTarget
    || document.querySelector(`[data-action='$ {
      action
    }
    -item']`);
    if (button) {
      button.disabled = true;
    };
    toastr.error("You do not have permission to perform this action.");
    return;
  }
  this.currentAction = action;
  this.modalTitle = action === "add" ? "Add Item" : "Edit Item";
  this.form = item
  ? {
    ...item
  }
  : {
    id: null,
    name: "",
    price: 0,
    description: "",
    stock: 0,
    low_stock_threshold: 5
  };
  this.modalOpen = true;
  document.documentElement.classList.add("overflow-hidden");
}
,
closeModal() {
  this.modalOpen = false;
  document.documentElement.classList.remove("overflow-hidden");
}
,
async saveItem() {
  if (!this.form.name) {
    toastr.warning("Item name is required.");
    return;
  }
  const isEdit = this.currentAction === "edit";
  const url = isEdit
  ? `<?php
  echo $systemFolder;
  ?>/Pages/Script/api/items/update.php`
  : `<?php
  echo $systemFolder;
  ?>/Pages/Script/api/items/create.php`;
  const payload = {
    ...this.form
  };
  const res = await fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    }
    ,
    body: JSON.stringify(payload)
  }
);
const data = await res.json();
if (data.status === "success") {
  toastr.success(isEdit ? "Item updated!" : "Item created!");
  this.loadItems();
  this.closeModal();
}
else {
  toastr.error(data.message || "Failed to save item");
}
}
,
async deleteItem(id, btn) {
  try {
    // 🔐 Permission check (client-side UX guard)
    if (!<?= json_encode($isSuperAdminLoggedIn) ?>) {
      if (btn) {
        btn.disabled = true;
        btn.classList.add("opacity-50", "cursor-not-allowed");
      }
      toastr.error("You do not have permission to perform this action.");
      return;
    }
    const confirmed = await showConfirm( {
      title: "Delete Item",
      message: "Are you sure you want to delete this item?"
    }
  );
  if (!confirmed) return;
  // UI loading state
  if (btn) {
    btn.disabled = true;
    btn.dataset.oldText = btn.innerText;
    btn.innerText = "Deleting...";
  }
  const res = await fetch(`<?php
  echo $systemFolder ?>/Pages/Script/api/items/delete.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    }
    ,
    body: JSON.stringify( {
      id
    }
  )
}
);
const data = await res.json().catch(() => null);
if (!data) {
  toastr.error("Invalid server response");
  return;
}
// success
if (data.status === "success") {
  toastr.success("Item deleted");
  this.loadItems();
  return;
}
// error handling
if (data.status === "error") {
  const code = String(data.code || "").toLowerCase();
  if (code === "unauthorized" || code === "forbidden") {
    toastr.error("You do not have permission to perform this action.");
    if (btn) {
      btn.disabled = true;
      btn.classList.add("opacity-50", "cursor-not-allowed");
    }
    return;
  }
  toastr.error(data.message || "Delete failed");
}
}
catch (err) {
  console.error(err);
  toastr.error("Something went wrong");
}
finally {
  // 🔥 IMPORTANT FIX:
  // do NOT restore button if we intentionally disabled it
  if (btn && btn.dataset.oldText && !btn.disabled) {
    btn.innerText = btn.dataset.oldText;
  }
}
}
,
sortItems(key) {
  if (this.sortKey === key) {
    this.sortAsc = !this.sortAsc;
  }
  else {
    this.sortKey = key;
    this.sortAsc = true;
  }
  this.items.sort((a, b) => {
    if (a[key] < b[key]) return this.sortAsc ? -1 : 1;
    if (a[key] > b[key]) return this.sortAsc ? 1 : -1;
    return 0;
  }
);
}
,
async clearItems() {
  const confirmed = await showConfirm( {
    title: "Delete All Items",
    message: "⚠️ This will delete ALL items permanently. Continue?"
  }
);
if (!confirmed) return;
// 🔐 Permission check (client-side UX guard)
if (!<?= json_encode($isSuperAdminLoggedIn) ?>) {
  let button = document.querySelector("button[data-action='clear-items']");
  if (button) {
    button.disabled = true;
  };
  toastr.error("You do not have permission to perform this action.");
  return;
}
try {
  const res = await fetch(`<?php
  echo $systemFolder ?>/Pages/Script/api/items/clear.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    }
    ,
    body: JSON.stringify( {
      action: "hard_reset",
      confirm: "YES_RESET_ITEMS"
    }
  )
}
);
const data = await res.json();
if (data.status === "success") {
  this.items = [];
  this.currentPage = 1;
  toastr.success("All items cleared!");
}
else if (data.status == "error" && data?.code?.toLowerCase() == "unauthorized") {
}
else {
  toastr.error(data.message || "Failed to clear items");
}
}
catch(err) {
  toastr.error("Error while clearing items")
  console.log("Error while clearing items", err.stack);
}
}
,
async importItems(event) {
  const file = event.target.files[0];
  if (!file) return;
  const confirmed = await showConfirm( {
    title: "Import Items",
    message: "⚠️ This will REPLACE all items in the database. Continue?"
  }
);
if (!confirmed) {
  event.target.value = "";
  return;
}
const reader = new FileReader();
reader.onload = async (e) => {
  try {
    let json;
    // STEP 1: SAFE JSON parse from file
    try {
      json = JSON.parse(e.target.result);
    }
    catch (err) {
      toastr.error("Invalid JSON file");
      console.error("JSON parse error:", err);
      return;
    }
    let items = [];
    if (Array.isArray(json)) {
      items = json;
    }
    else if (json.items && Array.isArray(json.items)) {
      items = json.items;
    }
    else {
      toastr.error("Invalid JSON format");
      return;
    }
    // STEP 2: SEND TO SERVER
    const res = await fetch(`<?php
    echo $systemFolder ?>/Pages/Script/api/items/import.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      }
      ,
      body: JSON.stringify( {
        items
      }
    )
  }
);
// 🔥 IMPORTANT FIX: never use res.json() directly
const text = await res.text();
console.log("RAW IMPORT RESPONSE:", text);
let data;
try {
  data = JSON.parse(text);
}
catch (err) {
  console.error("Server returned non-JSON response:", text);
  toastr.error("Server error (check console)");
  return;
}
// STEP 3: HANDLE RESPONSE
if (data.status === "success") {
  this.loadItems();
  toastr.success("Items imported successfully!");
}
else {
  toastr.error(data.message || "Import failed");
}
}
catch (err) {
  console.error("Import error:", err);
  toastr.error("Import failed (check console)");
}
event.target.value = "";
};
reader.readAsText(file);
}
};
}
document.addEventListener("alpine:init", () => {
  Alpine.data("itemsDashboard", itemsDashboard);
}
);
</script>
<script>
tippy("[data-tippy-content]");
document.getElementById("confirmModal").addEventListener("click", (e) => {
  if (e.target.id === "confirmModal") {
    hideConfirm();
  }
}
);
function showConfirm( {
  title = "Confirm", message = "Are you sure?"
}
) {
  return new Promise((resolve) => {
    const modal = document.getElementById("confirmModal");
    const titleEl = document.getElementById("confirmTitle");
    const messageEl = document.getElementById("confirmMessage");
    const btnOk = document.getElementById("confirmOk");
    const btnCancel = document.getElementById("confirmCancel");
    const html = document.documentElement;
    html.classList.add("overflow-hidden");
    titleEl.innerText = title;
    messageEl.innerText = message;
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    const cleanup = () => {
      modal.classList.add("hidden");
      modal.classList.remove("flex");
      html.classList.remove("overflow-hidden");
      btnOk.onclick = null;
      btnCancel.onclick = null;
    };
    btnOk.onclick = () => {
      cleanup();
      resolve(true);
    };
    btnCancel.onclick = () => {
      cleanup();
      resolve(false);
    };
  }
);
};
function hideConfirm() {
  const modal = document.getElementById("confirmModal");
  modal.classList.add("hidden");
  modal.classList.remove("flex");
  document.documentElement.classList.remove("overflow-hidden");
};
$(document).on("keydown", (e) => {
  const modal = document.getElementById("confirmModal");
  if (modal.classList.contains("flex")) {
    if (e.key === "Escape") hideConfirm();
  };
}
);
</script>
</body>
</html>