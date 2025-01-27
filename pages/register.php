<?php
session_start();
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $errors = [];

        $name = validateInput($_POST['name']);

        $email = validateEmail($_POST['email']);

        $dob = validateInput($_POST['dob']);

        $password = hashPassword($_POST['password']);

        $profilePicture = handleFileUpload($_FILES['profile_picture'], '../assets/uploads/');


        if (!empty($errors)) {
            foreach ($errors as $key => $message) {
                $_SESSION['error_' . $key] = $message;
            }
            header('Location: register.php');
            exit;
        }

        $userData = [
            'name' => $name,
            'email' => $email,
            'dob' => $dob,
            'password' => $password,
            'profile_picture' => $profilePicture,
            'registration_date' => (new DateTime())->format('Y-m-d H:i:s'),
        ];

        saveUserData($userData, '../data/users.json');
        logActivity($email, 'User Registered');

        $_SESSION['success'] = 'Registration successful!';
        $_SESSION['show_modal'] = true;
        header('Location: register.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: register.php');
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <form id="registrationForm" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold capitalize text-center mb-4">Register Page</h2>

        <!-- full name  -->
        <div class="mb-4">
            <label class="block text-gray-700">Full Name <span class="text-red-600">*</span></label>
            <input type="text" name="name" id="name" class="w-full border rounded p-2" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>
        <div class="text-red-500 mb-4"></div>

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-gray-700">Email <span class="text-red-600">*</span></label>
            <input type="text" name="email" id="email" class="w-full border rounded p-2" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <div id="emailErrorContainer" class="text-red-500 mb-4"></div>

        <!-- Date of Birth -->

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">
                Date of Birth <span class="text-red-600">*</span>
            </label>
            <input type="text" id="dob" name="dob" class="mt-1 px-4 py-2 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 cursor-pointer"
                placeholder="DD-MM-YYYY" value="<?= isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>">
        </div>
        <div class="text-red-500 mb-4"></div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block text-gray-700">Password <span class="text-red-600">*</span></label>
            <input type="password" name="password" id="password" class="w-full border rounded p-2" value="<?= isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>">
        </div>
        <div class="text-red-500 mb-4"></div>

        <!-- Profile Picture -->

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Profile Image <span class="text-red-600">*</span></label>
            <input type="file" id="profile_picture" name="profile_picture"
                class="mt-1 px-4 py-2 w-full border border-gray-300 rounded-md"
                accept="image/*" onchange="previewImage(event)">
            <img id="image_preview" src="" alt="Image Preview" class="mt-2 hidden border border-gray-300 rounded-md" style="max-width: 100%; height: auto;">
        </div>
        <div class="text-red-500 mb-4"></div>

        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Register</button>
    </form>
</div>

<!-- Success Modal  -->

<div id="successModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-80 relative">

        <div class="flex justify-center items-center mb-4">
            <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                </svg>
            </div>
        </div>

        <h2 class="text-xl font-bold text-center mb-2">Success</h2>

        <p class="text-center text-gray-700">
            Registration successful! Click OK to proceed to the login page.
        </p>

        <button id="modalOkButton" class="bg-blue-500 text-white py-2 px-4 rounded mt-6 w-full hover:bg-blue-600 transition">
            OK
        </button>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
    document.getElementById('registrationForm').addEventListener('submit', function(event) {
        event.preventDefault();
        let formValid = true;

        const errorMessages = document.querySelectorAll('.text-red-500');
        errorMessages.forEach(msg => msg.classList.add('hidden'));

        const name = document.getElementById('name').value.trim();
        if (!name) {
            const nameError = document.createElement('div');
            nameError.classList.add('text-red-500');
            nameError.textContent = 'Full Name is required.';
            document.getElementById('name').parentNode.appendChild(nameError);
            formValid = false;
        }

        const email = document.getElementById('email').value.trim();
        const emailErrorContainer = document.getElementById('email').parentNode;

        if (!email) {
            createErrorMessage('Email is required.', emailErrorContainer);
            formValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            createErrorMessage('Invalid email format.', emailErrorContainer);
            formValid = false;
        }

        const dob = document.getElementById('dob').value.trim();
        if (!dob) {
            const dobError = document.createElement('div');
            dobError.classList.add('text-red-500');
            dobError.textContent = 'Date of Birth is required.';
            document.getElementById('dob').parentNode.appendChild(dobError);
            formValid = false;
        }

        const password = document.getElementById('password').value.trim();
        const passwordErrorContainer = document.getElementById('password').parentNode;

        const existingPasswordErrors = passwordErrorContainer.querySelectorAll('.text-red-500');
        existingPasswordErrors.forEach((error) => error.remove());

        if (!password) {
            const passwordError = document.createElement('div');
            passwordError.classList.add('text-red-500');
            passwordError.textContent = 'Password is required.';
            passwordErrorContainer.appendChild(passwordError);
            formValid = false;
        } else if (password.length < 8) {
            const lengthError = document.createElement('div');
            lengthError.classList.add('text-red-500');
            lengthError.textContent = 'Password must be at least 8 character long.';
            passwordErrorContainer.appendChild(lengthError);
            formValid = false;
        }

        const profilePicture = document.getElementById('profile_picture').files.length;
        if (!profilePicture) {
            const profilePictureError = document.createElement('div');
            profilePictureError.classList.add('text-red-500');
            profilePictureError.textContent = 'Profile Picture is required.';
            document.getElementById('profile_picture').parentNode.appendChild(profilePictureError);
            formValid = false;
        }

        if (formValid) {
            this.submit();
        }
    });

    function createErrorMessage(message, container) {
        const errorMessage = document.createElement('div');
        errorMessage.classList.add('text-red-500', 'mt-1');
        errorMessage.textContent = message;
        container.appendChild(errorMessage);
    }

    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('image_preview');

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };

            reader.readAsDataURL(file);
        } else {
            preview.src = "";
            preview.classList.add('hidden');
        }
    }

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

    // modal 
    <?php if (isset($_SESSION['show_modal']) && $_SESSION['show_modal']): ?>
        document.getElementById('successModal').classList.remove('hidden');

        <?php unset($_SESSION['show_modal']); ?>
    <?php endif; ?>

    document.getElementById('modalOkButton').addEventListener('click', function() {
        document.getElementById('successModal').classList.add('hidden');
        window.location.href = 'login.php';
    });
</script>