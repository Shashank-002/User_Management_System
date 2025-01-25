<?php
session_start();
require_once '../includes/functions.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = validateInput($_POST['name']);
        $email = validateEmail($_POST['email']);
        $dob = validateInput($_POST['dob']);
        $profilePicture = $user['profile_picture'];

        if (!empty($_FILES['profile_picture']['name'])) {
            $profilePicture = handleFileUpload($_FILES['profile_picture'], '../assets/uploads/');
        }

        $users = getUserData('../data/users.json');
        foreach ($users as &$u) {
            if ($u['email'] === $user['email']) {
                $u['name'] = $name;
                $u['email'] = $email;
                $u['dob'] = $dob;
                $u['profile_picture'] = $profilePicture;
                $_SESSION['user'] = $u;
                break;
            }
        }
        file_put_contents('../data/users.json', json_encode($users, JSON_PRETTY_PRINT));

        logActivity($email, 'User Updated Profile');

        $_SESSION['success'] = 'Profile updated successfully!';
        header('Location: profile.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-4">Profile</h2>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="text-green-500 mb-4"><?= $_SESSION['success'];
                                                unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="text-red-500 mb-4"><?= $_SESSION['error'];
                                            unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700">Full Name</label>
                <input type="text" name="name" value="<?= $user['name']; ?>" class="w-full border rounded p-2">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" value="<?= $user['email']; ?>" class="w-full border rounded p-2">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Date of Birth</label>
                <input type="text" id="dob" name="dob" value="<?= $user['dob']; ?>" class="w-full border rounded p-2 cursor-pointer">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Profile Picture</label>
                <img src="../assets/uploads/<?= $user['profile_picture']; ?>" alt="Profile Picture" class="w-20 h-20 rounded-full mb-2">
                <input type="file" name="profile_picture" class="w-full">
            </div>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Update Profile</button>
        </form>
    </div>
</div>
<?php include '../includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dobInput = document.getElementById('dob');

        flatpickr(dobInput, {
            dateFormat: "d-m-Y",
            maxDate: "today",
            allowInput: true,
            clickOpens: true,
        });

        dobInput.addEventListener('input', function() {
            const value = this.value;
            const regex = /^\d{2}-\d{2}-\d{4}$/;

            if (!regex.test(value)) {
                this.setCustomValidity("Please enter a date in the format DD-MM-YYYY.");
            } else {
                const [day, month, year] = value.split('-').map(Number);
                const isValidDate = validateDate(day, month, year);

                if (!isValidDate) {
                    this.setCustomValidity("Invalid date.");
                } else {
                    this.setCustomValidity("");
                }
            }
        });

        function validateDate(day, month, year) {
            const today = new Date();
            const maxYear = today.getFullYear();
            const isLeapYear = (y) => (y % 4 === 0 && y % 100 !== 0) || (y % 400 === 0);
            const daysInMonth = [31, isLeapYear(year) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

            if (year < 1000 || year > maxYear) return false;
            if (month < 1 || month > 12) return false;
            if (day < 1 || day > daysInMonth[month - 1]) return false;

            const enteredDate = new Date(year, month - 1, day);
            return enteredDate <= today;
        }
    });
</script>