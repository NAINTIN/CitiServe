<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment</title>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/docu_business_payment.css">
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="content-area">

  <!-- BREADCRUMB -->
  <div class="form-breadcrumb" id="form-breadcrumb"></div>

  <h1 class="form-title" id="pageTitle">Payment</h1>
  <p class="form-subtitle">Choose your preferred payment method and complete the transaction to proceed with your request.</p>

  <div class="form-stepper">

 <div class="form-stepper">

  <!-- Step 1 — Done (black) -->
  <div class="form-step">
    <div class="step-icon">
      <img src="images/docu-personal-info.png" class="step-img-only">
    </div>
    <div class="step-label done-text">
      <span class="step-name">Fill out Form</span>
      <span class="step-sub">Enter your details</span>
    </div>
  </div>

  <div class="step-arrow"><img src="images/docu-arrow.png"></div>

  <!-- Step 2 — Active (pink) -->
  <div class="form-step">
    <div class="step-icon active-icon">
      <img src="images/docu-full-payment.png" class="step-img-only">
    </div>
    <div class="step-label">
      <span class="step-name active-text">Payment</span>
      <span class="step-sub">Complete your payment</span>
    </div>
  </div>

  <div class="step-arrow"><img src="images/docu-arrow.png"></div>

  <!-- Step 3 — Inactive (grey) -->
  <div class="form-step">
    <div class="step-icon">
      <img src="images/docu-payment-info.png" class="step-img-only">
    </div>
    <div class="step-label inactive-text">
      <span class="step-name">Confirmation</span>
      <span class="step-sub">Review and confirm</span>
    </div>
  </div>
</div>
</div>

  <div class="form-wrapper">

    <div class="form-main">

      <!-- ORDER SUMMARY CARD -->
      <div class="form-card pink">
        <div class="form-card">
          <div class="form-card-bar">
            <img src="images/docu-order-summary.png">
            Order Summary
          </div>
          <div class="form-card-body">
            <div class="order-row">
              <div class="order-info">
                <span class="order-name" id="orderName">—</span>
                <span class="order-sub" id="orderSub">Barangay Kalayaan, Angono, Rizal</span>
              </div>
              <span class="order-price" id="orderPrice">—</span>
            </div>
            <div class="order-divider"></div>
            <div class="order-total-row">
              <span class="order-total-label">Total Amount Due</span>
              <span class="order-price order-total-price" id="orderTotal">—</span>
            </div>
          </div>
        </div>
      </div>

      <!-- SELECT PAYMENT METHOD CARD -->
      <div class="form-card">
        <div class="form-card-bar">
          <img src="images/docu-select-payment.png">
          Select Payment Method
        </div>
        <div class="form-card-body">
          <div class="payment-options">
            <div class="payment-option" id="opt-gcash" onclick="selectPayment('gcash')">
              <img src="images/docu-gcash.png" alt="GCash">
              <span class="pay-sub">E-Wallet • QR Code</span>
            </div>
            <div class="payment-option" id="opt-maya" onclick="selectPayment('maya')">
              <img src="images/docu-maya.png" alt="Maya">
              <span class="pay-sub">E-Wallet • QR Code</span>
            </div>
            <div class="payment-option" id="opt-instapay" onclick="selectPayment('instapay')">
              <img src="images/docu-instapay.png" alt="InstaPay">
              <span class="pay-sub">QR Code • Other banks</span>
            </div>
          </div>
          <div class="payment-error" id="paymentError" style="display:none;">
            <img src="images/awa.png" style="width:12px; height:12px; vertical-align:middle; margin-top:-3px; margin-right: -2px;">
            Please select a payment method
          </div>
        </div>
      </div>

      <!-- QR + INSTRUCTIONS CARD -->
      <div class="form-card" id="qrCard" style="display:none;">
        <div class="form-card-bar">
          <img src="images/docu-payment-proof.png">
          <span id="qrCardTitle">QR Code & Payment Instructions</span>
        </div>
        <div class="form-card-body">
          <div class="qr-row">

            <!-- LEFT: QR image -->
            <div class="qr-left">
              <img src="" class="qr-img" id="qrImage">
            </div>

            <!-- RIGHT: Instructions -->
            <div class="qr-right">
              <p class="how-to">How to Pay via <img id="howToLogo" src="" style="height:16px; vertical-align:middle; object-fit:contain;"></p>
              <ol class="pay-steps" id="paySteps"></ol>
              <div class="qr-bottom">
                <img src="images/save-QR.png" class="save-qr-btn" id="saveQrBtn" style="cursor:pointer; display:none;" alt="Save QR Code">
                <p class="instapay-note" id="instapayNote" style="display:none;">
                  <b>Note:</b> This QR code is powered by InstaPay which can<br> also be scanned by partner
                  <span class="banks-link" onclick="toggleBanks()">Banks and e-Wallets</span>.
                </p>
              </div>
            </div>

          </div>

          <!-- InstaPay partner banks panel -->
          <div id="banksPanel" style="display:none; min-width:220px;">
            <img src="images/docu-e-wallets.png" style="width:100%; border-radius:12px; display:block;">
          </div>

        </div>
      </div>

      <!-- PAYMENT PROOF CARD -->
      <div class="form-card" id="proofCard" style="display:none;">
        <div class="form-card-bar">
          <img src="images/docu-payment-proof.png">
          Payment Proof &nbsp;<span style="font-weight:400; font-size:15.2px; color:rgba(0, 0, 0, 0.5);">(Required – Your request will not be processed without payment verification)</span>
        </div>
        <div class="form-card-body">
          <div class="form-group">
            <label>Reference / Transaction Number <span class="req">*</span></label>
            <input type="text" placeholder="Enter reference/transaction number" id="refNumber">
            <div class="field-error" id="refNumberError">
              <img src="images/docu-field-required.png">
            </div>
          </div>
          <div class="form-group">
            <label>Upload Payment Screenshot / Proof <span class="req">*</span></label>
            <div class="upload-box" id="proofUpload2" onclick="document.getElementById('proofFileInput').click()">
              <div id="uploadDefault">
                <img src="images/click-to-upload.png" class="upload-icon">
                <span>Click to upload file</span>
              </div>
              <img id="uploadPreview" style="display:none; max-width:100%; max-height:180px; border-radius:8px; object-fit:contain;">
              <input type="file" id="proofFileInput" class="file-input" accept="image/*, application/pdf" style="display:none;">
            </div>
            <small class="upload-note">Upload a screenshot/proof of payment (JPG, PNG, PDF – max 5MB)</small>
            <div class="field-error upload-field-error" id="proofUpload2Error">
              <img src="images/docu-field-required.png">
            </div>
          </div>
        </div>

        <div class="form-btn-divider"></div>
        <div class="form-btn-row">
          <div class="form-btn-group">
            <button class="form-btn form-btn-back" onclick="window.history.back()">
              <img src="images/docu-back.png">
            </button>
            <button class="form-btn" onclick="showModal()">
              <img src="images/docu-submit -report.png">
            </button>
          </div>
        </div>
      </div>

      <!-- DEFAULT BUTTONS (before payment selected) -->
      <div id="defaultBtnRow">
        <div class="form-btn-divider-outside"></div>
        <div class="form-btn-row-outside">
          <div class="form-btn-group">
            <button class="form-btn form-btn-back" onclick="window.history.back()">
              <img src="images/docu-back.png">
            </button>
            <button class="form-btn" onclick="showModal()">
              <img src="images/docu-submit -report.png">
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="form-side">

      <div class="form-card">
        <div class="form-card-bar form-gradient">
          Order
        </div>
        <div class="form-card-body summary-body">
          <div class="summary-row">
            <span class="summary-label">Document</span>
            <span class="summary-value" id="sidebarDoc">—</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Total</span>
            <span class="summary-fee" id="sidebarFee">—</span>
          </div>
        </div>
      </div>

      <div class="important-box">
        <img src="images/docu-important.png" style="width:100%; border-radius:14px;">
      </div>

    </div>

  </div>

  <!-- LOGO -->
  <div class="form-logo">
    <img src="images/docu-logo.png">
    <div class="form-logo-text">
      <span class="logo-pink">CitiServe</span>
      <span class="logo-gray"> © 2026. All rights reserved.</span>
    </div>
  </div>

</div>

<!-- CONFIRM SUBMISSION MODAL -->
<div id="confirmModal" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-header">
      <img src="images/docu-checked.png" class="modal-icon">
      <div class="modal-text">
        <h2 class="modal-title">Confirm Submission</h2>
        <p class="modal-desc">Once submitted, you will no longer be able to make changes. Please make sure your uploaded file is correct and clearly visible before proceeding.</p>
      </div>
    </div>
    <div class="modal-btn-group">
      <button onclick="closeModal()" class="modal-btn">
        <img src="images/docu-cancel.png">
      </button>
      <button onclick="submitForm()" class="modal-btn">
        <img src="images/docu-confirm-submit.png">
      </button>
    </div>
  </div>
</div>
</div>

<script src="js/docu_business_payment.js"></script>
</body>
</html>
