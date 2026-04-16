<?php
require_once __DIR__ . '/../app/core/CitiServeData.php';

session_start();

$data = new CitiServeData();
$errors = [];
$old = [
    'first_name' => '',
    'last_name' => '',
    'address' => '',
    'email' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim(isset($_POST['first_name']) ? $_POST['first_name'] : '');
    $last_name = trim(isset($_POST['last_name']) ? $_POST['last_name'] : '');
    $address = trim(isset($_POST['address']) ? $_POST['address'] : '');
    $full_name = trim($first_name . ' ' . $last_name);
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

    $old['first_name'] = $first_name;
    $old['last_name'] = $last_name;
    $old['address'] = $address;
    $old['email'] = $email;

    if ($full_name === '') {
        $errors[] = 'Full name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if ($password === '' || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $existingUser = $data->findUserByEmail($email);

        if ($existingUser) {
            $errors[] = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $userId = $data->createUser([
                'full_name' => $full_name,
                'email' => $email,
                'password_hash' => $hash
            ]);

            $_SESSION['user_id'] = $userId;
            header('Location: /CitiServe/public/index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CitiServe – Create Account</title>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../frontend/login_register/register.css">
   <script src="../frontend/login_register/register.js" defer></script>
</head>
<body>

<!-- LEFT BACKGROUND SLIDER -->
<div class="left" id="leftSlide">
  <a href="/CitiServe/public/index.php" class="back-text">
    <span class="register-arrow"><</span>
    <span class="text">Back to Main Page</span>
  </a>

   <div class="arrow left-arrow" id="prevSlide"><</div>
<div class="arrow right-arrow" id="nextSlide">></div>
</div>

<div class="login-container">
  
  <!-- Logo -->
  <div class="logo">
    <img src="../frontend/login_register/images/login-logo.png" alt="CitiSen">
  </div>
  
<form method="post" action="" enctype="multipart/form-data">
<div class="right">
  <h1 class="panel-title">Create your <span>Account</span></h1>

  <!-- First & Last Name -->
  <div class="row-2">
    <div class="field">
      <label>First Name <span class="req">*</span></label>
      <div class="input-wrap">
        <img class="field-icon" src="../frontend/login_register/images/register-person (1).png" alt="">
        <input class="field-icon" type="text" name="first_name" placeholder="Ex. Juan" value="<?= htmlspecialchars($old['first_name'], ENT_QUOTES) ?>">
      </div>
    </div>
    <div class="field">
      <label>Last Name <span class="req">*</span></label>
      <div class="input-wrap">
     <input class="no-icon" type="text" name="last_name" placeholder="Ex. Dela Cruz" data-no-error-img value="<?= htmlspecialchars($old['last_name'], ENT_QUOTES) ?>">
      </div>
    </div>
  </div>

  <!-- Address -->
  <div class="field">
    <label>Address (Brgy. Kalayaan, Angono, Rizal) <span class="req">*</span></label>
    <div class="input-wrap">
      <img class="field-icon" src="../frontend/login_register/images/register-location.png" alt="">
      <input type="text" name="address" placeholder="Street Name, Building, House No." value="<?= htmlspecialchars($old['address'], ENT_QUOTES) ?>">
    </div>
  </div>

  <!-- Email -->
  <div class="field">
    <label>Email Address <span class="req">*</span></label>
    <div class="input-wrap">
      <img class="field-icon" src="../frontend/login_register/images/ic_round-email.png" alt="">
      <input type="email" name="email" placeholder="your@email.com" value="<?= htmlspecialchars($old['email'], ENT_QUOTES) ?>">
    </div>
  </div>

  <!-- Password -->
  <div class="field">
    <label>Password <span class="req">*</span></label>
    <div class="input-wrap">
      <img class="field-icon" src="../frontend/login_register/images/pass.icon.png" alt="">
      <input type="password" id="passInput" name="password" placeholder="Min. 8 characters">
      <input type="hidden" id="passwordConfirmInput" name="password_confirm" value="">
      <button class="eye-btn" id="eyeBtn" type="button">
        <img id="eyeIcon" src="../frontend/login_register/images/eye.png" alt="Toggle visibility">
      </button>
    </div>
  </div>

  <!-- Proof of Residency -->
  <div class="field">
    <label>Proof of Residency <span class="label-note">(Upload Valid ID / Proof of Billing)</span></label>
    <div class="file-row">
      <div class="file-name">
        <img src="../frontend/login_register/images/nofilechosen.png" alt="" id="fileStatusIcon">
        <span id="fileName">No File Chosen</span>
      </div>
      <button class="choose-btn" id="chooseBtn" type="button">
        <img src="../frontend/login_register/images/choosefile.png" alt="">
        Choose File
      </button>
      <input type="file" id="fileInput" name="proof_file" accept="image/*,.pdf">
    </div>
  </div>

  <!-- Terms -->
  <label class="terms-row">
  <input type="checkbox" id="termsCheck" name="terms_check">
  I agree to the <a href="#" id="regTermsLink">Terms and Conditions</a> and <a href="#" id="regPrivacyLink">Privacy Policy</a> of CitiServe
</label>

  <button class="create-btn" type="button">Create Account</button>

  <div class="divider">
    <span>Already have an account?</span>
  </div>

  <button class="login-btn" type="button" onclick="window.location.href='login.php'">Log In to Existing Account</button>

</div>

<div class="confirm-modal" id="confirmModal">
  <div class="confirm-box">
    <!-- 🔥 icon beside text -->
    <div class="confirm-top">
      <img src="../frontend/login_register/images/reg-u-sure.png" alt="" class="confirm-icon">
      <div class="confirm-text">
        <h2 class="confirm-title">Create Account?</h2>
        <p class="confirm-subtitle">Please review your information before proceeding. Once your account is created, some details may not be editable.</p>
      </div>
    </div>
    <!-- buttons -->
    <div class="confirm-btns">
      <button class="confirm-cancel" id="confirmCancel" type="button">
        <img src="../frontend/login_register/images/reg-cancel.png" alt="Cancel">
      </button>
      <button class="confirm-create" id="confirmCreate" type="submit">
        <img src="../frontend/login_register/images/reg-create.png" alt="Create Account">
      </button>
    </div>
  </div>
</div>
</form>

<!-- TERMS MODAL -->
<div class="reg-terms-modal" id="regTermsModal">
  <div class="reg-modal-box">
    <button class="reg-close-btn" id="closeRegTerms">✕</button>
    <div class="reg-modal-header" style="background:#FECA18;"></div>
    <h2 class="reg-modal-title" style="color:#FECA18;">Terms of Service</h2>
    <p class="reg-modal-date">Effective April 1, 2026</p>
    <div class="reg-modal-content">
      <p>Welcome to CitiServe – Barangay Kalayaan. By accessing and using this platform, you agree to comply with the following terms and conditions.</p>
      <h4>1. Purpose of the System</h4>
      <p>CitiServe is an online service platform developed to help residents of Barangay Kalayaan, Angono, Rizal request barangay documents and submit complaints in a more convenient and organized manner.</p>
      <h4>2. User Responsibility</h4>
      <p>Users are responsible for providing accurate, complete, and truthful information when registering, requesting documents, or submitting complaints through the system.</p>
      <h4>3. Proper Use of the Platform</h4>
      <p>Users must not use the system for:</p>
      <ul>
        <li>False or misleading document requests</li>
        <li>Fake or malicious complaints</li>
        <li>Uploading inappropriate, offensive, or unrelated files</li>
        <li>Unauthorized access or misuse of another person's account</li>
      </ul>
      <h4>4. Document Requests</h4>
      <p>Submission of a document request does not automatically guarantee approval. All requests are subject to review, validation, and approval by the authorized barangay staff.</p>
      <h4>5. Complaint Submissions</h4>
      <p>Complaints submitted through the system must be based on valid concerns and should contain accurate details to help barangay staff assess the issue properly.</p>
      <h4>6. Account Security</h4>
      <p>Users are responsible for keeping their login credentials confidential. CitiServe is not responsible for issues resulting from unauthorized access caused by sharing account information.</p>
      <h4>7. System Availability</h4>
      <p>CitiServe aims to provide continuous access to its services; however, temporary interruptions may occur due to maintenance, updates, or technical issues.</p>
      <h4>8. Modifications to the System</h4>
      <p>The developers and administrators may update or improve features of the system when necessary for functionality, security, or usability.</p>
      <h4>9. Acceptance of Terms</h4>
      <p>By using CitiServe, you confirm that you have read and agreed to these Terms and Conditions.</p>
      <p class="reg-last-update">Last updated: 04/01/2026</p>
    </div>
  </div>
</div>

<!-- PRIVACY MODAL -->
<div class="reg-terms-modal" id="regPrivacyModal">
  <div class="reg-modal-box">
    <button class="reg-close-btn" id="closeRegPrivacy" style="color:#F03871; border-color:#F03871;">✕</button>
    <div class="reg-modal-header" style="background:#F03871;"></div>
    <h2 class="reg-modal-title" style="color:#F03871;">Privacy Policy</h2>
    <p class="reg-modal-date">Effective April 1, 2026</p>
    <div class="reg-modal-content">
      <p>CitiServe values the privacy and security of user information. This Privacy Policy explains how personal data is collected, used, and protected within the system.</p>
      <h4>1. Information Collected</h4>
      <ul>
        <li>Full name</li>
        <li>Email Address</li>
        <li>Address</li>
        <li>Contact Number</li>
        <li>Uploaded proof of residency or supporting files</li>
        <li>Document request details</li>
        <li>Complaint details and uploaded evidence</li>
      </ul>
      <h4>2. Purpose of Data Collection</h4>
      <ul>
        <li>Account registration and verification</li>
        <li>Processing barangay document requests</li>
        <li>Handling complaint submissions</li>
        <li>Sending status updates and notifications</li>
        <li>Maintaining records and service history</li>
        <li>Improving system operations and user support</li>
      </ul>
      <h4>3. Data Protection</h4>
      <p>Reasonable measures are applied within the system to help protect stored user information from unauthorized access, misuse, or loss.</p>
      <h4>4. Limited Access</h4>
      <p>Only authorized users such as barangay staff, administrators, and system developers may access system data when necessary.</p>
      <h4>5. File Uploads</h4>
      <p>Uploaded files are used only for verification and processing purposes.</p>
      <h4>6. Data Accuracy</h4>
      <p>Users are encouraged to provide accurate and updated information.</p>
      <h4>7. Data Retention</h4>
      <p>Information may be stored for documentation and service-related purposes.</p>
      <h4>8. Policy Updates</h4>
      <p>CitiServe may update this Privacy Policy when necessary.</p>
      <h4>9. User Consent</h4>
      <p>By using CitiServe, you agree to this policy.</p>
      <p class="reg-last-update">Last updated: 04/01/2026</p>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('form[method="post"]');
    var passInput = document.getElementById('passInput');
    var passwordConfirmInput = document.getElementById('passwordConfirmInput');
    if (!form || !passInput || !passwordConfirmInput) return;
    form.addEventListener('submit', function () {
      passwordConfirmInput.value = passInput.value;
    });
  });
</script>
<?php if ($errors): ?>
<script>
  alert(<?= json_encode(implode("\n", $errors)) ?>);
</script>
<?php endif; ?>
</body>
</html>
