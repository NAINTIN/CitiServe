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
        window.location.href = '/CitiServe/public/notifications.php';
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
     CHARACTER COUNTER
     ========================================================= */
  var description = document.querySelector('textarea[name="description"]');
  var charCount = document.getElementById('charCount');

  if (description && charCount) {
    function updateCount() {
      charCount.textContent = description.value.length;
    }

    description.addEventListener('input', updateCount);
    updateCount();
  }

  /* =========================================================
     EVIDENCE UPLOAD
     ========================================================= */
  var evidenceUpload = document.getElementById('evidenceUpload');
  var fileList = document.getElementById('fileList');
  var uploadDefault = document.getElementById('uploadDefault');

  if (evidenceUpload && fileList && uploadDefault) {
    evidenceUpload.addEventListener('change', function () {
      fileList.innerHTML = '';

      if (evidenceUpload.files.length === 0) {
        uploadDefault.style.display = 'flex';
        fileList.style.display = 'none';
        return;
      }

      Array.from(evidenceUpload.files).forEach(function (file) {
        var item = document.createElement('div');
        item.className = 'file-item';
        item.textContent = file.name;
        fileList.appendChild(item);
      });

      uploadDefault.style.display = 'none';
      fileList.style.display = 'flex';
    });
  }

  /* =========================================================
     MAP
     ========================================================= */
  var useMyLocation = document.getElementById('useMyLocation');
  var locationInput = document.getElementById('complaintLocation');
  var googleMapFrame = document.getElementById('googleMapFrame');
  var mapBox = document.getElementById('locationMapBox');

  function updateMap(query) {
    if (!googleMapFrame || !mapBox || !query || !query.trim()) return;

    googleMapFrame.src =
      'https://www.google.com/maps?q=' +
      encodeURIComponent(query.trim()) +
      '&z=15&output=embed';

    mapBox.classList.add('has-map');
  }

  if (locationInput) {
    locationInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        updateMap(locationInput.value);
      }
    });
  }

  if (useMyLocation && locationInput) {
    useMyLocation.addEventListener('click', function () {
      if (!navigator.geolocation) {
        alert('Geolocation is not supported on this device.');
        return;
      }

      navigator.geolocation.getCurrentPosition(
        function (position) {
          var lat = position.coords.latitude.toFixed(6);
          var lng = position.coords.longitude.toFixed(6);
          var coords = lat + ', ' + lng;

          locationInput.value = coords;
          updateMap(coords);
        },
        function () {
          alert('Unable to get your location.');
        }
      );
    });
  }

  /* ========================= CUSTOM SELECT ========================= */
  var suffixSelect = document.getElementById('suffixSelect');
  var selectBox = document.getElementById('selectBox');
  var selectDropdown = document.getElementById('selectDropdown');
  var selectedText = document.getElementById('selectedText');
  var suffixValue = document.getElementById('suffixValue');
  var selectOptions = document.querySelectorAll('#selectDropdown .select-option');

  if (suffixSelect && selectBox && selectDropdown && selectedText && suffixValue) {
    function positionDropdown() {
      var rect = selectBox.getBoundingClientRect();

      selectDropdown.style.width = rect.width + 'px';
      selectDropdown.style.left = rect.left + 'px';
      selectDropdown.style.top = (rect.bottom + 2) + 'px';
    }

    function openDropdown() {
      positionDropdown();
      selectDropdown.style.display = 'block';
    }

    function closeDropdown() {
      selectDropdown.style.display = 'none';
    }

    selectBox.addEventListener('click', function (e) {
      e.stopPropagation();

      if (selectDropdown.style.display === 'block') {
        closeDropdown();
      } else {
        openDropdown();
      }
    });

    selectOptions.forEach(function (option) {
      option.addEventListener('click', function (e) {
        e.stopPropagation();

        selectOptions.forEach(function (item) {
          item.classList.remove('active');
        });

        option.classList.add('active');
        selectedText.textContent = option.textContent;
        suffixValue.value = option.getAttribute('data-value');
        closeDropdown();
      });
    });

    window.addEventListener('resize', function () {
      if (selectDropdown.style.display === 'block') {
        positionDropdown();
      }
    });

    window.addEventListener('scroll', function () {
      if (selectDropdown.style.display === 'block') {
        positionDropdown();
      }
    }, true);

    document.addEventListener('click', function () {
      closeDropdown();
    });
  }

/* =========================================================
   INLINE FIELD WARNINGS + MODALS
   ========================================================= */
var complaintForm = document.getElementById('complaintForm');
var submitModal = document.getElementById('submitModal');
var discardModal = document.getElementById('discardModal');

var cancelSubmitBtn = document.getElementById('cancelSubmitBtn');
var confirmSubmitBtn = document.getElementById('confirmSubmitBtn');

var confirmDiscardBtn = document.getElementById('confirmDiscardBtn');
var stayOnPageBtn = document.getElementById('stayOnPageBtn');

var leaveFormLinks = document.querySelectorAll('.leave-form-link');

var pendingLeaveUrl = null;
var isDirty = false;

var activeWarnings = [];

/* ---------- modal helpers ---------- */
function openModal(modal) {
  if (modal) modal.classList.add('open');
}

function closeModal(modal) {
  if (modal) modal.classList.remove('open');
}

function markFormDirty() {
  isDirty = true;
}

/* ---------- warning helpers ---------- */
function removeAllWarnings() {
  activeWarnings.forEach(function (item) {
    if (item.warning && item.warning.parentNode) {
      item.warning.parentNode.removeChild(item.warning);
    }
  });

  activeWarnings = [];

  document.querySelectorAll('.field-error').forEach(function (field) {
    field.classList.remove('field-error');
  });
}

function positionWarningItem(item) {
  if (!item || !item.field || !item.warning) return;

  var rect = item.field.getBoundingClientRect();
  var offsetX = 12;

  item.warning.style.top = (rect.top + rect.height / 2) + 'px';
  item.warning.style.left = (rect.right + offsetX) + 'px';
  item.warning.style.transform = 'translateY(-50%)';
}

function repositionAllWarnings() {
  activeWarnings.forEach(function (item) {
    positionWarningItem(item);
  });
}

function showFieldWarning(field, imageSrc) {
  if (!field) return;

  var existingIndex = activeWarnings.findIndex(function (item) {
    return item.field === field;
  });

  if (existingIndex !== -1) return;

  var warning = document.createElement('div');
  warning.className = 'field-warning';
  warning.innerHTML = '<img src="' + imageSrc + '" alt="Warning">';

  document.body.appendChild(warning);

  field.classList.add('field-error');

  var item = {
    field: field,
    warning: warning,
    imageSrc: imageSrc
  };

  activeWarnings.push(item);
  positionWarningItem(item);
}

function isFieldEmpty(field) {
  if (!field) return true;

  if (field.type === 'file') {
    return !field.files || field.files.length === 0;
  }

  if (field.type === 'hidden') {
    return !field.value || field.value.trim() === '';
  }

  return !field.value || field.value.trim() === '';
}

/* ---------- validation ---------- */
function validateRequiredFields() {
  if (!complaintForm) return true;

  var requiredFields = complaintForm.querySelectorAll('[required]');
  var hasError = false;
  var firstInvalid = null;

  requiredFields.forEach(function (field) {
    if (isFieldEmpty(field)) {
      hasError = true;
      if (!firstInvalid) firstInvalid = field;
      showFieldWarning(field, 'images/this_field_required.png');
    }
  });

  if (firstInvalid) {
    firstInvalid.focus();
  }

  return !hasError;
}

function validateEmailField() {
  if (!complaintForm) return true;

  var emailField = complaintForm.querySelector('input[name="email"]');
  if (!emailField) return true;

  var value = emailField.value.trim();

  if (value === '') return true;

  if (!value.endsWith('@gmail.com')) {
    showFieldWarning(emailField, 'images/enter_valid_email.png');
    emailField.focus();
    return false;
  }

  return true;
}

/* ---------- form events ---------- */
if (complaintForm) {
  complaintForm.querySelectorAll('input, textarea, select').forEach(function (field) {
    field.addEventListener('input', function () {
      markFormDirty();

      if (field.classList.contains('field-error')) {
        removeAllWarnings();
      }
    });

    field.addEventListener('change', function () {
      markFormDirty();

      if (field.classList.contains('field-error')) {
        removeAllWarnings();
      }
    });

    field.addEventListener('focus', function () {
      repositionAllWarnings();
    });
  });

  complaintForm.addEventListener('submit', function (e) {
    e.preventDefault();

    removeAllWarnings();

    var requiredValid = validateRequiredFields();
    var emailValid = validateEmailField();

    if (!requiredValid || !emailValid) {
      repositionAllWarnings();
      return;
    }

    openModal(submitModal);
  });
}

/* ---------- submit modal ---------- */
if (cancelSubmitBtn) {
  cancelSubmitBtn.addEventListener('click', function () {
    closeModal(submitModal);
  });
}

if (confirmSubmitBtn && complaintForm) {
  confirmSubmitBtn.addEventListener('click', function () {
    closeModal(submitModal);
    complaintForm.submit();
  });
}

/* ---------- discard modal ---------- */
leaveFormLinks.forEach(function (link) {
  link.addEventListener('click', function (e) {
    if (!isDirty) return;

    e.preventDefault();
    pendingLeaveUrl = link.getAttribute('href');
    openModal(discardModal);
  });
});

if (stayOnPageBtn) {
  stayOnPageBtn.addEventListener('click', function () {
    pendingLeaveUrl = null;
    closeModal(discardModal);
  });
}

if (confirmDiscardBtn) {
  confirmDiscardBtn.addEventListener('click', function () {
    if (pendingLeaveUrl) {
      window.location.href = pendingLeaveUrl;
    }
  });
}

/* ---------- close modals on backdrop ---------- */
[submitModal, discardModal].forEach(function (modal) {
  if (!modal) return;

  modal.addEventListener('click', function (e) {
    if (e.target === modal) {
      closeModal(modal);
    }
  });
});

/* ---------- keep warnings aligned while scrolling/resizing ---------- */
window.addEventListener('scroll', function () {
  repositionAllWarnings();
}, true);

window.addEventListener('resize', function () {
  repositionAllWarnings();
});

});