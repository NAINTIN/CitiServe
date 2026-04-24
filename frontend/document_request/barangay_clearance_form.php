<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Barangay Clearance - Request Form</title>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/barangay_clearance_form.css">
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="content-area">

 <div class="form-breadcrumb" id="form-breadcrumb"></div>

  <h1 class="form-title">Barangay Clearance – Request Form</h1>
  <p class="form-subtitle">Fill in all required fields accurately. Incomplete forms will not be processed.</p>

  <div class="form-stepper">

  <!-- Step 1 — Active -->
  <div class="form-step">
    <div class="step-icon active-icon">
      <img src="images/docu-personal-info.png" class="step-img-only">
    </div>
    <div class="step-label">
      <span class="step-name active-text">Fill out Form</span>
      <span class="step-sub">Enter your details</span>
    </div>
  </div>

  <div class="step-arrow"><img src="images/docu-arrow.png"></div>

  <!-- Step 2 — Inactive -->
  <div class="form-step">
    <div class="step-icon inactive-icon">
      <img src="images/docu-social-acc.png" class="step-img-only">
    </div>
    <div class="step-label inactive-text">
      <span class="step-name">Payment</span>
      <span class="step-sub">Complete your payment</span>
    </div>
  </div>

  <div class="step-arrow"><img src="images/docu-arrow.png"></div>

  <!-- Step 3 — Inactive -->
  <div class="form-step">
    <div class="step-icon inactive-icon">
      <img src="images/docu-payment-info.png" class="step-img-only">
    </div>
    <div class="step-label inactive-text">
      <span class="step-name">Confirmation</span>
      <span class="step-sub">Review and confirm</span>
    </div>
  </div>
</div>

  <div class="form-wrapper">

    <!-- MAIN FORM -->
    <div class="form-main">
      <div class="form-card">

        <div class="form-card-bar">
          <img src="images/req-info-bc.png">
          Request Information
        </div>

        <div class="form-card-body">

          <!-- Row 1: Name + Suffix -->
          <div class="form-row-4">
            <div class="form-group">
              <label>First Name <span class="req">*</span></label>
              <input type="text" placeholder="e.g. Juan" id="firstName">
              <div class="field-error" id="firstNameError">
                <img src="images/docu-field-required.png">
              </div>
            </div>
            <div class="form-group">
              <label>Middle Name</label>
              <input type="text" placeholder="e.g. Santos (Optional)">
            </div>
            <div class="form-group">
              <label>Last Name <span class="req">*</span></label>
              <input type="text" placeholder="e.g. Dela Cruz" id="lastName">
              <div class="field-error" id="lastNameError">
                <img src="images/docu-field-required.png">
              </div>
            </div>
            <div class="form-group">
              <label>Suffix</label>
              <div class="custom-select" id="suffixDropdown">
                <div class="custom-select-selected" onclick="toggleDropdown('suffixDropdown')">
                  <span class="custom-select-text">Select suffix</span>
                  <span class="custom-select-arrow">▾</span>
                </div>
                <div class="custom-select-options">
                  <div class="custom-select-option" onclick="selectOption('suffixDropdown', 'Jr')">Jr</div>
                    <div class="custom-select-option" onclick="selectOption('suffixDropdown', 'Sr')">Sr</div>
                    <div class="custom-select-option" onclick="selectOption('suffixDropdown', 'II')">II</div>
                    <div class="custom-select-option" onclick="selectOption('suffixDropdown', 'III')">III</div>
                    <div class="custom-select-option" onclick="selectOption('suffixDropdown', 'IV')">IV</div>
                    <div class="custom-select-option" onclick="selectOption('suffixDropdown', 'V')">V</div>
                    <div class="custom-select-option" onclick="selectOption('suffixDropdown', 'VI')">VI</div>
                    <div class="custom-select-option" onclick="selectOption('suffixDropdown', 'VII')">VII</div>
                    <div class="custom-select-option" onclick="selectOption('suffixDropdown', 'VIII')">VIII</div>
                    <div class="custom-select-option" onclick="selectOption('suffixDropdown', 'IX')">IX</div>
                    <div class="custom-select-option" onclick="selectOption('suffixDropdown', 'X')">X</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Complete Address -->
          <div class="form-group">
            <label>Complete Address <span class="req">*</span></label>
            <input type="text" placeholder="e.g. 123 Rizal St. Barangay Kalayaan, Angono, Rizal" id="completeAddress">
            <div class="field-error" id="completeAddressError">
              <img src="images/docu-field-required.png">
            </div>
          </div>

          <!-- Date of Birth + Age -->
          <div class="form-row-2">
            <div class="form-group">
              <label>Date of Birth <span class="req">*</span></label>
              <input type="date" id="dateOfBirth" class="date-input">
              <div class="field-error" id="dateOfBirthError">
                <img src="images/docu-field-required.png">
              </div>
            </div>
            <div class="form-group">
              <label>Age <span class="req">*</span></label>
              <input type="number" placeholder="e.g. 67" id="age" min="1" max="120">
              <div class="field-error" id="ageError">
                <img src="images/docu-field-required.png">
              </div>
            </div>
          </div>

          <!-- Civil Status + Citizenship -->
          <div class="form-row-2">
            <div class="form-group">
              <label>Civil Status <span class="req">*</span></label>
              <div class="custom-select" id="civilStatusDropdown">
                <div class="custom-select-selected" onclick="toggleDropdown('civilStatusDropdown')">
                  <span class="custom-select-text">Select civil status</span>
                  <span class="custom-select-arrow">▾</span>
                </div>
                <div class="custom-select-options">
                  <div class="custom-select-option" onclick="selectOption('civilStatusDropdown', 'Single')">Single</div>
                  <div class="custom-select-option" onclick="selectOption('civilStatusDropdown', 'Married')">Married</div>
                  <div class="custom-select-option" onclick="selectOption('civilStatusDropdown', 'Widowed')">Widowed</div>
                  <div class="custom-select-option" onclick="selectOption('civilStatusDropdown', 'Separated')">Separated</div>
                  <div class="custom-select-option" onclick="selectOption('civilStatusDropdown', 'Annulled')">Annulled</div>
                </div>
              </div>
              <div class="field-error" id="civilStatusError">
                <img src="images/docu-field-required.png">
              </div>
            </div>
            <div class="form-group">
              <label>Citizenship <span class="req">*</span></label>
              <input type="text" placeholder="e.g. Filipino" id="citizenship">
              <div class="field-error" id="citizenshipError">
                <img src="images/docu-field-required.png">
              </div>
            </div>
          </div>

          <!-- Contact Number + Email -->
          <div class="form-row-2">
            <div class="form-group">
              <label>Contact Number <span class="req">*</span></label>
              <input type="text" placeholder="e.g. 09XX-XXX-XXXX" id="contactNumber">
              <div class="field-error" id="contactNumberError">
                <img src="images/docu-field-required.png">
              </div>
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" placeholder="e.g. juand@email.com (Optional)" id="emailAddress">
              <div class="field-error" id="emailError">
                <img src="images/docu-email-required.png">
              </div>
            </div>
          </div>

          <!-- Purpose of Clearance -->
          <div class="form-group">
            <label>Purpose of Clearance <span class="req">*</span></label>
            <textarea placeholder="Enter purpose of Clearance..." id="purposeOfClearance" rows="4"></textarea>
            <div class="field-error textarea-error" id="purposeOfClearanceError">
              <img src="images/docu-field-required.png">
            </div>
          </div>

          <!-- Upload Valid ID -->
          <div class="form-group">
            <label>Upload Valid ID <span class="req">*</span></label>
            <div class="upload-box" id="validIdUpload" onclick="document.getElementById('validIdFileInput').click()">
              <div class="upload-default" id="validIdDefault">
                <img src="images/click-to-upload.png" class="upload-icon">
                <span>Click to upload file</span>
              </div>
              <img class="upload-preview-img" id="validIdPreview">
              <span class="upload-filename" id="validIdFilename"></span>
              <input type="file" id="validIdFileInput" class="file-input" accept="image/*, application/pdf" style="display:none;">
            </div>
            <small class="upload-note">Upload front side of any valid government-issued ID (JPG, PNG, PDF – max 5MB)</small>
            <div class="field-error upload-field-error" id="validIdError">
              <img src="images/docu-field-required.png">
            </div>
          </div>

        </div>

        <div class="form-btn-divider"></div>
        <div class="form-btn-row">
          <div class="form-btn-group">
            <button class="form-btn" onclick="hasFilledFields() ? openModal() : window.location.href='barangay_clearance.php'">
              <img src="images/docu-back.png">
            </button>
            <button class="form-btn" onclick="validateForm()">
              <img src="images/proceedd-to-payment.png">
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- SIDEBAR -->
    <div class="form-side">
      <div class="form-card">
        <div class="form-card-bar form-gradient">
          Document Summary
        </div>
        <div class="form-card-body summary-body">
          <div class="summary-row">
            <span class="summary-label">Document</span>
            <span class="summary-value">Barangay Clearance</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Fee</span>
            <span class="summary-fee">₱50.00</span>
          </div>
        </div>
      </div>
      <div class="form-reminders">
        <img src="images/docu-reminders.png">
      </div>
    </div>

  </div>

  <div class="form-logo">
    <img src="images/docu-logo.png">
    <div class="form-logo-text">
      <span class="logo-pink">CitiServe</span>
      <span class="logo-gray"> © 2026. All rights reserved.</span>
    </div>
  </div>

  

<div id="discardModal" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-header">
      <img src="images/finsl-dis-icon.png" class="modal-icon">
      <div class="modal-text">
        <h2 class="modal-title">Discard Changes?</h2>
        <p class="modal-desc">You have unsaved changes. If you leave this page, your progress will be lost.</p>
      </div>
    </div>
    <div class="modal-btn-group">
      <button onclick="window.history.back()" class="modal-btn">
        <img src="images/discard-button.png">
      </button>
      <button onclick="closeModal()" class="modal-btn">
        <img src="images/stay-button.png">
      </button>
    </div>
  </div>
</div>

</div>
<script src="js/barangay_clearance_form.js"></script>
</body>
</html>
