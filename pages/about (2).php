<?php
// pages/about.php - About Gymly and Our Team
require_once "../autoload.php";

// If user is logged in, refresh session activity
if (SessionManager::isLoggedIn()) {
    SessionManager::updateActivity();
}

$pageTitle = "About Us - Gymly";
include '../template/layout.php';
?>

<!-- HERO SECTION -->
<section class="hero-about position-relative text-white py-5">
    <div class="hero-overlay"></div>
    <div class="container position-relative z-1 py-5">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-3 fw-bold mb-4 text-gradient">Built By Students, For Everyone</h1>
                <p class="lead text-light mb-0">
                    Six university students who saw a problem and decided to build the solution
                </p>
            </div>
        </div>
    </div>
</section>

<!-- OUR STORY SECTION -->
<section class="py-5 bg-dark text-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card bg-black border-secondary rounded-4 p-4 p-md-5">
                    <h2 class="fw-bold text-gradient mb-4">The Story Behind Gymly</h2>
                    
                    <p class="text-light lead mb-4">
                        It started with a simple frustration. We were all trying to stay consistent with our fitness goals, 
                        but every gym management system we encountered felt outdated, complicated, or just plain annoying to use.
                    </p>
                    
                    <p class="text-light mb-4">
                        As students juggling classes, assignments, and trying to maintain some semblance of a fitness routine, 
                        we needed something different. Something that actually understood what it's like to track workouts 
                        between study sessions, book classes on the go, and stay motivated when life gets overwhelming.
                    </p>
                    
                    <p class="text-light mb-4">
                        So we built it ourselves. Late nights in computer labs, countless cups of coffee, debugging sessions 
                        that turned into impromptu gym sessions, and here we are—Gymly isn't just our project, it's our solution 
                        to a problem we lived every day.
                    </p>
                    
                    <div class="bg-dark border border-primary rounded-3 p-4 mb-4">
                        <p class="text-light mb-0 fst-italic">
                            <i class="bi bi-quote text-primary fs-3 me-2"></i>
                            We didn't set out to revolutionize fitness tech. We just wanted something that worked—something 
                            that made it easier to show up, track progress, and actually enjoy the journey. Turns out, 
                            we weren't the only ones who needed that.
                        </p>
                    </div>
                    
                    <p class="text-light mb-0">
                        Gymly is built with modern web technologies, secure authentication, real-time tracking, and a design 
                        that actually makes sense. Whether you're a gym owner managing memberships or a fitness enthusiast 
                        tracking your gains, we've got you covered.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WHAT WE DO SECTION -->
<section class="py-5 bg-black text-white border-top border-secondary">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-gradient">What Gymly Does</h2>
            <p class="text-light">Everything you need for modern gym management and fitness tracking</p>
        </div>
        
        <div class="row g-4">
            <?php
            $features = [
                ['icon' => 'bi-person-badge', 'title' => 'Membership Management', 'desc' => 'Streamline member registration, track subscriptions, and manage user profiles with ease. Secure authentication and verification built in.'],
                ['icon' => 'bi-graph-up-arrow', 'title' => 'Progress Tracking', 'desc' => 'Monitor workouts, track achievements, and visualize your fitness journey with real-time statistics and personalized insights.'],
                ['icon' => 'bi-calendar-check', 'title' => 'Class Scheduling', 'desc' => 'Book fitness classes, manage schedules, and never miss a session. Perfect for both gym owners and members.'],
                ['icon' => 'bi-shield-check', 'title' => 'Secure & Reliable', 'desc' => 'Built with PHP, PostgreSQL, and modern security practices. Your data is protected with encryption and secure authentication.'],
                ['icon' => 'bi-people', 'title' => 'Community Features', 'desc' => 'Connect with other members, share achievements, and stay motivated together. Fitness is better with community.'],
                ['icon' => 'bi-speedometer2', 'title' => 'Fast & Responsive', 'desc' => 'Optimized performance on all devices. Access your fitness data anywhere, anytime, from desktop to mobile.']
            ];

            foreach ($features as $feature): ?>
                <div class="col-md-4">
                    <div class="feature-card bg-dark p-4 rounded-4 border border-secondary h-100">
                        <i class="bi <?= $feature['icon'] ?> text-primary fs-1 mb-3"></i>
                        <h4 class="fw-semibold text-white mb-3"><?= htmlspecialchars($feature['title']) ?></h4>
                        <p class="text-light mb-0"><?= htmlspecialchars($feature['desc']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- MEET THE TEAM SECTION -->
<section class="py-5 bg-dark text-white border-top border-secondary">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-gradient">Meet The Team</h2>
            <p class="text-light">The students who made it happen</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <?php
            $team = [
                ['name' => 'Issa Abdullah', 'role' => 'Backend Developer', 'icon' => 'bi-database'],
                ['name' => 'Brian Bwogo', 'role' => 'Full Stack Developer', 'icon' => 'bi-code-slash'],
                ['name' => 'Nimrod Kobia', 'role' => 'Frontend Developer', 'icon' => 'bi-brush'],
                ['name' => 'Ryan Mbugua', 'role' => 'Backend Developer', 'icon' => 'bi-server'],
                ['name' => 'Chief Mwijukye', 'role' => 'UI/UX Designer', 'icon' => 'bi-palette'],
                ['name' => 'Mohamedek Yussuf', 'role' => 'Full Stack Developer', 'icon' => 'bi-laptop']
            ];
            
            foreach ($team as $member): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="team-card bg-black p-4 rounded-4 border border-secondary text-center h-100">
                        <div class="team-icon-wrapper bg-dark rounded-circle mx-auto mb-3" 
                             style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi <?= $member['icon'] ?> text-primary fs-1"></i>
                        </div>
                        <h4 class="fw-semibold text-white mb-2"><?= htmlspecialchars($member['name']) ?></h4>
                        <p class="text-light mb-0"><?= htmlspecialchars($member['role']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5">
            <p class="text-light fst-italic">
                Six different perspectives, one shared goal: making fitness accessible and manageable for everyone.
            </p>
        </div>
    </div>
</section>

<!-- TECH STACK SECTION -->
<section class="py-5 bg-black text-white border-top border-secondary">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-gradient">Built With Modern Technology</h2>
            <p class="text-light">Reliable, scalable, and secure</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <?php
            $techStack = [
                ['icon' => 'bi-filetype-php', 'name' => 'PHP', 'desc' => 'Backend Logic'],
                ['icon' => 'bi-database', 'name' => 'PostgreSQL', 'desc' => 'Database'],
                ['icon' => 'bi-bootstrap', 'name' => 'Bootstrap 5', 'desc' => 'Frontend Framework'],
                ['icon' => 'bi-shield-lock', 'name' => 'Security', 'desc' => 'Encrypted & Safe']
            ];

            foreach ($techStack as $tech): ?>
                <div class="col-md-3 col-sm-6 text-center">
                    <div class="tech-card bg-dark p-4 rounded-4 border border-secondary h-100">
                        <i class="bi <?= $tech['icon'] ?> text-primary fs-1 mb-3"></i>
                        <h5 class="text-white fw-semibold"><?= htmlspecialchars($tech['name']) ?></h5>
                        <p class="text-light small mb-0"><?= htmlspecialchars($tech['desc']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="py-5 bg-dark text-white border-top border-secondary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold mb-4 text-gradient">Ready to Start Your Journey?</h2>
                <p class="lead text-light mb-4">
                    Join thousands of users who are already transforming their fitness experience with Gymly
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <?php if (SessionManager::isLoggedIn()): ?>
                        <a href="tracking.php" class="btn btn-primary btn-lg fw-semibold">
                            <i class="bi bi-graph-up-arrow me-2"></i> Go to Dashboard
                        </a>
                    <?php else: ?>
                        <a href="signUpPage.php" class="btn btn-primary btn-lg fw-semibold">
                            <i class="bi bi-person-plus me-2"></i> Get Started Free
                        </a>
                        <a href="signInPage.php" class="btn btn-outline-light btn-lg fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../template/footer.php'; ?>

<!-- INLINE STYLES -->
<style>
.hero-about {
    background: linear-gradient(135deg, #0D0D0D 0%, #1C1C1C 100%);
    position: relative;
}
.hero-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: radial-gradient(circle at 20% 50%, rgba(13,110,253,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 50%, rgba(16,185,129,0.1) 0%, transparent 50%);
    z-index: 0;
}
.text-gradient {
    background: linear-gradient(90deg, #0D6EFD, #10B981);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.feature-card, .team-card, .tech-card {
    transition: all 0.3s ease;
}
.feature-card:hover, .team-card:hover, .tech-card:hover {
    transform: translateY(-5px);
    border-color: #0D6EFD !important;
    box-shadow: 0 10px 30px rgba(13,110,253,0.2);
}
.min-vh-50 { min-height: 50vh; }
@media (max-width: 768px) {
    .display-3 { font-size: 2.5rem; }
}
</style>

</body>
</html>
