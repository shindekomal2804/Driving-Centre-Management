<?php
/**
 * Homepage / Index Page
 */

$page_title = "Home";
$css_path = "../assets/css/style.css";
$js_path = "../assets/js/script.js";

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

// Get courses
$courses = getRows("SELECT * FROM courses WHERE status = ? LIMIT 3", "s", [STATUS_ACTIVE]);

// Get testimonials
$testimonials = getRows("SELECT * FROM testimonials WHERE status = ? LIMIT 6", "s", ['approved']);

include __DIR__ . '/../includes/user-header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1>Master the Art of Safe Driving</h1>
        <p>Professional driving training with experienced instructors</p>
        <a href="#courses" class="cta-button">
            <i class="fas fa-calendar me-2"></i> Book Your Lesson Today
        </a>
    </div>
</section>

<!-- Services Section -->
<section class="bg-light py-5">
    <div class="container">
        <h2>Our Services</h2>
        
        <div class="row g-4">
            <!-- Car Driving -->
            <div class="col-md-4">
                <div class="service-card">
                    <i class="fas fa-car"></i>
                    <h5>Car Driving</h5>
                    <p>Comprehensive car driving courses from beginner to advanced levels with certified instructors.</p>
                </div>
            </div>
            
            <!-- Bike Training -->
            <div class="col-md-4">
                <div class="service-card">
                    <i class="fas fa-motorcycle"></i>
                    <h5>Bike Training</h5>
                    <p>Professional motorcycle training focusing on safety, balance, and defensive riding techniques.</p>
                </div>
            </div>
            
            <!-- License Assistance -->
            <div class="col-md-4">
                <div class="service-card">
                    <i class="fas fa-certificate"></i>
                    <h5>License Assistance</h5>
                    <p>Complete license assistance with document guidance, form filling, and driving test preparation.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Courses Section -->
<section id="courses" class="py-5">
    <div class="container">
        <h2>Featured Courses</h2>
        
        <div class="row g-4">
            <?php foreach ($courses as $course): ?>
                <div class="col-md-4">
                    <div class="course-card">
                        <!-- Course Header -->
                        <div class="course-header">
                            <h6><?php echo htmlspecialchars($course['level']); ?></h6>
                            <h4><?php echo htmlspecialchars($course['title']); ?></h4>
                        </div>
                        
                        <!-- Course Body -->
                        <div class="course-body">
                            <p class="course-duration">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo htmlspecialchars($course['duration_weeks']); ?> weeks | 
                                <?php echo htmlspecialchars($course['duration_hours']); ?> hours
                            </p>
                            
                            <ul class="course-features">
                                <?php
                                $features = explode(',', $course['features']);
                                foreach (array_slice($features, 0, 4) as $feature):
                                ?>
                                    <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <div class="course-price">
                                ₹<?php echo number_format($course['price']); ?>
                            </div>
                            
                            <button class="enroll-btn" onclick="handleEnroll(<?php echo $course['id']; ?>)">
                                Enroll Now
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="./courses.php" class="btn btn-outline-primary btn-lg">
                View All Courses <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Instructors Section -->
<section class="bg-light py-5">
    <div class="container">
        <h2>Meet Our Expert Instructors</h2>
        
        <div class="row g-4">
            <?php
            $instructors = getRows("SELECT u.name, i.* FROM instructors i JOIN users u ON i.user_id = u.id WHERE i.status = ? LIMIT 3", "s", [STATUS_ACTIVE]);
            
            foreach ($instructors as $instructor):
            ?>
                <div class="col-md-4">
                    <div class="instructor-card">
                        <div class="instructor-image">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="info">
                            <h5><?php echo htmlspecialchars($instructor['name']); ?></h5>
                            <p><?php echo htmlspecialchars($instructor['specialty']); ?> | 
                               <?php echo htmlspecialchars($instructor['experience_years']); ?> years exp.</p>
                            <div class="instructor-rating">
                                <?php
                                $rating = round($instructor['rating']);
                                for ($i = 0; $i < 5; $i++):
                                    echo $i < $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                endfor;
                                ?>
                                (<?php echo htmlspecialchars($instructor['rating']); ?>/5)
                            </div>
                            <p class="text-muted small"><?php echo htmlspecialchars($instructor['bio']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <h2>What Our Students Say</h2>
        
        <div class="row g-4">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="testimonial-card">
                        <div class="stars">
                            <?php
                            for ($i = 0; $i < 5; $i++):
                                echo $i < $testimonial['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                            endfor;
                            ?>
                        </div>
                        
                        <p>"<?php echo htmlspecialchars($testimonial['message']); ?>"</p>
                        
                        <div class="client-name">
                            <i class="fas fa-user-circle me-2"></i>
                            <?php echo htmlspecialchars($testimonial['name']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h2 class="mb-4">Ready to Get Started?</h2>
        <p class="mb-4 fs-5">Join hundreds of satisfied students who have passed their driving tests with DriveEasy</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="./register.php" class="btn btn-warning btn-lg me-2">
                <i class="fas fa-user-plus me-2"></i> Register Now
            </a>
            <a href="./login.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i> Login
            </a>
        <?php else: ?>
            <a href="./user/dashboard.php" class="btn btn-warning btn-lg me-2">
                <i class="fas fa-dashboard me-2"></i> Go to Dashboard
            </a>
            <a href="./book-lesson.php" class="btn btn-light btn-lg">
                <i class="fas fa-calendar-plus me-2"></i> Book a Lesson
            </a>
        <?php endif; ?>
    </div>
</section>

<!-- Contact Call Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3>Have Questions?</h3>
                <p class="fs-5">Our friendly team is here to help you choose the perfect course.</p>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <strong>Call Us:</strong> +91-9876543210
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-envelope text-primary me-2"></i>
                        <strong>Email:</strong> info@driveeasy.com
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        <strong>Visit Us:</strong> 123 Driving Ave, City 12345
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <img src="https://via.placeholder.com/400x300?text=Contact+Us" alt="Contact" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<script>
function handleEnroll(courseId) {
    <?php if (!isset($_SESSION['user_id'])): ?>
        // Redirect to login if not logged in
        window.location.href = './login.php?redirect=./book-lesson.php?course_id=' + courseId;
    <?php else: ?>
        // Redirect to booking page
        window.location.href = './book-lesson.php?course_id=' + courseId;
    <?php endif; ?>
}
</script>

<?php include __DIR__ . '/../includes/user-footer.php'; ?>
