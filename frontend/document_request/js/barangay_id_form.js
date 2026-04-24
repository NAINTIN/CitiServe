 const trail = [
  { label: "Document Requests",  href: "document.php" },
  { label: "Request Document",   href: "document.php" },
  { label: "Barangay ID",  href: 'barangay_id.php' },
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
  
  /* ══ UPLOAD FIELDS ══ */
  const uploadFields = [
    { inputId: 'validIdFileInput', defaultId: 'validIdDefault', previewId: 'validIdPreview', filenameId: 'validIdFilename', boxId: 'validIdUpload', errorId: 'validIdError' },
    { inputId: 'photoFileInput',   defaultId: 'photoDefault',   previewId: 'photoPreview',   filenameId: 'photoFilename',   boxId: 'photoUpload',   errorId: 'photoError' },
  ];

  uploadFields.forEach(function (field) {
    document.getElementById(field.inputId).addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;
      const defaultEl  = document.getElementById(field.defaultId);
      const previewEl  = document.getElementById(field.previewId);
      const filenameEl = document.getElementById(field.filenameId);
      const box        = document.getElementById(field.boxId);
      const error      = document.getElementById(field.errorId);

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

  /* ══ LIVE INPUT VALIDATION ══ */
  document.querySelectorAll('input').forEach(el => {
    el.addEventListener('input', function () {
      this.classList.toggle('filled', this.value.trim() !== '');
      this.classList.remove('input-error');
      const error = document.getElementById(this.id + 'Error');
      if (error) error.style.display = 'none';
    });
    if (el.type === 'date') {
      el.addEventListener('change', function () {
        this.classList.toggle('filled', this.value !== '');
        this.classList.remove('input-error');
        const error = document.getElementById(this.id + 'Error');
        if (error) error.style.display = 'none';
      });
    }
  });

  /* ══ DROPDOWN ══ */
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

    const errorMap = {
      genderDropdown:      'genderError',
      civilStatusDropdown: 'civilStatusError',
    };
    const errId = errorMap[dropdownId];
    if (errId) {
      const err = document.getElementById(errId);
      if (err) err.style.display = 'none';
    }
    dropdown.querySelector('.custom-select-selected').classList.remove('select-error');
  }

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.custom-select')) {
      document.querySelectorAll('.custom-select').forEach(d => d.classList.remove('open'));
    }
  });

  /* ══ AUTO-COMPUTE AGE FROM DOB ══ */
  document.getElementById('dateOfBirth').addEventListener('change', function () {
    const dob = new Date(this.value);
    if (!isNaN(dob.getTime())) {
      const today = new Date();
      let age = today.getFullYear() - dob.getFullYear();
      const m = today.getMonth() - dob.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
      const ageInput = document.getElementById('age');
      ageInput.value = age >= 0 ? age : '';
      ageInput.classList.toggle('filled', ageInput.value !== '');
      const ageError = document.getElementById('ageError');
      if (ageError) ageError.style.display = 'none';
    }
  });

  /* ══ VALIDATE FORM ══ */
  function validateForm() {
    let valid = true;

    const textFields = [
      { id: 'firstName',       errorId: 'firstNameError' },
      { id: 'lastName',        errorId: 'lastNameError' },
      { id: 'completeAddress', errorId: 'completeAddressError' },
      { id: 'contactNumber',   errorId: 'contactNumberError' },
      { id: 'age',             errorId: 'ageError' },
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

    // Date of Birth
    const dob = document.getElementById('dateOfBirth');
    const dobError = document.getElementById('dateOfBirthError');
    if (!dob.value) {
      dobError.style.display = 'flex';
      dob.classList.add('input-error');
      valid = false;
    } else {
      dobError.style.display = 'none';
      dob.classList.remove('input-error');
    }

    // Dropdowns
    [
      { dropId: 'genderDropdown',      errId: 'genderError' },
      { dropId: 'civilStatusDropdown', errId: 'civilStatusError' },
    ].forEach(d => {
      const dropdown = document.getElementById(d.dropId);
      const error    = document.getElementById(d.errId);
      if (!dropdown.dataset.value) {
        error.style.display = 'flex';
        dropdown.querySelector('.custom-select-selected').classList.add('select-error');
        valid = false;
      } else {
        error.style.display = 'none';
        dropdown.querySelector('.custom-select-selected').classList.remove('select-error');
      }
    });

    // Email
    const email      = document.getElementById('emailAddress');
    const emailError = document.getElementById('emailError');
    if (email.value.trim() !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      emailError.style.display = 'flex';
      valid = false;
    } else {
      emailError.style.display = 'none';
    }

    // Uploads
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
    window.location.href = 'docu_business_payment.php?doc=barangay_id';
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
