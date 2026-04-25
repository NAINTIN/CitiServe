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

  /* ================= FILTER + SEARCH ================= */
  const filterBox = document.getElementById('filterBox');
  const filterDropdown = document.getElementById('filterDropdown');
  const selectedStatusText = document.getElementById('selectedStatusText');
  const filterOptions = document.querySelectorAll('.filter-option');
  const searchInput = document.getElementById('searchInput');
  const clearBtn = document.getElementById('clearBtn');
  const tableBody = document.getElementById('requestsTableBody');

  let currentFilter = 'all';

  function applyFilters() {
    if (!tableBody) return;

    const keyword = searchInput ? searchInput.value.trim().toLowerCase() : '';
    const rows = tableBody.querySelectorAll('.request-row');

    rows.forEach(function (row) {
      const complaintId = (row.getAttribute('data-complaint-id') || '').toLowerCase();
      const reporter = (row.getAttribute('data-reporter') || '').toLowerCase();
      const category = (row.getAttribute('data-category') || '').toLowerCase();
      const status = (row.getAttribute('data-status') || '').toLowerCase();

      const matchesSearch =
        keyword === '' ||
        complaintId.includes(keyword) ||
        reporter.includes(keyword) ||
        category.includes(keyword);

      const matchesFilter =
        currentFilter === 'all' || status === currentFilter;

      row.style.display = matchesSearch && matchesFilter ? '' : 'none';
    });
  }

  if (filterBox && filterDropdown) {
    filterBox.addEventListener('click', function (e) {
      e.stopPropagation();
      filterDropdown.classList.toggle('open');
    });

    filterOptions.forEach(function (option) {
      option.addEventListener('click', function (e) {
        e.stopPropagation();

        filterOptions.forEach(function (item) {
          item.classList.remove('active');
        });

        option.classList.add('active');
        currentFilter = option.getAttribute('data-value');

        if (selectedStatusText) {
          selectedStatusText.textContent = option.textContent;
        }

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

      if (selectedStatusText) {
        selectedStatusText.textContent = 'All';
      }

      filterOptions.forEach(function (item) {
        item.classList.remove('active');
      });

      const allOption = document.querySelector('.filter-option[data-value="all"]');
      if (allOption) {
        allOption.classList.add('active');
      }

      applyFilters();
    });
  }

  /* ================= MANAGE BUTTON PLACEHOLDER ================= */
const manageComplaintModal = document.getElementById('manageComplaintModal');
const manageComplaintClose = document.getElementById('manageComplaintClose');
const manageComplaintCancelBtn = document.getElementById('manageComplaintCancelBtn');

const manageComplaintId = document.getElementById('manageComplaintId');
const manageComplaintIdValue = document.getElementById('manageComplaintIdValue');
const manageComplaintReporter = document.getElementById('manageComplaintReporter');
const manageComplaintCategory = document.getElementById('manageComplaintCategory');
const manageComplaintDate = document.getElementById('manageComplaintDate');
const manageComplaintDescription = document.getElementById('manageComplaintDescription');
const manageComplaintLocationText = document.getElementById('manageComplaintLocationText');
const manageComplaintMap = document.getElementById('manageComplaintMap');
const manageComplaintEvidenceSection = document.getElementById('manageComplaintEvidenceSection');
const manageComplaintEvidence = document.getElementById('manageComplaintEvidence');
const manageComplaintSelectedStatus = document.getElementById('manageComplaintSelectedStatus');

function openManageComplaintModal(row) {
  if (!row || !manageComplaintModal) return;

  const id = row.dataset.complaintId || '';

  manageComplaintId.textContent = id;
  manageComplaintIdValue.textContent = id;
  manageComplaintReporter.textContent = row.dataset.reporter || '';
  manageComplaintCategory.textContent = row.dataset.category || '';
  manageComplaintDate.textContent = row.dataset.datetimeFull || row.dataset.date || '';
  manageComplaintDescription.textContent = row.dataset.description || '';
  manageComplaintLocationText.textContent = row.dataset.locationText || '';
  manageComplaintSelectedStatus.textContent = row.dataset.status || 'Received';

  if ((row.dataset.type || '').toLowerCase() === 'anonymous') {
    manageComplaintReporter.style.color = '#EA5F00';
  } else {
    manageComplaintReporter.style.color = '#111111';
  }

  const mapQuery = row.dataset.mapQuery || '';
  manageComplaintMap.src =
    'https://www.google.com/maps?q=' + encodeURIComponent(mapQuery) + '&z=15&output=embed';

  const evidence = row.dataset.evidence || '';
  if (evidence.trim() !== '') {
    manageComplaintEvidenceSection.classList.add('show');
    manageComplaintEvidence.textContent = evidence;
  } else {
    manageComplaintEvidenceSection.classList.remove('show');
    manageComplaintEvidence.textContent = '';
  }

  manageComplaintModal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeManageComplaintModal() {
  manageComplaintModal.classList.remove('show');
  document.body.style.overflow = '';
}

document.querySelectorAll('.manage-btn').forEach(function (btn) {
  btn.addEventListener('click', function () {
    openManageComplaintModal(btn.closest('.request-row'));
  });
});

[manageComplaintClose, manageComplaintCancelBtn].forEach(function (btn) {
  if (btn) btn.addEventListener('click', closeManageComplaintModal);
});

if (manageComplaintModal) {
  manageComplaintModal.addEventListener('click', function (e) {
    if (e.target === manageComplaintModal) closeManageComplaintModal();
  });
}

const manageComplaintStatusBox = document.getElementById('manageComplaintStatusBox');
const manageComplaintStatusDropdown = document.getElementById('manageComplaintStatusDropdown');
const manageComplaintStatusOptions = document.querySelectorAll('#manageComplaintStatusDropdown .manage-status-option');

if (manageComplaintStatusBox && manageComplaintStatusDropdown) {
  manageComplaintStatusBox.addEventListener('click', function (e) {
    e.stopPropagation();
    manageComplaintStatusDropdown.classList.toggle('open');
  });

  manageComplaintStatusOptions.forEach(function (option) {
    option.addEventListener('click', function (e) {
      e.stopPropagation();

      manageComplaintStatusOptions.forEach(item => item.classList.remove('active'));
      option.classList.add('active');

      manageComplaintSelectedStatus.textContent = option.dataset.value;
      manageComplaintStatusDropdown.classList.remove('open');
    });
  });

  document.addEventListener('click', function () {
    manageComplaintStatusDropdown.classList.remove('open');
  });
}
});