
  /* ══════════════════════════
     DOCUMENT CONFIG — all 7
  ══════════════════════════ */
  const documentConfig = {
    business_clearance: {
      title: 'Barangay Business Clearance',
      breadcrumbLabel: 'Business Clearance',
      breadcrumbHref: 'business_clearance.php',
      location: 'Barangay Kalayaan, Angono, Rizal',
      price: '₱150.00',
      gcashNumber: '0961-443-5929',
      mayaNumber: '0967-487-9810',
    },
    barangay_clearance: {
      title: 'Barangay Clearance',
      breadcrumbLabel: 'Barangay Clearance',
      breadcrumbHref: 'barangay_clearance.php',
      location: 'Barangay Kalayaan, Angono, Rizal',
      price: '₱50.00',
      gcashNumber: '0961-443-5929',
      mayaNumber: '0967-487-9810',
    },
    barangay_id: {
      title: 'Barangay ID',
      breadcrumbLabel: 'Barangay ID',
      breadcrumbHref: 'barangay_id.php',
      location: 'Barangay Kalayaan, Angono, Rizal',
      price: '₱75.00',
      gcashNumber: '0961-443-5929',
      mayaNumber: '0967-487-9810',
    },
    barangay_permit: {
      title: 'Barangay Permit (Construction)',
      breadcrumbLabel: 'Barangay Permit (Construction)',
      breadcrumbHref: 'barangay_permit.php',
      location: 'Barangay Kalayaan, Angono, Rizal',
      price: '₱200.00',
      gcashNumber: '0961-443-5929',
      mayaNumber: '0967-487-9810',
    },
    indigency: {
      title: 'Certificate of Indigency',
      breadcrumbLabel: 'Barangay Indigency',
      breadcrumbHref: 'indigency.php',
      location: 'Barangay Kalayaan, Angono, Rizal',
      price: '₱20.00',
      gcashNumber: '0961-443-5929',
      mayaNumber: '0967-487-9810',
    },
    residency: {
      title: 'Certificate of Residency',
      breadcrumbLabel: 'Barangay Residency',
      breadcrumbHref: 'residency.php',
      location: 'Barangay Kalayaan, Angono, Rizal',
      price: '₱30.00',
      gcashNumber: '0961-443-5929',
      mayaNumber: '0967-487-9810',
    },
    solo_parent: {
      title: 'Solo Parent Certificate',
      breadcrumbLabel: 'Solo Parent Certificate',
      breadcrumbHref: 'solo_parent.php',
      location: 'Barangay Kalayaan, Angono, Rizal',
      price: '₱50.00',
      gcashNumber: '0961-443-5929',
      mayaNumber: '0967-487-9810',
    },
  };

  /* ══════════════════════════
     READ URL PARAM + POPULATE
  ══════════════════════════ */
  const params = new URLSearchParams(window.location.search);
  const docKey = params.get('doc') || 'business_clearance';
  const currentDoc = documentConfig[docKey] || documentConfig['business_clearance'];

  document.addEventListener('DOMContentLoaded', function () {

    // Page title
    document.getElementById('pageTitle').textContent = currentDoc.title + ' – Payment';
    document.title = currentDoc.title + ' - Payment';

    // Breadcrumb
    const trail = [
      { label: 'Document Requests', href: 'document.php' },
      { label: 'Request Document',  href: 'document.php' },
      { label: currentDoc.breadcrumbLabel, href: currentDoc.breadcrumbHref },
      { label: 'Payment', href: null },
    ];
    document.getElementById('form-breadcrumb').innerHTML = trail.map((item, i) => {
      const isLast = i === trail.length - 1;
      const sep = i > 0 ? `<span class="form-sep">></span>` : '';
      if (isLast) return `${sep}<span class="form-active">${item.label}</span>`;
      return `${sep}<a href="${item.href}">${item.label}</a>`;
    }).join('');

    // Order summary (main)
    document.getElementById('orderName').textContent = currentDoc.title;
    document.getElementById('orderSub').textContent = currentDoc.location;
    document.getElementById('orderPrice').textContent = currentDoc.price;
    document.getElementById('orderTotal').textContent = currentDoc.price;

    // Sidebar
    document.getElementById('sidebarDoc').textContent = currentDoc.title;
    document.getElementById('sidebarFee').textContent = currentDoc.price;

    // Upload preview
    const proofFileInput = document.getElementById('proofFileInput');
    const uploadPreview  = document.getElementById('uploadPreview');
    const uploadDefault  = document.getElementById('uploadDefault');
    const proofUploadBox = document.getElementById('proofUpload2');

    proofFileInput.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
          uploadPreview.src = e.target.result;
          uploadPreview.style.display = 'block';
          uploadDefault.style.display = 'none';
        };
        reader.readAsDataURL(file);
      } else {
        uploadDefault.querySelector('span').textContent = file.name;
        uploadPreview.style.display = 'none';
        uploadDefault.style.display = 'flex';
      }

      document.getElementById('proofUpload2Error').style.display = 'none';
      proofUploadBox.classList.remove('upload-error');
    });

    // Input live validation
    document.querySelectorAll('input').forEach(el => {
      el.addEventListener('input', function () {
        this.classList.toggle('filled', this.value.trim() !== '');
        this.classList.remove('input-error');
        const error = document.getElementById(this.id + 'Error');
        if (error) error.style.display = 'none';
      });
    });

    // Save QR button
    document.getElementById('saveQrBtn').addEventListener('click', function () {
      const qrImage = document.getElementById('qrImage');
      const fileNames = {
        gcash: 'QR-GCash.png',
        maya: 'QR-Maya.png',
        instapay: 'QR-InstaPay.png',
      };
      const a = document.createElement('a');
      a.href = qrImage.src;
      a.download = fileNames[selectedPayment] || 'QR-Code.png';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    });

  });

  /* ══════════════════════════
     PAYMENT CONFIG (dynamic)
  ══════════════════════════ */
  function getPaymentConfig() {
    return {
      gcash: {
        title: 'GCash – QR Code & Payment Instructions',
        logo: 'images/docu-gcash.png',
        qr: 'images/docu-qr-gcash.png',
        isInstapay: false,
        steps: [
          'Open the <strong>GCash</strong> app on your phone.',
          `You may send via <strong>Send Money</strong> using the number: <span class="highlight">${currentDoc.gcashNumber}</span>, or scan the QR code.`,
          'Use your e-Wallet\'s app to <strong>scan the QR code</strong> to proceed with payment.',
          `Enter the exact amount: <span class="highlight">${currentDoc.price}</span>`,
          'Confirm and complete the transaction.',
          '<strong>Take a screenshot</strong> of the successful transaction.',
          '<strong>Enter the reference/transaction number</strong> below and upload the screenshot as proof.',
        ],
      },
      maya: {
        title: 'Maya – QR Code & Payment Instructions',
        logo: 'images/docu-maya.png',
        qr: 'images/docu-qr-maya.png',
        isInstapay: false,
        steps: [
          'Open the <strong>Maya</strong> app on your phone.',
          `You may send via <strong>Send Money</strong> using the number: <span class="highlight">${currentDoc.mayaNumber}</span>, or scan the QR code.`,
          'Use your e-Wallet\'s app to <strong>scan the QR code</strong> to proceed with payment.',
          `Enter the exact amount: <span class="highlight">${currentDoc.price}</span>`,
          'Confirm and complete the transaction.',
          '<strong>Take a screenshot</strong> of the successful transaction.',
          '<strong>Enter the reference/transaction number</strong> below and upload the screenshot as proof.',
        ],
      },
      instapay: {
        title: 'InstaPay – QR Code & Payment Instructions',
        logo: 'images/docu-instapay.png',
        qr: 'images/docu-qr-instapay.png',
        isInstapay: true,
        steps: [
          'Open <strong>any e-wallet/bank app</strong> on your phone.',
          'Look for the <strong>Scan QR</strong> or <strong>Pay via QR</strong> option.',
          'Scan the QR code to proceed with payment.',
          `Enter the exact amount: <span class="highlight">${currentDoc.price}</span>`,
          'Confirm and complete the transaction.',
          '<strong>Take a screenshot</strong> of the successful transaction.',
          '<strong>Enter the reference/transaction number</strong> below and upload the screenshot as proof.',
        ],
      },
    };
  }

  /* ══════════════════════════
     SELECT PAYMENT
  ══════════════════════════ */
  let selectedPayment = null;

  function selectPayment(method) {
    selectedPayment = method;
    document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
    document.getElementById('opt-' + method).classList.add('selected');
    document.getElementById('paymentError').style.display = 'none';

    const config = getPaymentConfig()[method];

    document.getElementById('qrCardTitle').textContent = config.title;
    document.getElementById('qrImage').src = config.qr;
    document.getElementById('howToLogo').src = config.logo;

    const stepsList = document.getElementById('paySteps');
    stepsList.innerHTML = config.steps.map(s => `<li>${s}</li>`).join('');

    const note  = document.getElementById('instapayNote');
    const panel = document.getElementById('banksPanel');
    const saveBtn = document.getElementById('saveQrBtn');

    if (config.isInstapay) {
      note.style.display = 'block';
    } else {
      note.style.display = 'none';
      panel.style.display = 'none';
    }

    saveBtn.style.display = 'block';

    document.getElementById('qrCard').style.display = 'block';
    document.getElementById('proofCard').style.display = 'block';
    document.getElementById('defaultBtnRow').style.display = 'none';
  }

  function toggleBanks() {
    const panel = document.getElementById('banksPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
  }

  /* ══════════════════════════
     VALIDATION
  ══════════════════════════ */
  function validatePayment() {
    let valid = true;

    if (!selectedPayment) {
      document.getElementById('paymentError').style.display = 'flex';
      return false;
    }

    const ref = document.getElementById('refNumber');
    const refError = document.getElementById('refNumberError');
    if (!ref.value.trim()) {
      refError.style.display = 'flex';
      ref.classList.add('input-error');
      valid = false;
    } else {
      refError.style.display = 'none';
      ref.classList.remove('input-error');
    }

    const proofFile = document.getElementById('proofFileInput');
    const proofError = document.getElementById('proofUpload2Error');
    const proofBox   = document.getElementById('proofUpload2');
    if (!proofFile || !proofFile.files || proofFile.files.length === 0) {
      proofError.style.display = 'flex';
      proofBox.classList.add('upload-error');
      valid = false;
    } else {
      proofError.style.display = 'none';
      proofBox.classList.remove('upload-error');
    }

    return valid;
  }

  function showModal() {
  if (!validatePayment()) return;
  document.getElementById('confirmModal').style.display = 'flex';
}

function closeModal() {
  document.getElementById('confirmModal').style.display = 'none';
}

function submitForm() {
  closeModal();
  window.location.href = 'request_submitted.php?doc=' + docKey;
}
