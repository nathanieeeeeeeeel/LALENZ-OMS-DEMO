<?php
// expects:
// $isSuperAdminLoggedIn
// $passwordLastChanged (optional)
$settings
// $twoFactorEnabled (bool)
// $loginSessions (array)
?>

<!-- SECURITY SETTINGS -->
<div class="tab-section relative group p-8 rounded-[2rem]
  bg-white/80 dark:bg-gray-900/70
  border border-white/30 dark:border-gray-800
  shadow-xl shadow-black/5
  backdrop-blur-md overflow-hidden
  transition-all duration-300
  hover:-translate-y-1 hover:shadow-2xl" data-section="security">

  <!-- glow accents -->
  <div class="absolute -top-24 -right-24 w-52 h-52 bg-red-500/10 blur-3xl rounded-full"></div>
  <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-red-500 via-orange-500 to-amber-500"></div>

  <h2 class="text-xl font-black mb-6 flex items-center gap-3">
    <span class="flex items-center justify-center w-11 h-11 rounded-2xl bg-red-500/10 text-red-500">
      <i class="fa-solid fa-shield-halved"></i>
    </span>
    Security Settings
  </h2>

  <div class="space-y-6">

    <!-- PASSWORD -->
    <div class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">
      <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
        Password Management
      </label>

      <div class="mt-3 flex flex-col gap-3">

        <button
            type="button"
            onclick="openModal('changePasswordModal')"
            class="w-full sm:w-fit px-5 py-3 rounded-xl bg-red-500 text-white font-semibold
            hover:bg-red-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
            Change Password
            </button>

        <p class="text-xs text-gray-500 dark:text-gray-300">
          Last changed:
          <?php echo $settings['password_last_changed'] ?? "Unknown"; ?>
        </p>

      </div>
    </div>

    <!-- 2FA -->
    <div class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">
      <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
        Two-Factor Authentication (2FA)
      </label>

      <div class="mt-3 flex items-center justify-between">

        <div class="text-sm">
          Status:
          <span class="font-bold <?= !empty($twoFactorEnabled) ? 'text-emerald-500' : 'text-red-500' ?>">
            <?= !empty($twoFactorEnabled) ? "Enabled" : "Disabled" ?>
          </span>
        </div>

        <button
            type="button"
            class="px-5 py-2.5 rounded-xl font-semibold
            <?= !empty($twoFactorEnabled) ? 'bg-red-500 hover:bg-red-600' : 'bg-emerald-500 hover:bg-emerald-600' ?>
            text-white transition disabled:opacity-50 disabled:cursor-not-allowed">
            <?= !empty($twoFactorEnabled) ? "Disable" : "Enable" ?>
            </button>

      </div>
    </div>

    <!-- LOGIN SESSIONS -->
    <div class="bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">

      <div class="flex items-center justify-between">
        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
          Recent Login Activity
        </label>

        <!-- CLEAR ALL SESSIONS -->
        <button
          type="button"
          onclick="clearLoginHistory()"
          class="text-xs px-3 py-1.5 rounded-lg bg-red-500 text-white font-semibold hover:bg-red-600 transition">
          Clear All
        </button>
      </div>

      <div class="mt-4 space-y-3">
        <div id="loginSessionsContainer" class="flex flex-col gap-3">
          <p class="text-sm text-gray-500 dark:text-gray-300">Loading recent sessions...</p>
        </div>
      </div>

    </div>

    <!-- API KEYS (HIDDEN) -->
    <div class="hidden bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-5 rounded-2xl">
      <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
        API Access
      </label>

      <div class="mt-3 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">

        <input type="text"
          readonly
          value="••••••••••••••••"
          class="w-full rounded-xl px-4 py-3 bg-gray-200/70 dark:bg-gray-900/60 border border-gray-300 dark:border-gray-700 text-sm">

        <button
          type="button"
          <?php if ($isSuperAdminLoggedIn) echo "disabled"; ?>
          class="px-5 py-3 rounded-xl bg-amber-500 text-white font-semibold
          hover:bg-amber-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
          Regenerate Key
        </button>

      </div>
    </div>

  </div>
</div>

<!-- CHANGE PASSWORD MODAL -->
<div id="changePasswordModal" class="fixed modal inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 z-50">
  <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md p-6 relative">
    
    <!-- Modal Header -->
    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Change Password</h3>
    <button onclick="closeModal('changePasswordModal')" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-2xl font-bold">
      &times;
    </button>

    <!-- Modal Body -->
    <form id="changePasswordForm" class="space-y-4">
      <div>
        <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Current Password</label>
        <input type="password" name="current_password" required
          class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">New Password</label>
        <input type="password" name="new_password" required
          class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Confirm New Password</label>
        <input type="password" name="confirm_password" required
          class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
      </div>

      <div class="flex justify-end gap-3 mt-4">
        <button type="button" onclick="closeModal('changePasswordModal')" 
          class="px-5 py-2 rounded-xl bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-600 transition">
          Cancel
        </button>

        <button type="submit"
          class="px-5 py-2 rounded-xl bg-emerald-500 text-white font-semibold enabled:hover:bg-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed transition">
          Save
        </button>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script>
  // Open modal
  const openModal = (id) => {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('opacity-0', 'pointer-events-none');
    modal.classList.add('opacity-100');
    document.documentElement.classList.add('overflow-hidden'); // prevent background scroll
  }

  // Close modal
  const closeModal = (id) => {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('opacity-0', 'pointer-events-none');
    modal.classList.remove('opacity-100');
    document.documentElement.classList.remove('overflow-hidden'); // restore background scroll
    if (id === 'changePasswordModal') {
      document.getElementById('changePasswordForm').reset();
    }
  }

  // Close modal when clicking outside modal content
  document.addEventListener('click', (e) => {
    const modal = e.target.closest('.modal');
    if (!modal) return;

    // Only close if the click is on the overlay, not inside the content
    const content = modal.querySelector('div > form, div > div');
    if (!content) return;
    if (!content.contains(e.target)) {
      const openModals = document.querySelectorAll('.modal.opacity-100');
      openModals.forEach(modal => closeModal(modal.id));
      // closeModal(modal.id);
      alert("Modal closed by clicking outside. You can customize this behavior. (ID: " + modal.id + ")");
    }
  });

  // Close modal on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const openModals = document.querySelectorAll('.modal.opacity-100');
      openModals.forEach(modal => closeModal(modal.id));
    }
  });

  const changePasswordForm = document.getElementById('changePasswordForm');

  changePasswordForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin animate-spin"></i>';
    const payload = {
      current_password: this.current_password.value,
      new_password: this.new_password.value,
      confirm_password: this.confirm_password.value
    };

    try {
      const res = await fetch("<?php echo $systemFolder; ?>/Pages/Script/api/password/change.php", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        credentials: 'same-origin' // cookies are sent
      });

      if (!res.ok) throw new Error('Network response not OK');

      const data = await res.json();

      if (data.success) {
        toastr.success('Password changed successfully!'); // simpler than toastr for now
        closeModal('changePasswordModal');
      } else {
        toastr.error((data.message || 'Unknown error'));
      }
    } catch (err) {
      console.error(err);
      toastr.error('Server or network error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Save';
    }
  });

  fetch("./../../Pages/Script/get_login_history.php", {
    credentials: "include"
  })
    .then(async res => {
      const data = await res.json();
      console.log("Raw response:", res);
      return data; // 🔥 THIS WAS MISSING
    })
    .then(data => {

      console.log("Parsed:", data);
      const sessionsContainer = document.querySelector("#loginSessionsContainer");
      if (!sessionsContainer) return;
      if (data.success && data.data.length > 0) {
        sessionsContainer.innerHTML = data.data.slice(0, 5).map(session => {

          let statuses = {
            "success": "Logged In",
            "failed": "Login Attempt Failed"
          };
          const loginTime = new Date(session.login_time);
          const isRecent = loginTime.getTime() > Date.now() - 15 * 60 * 1000;

          return `
            <div class="flex items-start justify-between p-3 rounded-xl bg-white/60 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700">

              <div class="text-sm">
                <div class="font-semibold">${session.device || 'Unknown Device'}</div>

                <div class="text-xs text-gray-500">
                  ${session.ip_address || 'Unknown IP'} · ${statuses[session.status] || 'Unknown Status'}
                </div>

                <div class="text-xs text-gray-400">
                  ${new Date(session.login_time).toLocaleString()}
                  ${isRecent ? ` (${moment(loginTime).fromNow()})` : ''}
                </div>
              </div>

            </div>
          `;
        }).join('');
      } else {
        sessionsContainer.innerHTML = `<p class="text-sm text-gray-500 dark:text-gray-300">No recent login activity found.</p>`;
      };
    })
    .catch(err => {
      console.error("Fetch error:", err);
    });
</script>