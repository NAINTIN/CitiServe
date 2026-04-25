<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log-In</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="login.js" defer></script>
</head>
<body>
  
<!-- LEFT BACKGROUND SLIDER -->
<div class="login-left" id="leftSlide">
  <a href="index.html" class="back-text">
    <span class="mainpage-arrow"><</span>
    <span class="text">Back to Main Page</span>
  </a>

   <div class="login-arrow login-left-arrow" id="prevSlide"><</div>
<div class="login-arrow login-right-arrow" id="nextSlide">></div>
</div>

<div class="login-container">
  
  <!-- Logo -->
  <div class="logo">
    <img src="images/login-logo.png" alt="CitiSen">
  </div>
  <!-- Title -->
  <br><span>CitiSen!</span></h1>
  <p class="subtitle">Log in to manage requests, file complaints, and stay<br> updated–no long barangay lines needed.</p>
  <!-- Toggle -->
  <div class="toggle">
    <button id="resBtn" class="active">
      <img id="resIcon" src="images/resident-icon.png" alt=""> Resident
    </button>
    <button id="bsBtn">
      <img id="bsIcon" src="images/barangaystaff-icon.png" alt=""> Barangay Staff
    </button>
  </div>
  <!-- Email -->
  <div class="input-group">
    <label>Email Address</label>
    <div class="input">
      <img src="images/ic_round-email.png" alt="">
      <input type="email" placeholder="Enter your email">
    </div>
  </div>
  <!-- Password -->
  <div class="input-group">
    <label>Password</label>
    <div class="input">
      <img src="images/pass.icon.png" alt="">
      <input type="password" id="pass" placeholder="Enter your password">
      <img src="images/eye.png" class="eye" id="eye" alt="">
    </div>
    <div class="error-msg" id="errorMsg" style="display:none;">
      <img src="images/awa.png" alt="error"> Wrong username or password
    </div>
    <a href="#" class="forgot">Forgot your password?</a>
  </div>
  <!-- Login Button -->
  <button class="login-btn">Log In</button>
  <!-- Terms -->
  <p class="terms">By tapping "Login" you agree to our <a href="#" id="termsLink">Terms of Service</a> & <a href="#" id="privacyLink">Privacy Policy</a></p>
  <div class="divider">
    <span>New here?</span>
  </div>
  <!-- Create Account -->
  <button class="create-btn">Create an Account</button>
  <!-- Footer -->
  <p class="footer-text">Join the digital barangay–faster than waiting in line</p>
</div>
<div class="privacy-modal" id="privacyModal">
  <div class="privacy-box">
   <button class="close-btn" id="closePrivacy">✕</button>
    <img src="images/login-flower.png" class="flower" alt="flower">
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
    <img src="images/login-flower1.png" class="flower-terms" alt="flower">
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

<!-- FORGOT PASSWORD MODAL -->
<div class="forgot-modal" id="forgotModal">
  <div class="forgot-box">
    <div class="forgot-header"></div>
    <img src="images/forgot-pass-ekis.png" class="close-btn-forgot" id="closeForgot" alt="close">
    <h2 class="forgot-title">Forgot Password</h2>
    <p class="forgot-subtitle">Enter your email for the verification proccess,<br>we will send 4 digits code to your email.</p>
    <div class="input-group" style="width:100%">
      <label>Email Address</label>
      <div class="input">
        <img src="images/ic_round-email.png" alt="">
        <input type="email" id="forgotEmail" placeholder="Enter your email">
      </div>
    </div>
    <button class="forgot-continue-btn">Continue</button>
  </div>
</div>

<!-- VERIFICATION MODAL -->
<div class="verify-modal" id="verifyModal">
  <div class="verify-box">
    <img src="images/forgot-pass-ekis.png" class="close-btn-verify" id="closeVerify" alt="close">
    <div class="verify-header"></div>
    <button class="verify-back" id="verifyBack">< Back</button>
    <h2 class="verify-title">Verification</h2>
    <p class="verify-subtitle">Enter your 4 digits code that you received on<br>your email.</p>
    <div class="otp-inputs">
      <input class="otp-box" maxlength="1" type="text">
      <input class="otp-box" maxlength="1" type="text">
      <input class="otp-box" maxlength="1" type="text">
      <input class="otp-box" maxlength="1" type="text">
    </div>
    <p class="otp-timer" id="otpTimer">00:30</p>
   <button class="verify-btn" id="verifyBtn">Verify</button>
    <p class="resend-text">Didn't receive a code? <a href="#" class="resend-link" id="resendCode">Resend</a></p>
  </div>
</div>

<!-- NEW PASSWORD MODAL -->
<div class="newpass-modal" id="newPassModal">
  <div class="newpass-box">
    <img src="images/forgot-pass-ekis.png" class="close-btn-newpass" id="closeNewPass" alt="close">
    <div class="newpass-header"></div>
    <button class="newpass-back" id="newPassBack">< Back</button>
    <h2 class="newpass-title">New Password</h2>
    <p class="newpass-subtitle">Set the new password for your account so you can login and access all featuress.</p>

    <div class="newpass-input-group">
      <label>New Password</label>
      <div class="newpass-input">
        <img src="images/pass.icon.png" alt="">
        <input type="password" id="newPass" placeholder="Min. 8 characters">
        <img src="images/eye.png" class="newpass-eye" id="newPassEye" alt="">
      </div>
    </div>

    <div class="newpass-input-group">
      <label>Confirm Password</label>
      <div class="newpass-input">
        <img src="images/pass.icon.png" alt="">
        <input type="password" id="confirmPass" placeholder="Confirm your new password">
        <img src="images/eye.png" class="newpass-eye" id="confirmPassEye" alt="">
      </div>
    </div>

    <button class="newpass-btn">Update Password</button>
  </div>
</div>

<!-- PASSWORD CHANGED MODAL -->
<div class="passchanged-modal" id="passChangedModal">
  <div class="passchanged-box">
    <div class="passchanged-header"></div>
    <img src="images/imagesprofile-password-change.png.png" class="passchanged-icon" alt="success">
    <h2 class="passchanged-title">Password Changed!</h2>
    <p class="passchanged-subtitle">Your password has been changed successfully.</p>
    <button class="passchanged-btn" id="passChangedContinue">Continue</button>
  </div>
</div>
</body>
</html>