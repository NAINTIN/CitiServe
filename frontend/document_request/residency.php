<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Certificate of Residency</title>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/residency.css">
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="content-area">
<div class="form-breadcrumb" id="form-breadcrumb"></div>

<div class="docu-certificate-of-residency-wrapper">

  <!-- LEFT -->
  <div class="docu-certificate-of-residency-main">

    <h1 class="docu-certificate-of-residency-title">Certificate of Residency</h1>

    <!-- ABOUT -->
    <div class="docu-certificate-of-residency-card">
      <div class="docu-certificate-of-residency-card-bar docu-certificate-of-residency-pink">
        <img src="images/docu-about-residency.png">
        About This Document
      </div>
      <div class="docu-certificate-of-residency-card-body">
        This certificate confirms that the applicant is a registered resident of Barangay Kalayaan.
        Used for school enrollment, court filings, and other official purposes.
      </div>
    </div>

    <!-- REQUIREMENTS -->
    <div class="docu-certificate-of-residency-card">
      <div class="docu-certificate-of-residency-card-bar1">
        <img src="images/requirements-to-hold.png">
        Requirement/s to Upload
      </div>

      <div class="docu-certificate-of-residency-card-body docu-certificate-of-residency-grid-2">

        <div class="docu-certificate-of-residency-req-item">
          <img src="images/req-one.png">
          <span>Valid Government-Issued ID</span>
        </div>

        <div class="docu-certificate-of-residency-req-item">
          <img src="images/req-two.png">
          <span>Proof of Address (utility bill or lease contract)</span>
        </div>

      </div>
    </div>

    <!-- INFO -->
    <div class="docu-certificate-of-residency-card">
      <div class="docu-certificate-of-residency-card-bar">
        <img src="images/information-reuired.png">
        Information Required in the Form
      </div>

      <div class="docu-certificate-of-residency-card-body docu-certificate-of-residency-grid-3">

        <!-- Row 1 -->
        <div><img src="images/full-circle.png"> Full Name <span class="docu-certificate-of-residency-required">*</span></div>
        <div><img src="images/full-circle.png"> Civil Status <span class="docu-certificate-of-residency-required">*</span></div>
        <div><img src="images/full-circle.png"> Length of Residency (in years) <span class="docu-certificate-of-residency-required">*</span></div>

        <!-- Row 2 -->
        <div><img src="images/full-circle.png"> Complete Address <span class="docu-certificate-of-residency-required">*</span></div>
        <div><img src="images/full-circle.png"> Citizenship <span class="docu-certificate-of-residency-required">*</span></div>
        <div><img src="images/full-circle.png"> Purpose of Request <span class="docu-certificate-of-residency-required">*</span></div>

        <!-- Row 3 -->
        <div><img src="images/full-circle.png"> Date of Birth <span class="docu-certificate-of-residency-required">*</span></div>
        <div><img src="images/full-circle.png"> Contact Number <span class="docu-certificate-of-residency-required">*</span></div>
        <div><img src="images/full-circle.png"> Upload Valid ID <span class="docu-certificate-of-residency-required">*</span></div>

        <!-- Row 4 -->
        <div><img src="images/full-circle.png"> Age <span class="docu-certificate-of-residency-required">*</span></div>
        <div><img src="images/no-fill-circle.png"> Email Address</div>
        <div><img src="images/full-circle.png"> Upload Proof of Address <span class="docu-certificate-of-residency-required">*</span></div>

      </div>

      <div class="docu-certificate-of-residency-bottom-row">
        <small class="docu-certificate-of-residency-note">* Required fields</small>
        <div class="docu-certificate-of-residency-copyright">
          <span class="docu-certificate-of-residency-brand">CitiServe</span> © 2026. All rights reserved.
        </div>
      </div>
    </div>

  </div>

  <!-- RIGHT -->
  <div class="docu-certificate-of-residency-side">

    <div class="docu-certificate-of-residency-card">
      <div class="docu-certificate-of-residency-card-bar docu-certificate-of-residency-gradient">
        Document Fee
      </div>
      <div class="docu-certificate-of-residency-card-body docu-certificate-of-residency-center">
        <h2>₱30.00</h2>
        <p>Payment required before processing</p>
      </div>
    </div>

    <div class="docu-certificate-of-residency-card">
      <div class="docu-certificate-of-residency-card-bar">
        Processing Time
      </div>
      <div class="docu-certificate-of-residency-card-body docu-certificate-of-residency-proc">
        <div class="docu-certificate-of-residency-card-body">
          <div class="docu-certificate-of-residency-bold">
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

    <div class="docu-certificate-of-residency-claim">
      <img src="images/physical-claiming.png">
    </div>

    <div class="docu-certificate-of-residency-btn-group">
      <a href="residency_form.php" class="docu-certificate-of-residency-btn">
        <img src="images/request-form.png">
      </a>
      <a href="document.php" class="docu-certificate-of-residency-btn">
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
  { label: "Barangay Residency",  href: null }
];

  function renderBreadcrumb() {
    const el = document.getElementById("form-breadcrumb");
    el.innerHTML = trail.map((item, i) => {
      const isLast = i === trail.length - 1;
      const sep = i > 0 ? `<span class="form-sep">›</span>` : "";
      if (isLast) return `${sep}<span class="form-active">${item.label}</span>`;
      return `${sep}<a href="${item.href}">${item.label}</a>`;
    }).join("");
  }
  renderBreadcrumb();
  </script>
</body>
</html>
