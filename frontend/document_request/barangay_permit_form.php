<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Barangay Permit (Construction) – Request Form</title>
  <link rel="stylesheet" href="css/barangay_permit_form.css">
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="content-area">

  <!-- BREADCRUMB -->
  <div class="form-breadcrumb" id="form-breadcrumb"></div>

  <h1 class="form-title">Barangay Permit (Construction) – Request Form</h1>
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

    <!-- MAIN -->
    <div class="form-main">
      <div class="form-card">

        <div class="form-card-bar">
          <img src="images/req-info-bp.png">
          Request Information
        </div>

        <div class="form-card-body">

          <!-- NAME -->
          <div class="form-row-4">
            <div class="form-group">
              <label>First Name (Applicant) <span class="req">*</span></label>
              <input type="text" id="firstName" placeholder="e.g. Juan">
              <div class="field-error" id="firstNameError">
                <img src="images/docu-field-required.png">
              </div>
            </div>

            <div class="form-group">
              <label>Middle Name</label>
              <input type="text" placeholder="e.g. Santos">
            </div>

            <div class="form-group">
              <label>Last Name <span class="req">*</span></label>
              <input type="text" id="lastName" placeholder="e.g. Dela Cruz">
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


          <!-- ADDRESS -->
          <div class="form-group">
            <label>Complete Address <span class="req">*</span></label>
            <input type="text" id="address" placeholder="e.g. 123 Rizal St...">
            <div class="field-error" id="addressError">
              <img src="images/docu-field-required.png">
            </div>
          </div>

          <!-- CONTACT -->
          <div class="form-row-2">
            <div class="form-group">
              <label>Contact Number <span class="req">*</span></label>
              <input type="text" id="contactNumber" placeholder="09XX-XXX-XXXX">
              <div class="field-error" id="contactNumberError">
                <img src="images/docu-field-required.png">
              </div>
            </div>

            <div class="form-group">
              <label>Email Address</label>
              <input type="email" id="emailAddress" placeholder="Optional">
              <div class="field-error" id="emailError">
                <img src="images/docu-email-required.png">
              </div>
            </div>
          </div>

          <!-- PURPOSE -->
          <div class="form-row-2">
            <div class="form-group">
              <label>Purpose / Nature of Activity <span class="req">*</span></label>
              <div class="custom-select" id="purposeDropdown">
                <div class="custom-select-selected" onclick="toggleDropdown('purposeDropdown')">
                  <span class="custom-select-text">Select purpose</span>
                  <span class="custom-select-arrow">▾</span>
                </div>
                <div class="custom-select-options">
                  <div class="custom-select-option" onclick="selectOption('purposeDropdown','New Construction')">New Construction</div>
                  <div class="custom-select-option" onclick="selectOption('purposeDropdown','Renovation / Remodeling')">Commercial</div>
                  <div class="custom-select-option" onclick="selectOption('purposeDropdown','Repair / Maintenance')">Repair / Maintenance</div>
                  <div class="custom-select-option" onclick="selectOption('purposeDropdown','Extension / Expansion')">Extension / Expansion</div>
                  <div class="custom-select-option" onclick="selectOption('purposeDropdown','Demolition')">Demolition</div>
                  <div class="custom-select-option" onclick="selectOption('purposeDropdown','Electrical Work')">Electrical Work</div>
                  <div class="custom-select-option" onclick="selectOption('purposeDropdown','Plumbing Work')">Plumbing Work</div>
                  <div class="custom-select-option" onclick="selectOption('purposeDropdown','Fencing / Perimeter Wall')">Fencing / Perimeter Wall</div>
                  <div class="custom-select-option" onclick="selectOption('purposeDropdown','Others')">Others (please specify)</div>
                </div>
              </div>
              <div class="field-error" id="purposeError">
                <img src="images/docu-field-required.png">
              </div>
            </div>

            <div class="form-group">
              <label>Others (Please specify) <span class="req">*</span></label>
              <input type="text" id="purposeOther" class="others-input" disabled>
              <div class="field-error" id="purposeOtherError">
                <img src="images/docu-field-required.png">
              </div>
            </div>
          </div>

          <!-- LOCATION -->
          <div class="form-group">
            <label>Location of Construction / Activity <span class="req">*</span></label>
            <input type="text" id="location">
            <div class="field-error" id="locationError">
              <img src="images/docu-field-required.png">
            </div>
          </div>

          <!-- DATES -->
          <div class="form-row-2">
            <div class="form-group">
              <label>Estimated Start Date <span class="req">*</span></label>
              <input type="date" id="startDate">
              <div class="field-error" id="startDateError">
                <img src="images/docu-field-required.png">
              </div>
            </div>

            <div class="form-group">
              <label>Estimated Completion Date <span class="req">*</span></label>
              <input type="date" id="endDate">
              <div class="field-error" id="endDateError">
                <img src="images/docu-field-required.png">
              </div>
            </div>
          </div>

          <!-- UPLOAD PLAN -->
          <div class="form-group">
            <label>Upload Construction Plan / Sketch <span class="req">*</span></label>
            <div class="upload-box" id="planUpload" onclick="document.getElementById('planFileInput').click()">
              <div class="upload-default" id="planDefault">
                <img src="images/click-to-upload.png" class="upload-icon">
                <span>Click to upload file</span>
              </div>
              <img id="planPreview" class="upload-preview-img">
              <span id="planFilename" class="upload-filename"></span>
              <input type="file" id="planFileInput" class="file-input">
            </div>
            <div class="field-error upload-field-error" id="planError">
              <img src="images/docu-field-required.png">
            </div>
          </div>

          <!-- UPLOAD ID -->
          <div class="form-group">
            <label>Upload Applicant’s Valid ID <span class="req">*</span></label>
            <div class="upload-box" id="validIdUpload" onclick="document.getElementById('validIdFileInput').click()">
              <div class="upload-default" id="validIdDefault">
                <img src="images/click-to-upload.png" class="upload-icon">
                <span>Click to upload file</span>
              </div>
              <img id="validIdPreview" class="upload-preview-img">
              <span id="validIdFilename" class="upload-filename"></span>
              <input type="file" id="validIdFileInput" class="file-input">
            </div>
            <div class="field-error upload-field-error" id="validIdError">
              <img src="images/docu-field-required.png">
            </div>
          </div>

        </div>

        <!-- BUTTONS (SAME STRUCTURE) -->
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

    <!-- SIDE -->
    <div class="form-side">
      <div class="form-card">
        <div class="form-card-bar form-gradient">
          Document Summary
        </div>
        <div class="form-card-body summary-body">
          <div class="summary-row">
            <span class="summary-label">Document</span>
            <span class="summary-value">Barangay Permit (Construction)</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Fee</span>
            <span class="summary-fee">₱200.00</span>
          </div>
        </div>
      </div>

      <div class="form-reminders">
        <img src="images/docu-reminders.png">
      </div>
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

<script src="js/barangay_permit_form.js"></script>
</body>
</html>