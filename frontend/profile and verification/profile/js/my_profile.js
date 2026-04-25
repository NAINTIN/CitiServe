  /* Dropdown — now uses a hidden input instead of localStorage */
  function toggleDropdown(id) {
    const d = document.getElementById(id);
    document.querySelectorAll('.custom-select').forEach(x => { if (x.id !== id) x.classList.remove('open'); });
    d.classList.toggle('open');
  }
  function selectOption(dropdownId, value, hiddenInputId) {
    const d = document.getElementById(dropdownId);
    d.querySelector('.custom-select-text').textContent = value;
    d.querySelectorAll('.custom-select-option').forEach(o => o.classList.toggle('selected', o.textContent.trim() === value));
    document.getElementById(hiddenInputId).value = value;
    d.classList.add('filled');
    d.classList.remove('open');
  }
  document.addEventListener('click', e => {
    if (!e.target.closest('.custom-select')) document.querySelectorAll('.custom-select').forEach(d => d.classList.remove('open'));
  });

  /* Sidenav */
  const sectionMap = {
    '#profile':      'section-profile',
    '#verification': 'section-verification',
    '#password':     'section-password'
  };

  function showSection(href) {
    document.querySelectorAll('.profile-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.sidenav-item').forEach(l => {
      l.classList.remove('active');
      const img = l.querySelector('img');
      if (img) img.src = img.dataset.src;
    });
    const target = sectionMap[href];
    if (target) document.getElementById(target).classList.add('active');
    const link = document.querySelector(`.sidenav-item[href="${href}"]`);
    if (link) {
      link.classList.add('active');
      const img = link.querySelector('img');
      if (img) img.src = img.dataset.pink;
    }
  }

  document.querySelectorAll('.sidenav-item').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const href = this.getAttribute('href');
      showSection(href);
      localStorage.setItem('activeTab', href);
    });
  });

  const savedTab = localStorage.getItem('activeTab');
  showSection(savedTab && sectionMap[savedTab] ? savedTab : '#profile');

  /* Document Upload preview */
  function handleDocUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    document.getElementById('uploadPlaceholder').style.display = 'none';
    const fileDiv = document.getElementById('uploadedFile');
    fileDiv.style.display = 'flex';
    document.getElementById('uploadedFileName').textContent = file.name;
    // Auto-submit the doc form
    document.getElementById('docForm').submit();
  }

  /* Toggle Password Visibility */
  function togglePw(inputId, btn) {
    const input = document.getElementById(inputId);
    const img = btn.querySelector('img');
    if (input.type === 'password') {
      input.type = 'text';
      img.src = 'images/eyeclosed.png';
    } else {
      input.type = 'password';
      img.src = 'images/eye.png';
    }
  }


  document.addEventListener('DOMContentLoaded', function () {

  /* ========================== NOTIFICATION PANEL =========================== */
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
});