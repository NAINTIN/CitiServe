
  const trail = [
    { label: "Document Requests", href: "document.php" },
    { label: "Request Document",  href: "document.php" },
    { label: "Barangay Clearance", href: "barangay_clearance.php" },
    { label: "Form", href: null }
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
     UPLOAD PREVIEW
  ══════════════════════════ */
  const uploadFields = [
    { inputId: 'validIdFileInput', defaultId: 'validIdDefault', previewId: 'validIdPreview', filenameId: 'validIdFilename', boxId: 'validIdUpload', errorId: 'validIdError' },
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

  /* ══════════════════════════
     AUTO-CALCULATE AGE
  ══════════════════════════ */
  document.getElementById('dateOfBirth').addEventListener('change', function () {
    const dob = new Date(this.value);
    if (!this.value) return;
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    document.getElementById('age').value = age >= 0 ? age : '';
    document.getElementById('age').classList.add('filled');
    document.getElementById('ageError').style.display = 'none';
    document.getElementById('age').classList.remove('input-error');
    this.classList.toggle('filled', this.value !== '');
    document.getElementById('dateOfBirthError').style.display = 'none';
    this.classList.remove('input-error');
  });

  /* ══════════════════════════
     LIVE VALIDATION
  ══════════════════════════ */
  document.querySelectorAll('input, textarea').forEach(el => {
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

    const errorMap = { civilStatusDropdown: 'civilStatusError' };
    const errorId = errorMap[dropdownId];
    if (errorId) document.getElementById(errorId).style.display = 'none';
    dropdown.querySelector('.custom-select-selected').classList.remove('select-error');
  }

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.custom-select')) {
      document.querySelectorAll('.custom-select').forEach(d => d.classList.remove('open'));
    }
  });

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

  /* ══════════════════════════
     VALIDATE + SUBMIT
  ══════════════════════════ */
  function validateForm() {
    let valid = true;

    const textFields = [
      { id: 'firstName',          errorId: 'firstNameError' },
      { id: 'lastName',           errorId: 'lastNameError' },
      { id: 'completeAddress',    errorId: 'completeAddressError' },
      { id: 'dateOfBirth',        errorId: 'dateOfBirthError' },
      { id: 'age',                errorId: 'ageError' },
      { id: 'citizenship',        errorId: 'citizenshipError' },
      { id: 'contactNumber',      errorId: 'contactNumberError' },
      { id: 'purposeOfClearance', errorId: 'purposeOfClearanceError' },
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

    // Civil Status dropdown
    const csDropdown = document.getElementById('civilStatusDropdown');
    const csError    = document.getElementById('civilStatusError');
    if (!csDropdown.dataset.value) {
      csError.style.display = 'flex';
      csDropdown.querySelector('.custom-select-selected').classList.add('select-error');
      valid = false;
    } else {
      csError.style.display = 'none';
      csDropdown.querySelector('.custom-select-selected').classList.remove('select-error');
    }

    // Email optional but validate format
    const email      = document.getElementById('emailAddress');
    const emailError = document.getElementById('emailError');
    if (email.value.trim() !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      emailError.style.display = 'flex';
      valid = false;
    } else {
      emailError.style.display = 'none';
    }

    // File upload
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

    // Submit if valid
    if (valid) {
      const formData = new FormData();
      formData.append('firstName',          document.getElementById('firstName').value);
      formData.append('lastName',           document.getElementById('lastName').value);
      formData.append('completeAddress',    document.getElementById('completeAddress').value);
      formData.append('dateOfBirth',        document.getElementById('dateOfBirth').value);
      formData.append('age',                document.getElementById('age').value);
      formData.append('civilStatus',        document.getElementById('civilStatusDropdown').dataset.value);
      formData.append('citizenship',        document.getElementById('citizenship').value);
      formData.append('contactNumber',      document.getElementById('contactNumber').value);
      formData.append('emailAddress',       document.getElementById('emailAddress').value);
      formData.append('purposeOfClearance', document.getElementById('purposeOfClearance').value);
      formData.append('validId',            document.getElementById('validIdFileInput').files[0]);

      fetch('submit_clearance.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
           window.location.href = 'docu_business_payment.php?doc=barangay_clearance';
        }
      });
    }
  }