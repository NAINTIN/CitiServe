document.addEventListener('DOMContentLoaded', function () {
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