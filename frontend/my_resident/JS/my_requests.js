document.addEventListener('DOMContentLoaded', function () {
  /* =========================================================
     NOTIFICATION PANEL
     ========================================================= */
  var notifBtn = document.getElementById('notifBtn');
  var notifIcon = document.getElementById('notifIcon');
  var notifPanel = document.getElementById('notifPanel');

  if (notifBtn && notifIcon && notifPanel) {
    var imgOn = notifBtn.getAttribute('data-img-on');
    var imgOff = notifBtn.getAttribute('data-img-off');
    var imgActive = notifBtn.getAttribute('data-img-active');
    var panelOpen = false;

    function getUnreadItems() {
      return notifPanel.querySelectorAll('.notif-item.unread');
    }

    function updateUnreadUI() {
      var unreadCount = getUnreadItems().length;
      notifBtn.setAttribute('data-has-notif', unreadCount > 0 ? '1' : '0');

      if (!panelOpen) {
        notifIcon.src = unreadCount > 0 ? imgOn : imgOff;
      }
    }

    function markVisibleAsPendingRead() {
      notifPanel.querySelectorAll('.notif-item.unread').forEach(function (item) {
        if (item.style.display !== 'none') {
          item.setAttribute('data-pending-read', '1');
        }
      });
    }

    function applyPendingRead() {
      notifPanel.querySelectorAll('.notif-item[data-pending-read="1"]').forEach(function (item) {
        item.classList.remove('unread');
        item.classList.add('is-read');
        item.removeAttribute('data-pending-read');
      });

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

      var unreadCount = getUnreadItems().length;
      notifIcon.src = unreadCount > 0 ? imgOn : imgOff;
    }

    notifBtn.addEventListener('mousedown', function () {
      notifIcon.src = imgActive;
    });

    notifBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      panelOpen ? closeNotifPanel() : openNotifPanel();
    });

    notifBtn.addEventListener('mouseleave', function () {
      if (!panelOpen) {
        var unreadCount = getUnreadItems().length;
        notifIcon.src = unreadCount > 0 ? imgOn : imgOff;
      }
    });

    document.addEventListener('click', function (e) {
      if (panelOpen && !notifPanel.contains(e.target) && !notifBtn.contains(e.target)) {
        closeNotifPanel();
      }
    });

    notifPanel.addEventListener('click', function (e) {
      var item = e.target.closest('.notif-item');
      if (!item) return;

      item.setAttribute('data-pending-read', '1');

      var link = item.getAttribute('data-link');
      if (link) {
        applyPendingRead();

        setTimeout(function () {
          if (link.startsWith('http')) {
            window.open(link, '_blank');
          } else {
            window.location.href = link;
          }
        }, 150);
      }
    });

    var tabs = notifPanel.querySelectorAll('.notif-tab');
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function (e) {
        e.stopPropagation();

        tabs.forEach(function (t) {
          t.classList.remove('active');
        });
        tab.classList.add('active');

        var filter = tab.getAttribute('data-filter');

        notifPanel.querySelectorAll('.notif-item').forEach(function (item) {
          item.style.display =
            filter === 'all' || item.getAttribute('data-category') === filter ? '' : 'none';
        });

        notifPanel.querySelectorAll('.notif-section-label').forEach(function (label) {
          var sibling = label.nextElementSibling;
          var hasVisible = false;

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

    var seePrev = document.getElementById('notifSeePrev');
    if (seePrev) {
      seePrev.addEventListener('click', function (e) {
        e.stopPropagation();
        applyPendingRead();
        window.location.href = 'notifications.php';
      });
    }

    updateUnreadUI();
  }

  /* =========================================================
     PROFILE PANEL
     ========================================================= */
  var profilePill = document.getElementById('profilePill');
  var profilePanel = document.getElementById('profilePanel');

  if (profilePill && profilePanel) {
    var profileOpen = false;

    function openProfilePanel() {
      profileOpen = true;
      profilePanel.classList.add('open');
    }

    function closeProfilePanel() {
      profileOpen = false;
      profilePanel.classList.remove('open');
    }

    profilePill.addEventListener('click', function (e) {
      e.stopPropagation();

      if (profileOpen) {
        closeProfilePanel();
      } else {
        openProfilePanel();

        if (notifPanel) {
          notifPanel.classList.remove('open');
        }
      }
    });

    profilePanel.addEventListener('click', function (e) {
      e.stopPropagation();
    });

    document.addEventListener('click', function (e) {
      if (
        profileOpen &&
        !profilePanel.contains(e.target) &&
        !profilePill.contains(e.target)
      ) {
        closeProfilePanel();
      }
    });
  }

  /* =========================================================
     CUSTOM FILTER
     ========================================================= */
  var filterBox = document.getElementById('filterBox');
  var filterDropdown = document.getElementById('filterDropdown');
  var selectedStatusText = document.getElementById('selectedStatusText');
  var filterOptions = document.querySelectorAll('.filter-option');
  var searchInput = document.getElementById('searchInput');
  var clearBtn = document.getElementById('clearBtn');
  var tableBody = document.getElementById('requestsTableBody');

  var currentFilter = 'all';

  function applyFilters() {
    if (!tableBody) return;

    var rows = tableBody.querySelectorAll('.request-row');
    var keyword = searchInput ? searchInput.value.trim().toLowerCase() : '';

    rows.forEach(function (row) {
      var requestId = (row.getAttribute('data-request-id') || '').toLowerCase();
      var documentName = (row.getAttribute('data-document') || '').toLowerCase();
      var status = (row.getAttribute('data-status') || '').toLowerCase();

      var matchesSearch =
        keyword === '' ||
        requestId.includes(keyword) ||
        documentName.includes(keyword);

      var matchesFilter =
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
    searchInput.addEventListener('input', function () {
      applyFilters();
    });
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

      var allOption = document.querySelector('.filter-option[data-value="all"]');
      if (allOption) {
        allOption.classList.add('active');
      }

      applyFilters();
    });
  }



  /* =========================================================
     REQUEST DETAILS MODAL
     ========================================================= */
  var requestDetailsModal = document.getElementById('requestDetailsModal');
  var requestDetailsClose = document.getElementById('requestDetailsClose');

  var modalRequestId = document.getElementById('modalRequestId');
  var modalDocumentType = document.getElementById('modalDocumentType');
  var modalDateSubmitted = document.getElementById('modalDateSubmitted');
  var modalFee = document.getElementById('modalFee');
  var modalPaymentMethod = document.getElementById('modalPaymentMethod');
  var modalReferenceNumber = document.getElementById('modalReferenceNumber');
  var modalStatusImage = document.getElementById('modalStatusImage');

  function openRequestDetailsModal(row) {
    if (!row || !requestDetailsModal) return;

    modalRequestId.textContent = row.getAttribute('data-request-id') || '';
    modalDocumentType.textContent = row.getAttribute('data-document') || '';
    modalDateSubmitted.textContent = row.getAttribute('data-datetime-full') || '';
    modalFee.textContent = row.getAttribute('data-fee') || '';
    modalPaymentMethod.textContent = row.getAttribute('data-payment') || '';
    modalReferenceNumber.textContent = row.getAttribute('data-reference-number') || '';

    if (modalStatusImage) {
      modalStatusImage.src = row.getAttribute('data-status-image') || '';
      modalStatusImage.alt = row.getAttribute('data-status') || 'Status';
    }

    requestDetailsModal.classList.add('show');
    document.body.style.overflow = 'hidden';

    var requestDetailWarning = document.getElementById('requestDetailWarning');

    var status = row.getAttribute('data-status') || '';

    if (requestDetailWarning) {
      if (status.toLowerCase() === 'rejected') {
        requestDetailWarning.style.display = 'flex';
      } else {
        requestDetailWarning.style.display = 'none';
      }
    }
  }

  function closeRequestDetailsModal() {
    if (!requestDetailsModal) return;

    requestDetailsModal.classList.remove('show');
    document.body.style.overflow = '';
  }

  document.querySelectorAll('.details-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var row = btn.closest('.request-row');
      openRequestDetailsModal(row);
    });
  });

  if (requestDetailsClose) {
    requestDetailsClose.addEventListener('click', function () {
      closeRequestDetailsModal();
    });
  }

  if (requestDetailsModal) {
    requestDetailsModal.addEventListener('click', function (e) {
      if (e.target === requestDetailsModal) {
        closeRequestDetailsModal();
      }
    });
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && requestDetailsModal && requestDetailsModal.classList.contains('show')) {
      closeRequestDetailsModal();
    }
  });


});