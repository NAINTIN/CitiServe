<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CitiServe</title>
    
    <link rel="stylesheet" href="css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

    <!-- ================= NAV BAR ================= -->
    <nav class="navbar" id="navbar">
        <img src="images/logo_half.png" class="landing_logo1" id="navLogo">

    <div class="nav-right">
        <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#features">Features</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contacts">Contacts</a></li>
        </ul>

        <a href="#" class="login-btn">Login 🠚</a>
    </div>
    </nav>


    <!-- ================= HOME  ================= -->
    <section id="home" class="hero">
    <img src="images/landing_folder.png" class="landing_folder">

    <div class="hero-content">
        <img src="images/landing_location.png" class="landing_location">

        <p class="home-title"> Welcome to <span><b> CitiServe </b></span> </p>
        <p class="home-subtitle"><i> An Integrated Digital Citizen Service Platform</i></p>
        <p class="home-subtext"> Stay connected with Barangay Kalayaan 24/7, anywhere. </p>

        <a href="#" class="start-btn">
        <span class="btn-text">GET STARTED</span>
        <span class="arrow">🡪</span>
        </a>
    </div>
    </section>


    <!-- ================= SERVICES ================= -->
    <section id="services" class="services-section">
        <img src="images/kalayaan.png" class="bg-left" alt="">
        <img src="images/angono 2.png" class="bg-right" alt="">

        <div class="services-top-content">
            <img src="images/our services.png" class="services-img" alt="Our Services">

            <h1>
                Everything You Need,<br>
                <span>All In One Place</span>
            </h1>

            <p>
                Access essential barangay services through a simple and organized
                digital platform designed to make requests, report concerns, and stay
                updated every step of the way.
            </p>
        </div>

        <div class="service-cards">
            <div class="service-card yellow-card">
                <img src="images/document_icon.png" class="service-card-icon">
                <h3>Document Request</h3>
                <p>
                    Request official barangay documents online with a guided process
                    from submission to release, ensuring a clear and organized request experience.
                </p>
                <ul class="yellow-list">
                    <li>Easy online submission</li>
                    <li>Clear requirements and instructions</li>
                    <li>Status tracking and updates</li>
                </ul>
            </div>

            <div class="service-card pink-card">
                <img src="images/compliant_icon.png" class="service-card-icon">
                <h3>Complaint Management</h3>
                <p>
                    Report community concerns through a structured complaint system
                    that helps the barangay review, track, and resolve issues efficiently.
                </p>
                <ul class="pink-list">
                    <li>Simple complaint submission</li>
                    <li>Evidence and location support</li>
                    <li>Complaint status tracking</li>
                </ul>
            </div>
        </div>
    </section>
    <div class="section-gradient"></div>


    <!-- ================= FEATURES ================= -->
    <section id="features" class="choose_section">
        <div class="choose_wrapper">
            <div class="choose_top-image">
                <img src="images/choose.png" alt="">
            </div>

            <h1>
                Built For Modern <br>
                <span>Barangay Living</span>
            </h1>

            <p class="choose_subtext">
                Experience the next generation of barangay services with our powerful features designed for your convenience.
            </p>

            <!-- CAROUSEL -->
            <div class="carousel-container">
                <div class="carousel" id="heroCarousel">
                    <div class="card">
                        <div class="icon"><img src="images/availability.png"></div>
                        <div class="title">24/7 Availability</div>
                        <div class="desc">
                            Access barangay services<br>anytime and anywhere<br>without needing to visit the<br>office or wait in long lines.
                        </div>
                    </div>
                    <div class="card">
                        <div class="icon"><img src="images/security.png"></div>
                        <div class="title">Secure & Private</div>
                        <div class="desc">
                            Resident information and<br>submitted data are protected<br>through secure system access<br>and proper data handling.
                        </div>
                    </div>
                    <div class="card">
                        <div class="icon"><img src="images/fast.png"></div>
                        <div class="title">Lightning Fast</div>
                        <div class="desc">
                            Optimized workflows help<br>reduce manual steps, making<br>document requests and<br>complaint submissions quicker.
                        </div>
                    </div>
                    <div class="card">
                        <div class="icon"><img src="images/realtime.png"></div>
                        <div class="title">Real-Time Tracking</div>
                        <div class="desc">
                            Monitor the progress of your<br>document requests and complaints<br>with clear status updates from<br>submission to completion.
                        </div>
                    </div>
                    <div class="card">
                        <div class="icon"><img src="images/organized.png"></div>
                        <div class="title">Organized System</div>
                        <div class="desc">
                            A structured platform that keeps<br>service requests, complaints, and<br>records properly managed and<br>easy to access.
                        </div>
                    </div>
                    <div class="card">
                        <div class="icon"><img src="images/mobile_responsive.png"></div>
                        <div class="title">Mobile Responsive</div>
                        <div class="desc">
                            Fully optimized for desktop, tablet,<br>and mobile devices so residents<br>can access services on any<br>device.
                        </div>
                    </div>
                </div>

                <div class="choose_arrow-container">
                    <div class="choose_arrow" id="leftBtn">🡨</div>
                    <div class="choose_arrow" id="rightBtn">🡪</div>
                </div>
            </div>
        </div>

        <!-- MARQUEE -->
        <div class="marquee">
            <div class="marquee-content" id="marqueeText">
                <span><span class="pink">Serbisyo</span> sa Diyos at sa Tao</span>
                <span><span class="pink">Serbisyo</span> sa Diyos at sa Tao</span>
                <span><span class="pink">Serbisyo</span> sa Diyos at sa Tao</span>
                <span><span class="pink">Serbisyo</span> sa Diyos at sa Tao</span>
                <span><span class="pink">Serbisyo</span> sa Diyos at sa Tao</span>
            </div>
        </div>
    </div>
    </section>


    <!-- ================= ABOUT ================= -->
    <section id="about" class="about_container">

        <div class="about_left">
            <img src="images/captain.png" alt="Captain">
        </div>
        <div class="about_right">

            <div class="about_about">
                <img src="images/about.png" alt="About">
            </div>

            <h1>
                Committed To <br>
                <span>Excellence & Service</span>
            </h1>

            <p>
                CitiServe is Barangay Kalayaan's digital transformation initiative,
                designed to bring government services closer to our residents.
                We understand that your time is valuable, and accessing barangay
                services should be convenient and hassle-free.
            </p>

            <p>
                Through this platform, residents of Barangay Kalayaan can now
                request important documents and report community concerns
                without the need to visit the barangay hall during office hours.
                Our system ensures transparency, efficiency, and accountability
                in every transaction.
            </p>

            <p>
                Together, we're building a more connected and responsive community.
            </p>

            <a href="https://www.facebook.com/bagongbarangaykalayaan" target="_blank" class="about_btn">
                <span class="arrow-btn-text">Learn More About Us</span>
                <span class="about-arrow">🡪</span>
            </a>
        </div>
    </section>
    
    
    <!-- ================= HOW IT WORKS ================= -->
    <section class="works-section">

        <div class="works-bg">
            <img src="images/landing_works.png" alt="Background Design">
        </div>

        <div class="works-top-img">
            <img src="images/howcitiserve.png" alt="CitiServe Logo">
        </div>

        <div class="works-header">
            <h1>We Make Things</h1>
            <h2>Easy For You</h2>
            <p>
                Enjoy a faster, simpler way to access barangay services with
                features built for your convenience.
            </p>
        </div>

        <div class="works-cards">

            <div class="works-card">
                <div class="works-card-number">1</div>
                <img src="images/icon1_work.png" class="works-card-icon">
                <h3>Submit Requests</h3>
                <p>
                    Fill out the online form, upload required documents,
                    and submit your request or complaint through the platform.
                </p>
            </div>

            <div class="works-card">
                <div class="works-card-number">2</div>
                <img src="images/icon2_work.png" class="works-card-icon">
                <h3>Barangay Review</h3>
                <p>
                    Our barangay staff will review your submission,
                    verify requirements, and process your request
                    within 3-5 business days.
                </p>
            </div>

            <div class="works-card">
                <div class="works-card-number">3</div>
                <img src="images/icon3_work.png" class="works-card-icon">
                <h3>Claim or Resolve</h3>
                <p>
                    Receive a notification when your document is ready
                    for pickup or when your complaint has been addressed and resolved.
                </p>
            </div>
        </div>
    </section>

    
    <!-- ================= FAQS ================= -->

    <?php
    $faqs = [
        [
            "question" => "What is CitiServe?",
            "answer" => "CitiServe is an online platform designed for residents of Barangay Kalayaan to conveniently request documents, submit complaints, and track the status of their requests without needing to visit the barangay office in person."
        ],
        [
            "question" => "Who can use the system?",
            "answer" => "Only verified residents of Barangay Kalayaan can use CitiServe. Users must register and submit proof of residency, which will be manually verified by barangay staff before gaining full access."
        ],
        [
            "question" => "How do I request a barangay document?",
            "answer" => "You can request a document by logging into your account, selecting the desired service, filling out the request form, uploading the required documents, and submitting proof of payment if applicable."
        ],
        [
            "question" => "What payment methods are accepted?",
            "answer" => "Payments can be made through digital platforms such as GCash or Maya. After payment, users must upload a screenshot or photo of the payment as proof for manual verification."
        ],
        [
            "question" => "How long does it take to process requests?",
            "answer" => "Processing time depends on the type of document requested. Some documents are released on the same day, while others may take 1–3 days as indicated in the system."
        ],
        [
            "question" => "How will I know the status of my request?",
            "answer" => "You can track your request through the request history page. The system will also send notifications when your request status changes (e.g., Pending, Claimable, Released)."
        ],
        [
            "question" => "Can I submit complaints through CitiServe?",
            "answer" => "Yes, residents can submit complaints by selecting a category, providing details, uploading evidence if available, and marking the location on the map."
        ],
        [
            "question" => "Can I submit complaints anonymously?",
            "answer" => "Yes, the system allows anonymous complaint submissions; however, a disclaimer will inform users that anonymous reports may have limited follow-up or verification."
        ],
        [
            "question" => "What should I do if my request is rejected?",
            "answer" => "If your request is rejected, you can check the reason provided in the system, correct any issues (such as missing requirements), and submit a new request."
        ],
        [
            "question" => "Is my personal information secure?",
            "answer" => "Yes, the system implements basic security measures and access controls to protect user data. Only authorized barangay staff can view and manage your submitted information."
        ],
    ];
    ?>

    <div class="faq-wrapper">
        <div class="faq-left">
            <div class="faq-title">
                <span class="letter-f">F</span>requently<br>
                <span class="letter-a">A</span>sked<br>
                <span class="letter-q">Q</span>uestions
            </div>
            <div class="faq-decoration">
                <img src="images/faq_icon.png" alt="FAQ Decoration">
            </div>
        </div>

        <div class="faq-right">
            <div class="faq-list">
                <?php foreach ($faqs as $index => $faq): ?>
                <div class="faq-item" data-index="<?= $index ?>">
                    <div class="faq-question" onclick="toggleFAQ(<?= $index ?>)">
                        <span class="question-text"><?= htmlspecialchars($faq['question']) ?></span>
                        <button class="faq-btn" aria-label="Toggle answer">
                            <span class="btn-icon">&plus;</span>
                        </button>
                    </div>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars($faq['answer']) ?></p>
                    </div>
                    <div class="faq-divider"></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>


    <!-- ================= CONTACTS ================= -->
    <section id="contacts" class="contacts-section">

        <div class="contacts-badge">
            <img src="images/contacts.png" alt="Get In Touch" class="badge-icon">
        </div>

        <div class="contacts-header">
            <h1>We're Here To <span class="gradient-text">Help You</span></h1>
            <p>Need assistance with registration or have questions about our services?<br>Our team is ready to support you.</p>
        </div>

        <div class="contacts-cards">

            <div class="contact-card">
                <div class="contact-card-icon">
                    <img src="images/email.png" alt="Email">
                </div>
                <h3>Email Us</h3>
                <p class="card-sub">For general inquiries and support</p>
                <p class="card-link">barangaykalayaan@gmail.com</p>
            </div>

            <div class="contact-card">
                <div class="contact-card-icon">
                    <img src="images/office.png" alt="Office Hours">
                </div>
                <h3>Office Hours</h3>
                <p class="card-sub">Visit us during these hours</p>
                <p class="card-link">Monday - Friday<br>8:00 AM - 5:00 PM</p>
            </div>

            <div class="contact-card">
                <div class="contact-card-icon">
                    <img src="images/visit.png" alt="Visit Us">
                </div>
                <h3>Visit Us</h3>
                <p class="card-sub">Come to our barangay hall</p>
                <p class="card-link">G4HX+529, Aguinaldo St,<br>Kalayaan, Angono, 1930 Rizal</p>
            </div>

        </div>

        <div class="support-rect">
            <div class="support-icon">
                <img src="images/support.png" alt="Support">
            </div>
            <h2>Need Technical Support?</h2>
            <p>Having trouble with registration or accessing services? Our technical<br>support team is ready to assist you during office hours.</p>
            <div class="support-link">
                <img src="images/call.png" alt="Call" class="call-icon">
                <span>(02) 8584 7719</span>
            </div>
        </div>
    </section>


    <!-- ================= MAP ================= -->
    <div class="map-section">

        <div class="map-embed">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d600!2d121.147609!3d14.5279029!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c6ea73aa13ad%3A0x5f9e3643760bd36c!2sBarangay%20Kalayaan!5e0!3m2!1sen!2sph!4v1774681255226!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">;
            </iframe>
        </div>

        <div class="map-card">
            <div class="map-card-logo">
                <img src="images/logo_pink.png" alt="CitiServe Logo">
            </div>

            <a href="mailto:barangaykalayaan@gmail.com" class="map-email">
                barangaykalayaan@gmail.com
            </a>

            <p class="map-address">
                G4HX+529,<br>
                Aguinaldo St, Kalayaan,<br>
                Angono, 1930 Rizal
            </p>

            <a href="https://www.facebook.com/bagongbarangaykalayaan" target="_blank" class="map-fb">
                <img src="images/fb.png" alt="Facebook">
            </a>
        </div>

        <button class="scroll-top-btn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
            <img src="images/arrowup.png" alt="Scroll to Top">
        </button>
    </div>


    <!-- ================= FOOTER ================= -->
    <footer class="site-footer">
        <div class="footer-left">
            <p>© 2026 CitiServe - Barangay Kalayaan, Angono, 1930 Rizal</p>
        </div>

        <div class="footer-center">
            <img src="images/footer_logo.png" alt="Footer Logos" class="footer-side-logo">
            <img src="images/whitelogo_solo.png" alt="CitiServe White Logo" class="footer-main-logo">
        </div>

        <div class="footer-right">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms & conditions</a>
        </div>
    </footer>

<script src="JS/script.js"></script>
</body>
</html>