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

    profilePanel.addEventListener('click', function (e) {
      e.stopPropagation();
    });

    document.addEventListener('click', function () {
      if (profileOpen) {
        profilePanel.classList.remove('open');
        profileOpen = false;
      }
    });
  }

  // ----------------------------------- RESIDENT SORT

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
      const id = (row.dataset.residentId || '').toLowerCase();
      const name = (row.dataset.name || '').toLowerCase();
      const email = (row.dataset.email || '').toLowerCase();
      const status = (row.dataset.status || '').toLowerCase();

      const matchesSearch =
        keyword === '' ||
        id.includes(keyword) ||
        name.includes(keyword) ||
        email.includes(keyword);

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

  document.querySelectorAll('.manage-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const row = btn.closest('.request-row');

      console.log('View Profile:', {
        id: row.dataset.residentId,
        name: row.dataset.name,
        email: row.dataset.email
      });
    });
  });


// ----------------------------------- RESIDENT PROFILE MODAL

const residentProfileModal = document.getElementById('residentProfileModal');
const residentProfileCloseX = document.getElementById('residentProfileCloseX');
const residentProfileCloseBtn = document.getElementById('residentProfileCloseBtn');

const profileInitials = document.getElementById('profileInitials');
const profileName = document.getElementById('profileName');
const profileId = document.getElementById('profileId');
const profileTypeImg = document.getElementById('profileTypeImg');
const profileEmail = document.getElementById('profileEmail');
const profileContact = document.getElementById('profileContact');
const profileBirthdate = document.getElementById('profileBirthdate');
const profileGender = document.getElementById('profileGender');
const profileCivil = document.getElementById('profileCivil');
const profileCitizenship = document.getElementById('profileCitizenship');
const profileAddress = document.getElementById('profileAddress');
const profileJoined = document.getElementById('profileJoined');
const profileRequests = document.getElementById('profileRequests');
const profileComplaints = document.getElementById('profileComplaints');
const profileStatus = document.getElementById('profileStatus');
const profileStatusBox = document.getElementById('profileStatusBox');

const toggleResidentStatusBtn = document.getElementById('toggleResidentStatusBtn');
const toggleResidentStatusImg = document.getElementById('toggleResidentStatusImg');

let currentResidentRow = null;

function updateProfileStatusUI(status) {
  const isActive = status.toLowerCase() === 'active';

  profileStatus.textContent = isActive ? 'Active' : 'Inactive';

  profileStatusBox.classList.toggle('active', isActive);
  profileStatusBox.classList.toggle('inactive', !isActive);

  toggleResidentStatusImg.src = isActive
    ? 'images/resident_mark_inactive.png'
    : 'images/resident_mark_active.png';
}

function openResidentProfile(row) {
  if (!row || !residentProfileModal) return;

  currentResidentRow = row;

  profileInitials.textContent = row.dataset.initials || '';
  profileName.textContent = row.dataset.name || '';
  profileId.textContent = row.dataset.residentId || '';
  profileEmail.textContent = row.dataset.email || '';
  profileContact.textContent = row.dataset.contact || '';
  profileBirthdate.textContent = row.dataset.birthdate || '';
  profileGender.textContent = row.dataset.gender || '';
  profileCivil.textContent = row.dataset.civil || '';
  profileCitizenship.textContent = row.dataset.citizenship || '';
  profileAddress.textContent = row.dataset.address || '';
  profileJoined.textContent = row.dataset.joined || '';
  profileRequests.textContent = row.dataset.requests || '0';
  profileComplaints.textContent = row.dataset.complaints || '0';

  profileTypeImg.src = row.dataset.type.toLowerCase() === 'basic'
    ? 'images/resident_basic_admin.png'
    : 'images/resident_full_admin.png';

  updateProfileStatusUI(row.dataset.status || 'Active');

  residentProfileModal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeResidentProfile() {
  residentProfileModal.classList.remove('show');
  document.body.style.overflow = '';
}

document.querySelectorAll('.manage-btn').forEach(btn => {
  btn.onclick = function () {
    openResidentProfile(btn.closest('.request-row'));
  };
});

[residentProfileCloseX, residentProfileCloseBtn].forEach(btn => {
  if (btn) btn.addEventListener('click', closeResidentProfile);
});

if (residentProfileModal) {
  residentProfileModal.addEventListener('click', function (e) {
    if (e.target === residentProfileModal) closeResidentProfile();
  });
}

if (toggleResidentStatusBtn) {
  toggleResidentStatusBtn.addEventListener('click', function () {
    if (!currentResidentRow) return;

    const currentStatus = currentResidentRow.dataset.status || 'Active';
    const newStatus = currentStatus.toLowerCase() === 'active' ? 'Inactive' : 'Active';

    currentResidentRow.dataset.status = newStatus;
    updateProfileStatusUI(newStatus);

    const statusImg = currentResidentRow.querySelector('.resident-status-img');
    if (statusImg) {
      statusImg.src = newStatus.toLowerCase() === 'active'
        ? 'images/resident_active_staff.png'
        : 'images/resident_inactive_staff.png';

      statusImg.alt = newStatus;
    }
  });
}




});