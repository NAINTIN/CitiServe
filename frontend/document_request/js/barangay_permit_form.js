 const trail = [
  { label: "Document Requests",  href: "document.php" },
  { label: "Request Document",   href: "document.php" },
  { label: "Barangay Permit (Construction)",  href: "barangay_permit.php" },
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
   UPLOAD PREVIEW — CONSTRUCTION
══════════════════════════ */
const uploadFields = [
  {
    inputId: 'planFileInput',
    defaultId: 'planDefault',
    previewId: 'planPreview',
    filenameId: 'planFilename',
    boxId: 'planUpload',
    errorId: 'planError'
  },
  {
    inputId: 'validIdFileInput',
    defaultId: 'validIdDefault',
    previewId: 'validIdPreview',
    filenameId: 'validIdFilename',
    boxId: 'validIdUpload',
    errorId: 'validIdError'
  }
];

uploadFields.forEach(function (field) {
  document.getElementById(field.inputId)?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const defaultEl = document.getElementById(field.defaultId);
    const previewEl = document.getElementById(field.previewId);
    const filenameEl = document.getElementById(field.filenameId);
    const box = document.getElementById(field.boxId);
    const error = document.getElementById(field.errorId);

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
      previewEl.style.display = 'none';
      filenameEl.textContent = file.name;
      filenameEl.style.display = 'block';
      defaultEl.style.display = 'none';
    }

    error.style.display = 'none';
    box.classList.remove('upload-error');
  });
});


/* ══════════════════════════
   INPUT LIVE VALIDATION (SAME AS YOUR SYSTEM)
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
   DROPDOWN (PURPOSE)
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

  const error = document.getElementById('purposeError');
  if (error) error.style.display = 'none';

  /* ENABLE / DISABLE OTHERS */
  const othersInput = document.getElementById('purposeOther');

  if (value === 'Others') {
    othersInput.disabled = false;
    othersInput.focus();
  } else {
    othersInput.disabled = true;
    othersInput.value = '';
    const othersError = document.getElementById('purposeOtherError');
    if (othersError) othersError.style.display = 'none';
  }
}

/* CLOSE DROPDOWN */
document.addEventListener('click', function (e) {
  if (!e.target.closest('.custom-select')) {
    document.querySelectorAll('.custom-select')
      .forEach(d => d.classList.remove('open'));
  }
});


/* ══════════════════════════
   VALIDATE FORM (MATCHED STRUCTURE)
══════════════════════════ */
function validateForm() {
  let valid = true;

  const textFields = [
    { id: 'firstName', errorId: 'firstNameError' },
    { id: 'lastName', errorId: 'lastNameError' },
    { id: 'address', errorId: 'addressError' },
    { id: 'contactNumber', errorId: 'contactNumberError' },
    { id: 'location', errorId: 'locationError' },
    { id: 'startDate', errorId: 'startDateError' },
    { id: 'endDate', errorId: 'endDateError' },
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

  /* PURPOSE DROPDOWN */
  const purposeDropdown = document.getElementById('purposeDropdown');
  const purposeError = document.getElementById('purposeError');

  if (!purposeDropdown.dataset.value) {
    purposeError.style.display = 'flex';
    purposeDropdown.querySelector('.custom-select-selected')
      .classList.add('select-error');
    valid = false;
  } else {
    purposeError.style.display = 'none';
    purposeDropdown.querySelector('.custom-select-selected')
      .classList.remove('select-error');
  }

  /* OTHERS FIELD */
  if (purposeDropdown.dataset.value === 'Others') {
    const othersInput = document.getElementById('purposeOther');
    const othersError = document.getElementById('purposeOtherError');

    if (!othersInput.value.trim()) {
      othersError.style.display = 'flex';
      othersInput.classList.add('input-error');
      valid = false;
    } else {
      othersError.style.display = 'none';
      othersInput.classList.remove('input-error');
    }
  }

  /* EMAIL */
  const email = document.getElementById('emailAddress');
  const emailError = document.getElementById('emailError');

  if (email.value.trim() !== '' &&
      !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
    emailError.style.display = 'flex';
    valid = false;
  } else {
    emailError.style.display = 'none';
  }

  /* FILE VALIDATION */
  uploadFields.forEach(field => {
    const fileInput = document.getElementById(field.inputId);
    const error = document.getElementById(field.errorId);
    const box = document.getElementById(field.boxId);

    if (!fileInput || fileInput.files.length === 0) {
      error.style.display = 'flex';
      box.classList.add('upload-error');
      valid = false;
    } else {
      error.style.display = 'none';
      box.classList.remove('upload-error');
    }
  });
if (valid) {
    window.location.href = 'docu_business_payment.php?doc=barangay_permit';
  }

  return valid;
}

/* ══════════════════════════
     MODAL
  ══════════════════════════ */
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
    document.getElementById('discardModal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('discardModal').style.display = 'none';
  }
