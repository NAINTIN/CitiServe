document.addEventListener('DOMContentLoaded', function () {
  var notifBtn = document.getElementById('notifBtn');
  var notifIcon = document.getElementById('notifIcon');
  var notifPanel = document.getElementById('notifPanel');

  if (notifBtn && notifIcon && notifPanel) {
    var notifCountBadge = document.getElementById('notifCount');
    var imgOn = notifBtn.getAttribute('data-img-on');
    var imgOff = notifBtn.getAttribute('data-img-off');
    var imgActive = notifBtn.getAttribute('data-img-active');
    var readUrl = notifBtn.getAttribute('data-read-url');
    var csrfToken = notifBtn.getAttribute('data-csrf-token');
    var panelOpen = false;

    function getUnreadItems() {
      return notifPanel.querySelectorAll('.notif-item.unread');
    }

    function updateUnreadUI() {
      var unreadCount = getUnreadItems().length;
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

    function markVisibleAsPendingRead() {
      notifPanel.querySelectorAll('.notif-item.unread').forEach(function (item) {
        if (item.style.display !== 'none') {
          item.setAttribute('data-pending-read', '1');
        }
      });
    }

    function persistRead(ids) {
      if (!readUrl || !csrfToken || !ids || !ids.length) {
        return;
      }
      var body = new URLSearchParams();
      body.set('_csrf_token', csrfToken);
      body.set('action', 'mark_many');
      body.set('ids', ids.join(','));
      fetch(readUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
        body: body.toString()
      }).catch(function () {});
    }

    function applyPendingRead() {
      var idsToPersist = [];
      notifPanel.querySelectorAll('.notif-item[data-pending-read="1"]').forEach(function (item) {
        var id = parseInt(item.getAttribute('data-id') || '0', 10);
        if (id > 0) {
          idsToPersist.push(id);
        }
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
      if (panelOpen && !notifPanel.contains(e.target) && !notifBtn.contains(e.target)) {
        closeNotifPanel();
      }
    });

    notifPanel.addEventListener('click', function (e) {
      var item = e.target.closest('.notif-item');
      if (!item) {
        return;
      }
      item.setAttribute('data-pending-read', '1');
      var link = item.getAttribute('data-link');
      if (link) {
        applyPendingRead();
        setTimeout(function () {
          window.location.href = link;
        }, 120);
      }
    });

    var tabs = notifPanel.querySelectorAll('.notif-tab');
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function (e) {
        e.stopPropagation();
        tabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');
        var filter = tab.getAttribute('data-filter');
        notifPanel.querySelectorAll('.notif-item').forEach(function (item) {
          item.style.display = filter === 'all' || item.getAttribute('data-category') === filter ? '' : 'none';
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
        window.location.href = '/CitiServe/public/admin/requests.php';
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

  // ============================== MODALS ==============================
  const announcementModal = document.getElementById("announcementModal");
  const addAnnouncementModal = document.getElementById("addAnnouncementModal");
  const editAnnouncementModal = document.getElementById("editAnnouncementModal");

  const cancelAddAnnouncement = document.getElementById("cancelAddAnnouncement");
  const cancelEditAnnouncement = document.getElementById("cancelEditAnnouncement");

  // ============================== ADD ANNOUNCEMENT BUTTONS ==============================
  function bindAddAnnouncementButtons() {
    const addAnnouncementBtns = document.querySelectorAll(".add-announcement-btn");

    addAnnouncementBtns.forEach(function (btn) {
      btn.onclick = function (e) {
        e.stopPropagation();

        if (!btn.classList.contains("inactive") && addAnnouncementModal) {
          addAnnouncementModal.style.display = "flex";
        }
      };
    });
  }

  bindAddAnnouncementButtons();

  if (cancelAddAnnouncement && addAnnouncementModal) {
    cancelAddAnnouncement.addEventListener("click", function () {
      addAnnouncementModal.style.display = "none";
    });
  }

  if (addAnnouncementModal) {
    addAnnouncementModal.addEventListener("click", function (e) {
      if (e.target === addAnnouncementModal) {
        addAnnouncementModal.style.display = "none";
      }
    });
  }

  if (announcementModal) {
    announcementModal.addEventListener("click", function (e) {
      if (e.target === announcementModal) {
        announcementModal.style.display = "none";
      }
    });
  }

  // ============================== UPLOAD IMAGE PREVIEW ==============================
  function bindUploadPreview(inputId, boxSelector) {
    const box = document.querySelector(boxSelector);
    const input = document.getElementById(inputId);

    if (!box || !input) return;

    input.onchange = function () {
      const file = this.files[0];
      if (!file) return;

      const imageUrl = URL.createObjectURL(file);
      box.classList.add("has-image");

      box.innerHTML = `
        <input type="file" id="${inputId}" accept="image/*" hidden>
        <img src="${imageUrl}" alt="Announcement preview" class="upload-ann-preview">
        <div class="change-image-label">Click to change image</div>
      `;

      bindUploadPreview(inputId, boxSelector);
    };
  }

  bindUploadPreview("announcementImage", "#addAnnouncementModal .upload-ann-box");
  bindUploadPreview("editAnnouncementImage", "#editAnnouncementModal .upload-ann-box");

  // ============================== EDIT ANNOUNCEMENT ==============================
  function bindEditAnnouncementButton() {
    const editAnnouncementBtn = document.querySelector(".ann-actions .announcement-action-img-btn:not(.disabled):first-child");

    if (editAnnouncementBtn && editAnnouncementModal) {
      editAnnouncementBtn.onclick = function (e) {
        e.stopPropagation();
        editAnnouncementModal.style.display = "flex";
      };
    }
  }

  bindEditAnnouncementButton();

  if (cancelEditAnnouncement && editAnnouncementModal) {
    cancelEditAnnouncement.addEventListener("click", function () {
      editAnnouncementModal.style.display = "none";
    });
  }

  if (editAnnouncementModal) {
    editAnnouncementModal.addEventListener("click", function (e) {
      if (e.target === editAnnouncementModal) {
        editAnnouncementModal.style.display = "none";
      }
    });
  }

  // ============================== REMOVE ANNOUNCEMENT ==============================
  function bindRemoveAnnouncementButton() {
    const removeAnnouncementBtn = document.querySelector(".ann-actions .announcement-action-img-btn:not(.disabled):last-child");

    if (removeAnnouncementBtn) {
      removeAnnouncementBtn.onclick = function (e) {
        e.stopPropagation();

        const announcementCard = document.querySelector(".admin-announcement-card");
        if (!announcementCard) return;

        announcementCard.classList.remove("has-announcement");
        announcementCard.classList.add("is-empty");

        announcementCard.innerHTML = `
          <div class="admin-ann-header">
            <div class="admin-ann-title">
              <img src="images/announcement_board_icon.png" alt="" class="admin-ann-icon">
              <span>Announcement Board</span>
            </div>

            <button type="button" class="announcement-img-btn add-announcement-btn">
              <img src="images/announcement_add_active.png" alt="Add Announcement">
            </button>
          </div>

          <div class="admin-ann-empty">
            <img src="images/announcement_empty_icon.png" alt="" class="admin-ann-empty-icon">
            <p>No announcement posted. Click "Add Announcement" to post one.</p>
            <span>Only one announcement can be active at a time.</span>
          </div>

          <div class="admin-ann-footer">
            <span class="ann-posted">Posted: N/A</span>

            <div class="ann-actions">
              <button type="button" class="announcement-action-img-btn disabled" disabled>
                <img src="images/announcement_edit.png" alt="Edit">
              </button>

              <button type="button" class="announcement-action-img-btn disabled" disabled>
                <img src="images/announcement_remove.png" alt="Remove">
              </button>
            </div>
          </div>
        `;

        bindAddAnnouncementButtons();
      };
    }
  }

  bindRemoveAnnouncementButton();
});