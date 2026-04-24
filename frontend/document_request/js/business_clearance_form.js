  const trail = [
  { label: "Document Requests",  href: "document.php" },
  { label: "Request Document",   href: "document.php" },
  { label: "Business Clearance",  href: "business_clearance.php"},
  { label: "Form",  href: null }
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

  /* ══════════════════════════
     UPLOAD PREVIEW — all 3 boxes
  ══════════════════════════ */
  const uploadFields = [
    { inputId: 'dtiFileInput',     defaultId: 'dtiDefault',     previewId: 'dtiPreview',     filenameId: 'dtiFilename',     boxId: 'dtiUpload',     errorId: 'dtiError' },
    { inputId: 'validIdFileInput', defaultId: 'validIdDefault', previewId: 'validIdPreview', filenameId: 'validIdFilename', boxId: 'validIdUpload', errorId: 'validIdError' },
    { inputId: 'proofFileInput',   defaultId: 'proofDefault',   previewId: 'proofPreview',   filenameId: 'proofFilename',   boxId: 'proofUpload',   errorId: 'proofError' },
  ];

  uploadFields.forEach(function (field) {
    document.getElementById(field.inputId).addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      const defaultEl   = document.getElementById(field.defaultId);
      const previewEl   = document.getElementById(field.previewId);
      const filenameEl  = document.getElementById(field.filenameId);
      const box         = document.getElementById(field.boxId);
      const error       = document.getElementById(field.errorId);

      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewEl.src = e.target.result;
          previewEl.style.display = 'block';
          filenameEl.style.display = 'none';
          defaultEl.style.display = 'none';
        };
        reader.readAsDataURL(file);
      } else {
        // PDF — show filename instead
        previewEl.style.display = 'none';
        filenameEl.textContent = file.name;
        filenameEl.style.display = 'block';
        defaultEl.style.display = 'none';
      }

      // clear error
      error.style.display = 'none';
      box.classList.remove('upload-error');
    });
  });


  /* ══════════════════════════
     INPUT LIVE VALIDATION
  ══════════════════════════ */
  document.querySelectorAll('input').forEach(el => {
    el.addEventListener('input', function () {
      this.classList.toggle('filled', this.value.trim() !== '');
      this.classList.remove('input-error');
      const error = document.getElementById(this.id + 'Error');
      if (error) error.style.display = 'none';
    });
  });


  /* ══════════════════════════
     DROPDOWN
  ══════════════════════════ */
  function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    document.querySelectorAll('.custom-select').forEach(d => {
      if (d.id !== id) d.classList.remove('open');
    });
    dropdown.classList.toggle('open');
  }

  function selectOption(dropdownId, value) {
    const dropdown = document.getElementById(dropdownId);
    dropdown.querySelector('.custom-select-text').textContent = value;
    dropdown.querySelectorAll('.custom-select-option').forEach(opt => {
      opt.classList.toggle('selected', opt.textContent.trim() === value);
    });
    dropdown.dataset.value = value;
    dropdown.classList.add('filled');
    dropdown.classList.remove('open');

    const error = document.getElementById(dropdownId === 'businessTypeDropdown' ? 'businessTypeError' : '');
    if (error) error.style.display = 'none';

    if (dropdownId === 'businessTypeDropdown') {
      const othersInput = document.getElementById('businessOther');
      if (value === 'Others') {
        othersInput.disabled = false;
        othersInput.classList.remove('others-disabled');
        othersInput.focus();
      } else {
        othersInput.disabled = true;
        othersInput.value = '';
        othersInput.classList.add('others-disabled');
        const othersError = document.getElementById('businessOtherError');
        if (othersError) othersError.style.display = 'none';
      }
    }
  }

  document.addEventListener('click', function(e) {
    if (!e.target.closest('.custom-select')) {
      document.querySelectorAll('.custom-select').forEach(d => d.classList.remove('open'));
    }
  });


  /* ══════════════════════════
     VALIDATE FORM
  ══════════════════════════ */
  function validateForm() {
    let valid = true;

    const textFields = [
      { id: 'firstName',       errorId: 'firstNameError' },
      { id: 'lastName',        errorId: 'lastNameError' },
      { id: 'businessName',    errorId: 'businessNameError' },
      { id: 'businessAddress', errorId: 'businessAddressError' },
      { id: 'contactNumber',   errorId: 'contactNumberError' },
    ];

    textFields.forEach(f => {
      const input = document.getElementById(f.id);
      const error = document.getElementById(f.errorId);
      if (!input.value.trim()) {
        error.style.display = 'flex';
        input.classList.add('input-error');
        valid = false;
      } else {
        error.style.display = 'none';
        input.classList.remove('input-error');
      }
    });

    const btDropdown = document.getElementById('businessTypeDropdown');
    const btError = document.getElementById('businessTypeError');
    if (!btDropdown.dataset.value) {
      btError.style.display = 'flex';
      btDropdown.querySelector('.custom-select-selected').classList.add('select-error');
      valid = false;
    } else {
      btError.style.display = 'none';
      btDropdown.querySelector('.custom-select-selected').classList.remove('select-error');
    }

    if (btDropdown.dataset.value === 'Others') {
      const othersInput = document.getElementById('businessOther');
      const othersError = document.getElementById('businessOtherError');
      if (!othersInput.value.trim()) {
        othersError.style.display = 'flex';
        othersInput.classList.add('input-error');
        valid = false;
      } else {
        othersError.style.display = 'none';
        othersInput.classList.remove('input-error');
      }
    }

    const email = document.getElementById('emailAddress');
    const emailError = document.getElementById('emailError');
    if (email.value.trim() !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      emailError.style.display = 'flex';
      valid = false;
    } else {
      emailError.style.display = 'none';
    }

    
    uploadFields.forEach(function (field) {
      const fileInput = document.getElementById(field.inputId);
      const error     = document.getElementById(field.errorId);
      const box       = document.getElementById(field.boxId);
      if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        error.style.display = 'flex';
        box.classList.add('upload-error');
        valid = false;
      } else {
        error.style.display = 'none';
        box.classList.remove('upload-error');
      }
    });

    if (valid) {
    window.location.href = 'docu_business_payment.php?doc=business';
  }

    return valid;
  }
  function hasFilledFields() {
  const inputs = document.querySelectorAll('input, textarea');
  for (let el of inputs) {
    if (el.value.trim() !== '') return true;
  }
  const dropdowns = document.querySelectorAll('.custom-select');
  for (let d of dropdowns) {
    if (d.dataset.value) return true;
  }
  const files = document.querySelectorAll('input[type="file"]');
  for (let f of files) {
    if (f.files && f.files.length > 0) return true;
  }
  return false;
  }

  function openModal() {
  const modal = document.getElementById('discardModal');
  modal.style.display = 'flex';
}

function closeModal() {
  document.getElementById('discardModal').style.display = 'none';
}
