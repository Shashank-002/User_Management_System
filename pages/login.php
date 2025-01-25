<?php
session_start();
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = validateEmail($_POST['email']);
        $password = $_POST['password'];

        $users = getUserData('../data/users.json');
        $user = array_filter($users, fn($u) => $u['email'] === $email);

        if (!$user) {
            throw new Exception('User not found.');
        }

        $user = reset($user);
        if (!verifyPassword($password, $user['password'])) {
            throw new Exception('Invalid password.');
        }

        $_SESSION['logged_in'] = true;
        $_SESSION['user'] = $user;
        if (isset($_POST['remember_me'])) {
            setcookie('user', $email, time() + (30 * 24 * 60 * 60), '/');
        }

        logActivity($email, 'User Logged In');
        header('Location: profile.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <form method="POST" class="bg-white p-6 rounded-lg shadow-md w-96">
        <h2 class="text-2xl text-center capitalize font-bold mb-4">Login page</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="text-red-500 mb-4"><?= $_SESSION['error'];
                                            unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" class="w-full border rounded p-2" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <?php if (isset($_SESSION['error_email'])): ?>
            <div class="text-red-500 mb-4"><?= $_SESSION['error_email'];
                                            unset($_SESSION['error_email']); ?></div>
        <?php endif; ?>
        <div class="mb-4">
            <label class="block text-gray-700">Password</label>
            <input type="password" name="password" class="w-full border rounded p-2">
            <div class="text-red-500 error-message hidden"></div>
        </div>
        <?php if (isset($_SESSION['error_password'])): ?>
            <div class="text-red-500 mb-4"><?= $_SESSION['error_password'];
                                            unset($_SESSION['error_password']); ?></div>
        <?php endif; ?>
        <div class="mb-4 flex items-center">
            <input type="checkbox" name="remember_me" class="mr-2">
            <label class="text-gray-700">Remember Me</label>
        </div>
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Login</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>

<script>
    document.querySelector('form').addEventListener('submit', function(event) {
        let formValid = true;

        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(msg => msg.classList.add('hidden'));

        const emailField = document.querySelector('input[name="email"]');
        if (!emailField.value.trim()) {
            const emailError = document.createElement('div');
            emailError.classList.add('error-message', 'text-red-500');
            emailError.textContent = 'Email is required.';
            emailField.parentNode.appendChild(emailError);
            formValid = false;
        }

        const passwordField = document.querySelector('input[name="password"]');
        if (!passwordField.value.trim()) {
            const passwordError = document.createElement('div');
            passwordError.classList.add('error-message', 'text-red-500');
            passwordError.textContent = 'Password is required.';
            passwordField.parentNode.appendChild(passwordError);
            formValid = false;
        }

        if (!formValid) {
            event.preventDefault();
        }
    });
</script>