<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Barangay Clearance</title>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/barangay_clearance.css">
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="content-area">

<div class="form-breadcrumb" id="form-breadcrumb"></div>

<div class="docu-barangay-clearance-wrapper">

  <!-- LEFT -->
  <div class="docu-barangay-clearance-main">

    <h1 class="docu-barangay-clearance-title">Barangay Clearance</h1>

    <!-- ABOUT -->
    <div class="docu-barangay-clearance-card">
      <div class="docu-barangay-clearance-card-bar docu-barangay-clearance-pink">
        <img src="images/docu-about-bc.png">
        About This Document
      </div>
      <div class="docu-barangay-clearance-card-body">
        A Barangay Clearance certifies that a resident has no derogatory record within the barangay.
        Commonly required for employment, business, and government transactions.
      </div>
    </div>

    <!-- REQUIREMENTS -->
    <div class="docu-barangay-clearance-card">
      <div class="docu-barangay-clearance-card-bar1">
        <img src="images/requirements-to-hold.png">
        Requirement/s to Upload
      </div>

      <div class="docu-barangay-clearance-card-body docu-barangay-clearance-grid-2">

        <div class="docu-barangay-clearance-req-item">
          <img src="images/req-one.png">
          <span>Valid Government-Issued ID (upload required)</span>
        </div>

      </div>
    </div>

    <!-- INFO -->
    <div class="docu-barangay-clearance-card">
      <div class="docu-barangay-clearance-card-bar">
        <img src="images/information-reuired.png">
        Information Required in the Form
      </div>

      <div class="docu-barangay-clearance-card-body docu-barangay-clearance-grid-3">

        <!-- Row 1 -->
        <div><img src="images/full-circle.png"> Full Name <span class="docu-barangay-clearance-required">*</span></div>
        <div><img src="images/full-circle.png"> Civil Status <span class="docu-barangay-clearance-required">*</span></div>
        <div><img src="images/full-circle.png"> Purpose of Clearance <span class="docu-barangay-clearance-required">*</span></div>

        <!-- Row 2 -->
        <div><img src="images/full-circle.png"> Complete Address <span class="docu-barangay-clearance-required">*</span></div>
        <div><img src="images/full-circle.png"> Citizenship <span class="docu-barangay-clearance-required">*</span></div>
        <div><img src="images/full-circle.png"> Upload valid ID <span class="docu-barangay-clearance-required">*</span></div>

        <!-- Row 3 -->
        <div><img src="images/full-circle.png"> Date of Birth <span class="docu-barangay-clearance-required">*</span></div>
        <div><img src="images/full-circle.png"> Contact Number <span class="docu-barangay-clearance-required">*</span></div>
        <div></div>

        <!-- Row 4 -->
        <div><img src="images/full-circle.png"> Age <span class="docu-barangay-clearance-required">*</span></div>
        <div><img src="images/no-fill-circle.png"> Email Address</div>
        <div></div>

      </div>

      <div class="docu-barangay-clearance-bottom-row">
        <small class="docu-barangay-clearance-note">* Required fields</small>
        <div class="docu-barangay-clearance-copyright">
          <span class="docu-barangay-clearance-brand">CitiServe</span> © 2026. All rights reserved.
        </div>
      </div>
    </div>

  </div>

  <!-- RIGHT -->
  <div class="docu-barangay-clearance-side">

    <div class="docu-barangay-clearance-card">
      <div class="docu-barangay-clearance-card-bar docu-barangay-clearance-gradient">
        Document Fee
      </div>
      <div class="docu-barangay-clearance-card-body docu-barangay-clearance-center">
        <h2>₱50.00</h2>
        <p>Payment required before processing</p>
      </div>
    </div>

    <div class="docu-barangay-clearance-card">
      <div class="docu-barangay-clearance-card-bar">
        Processing Time
      </div>
      <div class="docu-barangay-clearance-card-body docu-barangay-clearance-proc">
        <div class="docu-barangay-clearance-card-body">
          <div class="docu-barangay-clearance-bold">
            <img src="images/processing-time.png">
            Same day (if submitted before 3:00 PM)
          </div>
          <small>
            <p>Requests after 3:00 PM may be processed the next business day.</p>
            <p>If there are many requests queued in the system, processing may take longer than the estimated time.</p>
          </small>
        </div>
      </div>
    </div>

    <div class="docu-barangay-clearance-claim">
      <img src="images/physical-claiming.png">
    </div>

    <div class="docu-barangay-clearance-btn-group">
      <a href="barangay_clearance_form.php" class="docu-barangay-clearance-btn">
        <img src="images/request-form.png">
      </a>
      <a href="document.php" class="docu-barangay-clearance-btn">
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
  { label: "Barangay Clearance",  href: null }
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
