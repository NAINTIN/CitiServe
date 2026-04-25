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
      const requestId = (row.getAttribute('data-request-id') || '').toLowerCase();
      const resident = (row.getAttribute('data-resident') || '').toLowerCase();
      const documentName = (row.getAttribute('data-document') || '').toLowerCase();
      const status = (row.getAttribute('data-status') || '').toLowerCase();

      const matchesSearch =
        keyword === '' ||
        requestId.includes(keyword) ||
        resident.includes(keyword) ||
        documentName.includes(keyword);

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

  /* ================= MANAGE REQUEST MODAL ================= */
  const manageModal = document.getElementById('manageRequestModal');
  const manageClose = document.getElementById('manageRequestClose');
  const manageCancelBtn = document.getElementById('manageCancelBtn');

  const manageRequestId = document.getElementById('manageRequestId');
  const manageResident = document.getElementById('manageResident');
  const manageDocument = document.getElementById('manageDocument');
  const manageFee = document.getElementById('manageFee');
  const managePayment = document.getElementById('managePayment');
  const manageReference = document.getElementById('manageReference');
  const manageDate = document.getElementById('manageDate');
  const manageSelectedStatus = document.getElementById('manageSelectedStatus');
  const manageRequirementsBox = document.getElementById('manageRequirementsBox');

  function renderRequirements(row) {
    if (!manageRequirementsBox) return;

    let requirements = [];

    try {
      requirements = JSON.parse(row.dataset.requirements || '[]');
    } catch (error) {
      requirements = [];
    }

    manageRequirementsBox.innerHTML = `
      <div class="manage-req-title">Uploaded Requirement/s</div>
    `;

    if (requirements.length === 0) {
      manageRequirementsBox.innerHTML += `
        <div class="manage-req-empty">No uploaded requirements</div>
      `;
      return;
    }

    requirements.forEach(function (file) {
      manageRequirementsBox.innerHTML += `
        <div class="manage-req-item">
          <span>${file}</span>
          <button type="button">View</button>
        </div>
      `;
    });
  }

  function openManageModal(row) {
    if (!row || !manageModal) return;

    manageRequestId.textContent = row.dataset.requestId || '';
    manageResident.textContent = row.dataset.resident || '';
    manageDocument.textContent = row.dataset.document || '';
    manageFee.textContent = row.dataset.fee || '';
    managePayment.textContent = row.dataset.payment || '';
    manageReference.textContent = row.dataset.referenceNumber || '3014823639257';
    manageDate.textContent = row.dataset.datetimeFull || row.dataset.date || '';
    manageSelectedStatus.textContent = row.dataset.status || 'Received';

    renderRequirements(row);

    manageModal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeManageModal() {
    if (!manageModal) return;
    manageModal.classList.remove('show');
    document.body.style.overflow = '';
  }

  document.querySelectorAll('.manage-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      openManageModal(btn.closest('.request-row'));
    });
  });

  [manageClose, manageCancelBtn].forEach(function (btn) {
    if (btn) btn.addEventListener('click', closeManageModal);
  });

  if (manageModal) {
    manageModal.addEventListener('click', function (e) {
      if (e.target === manageModal) closeManageModal();
    });
  }

  /* ================= MANAGE STATUS DROPDOWN ================= */
  const manageStatusBox = document.getElementById('manageStatusBox');
  const manageStatusDropdown = document.getElementById('manageStatusDropdown');
  const manageStatusOptions = document.querySelectorAll('.manage-status-option');

  if (manageStatusBox && manageStatusDropdown) {
    manageStatusBox.addEventListener('click', function (e) {
      e.stopPropagation();
      manageStatusDropdown.classList.toggle('open');
    });

    manageStatusOptions.forEach(function (option) {
      option.addEventListener('click', function (e) {
        e.stopPropagation();

        manageStatusOptions.forEach(function (item) {
          item.classList.remove('active');
        });

        option.classList.add('active');
        manageSelectedStatus.textContent = option.dataset.value;
        manageStatusDropdown.classList.remove('open');
      });
    });

    document.addEventListener('click', function () {
      manageStatusDropdown.classList.remove('open');
    });
  }
});