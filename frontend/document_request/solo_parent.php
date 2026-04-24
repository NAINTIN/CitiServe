<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Solo Parent Certificate</title>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/solo_parent.css">
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="content-area">
<div class="form-breadcrumb" id="form-breadcrumb"></div>

<div class="docu-solo-parent-certificate-wrapper">

  <!-- LEFT -->
  <div class="docu-solo-parent-certificate-main">

    <h1 class="docu-solo-parent-certificate-title">Solo Parent Certificate</h1>

    <!-- ABOUT -->
    <div class="docu-solo-parent-certificate-card">
      <div class="docu-solo-parent-certificate-card-bar docu-solo-parent-certificate-pink">
        <img src="images/docu-about-solo.png">
        About This Document
      </div>
      <div class="docu-solo-parent-certificate-card-body">
        Certificate issued to solo parents under Republic Act 8972. Entitles the solo parent to benefits
        and privileges under the Solo Parents' Welfare Act.
      </div>
    </div>

    <!-- REQUIREMENTS -->
    <div class="docu-solo-parent-certificate-card">
      <div class="docu-solo-parent-certificate-card-bar1">
        <img src="images/requirements-to-hold.png">
        Requirement/s to Upload
      </div>

      <div class="docu-solo-parent-certificate-card-body docu-solo-parent-certificate-grid-2">

        <div class="docu-solo-parent-certificate-req-item">
          <img src="images/req-one.png">
          <span>Valid Government-Issued ID</span>
        </div>

        <div class="docu-solo-parent-certificate-req-item">
          <img src="images/req-two.png">
          <span>Child's PSA Birth Certificate</span>
        </div>

      </div>
    </div>

    <!-- INFO -->
    <div class="docu-solo-parent-certificate-card">
      <div class="docu-solo-parent-certificate-card-bar">
        <img src="images/information-reuired.png">
        Information Required in the Form
      </div>

      <div class="docu-solo-parent-certificate-card-body docu-solo-parent-certificate-grid-3">

        <!-- Row 1 -->
        <div><img src="images/full-circle.png"> Full Name of Parent <span class="docu-solo-parent-certificate-required">*</span></div>
        <div><img src="images/full-circle.png"> Contact Number <span class="docu-solo-parent-certificate-required">*</span></div>
        <div><img src="images/full-circle.png"> Reason for Solo Parenthood <span class="docu-solo-parent-certificate-required">*</span></div>

        <!-- Row 2 -->
        <div><img src="images/full-circle.png"> Complete Address <span class="docu-solo-parent-certificate-required">*</span></div>
        <div><img src="images/no-fill-circle.png"> Email Address</div>
        <div><img src="images/full-circle.png"> Upload Parent's Valid ID <span class="docu-solo-parent-certificate-required">*</span></div>

        <!-- Row 3 -->
        <div><img src="images/full-circle.png"> Date of Birth <span class="docu-solo-parent-certificate-required">*</span></div>
        <div><img src="images/full-circle.png"> Number of Children <span class="docu-solo-parent-certificate-required">*</span></div>
        <div><img src="images/full-circle.png"> Upload Child's PSA Birth Certificate <span class="docu-solo-parent-certificate-required">*</span></div>

        <!-- Row 4 -->
        <div><img src="images/full-circle.png"> Age <span class="docu-solo-parent-certificate-required">*</span></div>
        <div><img src="images/full-circle.png"> Name(s) of Child/Children <span class="docu-solo-parent-certificate-required">*</span></div>
        <div></div>

      </div>

      <div class="docu-solo-parent-certificate-bottom-row">
        <small class="docu-solo-parent-certificate-note">* Required fields</small>
        <div class="docu-solo-parent-certificate-copyright">
          <span class="docu-solo-parent-certificate-brand">CitiServe</span> © 2026. All rights reserved.
        </div>
      </div>
    </div>

  </div>

  <!-- RIGHT -->
  <div class="docu-solo-parent-certificate-side">

    <div class="docu-solo-parent-certificate-card">
      <div class="docu-solo-parent-certificate-card-bar docu-solo-parent-certificate-gradient">
        Document Fee
      </div>
      <div class="docu-solo-parent-certificate-card-body docu-solo-parent-certificate-center">
        <h2>₱50.00</h2>
        <p>Payment required before processing</p>
      </div>
    </div>

    <div class="docu-solo-parent-certificate-card">
      <div class="docu-solo-parent-certificate-card-bar">
        Processing Time
      </div>
      <div class="docu-solo-parent-certificate-card-body docu-solo-parent-certificate-proc">
        <div class="docu-solo-parent-certificate-card-body">
          <div class="docu-solo-parent-certificate-bold">
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

    <div class="docu-solo-parent-certificate-claim">
      <img src="images/physical-claiming.png">
    </div>

    <div class="docu-solo-parent-certificate-btn-group">
      <a href="solo_parent_form.php" class="docu-solo-parent-certificate-btn">
        <img src="images/request-form.png">
      </a>
      <a href="document.php" class="docu-solo-parent-certificate-btn">
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
  { label: "Solo Parent Cetificate",  href: null }
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
