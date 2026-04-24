<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Certificate of Indigency</title>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/indigency.css">
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="content-area">
<div class="form-breadcrumb" id="form-breadcrumb"></div>

<div class="docu-certificate-of-indigency-wrapper">

  <!-- LEFT -->
  <div class="docu-certificate-of-indigency-main">

    <h1 class="docu-certificate-of-indigency-title">Certificate of Indigency</h1>

    <!-- ABOUT -->
    <div class="docu-certificate-of-indigency-card">
      <div class="docu-certificate-of-indigency-card-bar docu-certificate-of-indigency-pink">
        <img src="images/docu-about-indigency.png">
        About This Document
      </div>
      <div class="docu-certificate-of-indigency-card-body">
        This certificate attests that the resident belongs to a low-income household.
        Typically used for medical assistance, scholarship applications, and government aid programs.
      </div>
    </div>

    <!-- REQUIREMENTS -->
    <div class="docu-certificate-of-indigency-card">
      <div class="docu-certificate-of-indigency-card-bar1">
        <img src="images/requirements-to-hold.png">
        Requirement/s to Upload
      </div>

      <div class="docu-certificate-of-indigency-card-body docu-certificate-of-indigency-grid-2">

        <div class="docu-certificate-of-indigency-req-item">
          <img src="images/req-one.png">
          <span>Valid Government-Issued ID (upload required)</span>
        </div>

      </div>
    </div>

    <!-- INFO -->
    <div class="docu-certificate-of-indigency-card">
      <div class="docu-certificate-of-indigency-card-bar">
        <img src="images/information-reuired.png">
        Information Required in the Form
      </div>

      <div class="docu-certificate-of-indigency-card-body docu-certificate-of-indigency-grid-3">

        <!-- Row 1 -->
        <div><img src="images/full-circle.png"> Full Name <span class="docu-certificate-of-indigency-required">*</span></div>
        <div><img src="images/full-circle.png"> Civil Status <span class="docu-certificate-of-indigency-required">*</span></div>
        <div><img src="images/full-circle.png"> Purpose (e.g., medical assistance, scholarship) <span class="docu-certificate-of-indigency-required">*</span></div>

        <!-- Row 2 -->
        <div><img src="images/full-circle.png"> Complete Address <span class="docu-certificate-of-indigency-required">*</span></div>
        <div><img src="images/full-circle.png"> Citizenship <span class="docu-certificate-of-indigency-required">*</span></div>
        <div><img src="images/full-circle.png"> Upload valid ID <span class="docu-certificate-of-indigency-required">*</span></div>

        <!-- Row 3 -->
        <div><img src="images/full-circle.png"> Date of Birth <span class="docu-certificate-of-indigency-required">*</span></div>
        <div><img src="images/full-circle.png"> Contact Number <span class="docu-certificate-of-indigency-required">*</span></div>
        <div></div>

        <!-- Row 4 -->
        <div><img src="images/full-circle.png"> Age <span class="docu-certificate-of-indigency-required">*</span></div>
        <div><img src="images/no-fill-circle.png"> Email Address</div>
        <div></div>

      </div>

      <div class="docu-certificate-of-indigency-bottom-row">
        <small class="docu-certificate-of-indigency-note">* Required fields</small>
        <div class="docu-certificate-of-indigency-copyright">
          <span class="docu-certificate-of-indigency-brand">CitiServe</span> © 2026. All rights reserved.
        </div>
      </div>
    </div>

  </div>

  <!-- RIGHT -->
  <div class="docu-certificate-of-indigency-side">

    <div class="docu-certificate-of-indigency-card">
      <div class="docu-certificate-of-indigency-card-bar docu-certificate-of-indigency-gradient">
        Document Fee
      </div>
      <div class="docu-certificate-of-indigency-card-body docu-certificate-of-indigency-center">
        <h2>₱20.00</h2>
        <p>Payment required before processing</p>
      </div>
    </div>

    <div class="docu-certificate-of-indigency-card">
      <div class="docu-certificate-of-indigency-card-bar">
        Processing Time
      </div>
      <div class="docu-certificate-of-indigency-card-body docu-certificate-of-indigency-proc">
        <div class="docu-certificate-of-indigency-card-body">
          <div class="docu-certificate-of-indigency-bold">
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

    <div class="docu-certificate-of-indigency-claim">
      <img src="images/physical-claiming.png">
    </div>

    <div class="docu-certificate-of-indigency-btn-group">
      <a href="indigency_form.php" class="docu-certificate-of-indigency-btn">
        <img src="images/request-form.png">
      </a>
      <a href="document.php" class="docu-certificate-of-indigency-btn">
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
  { label: "Barangay Indigendcy",  href: null }
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
