<?php
session_start();
?>

<?php include './includes/header.php'; ?>
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="text-center">
        <h1 class="text-2xl lg:text-4xl font-bold text-gray-800 mb-4 px-2">Welcome to the User Management System</h1>
        <p class="text-gray-600 mb-8 px-4">Create an account, log in securely, and effortlessly manage your personal profile at your convenience.
        <p>
        <div class="space-x-4">
            <a href="./pages/register.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Register</a>
            <a href="./pages/login.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Login</a>
        </div>
    </div>
</div>
<?php include './includes/footer.php'; ?>