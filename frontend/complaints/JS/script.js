document.querySelectorAll('.nav-links a').forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();

    const targetId = this.getAttribute('href');
    const target = document.querySelector(targetId);

    if (!target) return;

    const navbar = document.getElementById('navbar');
    const navbarHeight = navbar.offsetHeight;

    let extraOffset = 20;

    if (targetId === "#home") {
      extraOffset = 0;
    }
    else if (targetId === "#services") {
      extraOffset = -10;
    }
    else if (targetId === "#features") {
      extraOffset = -170;
    }
    else if (targetId === "#about") {
      extraOffset = 10;
    }
    else if (targetId === "#contacts") {
      extraOffset = -70;
    }

    const targetPosition =
      target.getBoundingClientRect().top +
      window.pageYOffset -
      navbarHeight -
      extraOffset;

    window.scrollTo({
      top: targetPosition,
      behavior: 'smooth'
    });
  });
});


// ================= FLOATING NAV =================
const navbar = document.getElementById('navbar');

window.addEventListener('scroll', () => {
  if(window.scrollY > 50){
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
});

// ================= CHANGE LOGO =================
const logo = document.getElementById("navLogo");

window.addEventListener('scroll', () => {
  if(window.scrollY > 50){
    navbar.classList.add('scrolled');
    
    logo.src = "images/logo_pink.png";
  } else {
    navbar.classList.remove('scrolled');
    logo.src = "images/logo_half.png";
  }
});


// ================= ACTIVE LINK =================
const sections = document.querySelectorAll("section");
const navLinks = document.querySelectorAll(".nav-links a");

window.addEventListener("scroll", () => {
  let current = "";

  sections.forEach(section => {
    const sectionTop = section.offsetTop - 120;

    if(window.scrollY >= sectionTop){
      current = section.getAttribute("id");
    }
  });

  navLinks.forEach(a => {
    a.classList.remove("active");
    if(a.getAttribute("href") === "#" + current){
      a.classList.add("active");
    }
  });
});

// ================= LOGIN BUTTON =================
document.addEventListener("DOMContentLoaded", function () {

  var btn = document.querySelector('.login-btn');

  btn.addEventListener('mousemove', function(e) {
    var rect = btn.getBoundingClientRect();

    var x = e.clientX - rect.left;
    var y = e.clientY - rect.top;

    var xPercent = (x / rect.width) * 100;
    var yPercent = (y / rect.height) * 100;

    btn.style.background = "radial-gradient(circle at " 
      + xPercent + "% " + yPercent + "%, "
      + "#F03871 0%, "
      + "#F03871 2%, "
      + "#FECA18 80%)";
  });

  btn.addEventListener('mouseleave', function() {
    isHovering = false;

    setTimeout(() => {
      if (!isHovering) {
        btn.style.background = "linear-gradient(90deg, #F03871, #FECA18)";
      }
    }, 100);
  });
});

// ================= START BUTTON =================
document.addEventListener("DOMContentLoaded", function () {
  var btn = document.querySelector('.start-btn');

  btn.addEventListener('mousemove', function(e) {
    var rect = btn.getBoundingClientRect();

    var x = e.clientX - rect.left;
    var y = e.clientY - rect.top;

    var xPercent = (x / rect.width) * 100;
    var yPercent = (y / rect.height) * 100;

    btn.style.background = "radial-gradient(circle at "
      + xPercent + "% " + yPercent + "%, "
      + "#d41e58 0%, "
      + "#d41e58 2%, "
      + "#F03871 80%)";
  });

  btn.addEventListener('mouseleave', function() {
    btn.style.background = "linear-gradient(90deg, #F03871, #F03871)";
  });
});


// ================= FAQS =================
function toggleFAQ(index) {
    const items = document.querySelectorAll('.faq-item');

    items.forEach((item, i) => {
        const answer = item.querySelector('.faq-answer');
        const btn    = item.querySelector('.faq-btn');
        const icon   = item.querySelector('.btn-icon');

        if (i === index) {
            const isOpen = item.classList.contains('active');

            if (isOpen) {
                item.classList.remove('active');
                answer.style.maxHeight = '0';
                answer.style.opacity   = '0';
                btn.classList.remove('active');
                icon.innerHTML = '&plus;';
            } else {
                item.classList.add('active');
                answer.style.maxHeight = answer.scrollHeight + 'px';
                answer.style.opacity   = '1';
                btn.classList.add('active');
                icon.innerHTML = '&minus;';
            }
        } else {
            item.classList.remove('active');
            answer.style.maxHeight = '0';
            answer.style.opacity   = '0';
            btn.classList.remove('active');
            icon.innerHTML = '&plus;';
        }
    });
}

// ================= CAROUSEL =================
const carousel = document.getElementById("heroCarousel");

let isDown = false;
let startX;
let scrollLeft;

carousel.addEventListener("mousedown", (e) => {
  isDown = true;
  startX = e.pageX - carousel.offsetLeft;
  scrollLeft = carousel.scrollLeft;
});

carousel.addEventListener("mouseleave", () => isDown = false);
carousel.addEventListener("mouseup", () => isDown = false);

carousel.addEventListener("mousemove", (e) => {
  if (!isDown) return;
  e.preventDefault();
  const x = e.pageX - carousel.offsetLeft;
  const walk = (x - startX) * 2;
  carousel.scrollLeft = scrollLeft - walk;
});

carousel.addEventListener("touchstart", (e) => {
  startX = e.touches[0].pageX - carousel.offsetLeft;
  scrollLeft = carousel.scrollLeft;
});

carousel.addEventListener("touchmove", (e) => {
  const x = e.touches[0].pageX - carousel.offsetLeft;
  const walk = (x - startX) * 2;
  carousel.scrollLeft = scrollLeft - walk;
});

const leftBtn = document.getElementById("leftBtn");
const rightBtn = document.getElementById("rightBtn");

rightBtn.onclick = () => {
  carousel.scrollLeft += 300;
  setTimeout(updateArrows, 100);
};

leftBtn.onclick = () => {
  carousel.scrollLeft -= 300;
  setTimeout(updateArrows, 100);
};

function updateArrows() {
  const maxScroll = carousel.scrollWidth - carousel.clientWidth;

  leftBtn.classList.toggle("disabled", carousel.scrollLeft <= 0);
  rightBtn.classList.toggle("disabled", carousel.scrollLeft >= maxScroll - 5);
}

carousel.addEventListener("scroll", updateArrows);
updateArrows();

const marquee = document.getElementById("marqueeText");
marquee.innerHTML += marquee.innerHTML;
