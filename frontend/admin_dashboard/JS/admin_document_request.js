document.addEventListener('DOMContentLoaded', function () {
  /* ================= NOTIFICATION PANEL ================= */
  const notifBtn = document.getElementById('notifBtn');
  const notifIcon = document.getElementById('notifIcon');
  const notifPanel = document.getElementById('notifPanel');

  if (notifBtn && notifIcon && notifPanel) {
    const notifCountBadge = document.getElementById('notifCount');
    const imgOn = notifBtn.getAttribute('data-img-on');
    const imgOff = notifBtn.getAttribute('data-img-off');
    const imgActive = notifBtn.getAttribute('data-img-active');
    const readUrl = notifBtn.getAttribute('data-read-url');
    const csrfToken = notifBtn.getAttribute('data-csrf-token');
    let panelOpen = false;

    function getUnreadItems() {
      return notifPanel.querySelectorAll('.notif-item.unread');
    }

    function updateUnreadUI() {
      const unreadCount = getUnreadItems().length;
      notifBtn.setAttribute('data-has-notif', unreadCount > 0 ? '1' : '0');
      if (notifCountBadge) {
        if (unreadCount > 0) {
          notifCountBadge.style.display = '';
          notifCountBadge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
        } else {
          notifCountBadge.style.display = 'none';
        }
      }
      if (!panelOpen) {
        notifIcon.src = unreadCount > 0 ? imgOn : imgOff;
      }
    }

    function persistRead(ids) {
      if (!readUrl || !csrfToken || !ids || !ids.length) return;
      const body = new URLSearchParams();
      body.set('_csrf_token', csrfToken);
      body.set('action', 'mark_many');
      body.set('ids', ids.join(','));
      fetch(readUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
        body: body.toString()
      }).catch(function () {});
    }

    function markVisibleAsPendingRead() {
      notifPanel.querySelectorAll('.notif-item.unread').forEach(function (item) {
        if (item.style.display !== 'none') item.setAttribute('data-pending-read', '1');
      });
    }

    function applyPendingRead() {
      const idsToPersist = [];
      notifPanel.querySelectorAll('.notif-item[data-pending-read="1"]').forEach(function (item) {
        const id = parseInt(item.getAttribute('data-id') || '0', 10);
        if (id > 0) idsToPersist.push(id);
        item.classList.remove('unread');
        item.classList.add('is-read');
        item.removeAttribute('data-pending-read');
      });
      persistRead(idsToPersist);
      updateUnreadUI();
    }

    function openNotifPanel() {
      panelOpen = true;
      notifPanel.classList.add('open');
      notifIcon.src = imgActive;
      markVisibleAsPendingRead();
    }

    function closeNotifPanel() {
      panelOpen = false;
      notifPanel.classList.remove('open');
      applyPendingRead();
      notifIcon.src = getUnreadItems().length > 0 ? imgOn : imgOff;
    }

    notifBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      panelOpen ? closeNotifPanel() : openNotifPanel();
    });

    document.addEventListener('click', function (e) {
      if (panelOpen && !notifPanel.contains(e.target) && !notifBtn.contains(e.target)) closeNotifPanel();
    });

    notifPanel.addEventListener('click', function (e) {
      const item = e.target.closest('.notif-item');
      if (!item) return;
      item.setAttribute('data-pending-read', '1');
      const link = item.getAttribute('data-link');
      if (link) {
        applyPendingRead();
        setTimeout(function () { window.location.href = link; }, 120);
      }
    });

    const tabs = notifPanel.querySelectorAll('.notif-tab');
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function (e) {
        e.stopPropagation();
        tabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');
        const filter = tab.getAttribute('data-filter');
        notifPanel.querySelectorAll('.notif-item').forEach(function (item) {
          item.style.display = filter === 'all' || item.getAttribute('data-category') === filter ? '' : 'none';
        });
        notifPanel.querySelectorAll('.notif-section-label').forEach(function (label) {
          let sibling = label.nextElementSibling;
          let hasVisible = false;
          while (sibling && !sibling.classList.contains('notif-section-label')) {
            if (sibling.classList.contains('notif-item') && sibling.style.display !== 'none') {
              hasVisible = true;
              break;
            }
            sibling = sibling.nextElementSibling;
          }
          label.style.display = hasVisible ? '' : 'none';
        });
        markVisibleAsPendingRead();
      });
    });

    const seePrev = document.getElementById('notifSeePrev');
    if (seePrev) {
      seePrev.addEventListener('click', function (e) {
        e.stopPropagation();
        applyPendingRead();
        window.location.href = '/CitiServe/public/admin/requests.php';
      });
    }

    updateUnreadUI();
  }

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