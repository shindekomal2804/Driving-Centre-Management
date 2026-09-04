<?php
/**
 * Courses Page
 */

$page_title = "Courses";
$css_path = "./assets/css/style.css";
$js_path = "./assets/js/script.js";

require_once './config/database.php';
require_once './config/constants.php';
require_once './includes/auth.php';

// Get filter parameter
$level_filter = isset($_GET['level']) ? trim($_GET['level']) : '';

// Build query
$query = "SELECT * FROM courses WHERE status = ?";
$types = "s";
$params = [STATUS_ACTIVE];

if (!empty($level_filter)) {
    $query .= " AND level = ?";
    $types .= "s";
    $params[] = $level_filter;
}

$query .= " ORDER BY created_at DESC";

// Get all courses
$courses = getRows($query, $types, $params);

include './includes/user-header.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <h2>Our Driving Courses</h2>
        
        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <a href="./courses.php" class="btn btn-<?php echo empty($level_filter) ? 'primary' : 'outline-primary'; ?>">
                        All Courses
                    </a>
                    <a href="./courses.php?level=Beginner" class="btn btn-<?php echo $level_filter === 'Beginner' ? 'primary' : 'outline-primary'; ?>">
                        <i class="fas fa-star me-1"></i> Beginner
                    </a>
                    <a href="./courses.php?level=Intermediate" class="btn btn-<?php echo $level_filter === 'Intermediate' ? 'primary' : 'outline-primary'; ?>">
                        <i class="fas fa-star-half-alt me-1"></i> Intermediate
                    </a>
                    <a href="./courses.php?level=Advanced" class="btn btn-<?php echo $level_filter === 'Advanced' ? 'primary' : 'outline-primary'; ?>">
                        <i class="fas fa-stars me-1"></i> Advanced
                    </a>
                </div>
            </div>
        </div>
        
        <?php if (empty($courses)): ?>
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-info-circle me-2"></i>
                <strong>No courses found.</strong> Please try another filter.
            </div>
        <?php else: ?>
            <!-- Courses Grid -->
            <div class="row g-4">
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="course-card">
                            <!-- Course Header -->
                            <div class="course-header">
                                <h6>
                                    <?php 
                                    $level = htmlspecialchars($course['level']);
                                    $icon = $level === 'Beginner' ? '★' : ($level === 'Intermediate' ? '★★' : '★★★');
                                    echo $icon . ' ' . $level;
                                    ?>
                                </h6>
                                <h4><?php echo htmlspecialchars($course['title']); ?></h4>
                            </div>
                            
                            <!-- Course Body -->
                            <div class="course-body">
                                <p class="course-duration">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo htmlspecialchars($course['duration_weeks']); ?> weeks | 
                                    <?php echo htmlspecialchars($course['duration_hours']); ?> hours
                                </p>
                                
                                <p class="text-muted small">
                                    <?php echo htmlspecialchars($course['description']); ?>
                                </p>
                                
                                <div class="my-3">
                                    <strong class="small">What you'll learn:</strong>
                                    <ul class="course-features">
                                        <?php
                                        $features = explode(',', $course['features']);
                                        foreach (array_slice($features, 0, 4) as $feature):
                                        ?>
                                            <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                
                                <div class="my-3">
                                    <strong class="small">Vehicle Types:</strong>
                                    <p class="small text-muted">
                                        <i class="fas fa-car me-1"></i>
                                        <?php echo htmlspecialchars($course['vehicle_types']); ?>
                                    </p>
                                </div>
                                
                                <div class="course-price">
                                    ₹<?php echo number_format($course['price']); ?>
                                </div>
                                
                                <button class="enroll-btn" onclick="handleEnroll(<?php echo $course['id']; ?>)">
                                    <i class="fas fa-check me-1"></i> Enroll Now
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5 bg-white">
    <div class="container">
        <h2>Why Choose DriveEasy?</h2>
        
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="feature-box p-4">
                    <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
                    <h5>Certified Instructors</h5>
                    <p>Trained and certified driving instructors with years of experience</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box p-4">
                    <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                    <h5>Flexible Scheduling</h5>
                    <p>Book lessons at your convenience with our flexible time slots</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box p-4">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h5>100% Safe</h5>
                    <p>Well-maintained vehicles and strict safety protocols for all lessons</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box p-4">
                    <i class="fas fa-award fa-3x text-primary mb-3"></i>
                    <h5>Proven Success Rate</h5>
                    <p>95% of our students pass their driving tests on first attempt</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box p-4">
                    <i class="fas fa-map-location-dot fa-3x text-primary mb-3"></i>
                    <h5>Convenient Locations</h5>
                    <p>Multiple training centers across the city for easy access</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box p-4">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h5>24/7 Support</h5>
                    <p>Round-the-clock customer support for all your queries</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h3 class="mb-4">Still not sure which course is right for you?</h3>
        <p class="mb-4 fs-5">Talk to our counselors to find the perfect course for your needs</p>
        <a href="./contact.php" class="btn btn-warning btn-lg">
            <i class="fas fa-comment me-2"></i> Contact Us Today
        </a>
    </div>
</section>

<script>
function handleEnroll(courseId) {
    <?php if (!isset($_SESSION['user_id'])): ?>
        window.location.href = './login.php?redirect=./book-lesson.php?course_id=' + courseId;
    <?php else: ?>
        window.location.href = './book-lesson.php?course_id=' + courseId;
    <?php endif; ?>
}
</script>

<?php include './includes/user-footer.php'; ?>
