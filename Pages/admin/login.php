<?php

if (session_status() === PHP_SESSION_NONE) session_start();

// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

// ----------------------------
// Includes
// ----------------------------

require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/init.php';


// Only redirect if truly logged in
if (!empty($_SESSION['admin_id'])) {
    header("Location: " . $systemFolder . "/Pages/admin/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth select-none">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo $systemName ?> - Sign In</title>
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
		<script>
			tailwind.config = {
				darkMode: "class", // Enable dark mode with class toggle
			};
		</script>
		<link rel="icon" type="image/png" href="<?php echo $logo; ?>" />
		<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js" type="text/javascript"></script>
		<link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<style id="f3d8kq">
			.fade-in {
				animation: fadeIn 220ms ease-out forwards;
			}

			.fade-out {
				animation: fadeOut 180ms ease-in forwards;
			}

			.slide-up {
				animation: slideUp 260ms cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
			}

			.slide-down {
				animation: slideDown 200ms ease-in forwards;
			}

			@keyframes fadeIn {
				from {
					opacity: 0;
					transform: translateY(6px);
				}
				to {
					opacity: 1;
					transform: translateY(0);
				}
			}

			@keyframes fadeOut {
				from {
					opacity: 1;
					transform: translateY(0);
				}
				to {
					opacity: 0;
					transform: translateY(-6px);
				}
			}

			@keyframes slideUp {
				from {
					opacity: 0;
					transform: translateY(14px) scale(0.98);
				}
				to {
					opacity: 1;
					transform: translateY(0) scale(1);
				}
			}

			@keyframes slideDown {
				from {
					opacity: 1;
					transform: translateY(0) scale(1);
				}
				to {
					opacity: 0;
					transform: translateY(10px) scale(0.98);
				}
			}

			.card-hover {
				transition: all 180ms ease;
			}

			.card-hover:hover {
				transform: translateY(-2px) scale(1.01);
			}
		</style>
	</head>
	<body class="bg-gray-100 text-gray-800 dark:bg-gray-950 dark:text-gray-100 font-sans">
		<!-- Navigation Bar (White Primary) -->
		<?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/navbar.php'; ?>

		<div class="min-h-screen flex items-center justify-center p-6">
			<div class="w-full max-w-4xl bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden grid md:grid-cols-2">
				<!-- LEFT PANEL -->
				<div class="hidden md:flex flex-col justify-between bg-gray-900 text-white p-10">
					<div>
						<img src="<?php echo $logo; ?>" class="w-14 h-14 rounded-lg mb-6" />

						<h1 class="text-3xl font-bold leading-tight">Order Management System</h1>

						<p class="mt-4 text-gray-300 leading-relaxed">Manage orders, inventory, staff, and reports in one secure dashboard.</p>
					</div>

					<p class="text-xs text-gray-400">© <?php echo date("Y"); ?> <?php echo $systemName; ?></p>
				</div>

				<!-- RIGHT PANEL -->
				<div class="p-10 flex items-center justify-center">
					<div class="w-full max-w-sm">
						<!-- HEADER -->
						<div class="text-center mb-8">
							<img src="<?php echo $logo; ?>" class="w-14 h-14 mx-auto mb-4 md:hidden" />

							<h2 class="text-2xl font-bold">Welcome Back</h2>
							<p class="text-sm text-gray-500 dark:text-gray-400">Choose account type to continue</p>
						</div>

						<!-- STEP 1: ROLE SELECTION -->
						<div id="roleBox" class="space-y-3">
							<button disabled onclick="selectRole('staff', 'Staff Account')" class="w-full flex items-center gap-4 p-4 rounded-xl border transition disabled:opacity-50 disabled:cursor-not-allowed enabled:hover:bg-transparent enabled:hover:border-gray-200 enabled:hover:border-green-500 enabled:hover:bg-green-50 dark:enabled:hover:bg-gray-800">
								<div class="w-10 h-10 flex items-center justify-center rounded-lg bg-green-100 dark:bg-green-900">
									<i class="fa fa-user text-green-600"></i>
								</div>

								<div class="text-left flex-1">
									<div class="font-semibold">Staff</div>
									<div class="text-xs text-gray-500">Coming soon!</div>
								</div>
							</button>

							<button onclick="selectRole('admin', 'Administrator')" class="w-full flex items-center gap-4 p-4 rounded-xl border hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-gray-800 transition">
								<div class="w-10 h-10 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
									<i class="fa fa-user-shield text-blue-600"></i>
								</div>

								<div class="text-left flex-1">
									<div class="font-semibold">Administrator</div>
									<div class="text-xs text-gray-500">Manage system & data</div>
								</div>
							</button>

							<button onclick="selectRole('superadmin', 'Super Administrator')" class="w-full flex items-center gap-4 p-4 rounded-xl border hover:border-red-500 hover:bg-red-50 dark:hover:bg-gray-800 transition">
								<div class="w-10 h-10 flex items-center justify-center rounded-lg bg-red-100 dark:bg-red-900">
									<i class="fa fa-crown text-red-600"></i>
								</div>

								<div class="text-left flex-1">
									<div class="font-semibold">Super Admin</div>
									<div class="text-xs text-gray-500">Full system access</div>
								</div>
							</button>

							<button onclick="selectRole('other', 'Other Account')" class="w-full flex items-center gap-4 p-4 rounded-xl border hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
								<div class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-200 dark:bg-gray-700">
									<i class="fa fa-key"></i>
								</div>

								<div class="text-left flex-1">
									<div class="font-semibold">Other Account</div>
									<div class="text-xs text-gray-500">Enter username manually</div>
								</div>
							</button>
						</div>

						<!-- STEP 2: LOGIN -->
						<div id="loginBox" class="hidden space-y-4">
							<button onclick="back()" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">← Back</button>

							<div class="text-center">
								<div class="text-sm text-gray-500">Signing in as</div>
								<div id="roleLabel" class="font-semibold"></div>
							</div>

							<!-- username -->
							<input id="username" type="text" placeholder="Username" class="w-full px-4 py-2 rounded-lg border bg-white dark:bg-gray-800 hidden" />

							<!-- password -->
							<input id="password" type="password" placeholder="Password" class="w-full px-4 py-2 rounded-lg border bg-white dark:bg-gray-800 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-950/50" />

							<!-- remember me -->
							<label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
								<input
									type="checkbox"
									id="remember"
									class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
								Remember me
							</label>
							<button
								id="loginBtn"
								onclick="login()"
								class="w-full bg-green-500 text-white py-2 rounded-lg transition
									hover:bg-green-600
									disabled:bg-gray-400
									disabled:hover:bg-gray-400
									disabled:cursor-not-allowed
									disabled:opacity-70">
								Login
							</button>

							<p id="error" class="text-red-500 text-sm text-center hidden"></p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="mb-8"><?php include $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Partials/footer.php'; ?></div>
		<!-- Production -->
		<script src="https://unpkg.com/@popperjs/core@2"></script>
		<script src="https://unpkg.com/tippy.js@6"></script>
		<script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/Functions.js?v=<?php echo time(); ?>" defer></script>
		<script type="text/javascript" src="<?php echo $systemFolder; ?>/Pages/Script/Dashboard/navbar.js?v=<?php echo time(); ?>" defer></script>
		<script>

			document.addEventListener("DOMContentLoaded", () => {

				fetch("<?php echo $systemFolder; ?>/Pages/Script/check_login_lock.php")
					.then(res => res.json())
					.then(data => {

						if (!data.locked) return;

						document.querySelectorAll("button[id='loginBtn']").forEach((el) => {
							el.disabled = true;
						});

						startLockCountdown(data.remaining_seconds);

					});

			});

			let selectedRole = null;

			function selectRole(role, label) {
				selectedRole = role;

				document.getElementById("roleBox").classList.add("hidden");
				document.getElementById("loginBox").classList.remove("hidden");
				document.getElementById("roleLabel").innerText = label;

				const userInput = document.getElementById("username");

				if (role === "other") {
					userInput.classList.remove("hidden");
				} else {
					userInput.classList.add("hidden");
					userInput.value = role;
				}
			}

			function back() {
				document.getElementById("roleBox").classList.remove("hidden");
				document.getElementById("loginBox").classList.add("hidden");

				document.getElementById("password").value = "";
				document.getElementById("username").value = "";
				document.getElementById("error").textContent = "";
			}

			document.querySelectorAll("input").forEach((input) => {
				input.addEventListener("keydown", function (e) {
					if (e.key === "Enter") {
						e.preventDefault();
						login();
					}
				});
			});

			function login() {
				const username = document.getElementById("username").value.trim();
				const password = document.getElementById("password").value.trim();

				const remember = document.getElementById("remember").checked;
				const error = document.getElementById("error");
				if (!username || !password) {
					error.textContent = "Please enter your credentials.";
					error.classList.remove("hidden");
					return;
				}

				error.classList.add("hidden");

				fetch("<?php echo $systemFolder; ?>/Pages/Script/admin_login.php", {
					method: "POST",
					headers: { "Content-Type": "application/json" },
					body: JSON.stringify({ username, password, role: selectedRole, remember }),
				})
					.then((res) => res.json())
					.then((d) => {
						if (d.status === "success") {
							if (d.redirect) {
								window.location.href = d.redirect;
								return;
							};
							window.location.href = "<?php echo $systemFolder; ?>/Pages/admin/dashboard.php";
						} else if (d.status === "locked") {
							error.textContent = d.message || "Too many login attempts. Please wait.";
							error.classList.remove("hidden");

							document.querySelectorAll("button[id='loginBtn']").forEach((el) => {
								el.disabled = true;
							});

							startLockCountdown(d.remaining_seconds);
							return;
						} else {
							error.textContent = d.message || "Login failed";
							error.classList.remove("hidden");
						}
					});
			}

			let lockTimer = null;

			function startLockCountdown(seconds) {

				clearInterval(lockTimer);

				const error = document.getElementById("error");

				function update() {

					if (seconds <= 0) {
						clearInterval(lockTimer);

						error.textContent = "";
						error.classList.add("hidden");

						// Re-enable login button
						document.getElementById("loginBtn").disabled = false;

						// Re-enable inputs
						document.getElementById("username").disabled = false;
						document.getElementById("password").disabled = false;
						document.getElementById("remember").disabled = false;

						return;
					}

					const mins = Math.floor(seconds / 60);
					const secs = seconds % 60;

					error.classList.remove("hidden");

					error.textContent =
						`Too many login attempts. Please wait ${String(mins).padStart(2, "0")}:${String(secs).padStart(2, "0")}.`;

					seconds--;
				}

				update();
				lockTimer = setInterval(update, 1000);
			}
		</script>
	</body>
</html>
