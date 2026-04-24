<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Barangay Business Clearance</title>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/business_clearance.css">
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="content-area">
<div class="form-breadcrumb" id="form-breadcrumb"></div>

<div class="docu-business-clearance-wrapper">

  <!-- LEFT -->
  <div class="docu-business-clearance-main">

    <h1 class="docu-business-clearance-title">Barangay Business Clearance</h1>

    <!-- ABOUT -->
    <div class="docu-business-clearance-card">
      <div class="docu-business-clearance-card-bar docu-business-clearance-pink">
        <img src="images/abouthisdocument.png">
        About This Document
      </div>
      <div class="docu-business-clearance-card-body">
        Required for all businesses operating within Barangay Kalayaan.
        Certifies that the business complies with barangay rules and regulations.
      </div>
    </div>

    <!-- REQUIREMENTS -->
    <div class="docu-business-clearance-card">
      <div class="docu-business-clearance-card-bar1">
        <img src="images/requirements-to-hold.png">
        Requirement/s to Upload
      </div>

      <div class="docu-business-clearance-card-body docu-business-clearance-grid-2">

        <div class="docu-business-clearance-req-item">
          <img src="images/req-one.png">
          <span>DTI Business Registration Certificate</span>
        </div>

        <div class="docu-business-clearance-req-item">
          <img src="images/req-three.png">
          <span>Proof of Business Location (photos or lease)</span>
        </div>

        <div class="docu-business-clearance-req-item">
          <img src="images/req-two.png">
          <span>Valid Government-Issued ID</span>
        </div>

      </div>
    </div>

    <!-- INFO -->
    <div class="docu-business-clearance-card">
      <div class="docu-business-clearance-card-bar">
        <img src="images/information-reuired.png">
        Information Required in the Form
      </div>

      <div class="docu-business-clearance-card-body docu-business-clearance-grid-3">

        <!-- Row 1 -->
        <div><img src="images/full-circle.png"> Business Owner Full Name <span class="docu-business-clearance-required">*</span></div>
        <div><img src="images/full-circle.png"> Contact Number <span class="docu-business-clearance-required">*</span></div>
        <div><img src="images/full-circle.png"> Upload Proof of Business Location <span class="docu-business-clearance-required">*</span></div>

        <!-- Row 2 -->
        <div><img src="images/full-circle.png"> Business Name <span class="docu-business-clearance-required">*</span></div>
        <div><img src="images/no-fill-circle.png"> Email Address</div>
         <div></div>

        <!-- Row 3 -->
        <div><img src="images/full-circle.png"> Business Address <span class="docu-business-clearance-required">*</span></div>
        <div><img src="images/full-circle.png"> Upload DTI Registration <span class="docu-business-clearance-required">*</span></div>
        <div></div>
        
        <!-- Row 4 (col 1 only) -->
        <div><img src="images/full-circle.png"> Type of Business / Nature of Business <span class="docu-business-clearance-required">*</span></div>
         <div><img src="images/full-circle.png"> Upload Owner's Valid ID <span class="docu-business-clearance-required">*</span></div>

      </div>

      <div class="docu-business-clearance-bottom-row">
  <small class="docu-business-clearance-note">* Required fields</small>
  <div class="docu-business-clearance-copyright">
    <span class="docu-business-clearance-brand">CitiServe</span> © 2026. All rights reserved.
  </div>
</div>
    </div>

  </div>

  <!-- RIGHT -->
  <div class="docu-business-clearance-side">

    <div class="docu-business-clearance-card">
      <div class="docu-business-clearance-card-bar docu-business-clearance-gradient">
        Document Fee
      </div>

      <div class="docu-business-clearance-card-body docu-business-clearance-center">
        <h2>₱150.00</h2>
        <p>Payment required before processing</p>
      </div>
    </div>

    <div class="docu-business-clearance-card">
      <div class="docu-business-clearance-card-bar">
        Processing Time
      </div>

        <div class="docu-business-clearance-card-body docu-business-clearance-proc">
      <div class="docu-business-clearance-card-body">
        <div class="docu-business-clearance-bold">
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

    <div class="docu-business-clearance-claim">
      <img src="images/physical-claiming.png">
    </div>

   <div class="docu-business-clearance-btn-group">

  <a href="business_clearance_form.php" class="docu-business-clearance-btn">
    <img src="images/request-form.png">
  </a>

  <a href="document.php" class="docu-business-clearance-btn">
    <img src="images/back-to-documents.png">
  </a>

</div>
</div>

<script>
  const trail = [
  { label: "Document Requests",  href: "document.php" },
  { label: "Request Document",   href: "document.php" },
  { label: "Barangay Business Clearance",  href: null }
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