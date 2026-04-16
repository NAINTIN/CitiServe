<?php
require_once __DIR__ . '/../app/core/CitiServeData.php';

session_start();

$data = new CitiServeData();
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $user = $data->findUserByEmail($email);

        if ($user && password_verify($password, $user->password_hash)) {
            $_SESSION['user_id'] = $user->id;
            header('Location: /CitiServe/public/index.php');
            exit;
        }

        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log-In</title>
    <link rel="stylesheet" href="../frontend/login_register/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <script src="../frontend/login_register/login.js" defer></script>
</head>
<body>
  
<!-- LEFT BACKGROUND SLIDER -->
<div class="login-left" id="leftSlide">
  <a href="/CitiServe/public/index.php" class="back-text">
    <span class="mainpage-arrow"><</span>
    <span class="text">Back to Main Page</span>
  </a>

   <div class="login-arrow login-left-arrow" id="prevSlide"><</div>
<div class="login-arrow login-right-arrow" id="nextSlide">></div>
</div>

<div class="login-container">
  
  <!-- Logo -->
  <div class="logo">
    <img src="../frontend/login_register/images/login-logo.png" alt="CitiSen">
  </div>
  <!-- Title -->
  <h1>Welcome back,<br><span>CitiSen!</span></h1>
  <p class="subtitle">Log in to manage requests, file complaints, and stay<br> updated–no long barangay lines needed.</p>

  <form method="post" action="">
  <!-- Toggle -->
  <div class="toggle">
    <button id="resBtn" class="active" type="button">
      <img id="resIcon" src="../frontend/login_register/images/resident-icon.png" alt=""> Resident
    </button>
    <button id="bsBtn" type="button">
      <img id="bsIcon" src="../frontend/login_register/images/barangaystaff-icon.png" alt=""> Barangay Staff
    </button>
  </div>
  <!-- Email -->
  <div class="input-group">
    <label>Email Address</label>
    <div class="input">
      <img src="../frontend/login_register/images/ic_round-email.png" alt="">
      <input type="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($email, ENT_QUOTES) ?>">
    </div>
  </div>
  <!-- Password -->
  <div class="input-group">
    <label>Password</label>
    <div class="input">
      <img src="../frontend/login_register/images/pass.icon.png" alt="">
      <input type="password" id="pass" name="password" placeholder="Enter your password">
      <img src="../frontend/login_register/images/eye.png" class="eye" id="eye" alt="">
    </div>
    <div class="error-msg" id="errorMsg" style="display:<?= $error ? 'flex' : 'none' ?>;">
      <img src="../frontend/login_register/images/awa.png" alt="error"> <?= htmlspecialchars($error, ENT_QUOTES) ?>
    </div>
    <a href="#" class="forgot">Forgot your password?</a>
  </div>
  <!-- Login Button -->
  <button class="login-btn" type="submit">Log In</button>
  </form>

  <!-- Terms -->
  <p class="terms">By tapping "Login" you agree to our <a href="#" id="termsLink">Terms of Service</a> & <a href="#" id="privacyLink">Privacy Policy</a></p>
  <div class="divider">
    <span>New here?</span>
  </div>
  <!-- Create Account -->
  <button class="create-btn" type="button" onclick="window.location.href='register.php'">Create an Account</button>
  <!-- Footer -->
  <p class="footer-text">Join the digital barangay–faster than waiting in line</p>
</div>
<div class="privacy-modal" id="privacyModal">
  <div class="privacy-box">
   <button class="close-btn" id="closePrivacy">✕</button>
    <img src="../frontend/login_register/images/login-flower.png" class="flower" alt="flower">
   <div class="privacy-header"></div>

    <h2 class="policy-title">Privacy Policy</h2>
    <p class="date">Effective April 1, 2026</p>

    <div class="privacy-content">
      <p>
        CitiServe values the privacy and security of user information. This Privacy Policy explains how personal data is collected, used, and protected within the system.
      </p>

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

      <p class="last-update">Last updated: 04/01/2026</p>
    </div>
  </div>
</div>

<!-- TERMS OF SERVICE MODAL -->
<div class="terms-modal" id="termsModal">
  <div class="terms-box">
    <button class="close-btn-terms" id="closeTerms">✕</button>
    <img src="../frontend/login_register/images/login-flower1.png" class="flower-terms" alt="flower">
    <div class="terms-header"></div>

    <h2 class="terms-title">Terms of Service</h2>
    <p class="date-terms">Effective April 1, 2026</p>

    <div class="terms-content">
      <p>
        Welcome to CitiServe – Barangay Kalayaan. By accessing and using this platform, you agree to comply with the following terms and conditions.
      </p>

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

      <p class="last-update">Last updated: 04/01/2026</p>
    </div>
  </div>
</div>

</body>
</html>
