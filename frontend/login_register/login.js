const scriptElement = document.currentScript || Array.from(document.scripts).find((script) => /(?:^|\/)login\.js(?:\?|#|$)/.test(script.src || ''));
if (!scriptElement || !scriptElement.src) {
  throw new Error('Unable to resolve login.js script URL for asset loading.');
}
const scriptUrl = scriptElement.src;
const assetsBaseUrl = new URL('images/', scriptUrl);
const assetPath = (fileName) => new URL(fileName, assetsBaseUrl).href;

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


/* TOGGLE */
const resBtn = document.getElementById("resBtn");
const bsBtn = document.getElementById("bsBtn");
const resIcon = document.getElementById("resIcon");
const bsIcon = document.getElementById("bsIcon");

function setResident() {
  resBtn.classList.add("active");
  bsBtn.classList.remove("active");
  resIcon.src = assetPath("resident-icon.png");
  bsIcon.src = assetPath("barangaystaff-icon.png");
}

function setBarangayStaff() {
  bsBtn.classList.add("active");
  resBtn.classList.remove("active");
  bsIcon.src = assetPath("barangaystaff-toggle.png");
  resIcon.src = assetPath("resident-toggle.png");
}

resBtn.onmouseenter = setResident;
bsBtn.onmouseenter = setBarangayStaff;


/* PASSWORD VISIBILITY */
const eye = document.getElementById("eye");
const pass = document.getElementById("pass");

eye.onclick = () => {
  if (pass.type === "password") {
    pass.type = "text";
    eye.src = assetPath("eyeclosed.png");
  } else {
    pass.type = "password";
    eye.src = assetPath("eye.png");
  }
};

/* ========================= */
/* 🔥 WRONG PASSWORD ERROR  */
/* ========================= */

const loginBtn = document.querySelector(".login-btn");
const errorMsg = document.getElementById("errorMsg");
const emailInput = document.querySelector('input[type="email"]');

// 🔥 REPLACE THESE WITH YOUR ACTUAL CREDENTIALS
const correctEmail = "admin@citiserve.com";
const correctPassword = "admin123";

loginBtn.onclick = function() {
  const emailVal = emailInput.value.trim();
  const passVal = pass.value.trim();

  if (emailVal === correctEmail && passVal === correctPassword) {
    errorMsg.style.display = "none";
    // redirect here if correct
    // window.location.href = "dashboard.html";
  } else {
    errorMsg.style.display = "flex";
  }
};

/* ========================= */
/* 🔥 PRIVACY MODAL CONTROL */
/* ========================= */

const privacyModal = document.getElementById("privacyModal");
const closeBtn = document.getElementById("closePrivacy");
const privacyLink = document.getElementById("privacyLink");

const privacyBg = assetPath("login-bg.png");

let previousBg = "";
let isModalOpen = false;

/* OPEN PRIVACY MODAL */
privacyLink.onclick = function(e) {
  e.preventDefault();

  if (isModalOpen) return;
  isModalOpen = true;

  privacyModal.style.display = "flex";

  clearInterval(slideInterval);

  previousBg = left.style.backgroundImage;
  left.style.backgroundImage = `url('${privacyBg}')`;

  document.querySelectorAll(".login-arrow").forEach(arrow => {
    arrow.style.display = "none";
  });

  document.body.classList.add("modal-open");
};

/* CLOSE PRIVACY MODAL */
closeBtn.onclick = function() {
  isModalOpen = false;

  privacyModal.style.display = "none";

  left.style.backgroundImage = previousBg;

  document.querySelectorAll(".login-arrow").forEach(arrow => {
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

  document.body.classList.remove("modal-open");
};


/* ========================= */
/* 🔥 TERMS MODAL CONTROL   */
/* ========================= */

const termsModal = document.getElementById("termsModal");
const closeTermsBtn = document.getElementById("closeTerms");
const termsLink = document.getElementById("termsLink");

const termsBg = assetPath("login-bg.png");

let isTermsOpen = false;

/* OPEN TERMS MODAL */
termsLink.onclick = function(e) {
  e.preventDefault();

  if (isTermsOpen) return;
  isTermsOpen = true;

  termsModal.style.display = "flex";

  clearInterval(slideInterval);

  previousBg = left.style.backgroundImage;
  left.style.backgroundImage = `url('${termsBg}')`;

  document.querySelectorAll(".login-arrow").forEach(arrow => {
    arrow.style.display = "none";
  });

  document.body.classList.add("modal-open");
};

/* CLOSE TERMS MODAL */
closeTermsBtn.onclick = function() {
  isTermsOpen = false;

  termsModal.style.display = "none";

  left.style.backgroundImage = previousBg;

  document.querySelectorAll(".login-arrow").forEach(arrow => {
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

  document.body.classList.remove("modal-open");
};
