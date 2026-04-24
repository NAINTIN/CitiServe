<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Barangay Permit (Construction)</title>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/barangay_permit.css">
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="content-area">
<div class="form-breadcrumb" id="form-breadcrumb"></div>

<div class="docu-barangay-permit-construction-wrapper">

  <!-- LEFT -->
  <div class="docu-barangay-permit-construction-main">

    <h1 class="docu-barangay-permit-construction-title">Barangay Permit (Construction)</h1>

    <!-- ABOUT -->
    <div class="docu-barangay-permit-construction-card">
      <div class="docu-barangay-permit-construction-card-bar docu-barangay-permit-construction-pink">
        <img src="images/docu-about-permit.png">
        About This Document
      </div>
      <div class="docu-barangay-permit-construction-card-body">
        Required permit for construction, renovation, or special activities within the barangay.
        Must be secured before starting any construction work.
      </div>
    </div>

    <!-- REQUIREMENTS -->
    <div class="docu-barangay-permit-construction-card">
      <div class="docu-barangay-permit-construction-card-bar1">
        <img src="images/requirements-to-hold.png">
        Requirement/s to Upload
      </div>

      <div class="docu-barangay-permit-construction-card-body docu-barangay-permit-construction-grid-2">

        <div class="docu-barangay-permit-construction-req-item">
          <img src="images/req-one.png">
          <span>Construction or Activity Plan / Sketch</span>
        </div>

        <div class="docu-barangay-permit-construction-req-item">
          <img src="images/req-two.png">
          <span>Valid Government-Issued ID</span>
        </div>

      </div>
    </div>

    <!-- INFO -->
    <div class="docu-barangay-permit-construction-card">
      <div class="docu-barangay-permit-construction-card-bar">
        <img src="images/information-reuired.png">
        Information Required in the Form
      </div>

      <div class="docu-barangay-permit-construction-card-body docu-barangay-permit-construction-grid-3">

        <!-- Row 1 -->
        <div><img src="images/full-circle.png"> Full Name of Applicant <span class="docu-barangay-permit-construction-required">*</span></div>
        <div><img src="images/full-circle.png"> Purpose / Nature of Activity <span class="docu-barangay-permit-construction-required">*</span></div>
        <div><img src="images/full-circle.png"> Upload Construction Plan / Sketch <span class="docu-barangay-permit-construction-required">*</span></div>

        <!-- Row 2 -->
        <div><img src="images/full-circle.png"> Complete Address <span class="docu-barangay-permit-construction-required">*</span></div>
        <div><img src="images/full-circle.png"> Location of Construction / Activity <span class="docu-barangay-permit-construction-required">*</span></div>
        <div><img src="images/full-circle.png"> Upload Applicant's Valid ID <span class="docu-barangay-permit-construction-required">*</span></div>

        <!-- Row 3 -->
        <div><img src="images/full-circle.png"> Contact Number <span class="docu-barangay-permit-construction-required">*</span></div>
        <div><img src="images/full-circle.png"> Estimated Start Date <span class="docu-barangay-permit-construction-required">*</span></div>
        <div></div>

        <!-- Row 4 -->
        <div><img src="images/no-fill-circle.png"> Email Address</div>
        <div><img src="images/full-circle.png"> Estimated Completion Date <span class="docu-barangay-permit-construction-required">*</span></div>
        <div></div>

      </div>

      <div class="docu-barangay-permit-construction-bottom-row">
        <small class="docu-barangay-permit-construction-note">* Required fields</small>
        <div class="docu-barangay-permit-construction-copyright">
          <span class="docu-barangay-permit-construction-brand">CitiServe</span> © 2026. All rights reserved.
        </div>
      </div>
    </div>

  </div>

  <!-- RIGHT -->
  <div class="docu-barangay-permit-construction-side">

    <div class="docu-barangay-permit-construction-card">
      <div class="docu-barangay-permit-construction-card-bar docu-barangay-permit-construction-gradient">
        Document Fee
      </div>
      <div class="docu-barangay-permit-construction-card-body docu-barangay-permit-construction-center">
        <h2>₱200.00</h2>
        <p>Payment required before processing</p>
      </div>
    </div>

    <div class="docu-barangay-permit-construction-card">
      <div class="docu-barangay-permit-construction-card-bar">
        Processing Time
      </div>
      <div class="docu-barangay-permit-construction-card-body docu-barangay-permit-construction-proc">
        <div class="docu-barangay-permit-construction-card-body">
          <div class="docu-barangay-permit-construction-bold">
            <img src="images/processing-time.png">
            2–3 business days
          </div>
          <small>
            <p>Requests after 3:00 PM may be processed the next business day.</p>
            <p>If there are many requests queued in the system, processing may take longer than the estimated time.</p>
          </small>
        </div>
      </div>
    </div>

    <div class="docu-barangay-permit-construction-claim">
      <img src="images/physical-claiming.png">
    </div>

    <div class="docu-barangay-permit-construction-btn-group">
      <a href="barangay_permit_form.php" class="docu-barangay-permit-construction-btn">
        <img src="images/request-form.png">
      </a>
      <a href="document.php" class="docu-barangay-permit-construction-btn">
        <img src="images/back-to-documents.png">
      </a>
    </div>

  </div>

</div>
</div>
<script>
  const trail = [
  { label: "Document Requests",  href: "document.php" },
  { label: "Request Document",   href: "document.php" },
  { label: "Barangay Permit (Construction)",  href: null }
];

  function renderBreadcrumb() {
    const el = document.getElementById("form-breadcrumb");
    el.innerHTML = trail.map((item, i) => {
      const isLast = i === trail.length - 1;
      const sep = i > 0 ? `<span class="form-sep">></span>` : "";
      if (isLast) return `${sep}<span class="form-active">${item.label}</span>`;
      return `${sep}<a href="${item.href}">${item.label}</a>`;
    }).join("");
  }
  renderBreadcrumb();
  </script>
</body>
</html>
