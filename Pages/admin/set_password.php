<?php
session_start();
require_once __DIR__ . "/../Script/auth_guard.php"; // ensures admin is logged in
require_once __DIR__ . "/../Script/db_connect.php";

// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

// Fetch user info to ensure is_default = 1
$stmt = $pdo->prepare("SELECT is_default FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$user = $stmt->fetch();

if (!$user) {
    die("Admin not found.");
}

// If password is already set, redirect to settings/dashboard
if ($user['is_default'] == 0) {
    header("Location: settings.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Set Your Password</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center">

<div class="bg-white shadow-lg rounded-2xl p-8 max-w-md w-full">
  <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Set Your New Password</h2>

  <div id="errorMsg" class="text-red-500 text-sm mb-4"></div>
  <div id="successMsg" class="text-green-500 text-sm mb-4"></div>

  <form id="setPasswordForm" class="space-y-4">
    <div>
      <label class="block text-gray-700 font-medium mb-1">New Password</label>
      <input type="password" name="new_password" required placeholder="Enter new password"
             class="w-full px-4 py-2 border rounded-xl border-gray-300 focus:ring-2 focus:ring-emerald-400 focus:outline-none">
    </div>

    <div>
      <label class="block text-gray-700 font-medium mb-1">Confirm Password</label>
      <input type="password" name="confirm_password" required placeholder="Confirm password"
             class="w-full px-4 py-2 border rounded-xl border-gray-300 focus:ring-2 focus:ring-emerald-400 focus:outline-none">
    </div>

    <button type="submit"
            class="w-full bg-emerald-500 text-white py-2 rounded-xl font-semibold hover:bg-emerald-600 transition">
      Set Password
    </button>
  </form>
</div>

<script>
const form = document.getElementById('setPasswordForm');
const errorMsg = document.getElementById('errorMsg');
const successMsg = document.getElementById('successMsg');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorMsg.textContent = '';
    successMsg.textContent = '';
    const btn = form.querySelector('button');
    btn.disabled = true;

    const payload = {
        new_password: form.new_password.value,
        confirm_password: form.confirm_password.value
    };

    try {
        const res = await fetch('<?php echo $systemFolder; ?>/Pages/Script/api/password/set.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
            credentials: 'same-origin'
        });

        if (!res.ok) throw new Error('Network response not OK');

        const data = await res.json();

        if (data.success) {
            successMsg.textContent = data.message;
            setTimeout(() => {
                window.location.href = '<?php echo $systemFolder; ?>/Pages/admin/settings.php';
            }, 1500);
        } else {
            errorMsg.textContent = data.message || 'Error occurred';
        }

    } catch (err) {
        console.error(err);
        errorMsg.textContent = 'Server or network error';
    } finally {
        btn.disabled = false;
    }
});
</script>

</body>
</html>