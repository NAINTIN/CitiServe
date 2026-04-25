document.addEventListener('DOMContentLoaded', function () {
  const profilePill = document.getElementById('profilePill');
  const profilePanel = document.getElementById('profilePanel');

  if (profilePill && profilePanel) {
    let profileOpen = false;

    profilePill.addEventListener('click', function (e) {
      e.stopPropagation();
      profileOpen = !profileOpen;
      profilePanel.classList.toggle('open', profileOpen);
    });

    profilePanel.addEventListener('click', e => e.stopPropagation());

    document.addEventListener('click', function () {
      if (profileOpen) {
        profilePanel.classList.remove('open');
        profileOpen = false;
      }
    });
  }

  const filterBox = document.getElementById('filterBox');
  const filterDropdown = document.getElementById('filterDropdown');
  const selectedStatusText = document.getElementById('selectedStatusText');
  const filterOptions = document.querySelectorAll('.filter-option');
  const searchInput = document.getElementById('searchInput');
  const clearBtn = document.getElementById('clearBtn');
  const tableBody = document.getElementById('requestsTableBody');

  let currentFilter = 'all';
  const originalRows = tableBody ? Array.from(tableBody.querySelectorAll('.request-row')) : [];

  function applyFilters() {
    if (!tableBody) return;

    const keyword = searchInput ? searchInput.value.trim().toLowerCase() : '';

    let filteredRows = originalRows.filter(row => {
      const id = (row.dataset.staffId || '').toLowerCase();
      const name = (row.dataset.name || '').toLowerCase();
      const email = (row.dataset.email || '').toLowerCase();
      const role = (row.dataset.role || '').toLowerCase();
      const status = (row.dataset.status || '').toLowerCase();

      const matchesSearch =
        keyword === '' ||
        id.includes(keyword) ||
        name.includes(keyword) ||
        email.includes(keyword) ||
        role.includes(keyword);

      const matchesStatus =
        currentFilter === 'all' ||
        currentFilter === 'az' ||
        currentFilter === 'za' ||
        status === currentFilter;

      return matchesSearch && matchesStatus;
    });

    if (currentFilter === 'az') {
      filteredRows.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
    }

    if (currentFilter === 'za') {
      filteredRows.sort((a, b) => b.dataset.name.localeCompare(a.dataset.name));
    }

    tableBody.innerHTML = '';
    filteredRows.forEach(row => tableBody.appendChild(row));
  }

  if (filterBox && filterDropdown) {
    filterBox.addEventListener('click', function (e) {
      e.stopPropagation();
      filterDropdown.classList.toggle('open');
    });

    filterOptions.forEach(option => {
      option.addEventListener('click', function (e) {
        e.stopPropagation();

        filterOptions.forEach(o => o.classList.remove('active'));
        option.classList.add('active');

        currentFilter = option.dataset.value;
        selectedStatusText.textContent = option.textContent;

        filterDropdown.classList.remove('open');
        applyFilters();
      });
    });

    document.addEventListener('click', function () {
      filterDropdown.classList.remove('open');
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', applyFilters);
  }

  if (clearBtn && searchInput) {
    clearBtn.addEventListener('click', function () {
      searchInput.value = '';
      currentFilter = 'all';
      selectedStatusText.textContent = 'All';

      filterOptions.forEach(o => o.classList.remove('active'));
      document.querySelector('.filter-option[data-value="all"]')?.classList.add('active');

      applyFilters();
    });
  }

  const staffModal = document.getElementById('staffProfileModal');
  const staffCloseX = document.getElementById('staffProfileCloseX');
  const staffCloseBtn = document.getElementById('staffProfileCloseBtn');

  const profileInitials = document.getElementById('profileInitials');
  const profileName = document.getElementById('profileName');
  const profileId = document.getElementById('profileId');
  const profileRole = document.getElementById('profileRole');
  const profileEmail = document.getElementById('profileEmail');
  const profileContact = document.getElementById('profileContact');
  const profileBirthdate = document.getElementById('profileBirthdate');
  const profileGender = document.getElementById('profileGender');
  const profileCivil = document.getElementById('profileCivil');
  const profileCitizenship = document.getElementById('profileCitizenship');
  const profileAddress = document.getElementById('profileAddress');
  const profileJoined = document.getElementById('profileJoined');
  const profileLastLogin = document.getElementById('profileLastLogin');
  const profileStatusImg = document.getElementById('profileStatusImg');
  const toggleStaffStatusBtn = document.getElementById('toggleStaffStatusBtn');
  const toggleStaffStatusImg = document.getElementById('toggleStaffStatusImg');

  let currentStaffRow = null;

  function updateStaffStatusUI(status) {
    const isActive = status.toLowerCase() === 'active';

    if (profileStatusImg) {
      profileStatusImg.src = isActive
        ? 'images/resident_active_staff.png'
        : 'images/resident_inactive_staff.png';
      profileStatusImg.alt = isActive ? 'Active' : 'Inactive';
    }

    if (toggleStaffStatusImg) {
      toggleStaffStatusImg.src = isActive
        ? 'images/resident_mark_inactive.png'
        : 'images/resident_mark_active.png';
      toggleStaffStatusImg.alt = isActive ? 'Mark as Inactive' : 'Mark as Active';
    }
  }

  function openStaffModal(row) {
    if (!row || !staffModal) return;

    currentStaffRow = row;

    profileInitials.textContent = row.dataset.initials || '';
    profileName.textContent = row.dataset.name || '';
    profileId.textContent = row.dataset.staffId || '';
    profileRole.textContent = row.dataset.role || '';
    profileEmail.textContent = row.dataset.email || '';
    profileContact.textContent = row.dataset.contact || '';
    profileBirthdate.textContent = row.dataset.birthdate || '';
    profileGender.textContent = row.dataset.gender || '';
    profileCivil.textContent = row.dataset.civil || '';
    profileCitizenship.textContent = row.dataset.citizenship || '';
    profileAddress.textContent = row.dataset.address || '';
    profileJoined.textContent = row.dataset.joined || '';
    profileLastLogin.textContent = row.dataset.lastLogin || '';

    updateStaffStatusUI(row.dataset.status || 'Active');

    staffModal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeStaffModal() {
    if (!staffModal) return;
    staffModal.classList.remove('show');
    document.body.style.overflow = '';
  }

  if (tableBody) {
    tableBody.addEventListener('click', function (e) {
      const viewBtn = e.target.closest('.manage-btn');
      if (viewBtn) {
        openStaffModal(viewBtn.closest('.request-row'));
        return;
      }
    });
  }

  [staffCloseX, staffCloseBtn].forEach(btn => {
    if (btn) btn.addEventListener('click', closeStaffModal);
  });

  if (staffModal) {
    staffModal.addEventListener('click', function (e) {
      if (e.target === staffModal) closeStaffModal();
    });
  }

  if (toggleStaffStatusBtn) {
    toggleStaffStatusBtn.addEventListener('click', function () {
      if (!currentStaffRow) return;

      const currentStatus = currentStaffRow.dataset.status || 'Active';
      const newStatus = currentStatus.toLowerCase() === 'active' ? 'Inactive' : 'Active';

      currentStaffRow.dataset.status = newStatus;
      updateStaffStatusUI(newStatus);

      const rowStatusImg = currentStaffRow.querySelector('.resident-status-img');
      if (rowStatusImg) {
        rowStatusImg.src = newStatus.toLowerCase() === 'active'
          ? 'images/resident_active_staff.png'
          : 'images/resident_inactive_staff.png';
        rowStatusImg.alt = newStatus;
      }

      applyFilters();
    });
  }

// ---------------------------- ADD STAFF MODAL

const addStaffOpenBtn = document.querySelector('.add-staff-btn');
const addStaffModal = document.getElementById('addStaffModal');
const addStaffCloseX = document.getElementById('addStaffCloseX');
const addStaffCancel = document.getElementById('addStaffCancel');
const addStaffSubmit = document.getElementById('addStaffSubmit');

const addStaffRoleBox = document.getElementById('addStaffRoleBox');
const addStaffRoleDropdown = document.getElementById('addStaffRoleDropdown');
const addStaffSelectedRole = document.getElementById('addStaffSelectedRole');
const addStaffOptions = document.querySelectorAll('.add-staff-option');

const staffSuccessNotif = document.getElementById('staffSuccessNotif');

function openAddStaffModal() {
  if (!addStaffModal) return;
  addStaffModal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeAddStaffModal() {
  if (!addStaffModal) return;
  addStaffModal.classList.remove('show');
  document.body.style.overflow = '';
}

if (addStaffOpenBtn) {
  addStaffOpenBtn.addEventListener('click', openAddStaffModal);
}

[addStaffCloseX, addStaffCancel].forEach(btn => {
  if (btn) btn.addEventListener('click', closeAddStaffModal);
});

if (addStaffModal) {
  addStaffModal.addEventListener('click', function (e) {
    if (e.target === addStaffModal) closeAddStaffModal();
  });
}

if (addStaffRoleBox && addStaffRoleDropdown) {
  addStaffRoleBox.addEventListener('click', function (e) {
    e.stopPropagation();
    addStaffRoleDropdown.classList.toggle('open');
  });

  addStaffOptions.forEach(option => {
    option.addEventListener('click', function (e) {
      e.stopPropagation();

      addStaffOptions.forEach(o => o.classList.remove('active'));
      option.classList.add('active');

      addStaffSelectedRole.textContent = option.dataset.value;
      addStaffRoleDropdown.classList.remove('open');
    });
  });

  document.addEventListener('click', function () {
    addStaffRoleDropdown.classList.remove('open');
  });
}

if (addStaffSubmit) {
  addStaffSubmit.addEventListener('click', function () {
    closeAddStaffModal();

    if (staffSuccessNotif) {
      staffSuccessNotif.classList.add('show');

      setTimeout(function () {
        staffSuccessNotif.classList.remove('show');
      }, 3000);
    }
  });
}

// ================= REMOVE STAFF =================
const removeModal = document.getElementById('removeStaffModal');
const cancelRemove = document.getElementById('cancelRemoveStaff');
const confirmRemove = document.getElementById('confirmRemoveStaff');

let selectedRowToRemove = null;

// OPEN MODAL (delegation para kahit sorted gumana)
if (tableBody) {
  tableBody.addEventListener('click', function (e) {
    const removeBtn = e.target.closest('.remove-staff-btn');
    if (removeBtn) {
      selectedRowToRemove = removeBtn.closest('.request-row');
      removeModal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  });
}

// CLOSE
function closeRemoveModal() {
  removeModal.classList.remove('show');
  document.body.style.overflow = '';
}

if (cancelRemove) {
  cancelRemove.addEventListener('click', closeRemoveModal);
}

if (removeModal) {
  removeModal.addEventListener('click', function (e) {
    if (e.target === removeModal) closeRemoveModal();
  });
}

// CONFIRM REMOVE
if (confirmRemove) {
  confirmRemove.addEventListener('click', function () {

    if (selectedRowToRemove) {
      selectedRowToRemove.remove();
    }

    closeRemoveModal();

    // SHOW SUCCESS NOTIF
    if (staffSuccessNotif) {
      staffSuccessNotif.src = "images/success_deletion_notif.png"; // palit image
      staffSuccessNotif.classList.add('show');

      setTimeout(function () {
        staffSuccessNotif.classList.remove('show');

        // balik sa original notif image (creation)
        staffSuccessNotif.src = "images/success_creation_notif.png";
      }, 3000);
    }
  });
}


});