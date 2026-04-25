/* BACKGROUND SLIDES */
const slides = [
  "images/Slide 1.png",
  "images/Slide 2.png",
  "images/Slide 3.png",
  "images/Slide 4.png"
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
  resIcon.src = "images/resident-icon.png";
  bsIcon.src = "images/barangaystaff-icon.png";
}

function setBarangayStaff() {
  bsBtn.classList.add("active");
  resBtn.classList.remove("active");
  bsIcon.src = "images/barangaystaff-toggle.png";
  resIcon.src = "images/resident-toggle.png";
}

resBtn.onmouseenter = setResident;
bsBtn.onmouseenter = setBarangayStaff;


/* PASSWORD VISIBILITY */
const eye = document.getElementById("eye");
const pass = document.getElementById("pass");

eye.onclick = () => {
  if (pass.type === "password") {
    pass.type = "text";
    eye.src = "images/eyeclosed.png";
  } else {
    pass.type = "password";
    eye.src = "images/eye.png";
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

const privacyBg = "images/login-bg.png";

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

const termsBg = "images/login-bg.png";

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


/* ========================= */
/* 🔥 FORGOT PASSWORD MODAL  */
/* ========================= */

const forgotModal = document.getElementById("forgotModal");
const closeForgotBtn = document.getElementById("closeForgot");
const forgotLink = document.querySelector(".forgot");

forgotLink.onclick = function(e) {
  e.preventDefault();
  forgotModal.style.display = "flex";
  document.body.classList.add("modal-open");
};

closeForgotBtn.onclick = function() {
  forgotModal.style.display = "none";
  document.body.classList.remove("modal-open");
};

// Close on backdrop click
forgotModal.onclick = function(e) {
  if (e.target === forgotModal) {
    forgotModal.style.display = "none";
    document.body.classList.remove("modal-open");
  }
};

/* ========================= */
/* 🔥 VERIFICATION MODAL     */
/* ========================= */

const verifyModal = document.getElementById("verifyModal");
const closeVerifyBtn = document.getElementById("closeVerify");
const verifyBackBtn = document.getElementById("verifyBack");
const continueForgotBtn = document.querySelector(".forgot-continue-btn");
const otpBoxes = document.querySelectorAll(".otp-box");
const otpTimerEl = document.getElementById("otpTimer");
const resendCode = document.getElementById("resendCode");

let timerInterval;

function startTimer(seconds) {
  clearInterval(timerInterval);
  let remaining = seconds;
  otpTimerEl.style.color = "#F03871";
  timerInterval = setInterval(() => {
    const m = String(Math.floor(remaining / 60)).padStart(2, "0");
    const s = String(remaining % 60).padStart(2, "0");
    otpTimerEl.textContent = `${m}:${s}`;
    if (remaining <= 0) {
      clearInterval(timerInterval);
      otpTimerEl.textContent = "00:00";
    }
    remaining--;
  }, 1000);
}

function openVerifyModal() {
  verifyModal.style.display = "flex";
  forgotModal.style.display = "none";
  otpBoxes.forEach(box => box.value = "");
  otpBoxes[0].focus();
  startTimer(30);
}

function closeVerifyModal() {
  verifyModal.style.display = "none";
  clearInterval(timerInterval);
  document.body.classList.remove("modal-open");
}

/* Continue button opens verification */
continueForgotBtn.onclick = function() {
  openVerifyModal();
};

/* Close X */
closeVerifyBtn.onclick = closeVerifyModal;

/* Back button goes back to forgot modal */
verifyBackBtn.onclick = function() {
  verifyModal.style.display = "none";
  forgotModal.style.display = "flex";
};

/* Resend */
resendCode.onclick = function(e) {
  e.preventDefault();
  otpBoxes.forEach(box => box.value = "");
  otpBoxes[0].focus();
  startTimer(30);
};

/* Auto-jump between OTP boxes */
otpBoxes.forEach((box, index) => {
  // Always focus first box when clicking any box
  box.addEventListener("click", () => {
    const firstEmpty = [...otpBoxes].findIndex(b => b.value === "");
    const focusIndex = firstEmpty === -1 ? otpBoxes.length - 1 : firstEmpty;
    otpBoxes[focusIndex].focus();

    
  });

  // Numbers only, auto-jump forward
  box.addEventListener("keydown", (e) => {
    box.classList.remove("wrong");
    // Allow only numbers
    if (!/^[0-9]$/.test(e.key) && e.key !== "Backspace") {
      e.preventDefault();
      return;
    }

    if (e.key === "Backspace") {
      e.preventDefault();
      // Delete current box first, if empty go to previous
      if (box.value !== "") {
        box.value = "";
        box.classList.remove("filled");
      } else if (index > 0) {
        otpBoxes[index - 1].value = "";
        otpBoxes[index - 1].classList.remove("filled");
        otpBoxes[index - 1].focus();
      }
    } else {
      e.preventDefault();
      box.value = e.key;
      box.classList.add("filled");
      if (index < otpBoxes.length - 1) {
        otpBoxes[index + 1].focus();
      }
    }
  });
});

// Force focus to first empty box on modal open
function openVerifyModal() {
  verifyModal.style.display = "flex";
  forgotModal.style.display = "none";
  otpBoxes.forEach(box => {
    box.value = "";
    box.classList.remove("filled");
  });
  otpBoxes[0].focus();
  startTimer(30);
}

/* ========================= */
/* 🔥 NEW PASSWORD MODAL     */
/* ========================= */

const newPassModal = document.getElementById("newPassModal");
const closeNewPassBtn = document.getElementById("closeNewPass");
const newPassBackBtn = document.getElementById("newPassBack");
const verifyBtn = document.querySelector(".verify-btn");

const newPassInput = document.getElementById("newPass");
const confirmPassInput = document.getElementById("confirmPass");
const newPassEye = document.getElementById("newPassEye");
const confirmPassEye = document.getElementById("confirmPassEye");

/* Verify button opens New Password modal 
verifyBtn.onclick = function() {
  newPassModal.style.display = "flex";
  verifyModal.style.display = "none";
  clearInterval(timerInterval);
}*/

const correctOTP = "1234"; // placeholder

verifyBtn.onclick = function() {
  const entered = [...otpBoxes].map(b => b.value).join("");

  if (entered === correctOTP) {
    otpBoxes.forEach(box => box.classList.remove("wrong"));
    newPassModal.style.display = "flex";
    verifyModal.style.display = "none";
    clearInterval(timerInterval);
  } else {
    otpBoxes.forEach(box => {
      box.classList.add("wrong");
      box.classList.remove("filled");
      box.value = "";
    });
    otpBoxes[0].focus();
  }
};

/* Close X */
closeNewPassBtn.onclick = function() {
  newPassModal.style.display = "none";
  document.body.classList.remove("modal-open");
};

/* Back goes to verify modal */
newPassBackBtn.onclick = function() {
  newPassModal.style.display = "none";
  verifyModal.style.display = "flex";
};

/* Eye toggle - New Password */
newPassEye.onclick = function() {
  if (newPassInput.type === "password") {
    newPassInput.type = "text";
    newPassEye.src = "images/eyeclosed.png";
  } else {
    newPassInput.type = "password";
    newPassEye.src = "images/eye.png";
  }
};

/* Eye toggle - Confirm Password */
confirmPassEye.onclick = function() {
  if (confirmPassInput.type === "password") {
    confirmPassInput.type = "text";
    confirmPassEye.src = "images/eyeclosed.png";
  } else {
    confirmPassInput.type = "password";
    confirmPassEye.src = "images/eye.png";
  }
};

/* ========================= */
/* 🔥 PASSWORD CHANGED MODAL */
/* ========================= */

const passChangedModal = document.getElementById("passChangedModal");
const passChangedContinue = document.getElementById("passChangedContinue");
const updatePassBtn = document.querySelector(".newpass-btn");

/* Update Password button opens success modal */
updatePassBtn.onclick = function() {
  newPassModal.style.display = "none";
  passChangedModal.style.display = "flex";
};

/* Continue closes everything */
passChangedContinue.onclick = function() {
  passChangedModal.style.display = "none";
  document.body.classList.remove("modal-open");
};