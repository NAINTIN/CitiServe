document.addEventListener('DOMContentLoaded', function () {
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
  const reviewDocumentLink = document.getElementById('reviewDocumentLink');

  const verifyForm = document.getElementById('verifyForm');
  const verifyUserId = document.getElementById('verifyUserId');
  const verifyAction = document.getElementById('verifyAction');
  const rejectAccountBtn = document.getElementById('rejectAccountBtn');
  const approveAccountBtn = document.getElementById('approveAccountBtn');

  function openReviewModal(row) {
    if (!row || !reviewModal) return;

    reviewResidentId.textContent = row.dataset.residentId || '';
    reviewSmallId.textContent = row.dataset.residentId || '';
    reviewInitials.textContent = row.dataset.initials || '';
    reviewName.textContent = row.dataset.name || '';
    reviewEmail.textContent = row.dataset.email || '';
    reviewDate.textContent = row.dataset.date || '';
    reviewAddress.textContent = row.dataset.address || 'No address provided';

    const docName = row.dataset.document || 'No document';
    reviewDocumentName.textContent = docName;

    // Set the document link to the actual file
    if (reviewDocumentLink) {
      if (docName !== 'No document') {
        reviewDocumentLink.href = '/CitiServe/public/uploads/proof_of_id/' + encodeURIComponent(docName);
        reviewDocumentLink.style.display = '';
      } else {
        reviewDocumentLink.href = '#';
        reviewDocumentLink.style.display = 'none';
      }
    }

    verifyUserId.value = row.dataset.residentId || '';

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

  if (approveAccountBtn) {
    approveAccountBtn.addEventListener('click', function () {
      if (verifyUserId.value) {
        verifyAction.value = 'verify';
        verifyForm.submit();
      }
    });
  }

  if (rejectAccountBtn) {
    rejectAccountBtn.addEventListener('click', function () {
      if (verifyUserId.value) {
        verifyAction.value = 'reject';
        verifyForm.submit();
      }
    });
  }
});
