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

  const tableBody = document.getElementById('verificationTableBody');

  const reviewModal = document.getElementById('accountReviewModal');
  const reviewClose = document.getElementById('accountReviewClose');

  const reviewResidentId = document.getElementById('reviewResidentId');
  const reviewSmallId = document.getElementById('reviewSmallId');
  const reviewInitials = document.getElementById('reviewInitials');
  const reviewName = document.getElementById('reviewName');
  const reviewEmail = document.getElementById('reviewEmail');
  const reviewDate = document.getElementById('reviewDate');
  const reviewAddress = document.getElementById('reviewAddress');
  const reviewDocumentName = document.getElementById('reviewDocumentName');

  function openReviewModal(row) {
    if (!row || !reviewModal) return;

    reviewResidentId.textContent = row.dataset.residentId || '';
    reviewSmallId.textContent = row.dataset.residentId || '';
    reviewInitials.textContent = row.dataset.initials || '';
    reviewName.textContent = row.dataset.name || '';
    reviewEmail.textContent = row.dataset.email || '';
    reviewDate.textContent = row.dataset.date || '';
    reviewAddress.textContent = row.dataset.address || '';
    reviewDocumentName.textContent = row.dataset.document || 'Valid ID (Front).jpg';

    reviewModal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeReviewModal() {
    if (!reviewModal) return;
    reviewModal.classList.remove('show');
    document.body.style.overflow = '';
  }

  if (tableBody) {
    tableBody.addEventListener('click', function (e) {
      const reviewBtn = e.target.closest('.review-btn');
      if (!reviewBtn) return;

      openReviewModal(reviewBtn.closest('.verification-row'));
    });
  }

  if (reviewClose) {
    reviewClose.addEventListener('click', closeReviewModal);
  }

  if (reviewModal) {
    reviewModal.addEventListener('click', function (e) {
      if (e.target === reviewModal) closeReviewModal();
    });
  }
});