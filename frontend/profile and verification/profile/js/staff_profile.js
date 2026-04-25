
  
  /* ── Dropdown ── */
  function toggleDropdown(id) {
    const d = document.getElementById(id);
    document.querySelectorAll('.custom-select').forEach(x => { if (x.id !== id) x.classList.remove('open'); });
    d.classList.toggle('open');
  }

  function selectOption(id, value) {
    const d = document.getElementById(id);
    d.querySelector('.custom-select-text').textContent = value;
    d.querySelectorAll('.custom-select-option').forEach(o => o.classList.toggle('selected', o.textContent.trim() === value));
    d.dataset.value = value;
    d.classList.add('filled');
    d.classList.remove('open');

    // sync hidden input for form submission
    const map = {
      'civilDropdown':  'civilStatus_val',
      'genderDropdown': 'gender_val',
      'suffixDropdown': 'suffix_val'
    };
    if (map[id]) document.getElementById(map[id]).value = value;
  }

  document.addEventListener('click', e => {
    if (!e.target.closest('.custom-select')) document.querySelectorAll('.custom-select').forEach(d => d.classList.remove('open'));
  });

  /* ── Sidenav tabs ── */
  const sectionMap = { '#profile': 'section-profile', '#password': 'section-password' };

  function showSection(href) {
    document.querySelectorAll('.profile-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.sidenav-item').forEach(link => {
      link.classList.remove('active');
      const img = link.querySelector('img');
      if (img) img.src = img.dataset.src;
    });
    const target = sectionMap[href];
    if (target) document.getElementById(target).classList.add('active');
    const activeLink = document.querySelector(`.sidenav-item[href="${href}"]`);
    if (activeLink) {
      activeLink.classList.add('active');
      const img = activeLink.querySelector('img');
      if (img) img.src = img.dataset.pink;
    }
  }

  document.querySelectorAll('.sidenav-item').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      showSection(this.getAttribute('href'));
      localStorage.setItem('staffActiveTab', this.getAttribute('href'));
    });
  });

  const savedTab = localStorage.getItem('staffActiveTab');
  showSection(savedTab && sectionMap[savedTab] ? savedTab : '#profile');

  /* ── Avatar Preview (client-side only before upload) ── */
  function previewAvatar(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { document.getElementById('avatarPreview').src = e.target.result; };
    reader.readAsDataURL(file);
  }

  /* ── Save Profile ── */
  async function handleSave() {
    const profileError = document.getElementById('profileError');
    profileError.style.display = 'none';

    const formData = new FormData();
    formData.append('first_name',   document.getElementById('firstNameInput').value.trim());
    formData.append('middle_name',  document.getElementById('middleNameInput').value.trim());
    formData.append('last_name',    document.getElementById('lastNameInput').value.trim());
    formData.append('suffix',       document.getElementById('suffix_val').value);
    formData.append('contact',      document.getElementById('contactInput').value.trim());
    formData.append('email',        document.getElementById('emailInput').value.trim());
    formData.append('dob',          document.getElementById('dobInput').value);
    formData.append('civil_status', document.getElementById('civilStatus_val').value);
    formData.append('citizenship',  document.getElementById('citizenshipInput').value.trim());
    formData.append('gender',       document.getElementById('gender_val').value);
    formData.append('address',      document.getElementById('addressInput').value.trim());

    const avatarFile = document.getElementById('avatarInput').files[0];
    if (avatarFile) formData.append('avatar', avatarFile);

    try {
      const res    = await fetch('update_profile.php', { method: 'POST', body: formData });
      const result = await res.json();

      if (result.success) {
        document.getElementById('userName').textContent = result.full_name;
        if (result.avatar_path) document.getElementById('avatarPreview').src = result.avatar_path;
        showBanner('successBanner');
      } else {
        profileError.textContent = result.message || 'Failed to save.';
        profileError.style.display = 'block';
      }
    } catch (err) {
      profileError.textContent = 'Server error. Please try again.';
      profileError.style.display = 'block';
    }
  }

  /* ── Change Password ── */
  async function handlePwSave() {
    const pwError = document.getElementById('pwError');
    pwError.style.display = 'none';

    const currentPw  = document.getElementById('currentPw').value;
    const newPw      = document.getElementById('newPw').value;
    const confirmPw  = document.getElementById('confirmPw').value;

    if (!currentPw || !newPw || !confirmPw) {
      pwError.textContent = 'Please fill in all password fields.';
      pwError.style.display = 'block';
      return;
    }
    if (newPw !== confirmPw) {
      pwError.textContent = 'New passwords do not match.';
      pwError.style.display = 'block';
      return;
    }
    if (newPw.length < 8) {
      pwError.textContent = 'New password must be at least 8 characters.';
      pwError.style.display = 'block';
      return;
    }

    try {
      const res    = await fetch('change_password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ current: currentPw, new_password: newPw })
      });
      const result = await res.json();

      if (result.success) {
        showBanner('pwSuccessBanner');
        document.getElementById('currentPw').value  = '';
        document.getElementById('newPw').value      = '';
        document.getElementById('confirmPw').value  = '';
      } else {
        pwError.textContent = result.message || 'Failed to change password.';
        pwError.style.display = 'block';
      }
    } catch (err) {
      pwError.textContent = 'Server error. Please try again.';
      pwError.style.display = 'block';
    }
  }

  /* ── Password Eye Toggle ── */
  function togglePw(inputId, btn) {
    const input = document.getElementById(inputId);
    const img   = btn.querySelector('img');
    if (input.type === 'password') {
      input.type = 'text';
      if (img) img.src = 'images/eyeclosed.png';
    } else {
      input.type = 'password';
      if (img) img.src = 'images/eye.png';
    }
  }

  /* ── Banner helper ── */
  function showBanner(id) {
    const b = document.getElementById(id);
    b.style.display = 'none';
    requestAnimationFrame(() => {
      b.style.display = 'flex';
      b.style.animation = 'none';
      b.offsetHeight;
      b.style.animation = 'fadeIn .4s ease';
    });
    setTimeout(() => {
      b.style.transition = 'opacity .5s';
      b.style.opacity = '0';
      setTimeout(() => { b.style.display = 'none'; b.style.opacity = '1'; b.style.transition = ''; }, 500);
    }, 4000);
  }
document.addEventListener('DOMContentLoaded', function () {
  /* ================= PROFILE DROPDOWN ================= */
  const profilePill = document.getElementById('profilePill');
  const profilePanel = document.getElementById('profilePanel');

  if (profilePill && profilePanel) {
    let profileOpen = false;

    function closeProfilePanel() {
      profileOpen = false;
      profilePanel.classList.remove('open');
    }

    profilePill.addEventListener('click', function (e) {
      e.stopPropagation();
      profileOpen = !profileOpen;
      profilePanel.classList.toggle('open', profileOpen);
    });

    profilePanel.addEventListener('click', function (e) {
      e.stopPropagation();
    });

    document.addEventListener('click', function () {
      if (profileOpen) closeProfilePanel();
    });
  }
});

