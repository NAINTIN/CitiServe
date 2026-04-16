const assetPath = (fileName) => `/CitiServe/public/assets/login-register/${fileName}`;

/* BACKGROUND SLIDES */
const slides = [
  assetPath("Slide 1.png"),
  assetPath("Slide 2.png"),
  assetPath("Slide 3.png"),
  assetPath("Slide 4.png")
];

let i = 0;
let slideInterval;
const left = document.getElementById("leftSlide");

/* AUTO SLIDE */
slideInterval = setInterval(() => {
  if (i < slides.length - 1) {
    i++;
  } else {
    i = 0;
  }
  changeSlide();
}, 5000);

const prevBtn = document.getElementById("prevSlide");
const nextBtn = document.getElementById("nextSlide");

/* CHANGE SLIDE */
function changeSlide() {
  left.style.backgroundImage = `url('${slides[i]}')`;
  left.style.backgroundSize = "cover";
  left.style.backgroundPosition = "center";
  updateArrows();
}

/* UPDATE ARROW STATES */
function updateArrows() {
  if (i === 0) {
    prevBtn.classList.add("disabled");
  } else {
    prevBtn.classList.remove("disabled");
  }
  if (i === slides.length - 1) {
    nextBtn.classList.add("disabled");
  } else {
    nextBtn.classList.remove("disabled");
  }
}

/* CLICK EVENTS */
prevBtn.onclick = () => {
  if (i > 0) {
    i--;
    changeSlide();
  }
};

nextBtn.onclick = () => {
  if (i < slides.length - 1) {
    i++;
    changeSlide();
  }
};

/* INIT */
changeSlide();


/* ══════════════════════════
   PASSWORD EYE TOGGLE
══════════════════════════ */
const passInput = document.getElementById('passInput');
const eyeBtn    = document.getElementById('eyeBtn');
const eyeIcon   = document.getElementById('eyeIcon');

eyeBtn.addEventListener('click', () => {
  if (passInput.type === 'password') {
    passInput.type = 'text';
    eyeIcon.src = assetPath('eyeclosed.png');
    eyeBtn.style.opacity = '0.7';
  } else {
    passInput.type = 'password';
    eyeIcon.src = assetPath('eye.png');
    eyeBtn.style.opacity = '0.35';
  }
});


/* ══════════════════════════
   FILE PICKER
══════════════════════════ */
const fileInput  = document.getElementById('fileInput');
const chooseBtn  = document.getElementById('chooseBtn');
const fileNameEl = document.getElementById('fileName');

chooseBtn.addEventListener('click', () => fileInput.click());

fileInput.addEventListener('change', function () {
  if (this.files[0]) {
    fileNameEl.textContent = this.files[0].name;
    fileNameEl.style.color = '#555';
  } else {
    fileNameEl.textContent = 'No File Chosen';
    fileNameEl.style.color = '';
  }
});


/* ========================= */
/* 🔥 REQUIRED FIELD CHECK   */
/* ========================= */

const allRequiredInputs = document.querySelectorAll('.field input[type="text"], .field input[type="email"], .field input[type="password"]');

// Add error img after each required input-wrap (skip last name)
allRequiredInputs.forEach(input => {
  if (input.hasAttribute('data-no-error-img')) return;

  const wrap = input.closest('.input-wrap');
  const errorImg = document.createElement('img');
  errorImg.src = assetPath('reg-field-required.png');
  errorImg.classList.add('required-error');
  errorImg.style.display = 'none';
  errorImg.style.position = 'absolute';
  errorImg.style.left = '-150px';
  errorImg.style.top = '50%';
  errorImg.style.transform = 'translateY(-50%)';
  errorImg.style.width = '140px';
  errorImg.style.height = 'auto';
  errorImg.style.zIndex = '999';
  wrap.appendChild(errorImg);
});

// File error img
const fileErrorImg = document.createElement('img');
fileErrorImg.src = assetPath('reg-field-required.png');
fileErrorImg.classList.add('required-error');
fileErrorImg.style.display = 'none';
fileErrorImg.style.position = 'absolute';
fileErrorImg.style.left = '-150px';
fileErrorImg.style.top = '50%';
fileErrorImg.style.transform = 'translateY(-50%)';
fileErrorImg.style.width = '140px';
fileErrorImg.style.height = 'auto';
fileErrorImg.style.zIndex = '999';

const fileRow = document.querySelector('.file-row');
fileRow.style.position = 'relative';
fileRow.appendChild(fileErrorImg);


/* ========================= */
/* 🔥 VALIDATE FORM          */
/* ========================= */

function validateForm() {
  let valid = true;

  allRequiredInputs.forEach(input => {
    const wrap = input.closest('.input-wrap');
    const errorImg = wrap.querySelector('.required-error');

    if (!input.value.trim()) {
      valid = false;
      if (errorImg) errorImg.style.display = 'block';
    } else {
      if (errorImg) errorImg.style.display = 'none';
    }
  });

  // Check file
  if (!fileInput.files[0]) {
    fileErrorImg.style.display = 'inline-block';
    valid = false;
  } else {
    fileErrorImg.style.display = 'none';
  }

  // Check checkbox
  const termsCheck = document.getElementById('termsCheck');
  if (!termsCheck.checked) {
    valid = false;
    termsCheck.style.outline = '2px solid #F03871';
  } else {
    termsCheck.style.outline = 'none';
  }

  return valid;
}


/* ========================= */
/* 🔥 CONFIRM MODAL          */
/* ========================= */

const confirmModal = document.getElementById("confirmModal");
const confirmCancel = document.getElementById("confirmCancel");
const confirmCreate = document.getElementById("confirmCreate");
const createBtn = document.querySelector(".create-btn");

createBtn.onclick = function() {
  if (!validateForm()) return;
  confirmModal.style.display = "flex";
};

confirmCancel.onclick = function() {
  confirmModal.style.display = "none";
};

confirmCreate.onclick = function() {
  confirmModal.style.display = "none";
  // ✅ redirect after creating
  // window.location.href = "login.html";
};


/* ========================= */
/* 🔥 REG TERMS MODAL        */
/* ========================= */

const regTermsModal = document.getElementById("regTermsModal");
const regTermsLink = document.getElementById("regTermsLink");
const closeRegTerms = document.getElementById("closeRegTerms");


let regPreviousBg = "";

regTermsLink.onclick = function(e) {
  e.preventDefault();
  regTermsModal.style.display = "flex";

  regPreviousBg = left.style.backgroundImage;
  left.style.backgroundImage = `url('${assetPath('login-bg.png')}')`;

  clearInterval(slideInterval);

  document.querySelectorAll(".arrow").forEach(arrow => {
    arrow.style.display = "none";
  });
};

closeRegTerms.onclick = function() {
  regTermsModal.style.display = "none";

  left.style.backgroundImage = regPreviousBg;

  document.querySelectorAll(".arrow").forEach(arrow => {
    arrow.style.display = "flex";
  });

  clearInterval(slideInterval);
  slideInterval = setInterval(() => {
    if (i < slides.length - 1) {
      i++;
    } else {
      i = 0;
    }
    changeSlide();
  }, 5000);
};


/* ========================= */
/* 🔥 REG PRIVACY MODAL      */
/* ========================= */

const regPrivacyModal = document.getElementById("regPrivacyModal");
const regPrivacyLink = document.getElementById("regPrivacyLink");
const closeRegPrivacy = document.getElementById("closeRegPrivacy");

regPrivacyLink.onclick = function(e) {
  e.preventDefault();
  regPrivacyModal.style.display = "flex";

  if (!regTermsModal.style.display || regTermsModal.style.display === "none") {
    regPreviousBg = left.style.backgroundImage;
  }
  left.style.backgroundImage = `url('${assetPath('login-bg.png')}')`;

  clearInterval(slideInterval);

  document.querySelectorAll(".arrow").forEach(arrow => {
    arrow.style.display = "none";
  });
};

closeRegPrivacy.onclick = function() {
  regPrivacyModal.style.display = "none";

  left.style.backgroundImage = regPreviousBg;

  document.querySelectorAll(".arrow").forEach(arrow => {
    arrow.style.display = "flex";
  });

  clearInterval(slideInterval);
  slideInterval = setInterval(() => {
    if (i < slides.length - 1) {
      i++;
    } else {
      i = 0;
    }
    changeSlide();
  }, 5000);
};
