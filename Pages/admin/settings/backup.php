<div class="tab-section relative group p-8 rounded-[2rem]
    bg-white/80 dark:bg-gray-900/70
    border border-white/30 dark:border-gray-800
    shadow-xl shadow-black/5
    backdrop-blur-md overflow-hidden
    transition-all duration-300
    hover:-translate-y-1 hover:shadow-2xl"
    data-section="backup" id="backup">

  <!-- Glow accents -->
  <div class="absolute -top-24 -right-24 w-52 h-52 bg-blue-500/10 blur-3xl rounded-full"></div>
  <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-500 via-cyan-500 to-teal-400"></div>

  <h2 class="text-xl font-black mb-6 flex items-center gap-3">
    <span class="flex items-center justify-center w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-500">
      <i class="fa-solid fa-database"></i>
    </span>
    Backup & Restore
  </h2>

  <div class="space-y-6">

    <!-- EXPORT BUTTON -->
    <div class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">
      <button type="button"
              onclick="exportDB(this)"
              class="w-full py-3 rounded-xl font-bold text-white bg-emerald-500 enabled:hover:bg-emerald-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
        Export Database
      </button>
    </div>

    <!-- IMPORT FORM -->
    <form id="importForm" method="POST" enctype="multipart/form-data"
          action="<?= $systemFolder; ?>/Pages/Script/import_db.php"
          class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl space-y-4">

      <input type="file" id="sqlFileInput" accept=".sql" name="sql_file" required
             class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                    file:text-sm file:font-semibold file:bg-blue-500 file:text-white
                    file:transition enabled:file:cursor-pointer hover:file:bg-blue-600
                    disabled:opacity-60 disabled:cursor-not-allowed
                    disabled:file:bg-gray-400/50 disabled:file:text-gray-200
                    disabled:pointer-events-none"
             <?= $isSuperAdminLoggedIn ? '' : 'disabled' ?>>

      <button id="importBtn" type="submit"
        class="w-full py-3 rounded-xl font-bold text-white bg-blue-500
        hover:bg-blue-600 transition
        disabled:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-blue-500"
        <?= $isSuperAdminLoggedIn ? '' : 'disabled' ?>>
        Import Database
      </button>

      <div id="importStatus" class="text-sm text-gray-400"></div>
    </form>

  </div>
</div>