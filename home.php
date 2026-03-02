<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Registration System</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
    background: linear-gradient(135deg, #e0e7ff 0%, #d1d5ff 100%);
    min-height: 100vh;
    color: #1f2937;
}

nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 60px;
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1000;
}

.logo {
    font-size: 24px;
    font-weight: 700;
    color: #4f46e5;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 25px;
}

.nav-links a {
    text-decoration: none;
    font-weight: 500;
    color: #4b5563;
    transition: 0.3s;
}

.nav-links a:hover {
    color: #4f46e5;
}

.signup-btn {
    padding: 10px 24px;
    background: #4f46e5;
    color: white;
    border-radius: 8px;
    font-weight: 500;
    transition: 0.3s;
}

.signup-btn:hover {
    background: #4338ca;
    transform: translateY(-1px);
}

.hometab {
    text-align: center;
    padding-top: 140px;
    padding-bottom: 60px;
}

.hometab h1 {
    font-size: 48px;
    font-weight: 800;
    color: #111827;
    margin-bottom: 20px;
}

.hometab p {
    max-width: 600px;
    margin: 0 auto 30px;
    font-size: 18px;
    line-height: 1.6;
    color: #6b7280;
}

.cta-btn {
    padding: 14px 32px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    background: #4f46e5;
    color: white;
    font-weight: 600;
    transition: 0.3s;
}

.cta-btn:hover {
    background: #4338ca;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
}

.features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    padding: 0 60px 80px;
    max-width: 1200px;
    margin: 0 auto;
}

.cardtab {
    background: white;
    padding: 32px;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: 0.3s;
}

.cardtab:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.icon-box {
    width: 48px;
    height: 48px;
    background: #eef2ff;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.icon-box svg {
    width: 24px;
    height: 24px;
    color: #4f46e5;
}

.cardtab h3 {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 12px;
}

.cardtab p {
    color: #6b7280;
    line-height: 1.6;
    font-size: 15px;
}

.slideshow-section {
    padding: 60px;
    max-width: 1200px;
    margin: 0 auto;
}

.slideshow-container {
    background: white;
    border-radius: 24px;
    padding: 60px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    position: relative;
    min-height: 500px;
}

.slide {
    display: none;
    animation: fadeIn 0.5s ease-in-out;
}

.slide.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.profile-section {
    text-align: center;
    padding: 40px 0;
}

.profile-img-container {
    width: 160px;
    height: 160px;
    margin: 0 auto 30px;
    position: relative;
}

.profile-img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #e0e7ff;
    background: #f3f4f6;
}

.profile-img-ring {
    position: absolute;
    top: -8px;
    left: -8px;
    right: -8px;
    bottom: -8px;
    border: 3px solid #4f46e5;
    border-radius: 50%;
    border-top-color: transparent;
    animation: rotate 8s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.profile-name {
    font-size: 36px;
    font-weight: 800;
    color: #111827;
    margin-bottom: 8px;
}

.profile-class {
    font-size: 18px;
    color: #4f46e5;
    font-weight: 600;
    margin-bottom: 12px;
}

.profile-desc {
    font-size: 14px;
    color: #6b7280;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.section-title {
    font-size: 28px;
    color: #111827;
    text-align: center;
    margin-bottom: 40px;
    font-weight: 700;
}

.courses-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
}

.course-card {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 16px;
    padding: 30px 20px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.course-card:hover {
    transform: translateY(-5px);
    border-color: #4f46e5;
    background: white;
    box-shadow: 0 10px 30px rgba(79, 70, 229, 0.15);
}

.course-icon {
    width: 56px;
    height: 56px;
    margin: 0 auto 15px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.course-icon svg {
    width: 28px;
    height: 28px;
    color: white;
}

.course-title {
    color: #374151;
    font-size: 14px;
    font-weight: 600;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.gallery-item {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    aspect-ratio: 16/10;
    background: #f3f4f6;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.gallery-item:hover {
    transform: scale(1.03);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    padding: 20px;
    transform: translateY(100%);
    transition: transform 0.3s ease;
}

.gallery-item:hover .gallery-overlay {
    transform: translateY(0);
}

.gallery-title {
    color: white;
    font-size: 14px;
    font-weight: 600;
}

.skills-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 16px;
    padding: 20px 0;
}

.skill-badge {
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    border-radius: 50px;
    padding: 12px 24px;
    color: #374151;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.skill-badge:hover {
    transform: translateY(-3px);
    border-color: #4f46e5;
    background: white;
    box-shadow: 0 5px 15px rgba(79, 70, 229, 0.1);
}

.skill-icon {
    width: 28px;
    height: 28px;
    background: #4f46e5;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: white;
    font-weight: 700;
}

.skill-icon svg {
    width: 16px;
    height: 16px;
    color: white;
}

.nav-dots {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 30px;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #d1d5db;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    padding: 0;
    display: inline-block;
}

.dot:hover, .dot.active {
    background: #4f46e5;
    transform: scale(1.2);
}

.nav-arrows {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    left: 0;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    pointer-events: none;
}

.arrow {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: white;
    border: 2px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4f46e5;
    font-size: 20px;
    cursor: pointer;
    pointer-events: all;
    transition: all 0.3s ease;
    text-decoration: none;
    font-weight: bold;
    
}

.arrow:hover {
    background: #4f46e5;
    color: white;
    border-color: #4f46e5;
}

footer {
    text-align: center;
    padding: 40px;
    background: white;
    margin-top: 60px;
}

footer p {
    color: #6b7280;
    font-size: 14px;
}

@media (max-width: 1024px) {
    .features {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .courses-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    nav {
        padding: 20px 30px;
    }
    
    .hometab h1 {
        font-size: 36px;
    }
    
    .features {
        grid-template-columns: 1fr;
        padding: 0 30px 60px;
    }
    
    .slideshow-section {
        padding: 30px;
    }
    
    .slideshow-container {
        padding: 30px;
        min-height: auto;
    }
    
    .courses-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .gallery-grid {
        grid-template-columns: 1fr;
    }
    
    .nav-arrows {
        display: none;
    }
    
    .profile-name {
        font-size: 28px;
    }
}
</style>
</head>
<body>

<nav>
    <div class="logo">MyStudentManager</div>
    <div class="nav-links">
        <a href="login.php">Login</a>
        <a href="signup.php" class="signup-btn" style = "color: white;">Sign Up</a>
    </div>
</nav>

<section class="hometab">
    <h1>Student Registration System</h1>
    <p>
        A comprehensive platform for managing student registrations and administrative tasks.
        Streamline your educational institution's enrollment process.
    </p>
    <a href="signup.php"><button class="cta-btn">Get Started</button></a>
</section>

<section class="features">
    <div class="cardtab">
        <div class="icon-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <h3>User Management</h3>
        <p>Create and manage administrative and standard user accounts with role-based access control.</p>
    </div>

    <div class="cardtab">
        <div class="icon-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <h3>Student Registration</h3>
        <p>Efficiently register and track students with comprehensive data management tools.</p>
    </div>

    <div class="cardtab">
        <div class="icon-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        </div>
        <h3>Analytics Dashboard</h3>
        <p>View comprehensive statistics and insights about your registered users and students.</p>
    </div>
</section>

<?php
$totalSlides = 6;
$currentSlide = isset($_GET['slide']) ? intval($_GET['slide']) : 1;
if ($currentSlide < 1 || $currentSlide > $totalSlides) {
    $currentSlide = 1;
}
$nextSlide = $currentSlide >= $totalSlides ? 1 : $currentSlide + 1;
$prevSlide = $currentSlide <= 1 ? $totalSlides : $currentSlide - 1;
?>

<section class="slideshow-section" id="portfolio">
    <div class="slideshow-container">
        <div class="nav-arrows">
            <a href="?slide=<?php echo $prevSlide; ?>#portfolio" class="arrow" style="margin-right: 80px;">‹</a>
            <a href="?slide=<?php echo $nextSlide; ?>#portfolio" class="arrow">›</a>
        </div>

        <div class="slide <?php echo $currentSlide == 1 ? 'active' : ''; ?>">
            <div class="profile-section">
                <div class="profile-img-container">
                    <div class="profile-img-ring"></div>
                    <img src="profile.png" alt="Ahishakiye Barack" class="profile-img">
                </div>
                <h1 class="profile-name">AHISHAKIYE Barack</h1>
                <p class="profile-class">System Designer</p>
                <p class="profile-desc">Web Designer | Vue.js Developer | Student</p>
            </div>
        </div>

        <div class="slide <?php echo $currentSlide == 2 ? 'active' : ''; ?>">
            <h2 class="section-title">Courses I Study</h2>
            <div class="courses-grid">
                <div class="course-card">
                    <div class="course-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="course-title">Computer Literacy</div>
                </div>
                <div class="course-card">
                    <div class="course-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    </div>
                    <div class="course-title">Web Development</div>
                </div>
                <div class="course-card">
                    <div class="course-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div class="course-title">JavaScript</div>
                </div>
                <div class="course-card">
                    <div class="course-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div class="course-title">Backend Design</div>
                </div>
                <div class="course-card">
                    <div class="course-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    </div>
                    <div class="course-title">Networking</div>
                </div>
            </div>
        </div>

        <div class="slide <?php echo $currentSlide == 3 ? 'active' : ''; ?>">
            <h2 class="section-title">My other projects</h2>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="proj1.jpg" alt="Project 1">
                    <div class="gallery-overlay">
                        <div class="gallery-title">E-Commerce Dashboard</div>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="proj2.jpg" alt="Project 2">
                    <div class="gallery-overlay">
                        <div class="gallery-title">Analytics Platform</div>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="proj3.jpg" alt="Project 3">
                    <div class="gallery-overlay">
                        <div class="gallery-title">Mobile App UI</div>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="proj4.jpg" alt="Project 4">
                    <div class="gallery-overlay">
                        <div class="gallery-title">Portfolio Website</div>
                    </div>
                </div>
            </div>
        </div>
         <div class="slide <?php echo $currentSlide == 4 ? 'active' : ''; ?>">
            <h2 class="section-title">My hobbies</h2>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="football.jpg" alt="Project 1">
                    <div class="gallery-overlay">
                        <div class="gallery-title">Playing Football</div>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="bako.jpg" alt="Project 2">
                    <div class="gallery-overlay">
                        <div class="gallery-title">Playing Basketball</div>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="movie.jpg" alt="Project 3">
                    <div class="gallery-overlay">
                        <div class="gallery-title">Watching Movies</div>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="download.jpg" alt="Project 4">
                    <div class="gallery-overlay">
                        <div class="gallery-title">Playing Games</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="slide <?php echo $currentSlide == 5 ? 'active' : ''; ?>">
            <h2 class="section-title">My Gallery</h2>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="yow.jpg" >
                </div>
                <div class="gallery-item">
                    <img src="yaw.jpg" >
                </div>
                <div class="gallery-item">
                    <img src="download.jpg">
                </div>
                <div class="gallery-item">
                    <img src="proj1.jpg">
                </div>
                <div class="gallery-item">
                    <img src="movie.jpg">
                </div>
                <div class="gallery-item">
                    <img src="proj2.jpg">
                </div>
                <div class="gallery-item">
                    <img src="proj3.jpg">
                </div>
                <div class="gallery-item">
                    <img src="yow.jpg">
                </div>
            </div>
        </div>
         <div class="slide <?php echo $currentSlide == 6 ? 'active' : ''; ?>">
            <h2 class="section-title">Skills & Technologies</h2>
            <div class="skills-container">
                <div class="skill-badge">
                    <span class="skill-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    </span>
                    HTML5
                </div>
                <div class="skill-badge">
                    <span class="skill-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    </span>
                    CSS
                </div>
                <div class="skill-badge">
                    <span class="skill-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    </span>
                    JavaScript
                </div>
                <div class="skill-badge">
                    <span class="skill-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                    </span>
                    Vue.js
                </div>
                <div class="skill-badge">
                    <span class="skill-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.131A8 8 0 008 8m0 0a8 8 0 00-8 8c0 2.33.311 4.598.903 6.768M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.131A8 8 0 008 8m0 0a8 8 0 00-8 8c0 2.33.311 4.598.903 6.768"></path></svg>
                    </span>
                    Git
                </div>
            </div>
            <div class="skills-container">
                <div class="skill-badge">
                    <span class="skill-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Game dev
                </div>
                <div class="skill-badge">
                    <span class="skill-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    </span>
                    PHP
                </div>
                <div class="skill-badge">
                    <span class="skill-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </span>
                    Unreal Engine 5
                </div>
                <div class="skill-badge">
                    <span class="skill-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </span>
                    Blender
                </div>
                <div class="skill-badge">
                    <span class="skill-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 01-2 2v4a2 2 0 012 2h14a2 2 0 012-2v-4a2 2 0 01-2-2m-2-4h.01M17 16h.01"></path></svg>
                    </span>
                    Node.js
                </div>
            </div>
        </div>
    </div>

    <div class="nav-dots">
        <a href="?slide=1#portfolio" class="dot <?php echo $currentSlide == 1 ? 'active' : ''; ?>"></a>
        <a href="?slide=2#portfolio" class="dot <?php echo $currentSlide == 2 ? 'active' : ''; ?>"></a>
        <a href="?slide=3#portfolio" class="dot <?php echo $currentSlide == 3 ? 'active' : ''; ?>"></a>
        <a href="?slide=4#portfolio" class="dot <?php echo $currentSlide == 4 ? 'active' : ''; ?>"></a>
        <a href="?slide=5#portfolio" class="dot <?php echo $currentSlide == 5 ? 'active' : ''; ?>"></a>
        <a href="?slide=6#portfolio" class="dot <?php echo $currentSlide == 6 ? 'active' : ''; ?>"></a>
    </div>
</section>

<footer>
    <p>© 2026 Student Registration System. All Rights Reserved.</p>
</footer>

</body>
</html>
