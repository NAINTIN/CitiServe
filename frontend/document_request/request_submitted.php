<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Request Submitted – CitiServe</title>
<link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/request_submitted.css">
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="content-area">
  <div class="ticket-wrapper">

    <!-- Ticket background image -->
    <img src="images/request-receipt.png" alt="" class="ticket-bg" />

    <div class="ticket-content">

      <!-- HEADER -->
      <div class="ticket-header">
        <img src="images/request-icon-success.png" alt="Success" class="success-icon" />
        <div class="ticket-title">Request Submitted</div>
        <div class="ticket-subtitle">Your document request has been received.</div>
      </div>

      <!-- Dashed divider with notch circles — only below Status -->
      <div class="divider-dashed-wrap"></div>

      <!-- Reference number box -->
      <div class="ref-box">
        <div class="ref-label">Request Reference Number</div>
        <div class="ref-number">DOC-0000000001</div>
        <div class="ref-hint">Save this number for tracking your request.</div>
      </div>

      <!-- Detail rows -->
      <div class="detail-row">
        <span class="detail-label">Document Type</span>
        <span class="detail-value">Barangay Business Clearance</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Amount</span>
        <span class="detail-value amount">₱150.00</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Date</span>
        <span class="detail-value">April 2, 2026</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Time</span>
        <span class="detail-value">9:20 AM</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Payment Method</span>
        <span class="detail-value">GCash</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Status</span>
        <span class="detail-value"><img src="images/request-reviewed.png" alt="Received" style="height: 20px; object-fit: contain;" /></span>
      </div>

           <!-- Solid divider below subtitle -->
      <div class="divider-solid"></div>

      <!-- WHAT HAPPENS NEXT -->
      <div class="next-title">What Happens Next</div>

      <div class="next-item">
        <img src="images/request-eye-icon.png" alt="" class="next-icon" />
        <div class="next-text">Barangay staff will review your uploaded documents and requirements.</div>
      </div>
      <div class="next-item">
        <img src="images/request-wallet.png" alt="" class="next-icon" />
        <div class="next-text">Your payment will be verified manually by the cashier.</div>
      </div>
      <div class="next-item">
        <img src="images/request-bell-icon.png" alt="" class="next-icon" />
        <div class="next-text">You will be notified when your document is ready for pickup.</div>
      </div>
      <div class="next-item">
        <img src="images/request-claim.png" alt="" class="next-icon" />
        <div class="next-text">Claim your document in person at the Barangay Hall with a valid ID.</div>
      </div>

      <!-- Reminder — full image -->
      <img src="images/request-reminder.png" alt="Reminder" class="reminder-img" />

<!-- Solid divider below subtitle -->
      <div class="divider-solid1"></div>

      <!-- Buttons — pure img, no styling keme -->
      <div class="btn-group">
        <button class="btn-img">
          <img src="images/request-view.png" alt="View My Requests" />
        </button>
        <button class="btn-img">
          <img src="images/request-back-to-dashboard.png" alt="Back to Dashboard" />
        </button>
      </div>
  </div>
 <!-- Logo — bottom-right ng ticket -->
    <div class="form-logo">
      <img src="images/docu-logo.png">
      <div class="form-logo-text">
        <span class="logo-pink">CitiServe</span>
        <span class="logo-gray"> © 2026. All rights reserved.</span>
      </div>
    </div>
    </div>

    <!-- Faded background logo -->
        <img src="images/request-faded-logo.png" class="faded-logo" /></div>
</div>

</body>
</html>
