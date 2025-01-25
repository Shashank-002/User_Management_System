<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://localhost:3000');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=PT+Serif&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 font-serif">
    <nav class="bg-gradient-to-r from-blue-500 to-purple-600 shadow-md py-4">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a href="<?= BASE_URL ?>/index.php" class="text-white text-sm md:text-base lg:text-xl font-semibold uppercase tracking-wider hover:text-orange-400 transition duration-300 ease-in-out font-serif">
                User Management System
            </a>
            <div class="space-x-6 hidden md:flex">
                <a href="<?= BASE_URL ?>/pages/register.php"
                    class="text-white text-lg hover:text-yellow-400 font-semibold py-2 px-6 rounded-md transition-colors duration-300 ease-in-out font-serif">
                    Register
                </a>
                <a href="<?= BASE_URL ?>/pages/login.php"
                    class="text-white text-lg hover:text-yellow-400 font-semibold py-2 px-6 rounded-md transition-colors duration-300 ease-in-out font-serif">
                    Login
                </a>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <a href="<?= BASE_URL ?>/pages/profile.php"
                        class="text-white text-lg hover:text-yellow-400 font-semibold py-2 px-6 rounded-md transition-colors duration-300 ease-in-out font-serif">
                        Profile
                    </a>
                    <a href="<?= BASE_URL ?>/pages/logout.php"
                        class="text-red-500 hover:text-red-700 text-lg font-semibold py-2 px-6 rounded-md transition-colors duration-300 ease-in-out font-serif">
                        Logout
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile menu toggle button -->
            <div class="md:hidden flex items-center">
                <button id="menuToggle" class="text-white focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobileMenu" class="md:hidden bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 hidden">
            <a href="<?= BASE_URL ?>/pages/register.php" class="block py-2 px-4 hover:bg-blue-700 font-serif">Register</a>
            <a href="<?= BASE_URL ?>/pages/login.php" class="block py-2 px-4 hover:bg-blue-700 font-serif">Login</a>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <a href="<?= BASE_URL ?>/pages/profile.php" class="block py-2 px-4 hover:bg-blue-700 font-serif">Profile</a>
                <a href="<?= BASE_URL ?>/pages/logout.php" class="block py-2 px-4 hover:bg-blue-700 font-serif">Logout</a>
            <?php endif; ?>
        </div>
    </nav>

    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>

</html>