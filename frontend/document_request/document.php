<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Barangay Document Requests</title>

<link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/document.css">
</head>

<body>

<?php include 'navbar.php'; ?>
<div class="content-area">
<div class="form-breadcrumb" id="form-breadcrumb"></div>


<div class="page-header">
  <h1>Barangay Document Requests</h1>
  <p>Select the document you need. Review requirements and fees before proceeding.</p>
</div>

<!-- REMINDERS --> <div class="reminders"> <img src="images/docu-yellowbg.png"> </div>

<div class="grid">


<!-- 1 -->
<div class="card" style="background-image:url('images/Barangay Business Clearance - Container (1).png')">
  <div class="card-content">
    <div class="card-main">
      <div class="card-top">
        <img src="images/docu-bbc.png" class="card-icon">
        <div>
          <div class="card-title">Barangay Business Clearance</div>
          <div class="card-desc">
            Required clearance for businesses in Barangay Kalayaan, certifying compliance with local regulations.
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="card-meta">
            <span><b>Fee:</b> <span class="price">₱150.00</span></span>
            <span><b>Processing:</b> <span class="processing">2–3 business days</span></span>
            <span><b>Requirements:</b>
                <ul>
                <li>DTI Business Registration Certificate</li>
                <li>Valid Government-Issued ID</li>
                <li>Proof of Business Location</li>
                </ul>
            </span>
        </div>
    </div>

    <a href="business_clearance.php">
     <img src="images/docu-view-deets.png" class="view-btn">
    </a>
  </div>
</div>

<!-- 2 -->
<div class="card" style="background-image:url('images/Barangay Clearance - Container.png')">
  <div class="card-content">
    <div class="card-main">
      <div class="card-top">
        <img src="images/docu-bc.png" class="card-icon">
        <div>
          <div class="card-title">Barangay Clearance</div>
          <div class="card-desc">
            General clearance certifying that a resident has no derogatory record in the barangay.
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <!-- 2 -->
        <div class="card-meta">
            <span><b>Fee:</b> <span class="price">₱50.00</span></span>
            <span><b>Processing:</b> <span class="processing">Same day (if submitted before 3 PM)</span></span>
            <span><b>Requirements:</b>
                <ul>
                <li>Valid Government-Issued ID</li>
                </ul>
            </span>
        </div>
    </div>

    <a href="barangay_clearance.php">
     <img src="images/docu-view-deets.png" class="view-btn">
    </a>
  </div>
</div>

<!-- 3 -->
<div class="card" style="background-image:url('images/Barangay ID - Container.png')">
  <div class="card-content">
    <div class="card-main">
      <div class="card-top">
        <img src="images/docu-bid.png" class="card-icon">
        <div>
          <div class="card-title">Barangay ID</div>
          <div class="card-desc">
            Official Barangay ID card issued to registered residents.
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="card-meta">
            <span><b>Fee:</b> <span class="price">₱75.00</span></span>
            <span><b>Processing:</b> <span class="processing">Same day (if submitted before 3 PM)</span></span>
            <span><b>Requirements:</b>
                <ul>
                <li>Valid Government-Issued ID</li>
                <li>1x1 ID Photo (white background)</li>
                </ul>
            </span>
            </div>
    </div>

   <a href="barangay_id.php">
     <img src="images/docu-view-deets.png" class="view-btn">
    </a>
  </div>
</div>

<!-- 4 -->
<div class="card" style="background-image:url('images/Barangay Permit (Construction) - Container.png')">
  <div class="card-content">
    <div class="card-main">
      <div class="card-top">
        <img src="images/docu-bp.png" class="card-icon">
        <div>
          <div class="card-title">Barangay Permit (Construction)</div>
          <div class="card-desc">
            Required permit for construction or renovation activities.
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <!-- 4 -->
        <div class="card-meta">
        <span><b>Fee:</b> <span class="price">₱200.00</span></span>
        <span><b>Processing:</b> <span class="processing">2–3 business days</span></span>
        <span><b>Requirements:</b>
            <ul>
            <li>Construction or Activity Plan</li>
            <li>Valid Government-Issued ID</li>
            </ul>
        </span>
        </div>
    </div>

   <a href="barangay_permit.php">
     <img src="images/docu-view-deets.png" class="view-btn">
    </a>
  </div>
</div>

<!-- 5 -->
<div class="card" style="background-image:url('images/Certificate of Indigency - Container.png')">
  <div class="card-content">
    <div class="card-main">
      <div class="card-top">
        <img src="images/docu-coi.png" class="card-icon">
        <div>
          <div class="card-title">Certificate of Indigency</div>
          <div class="card-desc">
            For low-income households (medical/scholarship use).
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <!-- 5 -->
        <div class="card-meta">
        <span><b>Fee:</b> <span class="price">₱20.00</span></span>
        <span><b>Processing:</b> <span class="processing">Same day (if submitted before 3 PM)</span></span>
        <span><b>Requirements:</b>
            <ul>
            <li>Valid Government-Issued ID</li>
            </ul>
        </span>
        </div>
    </div>

  <a href="indigency.php">
     <img src="images/docu-view-deets.png" class="view-btn">
    </a>
  </div>
</div>

<!-- 6 -->
<div class="card" style="background-image:url('images/Certificate of Residency - Container.png')">
  <div class="card-content">
    <div class="card-main">
      <div class="card-top">
        <img src="images/docu-br.png" class="card-icon">
        <div>
          <div class="card-title">Certificate of Residency</div>
          <div class="card-desc">
            Proof that the applicant is a bona fide resident.
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <!-- 6 -->
        <div class="card-meta">
        <span><b>Fee:</b> <span class="price">₱30.00</span></span>
        <span><b>Processing:</b> <span class="processing">Same day (if submitted before 3 PM)</span></span>
        <span><b>Requirements:</b>
            <ul>
            <li>Valid Government-Issued ID</li>
            <li>Proof of Address (utility bill or lease)</li>
            </ul>
        </span>
        </div>
    </div>

    <a href="residency.php">
     <img src="images/docu-view-deets.png" class="view-btn">
    </a>
  </div>
</div>

<!-- 7 -->
<div class="card" style="background-image:url('images/Solo Parent Certificate - Container.png')">
  <div class="card-content">
    <div class="card-main">
      <div class="card-top">
        <img src="images/docu-spc.png" class="card-icon">
        <div>
          <div class="card-title">Solo Parent Certificate</div>
          <div class="card-desc">
            Issued under Republic Act 8972 (Solo Parents’ Welfare Act).
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <!-- 7 -->
        <div class="card-meta">
        <span><b>Fee:</b> <span class="price">₱50.00</span></span>
        <span><b>Processing:</b> <span class="processing">Same day (if submitted before 3 PM)</span></span>
        <span><b>Requirements:</b>
            <ul>
            <li>Valid Government-Issued ID</li>
            <li>Child’s Birth Certificate</li>
            </ul>
        </span>
        </div>
    </div>

    <a href="solo_parent.php">
     <img src="images/docu-view-deets.png" class="view-btn">
    </a>
  </div>
</div>

<div class="footer">
  <img src="images/docu-logo.png" class="footer-logo">
  <div class="form-logo-text">
    <span class="logo-pink">CitiServe</span>
    <span class="logo-gray"> © 2026. All rights reserved.</span>
  </div>
</div>
</div>

<script>
const trail = [
  { label: "Document Requests",  href: "document.php" },
  { label: "Request Document",   href: null }
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

  renderBreadcrumb(); // ← ito yung kulang!
</script>
</body>
</html>