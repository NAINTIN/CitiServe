<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Barangay ID</title>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/barangay_id.css">
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="content-area">
<div class="form-breadcrumb" id="form-breadcrumb"></div>

<div class="docu-barangay-id-wrapper">

  <!-- LEFT -->
  <div class="docu-barangay-id-main">

    <h1 class="docu-barangay-id-title">Barangay ID</h1>

    <!-- ABOUT -->
    <div class="docu-barangay-id-card">
      <div class="docu-barangay-id-card-bar docu-barangay-id-pink">
        <img src="images/docu-about-id.png">
        About This Document
      </div>
      <div class="docu-barangay-id-card-body">
        Official Barangay ID issued to registered residents. Serves as a local identification document within the barangay.
      </div>
    </div>

    <!-- REQUIREMENTS -->
    <div class="docu-barangay-id-card">
      <div class="docu-barangay-id-card-bar1">
        <img src="images/requirements-to-hold.png">
        Requirement/s to Upload
      </div>

      <div class="docu-barangay-id-card-body docu-barangay-id-grid-2">

        <div class="docu-barangay-id-req-item">
          <img src="images/req-one.png">
          <span>Valid Government-Issued ID</span>
        </div>

        <div class="docu-barangay-id-req-item">
          <img src="images/req-two.png">
          <span>1x1 ID Photo (white background)</span>
        </div>

      </div>
    </div>

    <!-- INFO -->
    <div class="docu-barangay-id-card">
      <div class="docu-barangay-id-card-bar">
        <img src="images/information-reuired.png">
        Information Required in the Form
      </div>

      <div class="docu-barangay-id-card-body docu-barangay-id-grid-3">

        <!-- Row 1 -->
        <div><img src="images/full-circle.png"> Full Name <span class="docu-barangay-id-required">*</span></div>
        <div><img src="images/full-circle.png"> Gender <span class="docu-barangay-id-required">*</span></div>
        <div><img src="images/full-circle.png"> Upload Valid ID <span class="docu-barangay-id-required">*</span></div>

        <!-- Row 2 -->
        <div><img src="images/full-circle.png"> Complete Address <span class="docu-barangay-id-required">*</span></div>
        <div><img src="images/full-circle.png"> Civil Status <span class="docu-barangay-id-required">*</span></div>
        <div><img src="images/full-circle.png"> Upload 1x1 ID Photo (white background) <span class="docu-barangay-id-required">*</span></div>

        <!-- Row 3 -->
        <div><img src="images/full-circle.png"> Date of Birth <span class="docu-barangay-id-required">*</span></div>
        <div><img src="images/full-circle.png"> Contact Number <span class="docu-barangay-id-required">*</span></div>
        <div></div>

        <!-- Row 4 -->
        <div><img src="images/full-circle.png"> Age <span class="docu-barangay-id-required">*</span></div>
        <div><img src="images/no-fill-circle.png"> Email Address</div>
        <div></div>

      </div>

      <div class="docu-barangay-id-bottom-row">
        <small class="docu-barangay-id-note">* Required fields</small>
        <div class="docu-barangay-id-copyright">
          <span class="docu-barangay-id-brand">CitiServe</span> © 2026. All rights reserved.
        </div>
      </div>
    </div>

  </div>

  <!-- RIGHT -->
  <div class="docu-barangay-id-side">

    <div class="docu-barangay-id-card">
      <div class="docu-barangay-id-card-bar docu-barangay-id-gradient">
        Document Fee
      </div>
      <div class="docu-barangay-id-card-body docu-barangay-id-center">
        <h2>₱75.00</h2>
        <p>Payment required before processing</p>
      </div>
    </div>

    <div class="docu-barangay-id-card">
      <div class="docu-barangay-id-card-bar">
        Processing Time
      </div>
      <div class="docu-barangay-id-card-body docu-barangay-id-proc">
        <div class="docu-barangay-id-card-body">
          <div class="docu-barangay-id-bold">
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

    <div class="docu-barangay-id-claim">
      <img src="images/physical-claiming.png">
    </div>

    <div class="docu-barangay-id-btn-group">
      <a href="barangay_id_form.php" class="docu-barangay-id-btn">
        <img src="images/request-form.png">
      </a>
      <a href="document.php" class="docu-barangay-id-btn">
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
  { label: "Barangay ID",  href: null }
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
