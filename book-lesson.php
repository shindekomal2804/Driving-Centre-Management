<?php
/**
 * Book Driving Lesson Page
 */

$page_title = "Book a Lesson";
$css_path = "./assets/css/style.css";
$js_path = "./assets/js/script.js";

require_once './config/database.php';
require_once './config/constants.php';
require_once './includes/auth.php';

// Check if user is logged in
if (!AuthManager::isUserLoggedIn()) {
    $redirectUrl = './login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']);
    header('Location: ' . $redirectUrl);
    exit();
}

// Get current user
$user = AuthManager::getCurrentUser();

// Get course ID from URL
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Get courses for dropdown
$courses = getRows("SELECT id, title, level, price FROM courses WHERE status = ? ORDER BY title", "s", [STATUS_ACTIVE]);

// Get instructors for dropdown
$instructors = getRows(
    "SELECT i.id, u.name, i.specialty, i.rating FROM instructors i 
     JOIN users u ON i.user_id = u.id 
     WHERE i.status = ? AND u.status = ?
     ORDER BY i.rating DESC",
    "ss",
    [STATUS_ACTIVE, STATUS_ACTIVE]
);

include './includes/user-header.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4">
            <i class="fas fa-calendar-plus me-2"></i> Book a Driving Lesson
        </h2>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Lesson Details</h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Success Message -->
                        <div id="successAlert" class="alert alert-success d-none" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Success!</strong> Your booking has been created. Redirecting you to the payment page now.
                        </div>
                        
                        <!-- Error Message -->
                        <div id="errorAlert" class="alert alert-danger d-none" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span id="errorText"></span>
                        </div>
                        
                        <!-- Form -->
                        <form id="bookingForm" method="POST" data-validate="true">
                            <!-- Course Selection -->
                            <div class="mb-4">
                                <label for="course_id" class="form-label fw-bold">Select Course</label>
                                <select class="form-select form-control-lg" id="course_id" name="course_id" required>
                                    <option value="">-- Choose a course --</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>" 
                                                <?php echo $course_id == $course['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course['title']); ?> 
                                            (₹<?php echo number_format($course['price']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Instructor Selection -->
                            <div class="mb-4">
                                <label for="instructor_id" class="form-label fw-bold">Select Instructor</label>
                                <select class="form-select form-control-lg" id="instructor_id" name="instructor_id" required>
                                    <option value="">-- Choose an instructor --</option>
                                    <?php if (empty($instructors)): ?>
                                        <option value="" disabled>No instructors available</option>
                                    <?php else: ?>
                                        <?php foreach ($instructors as $instructor): ?>
                                            <option value="<?php echo $instructor['id']; ?>">
                                                <?php echo htmlspecialchars($instructor['name']); ?> 
                                                (<?php echo htmlspecialchars($instructor['specialty']); ?>) - 
                                                ⭐<?php echo htmlspecialchars($instructor['rating']); ?>/5
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            

                            
                            <div class="row">
                                <!-- Booking Date -->
                                <div class="col-md-6 mb-4">
                                    <label for="booking_date" class="form-label fw-bold">Preferred Date</label>
                                    <input type="date" class="form-control form-control-lg" id="booking_date" name="booking_date" required>
                                </div>
                                
                                <!-- Start Time -->
                                <div class="col-md-6 mb-4">
                                    <label for="start_time" class="form-label fw-bold">Start Time</label>
                                    <input type="time" class="form-control form-control-lg" id="start_time" name="start_time" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Duration -->
                                <div class="col-md-6 mb-4">
                                    <label for="duration" class="form-label fw-bold">Duration (hours)</label>
                                    <select class="form-select form-control-lg" id="duration" name="duration" required>
                                        <option value="">-- Select Duration --</option>
                                        <option value="1">1 Hour</option>
                                        <option value="2">2 Hours</option>
                                        <option value="3">3 Hours</option>
                                    </select>
                                </div>
                                
                                <!-- Vehicle Type -->
                                <div class="col-md-6 mb-4">
                                    <label for="vehicle_type" class="form-label fw-bold">Vehicle Type</label>
                                    <select class="form-select form-control-lg" id="vehicle_type" name="vehicle_type" required>
                                        <option value="">-- Select Vehicle --</option>
                                        <option value="car">Car (Manual)</option>
                                        <option value="car_auto">Car (Automatic)</option>
                                        <option value="bike">Bike</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Location -->
                            <div class="mb-4">
                                <label for="location" class="form-label fw-bold">Preferred Location</label>
                                <input type="text" class="form-control form-control-lg" id="location" name="location" placeholder="e.g., Near City Center" required>
                            </div>
                            
                            <!-- Special Notes -->
                            <div class="mb-4">
                                <label for="notes" class="form-label fw-bold">Special Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any special requests or information..."></textarea>
                            </div>
                            
                            <!-- Terms Checkbox -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the cancellation and rescheduling policy. 
                                    <a href="#" class="text-primary">Read more</a>
                                </label>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-credit-card me-2"></i> Continue to Payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Booking Summary -->
            <div class="col-lg-4">
                <div class="card shadow mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Booking Summary</h5>
                    </div>
                    
                    <div class="card-body">
                        <div class="summary-item mb-3">
                            <small class="text-muted">Selected Course</small>
                            <p id="summary_course" class="mb-0 fw-bold">Not selected</p>
                        </div>
                        
                        <hr>

                        <div class="summary-item mb-3">
                            <small class="text-muted">Instructor</small>
                            <p id="summary_instructor" class="mb-0 fw-bold">Not selected</p>
                        </div>
                        
                        <hr>
                        
                        <div class="summary-item mb-3">
                            <small class="text-muted">Date & Time</small>
                            <p id="summary_datetime" class="mb-0 fw-bold">Not selected</p>
                        </div>
                        
                        <hr>
                        
                        <div class="summary-item mb-3">
                            <small class="text-muted">Duration</small>
                            <p id="summary_duration" class="mb-0 fw-bold">Not selected</p>
                        </div>
                        
                        <hr>
                        
                        <div class="summary-item mb-3">
                            <small class="text-muted">Base Price</small>
                            <p id="summary_price" class="mb-0 fw-bold">₹0</p>
                        </div>
                        
                        <hr>
                        
                        <div class="summary-total">
                            <small class="text-muted">Total Amount</small>
                            <h6 class="text-primary fw-bold" id="summary_total">₹0</h6>
                        </div>
                        
                        <div class="alert alert-info mt-4" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>
                                After booking, you will be taken to the payment page to complete checkout securely.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Update summary on form change
document.getElementById('course_id').addEventListener('change', updateSummary);
document.getElementById('instructor_id').addEventListener('change', updateSummary);
document.getElementById('booking_date').addEventListener('change', updateSummary);
document.getElementById('start_time').addEventListener('change', updateSummary);
document.getElementById('duration').addEventListener('change', updateSummary);

// Set minimum date
document.getElementById('booking_date').min = new Date().toISOString().split('T')[0];

function updateSummary() {
    const courseSelect = document.getElementById('course_id');
    const instructorSelect = document.getElementById('instructor_id');
    const dateInput = document.getElementById('booking_date');
    const timeInput = document.getElementById('start_time');
    const durationSelect = document.getElementById('duration');
    
    // Update course
    const courseOption = courseSelect.options[courseSelect.selectedIndex];
    if (courseOption.value) {
        document.getElementById('summary_course').textContent = courseOption.text.split('(')[0].trim();
        const price = courseOption.text.match(/₹([\d,]+)/);
        if (price) {
            document.getElementById('summary_price').textContent = price[0];
        }
    }

    // Update instructor
    const instructorOption = instructorSelect.options[instructorSelect.selectedIndex];
    if (instructorOption && instructorOption.value) {
        document.getElementById('summary_instructor').textContent = instructorOption.text.split('(')[0].trim();
    } else {
        document.getElementById('summary_instructor').textContent = 'Not selected';
    }
    
    // Update date time
    if (dateInput.value && timeInput.value) {
        const date = new Date(dateInput.value);
        const dateStr = date.toLocaleDateString('en-IN', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
        document.getElementById('summary_datetime').textContent = dateStr + ' at ' + timeInput.value;
    }
    
    // Update duration
    if (durationSelect.value) {
        document.getElementById('summary_duration').textContent = durationSelect.value + ' Hour(s)';
    }
    
    // Calculate total
    calculateTotal();
}

function calculateTotal() {
    const priceText = document.getElementById('summary_price').textContent;
    const durationText = document.getElementById('summary_duration').textContent;
    
    const price = parseInt(priceText.replace(/[^0-9]/g, '')) || 0;
    const duration = parseInt(durationText) || 1;
    
    const total = price * duration;
    document.getElementById('summary_total').textContent = '₹' + total.toLocaleString('en-IN');
}

// Form submission
document.getElementById('bookingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const instructorId = formData.get('instructor_id');
    if (!instructorId) {
        showError('Please select an instructor before booking.');
        return;
    }

    const data = {
        course_id: formData.get('course_id'),
        instructor_id: instructorId,
        booking_date: formData.get('booking_date'),
        start_time: formData.get('start_time'),
        duration_hours: formData.get('duration'),
        vehicle_type: formData.get('vehicle_type'),
        location: formData.get('location'),
        notes: formData.get('notes')
    };
    
    try {
        const response = await fetch('./api/book-lesson.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (jsonErr) {
            const snippet = text.length > 200 ? text.substring(0, 200) + '...' : text;
            showError('Invalid JSON response from server: ' + snippet);
            console.error('Invalid JSON response:', text);
            return;
        }

        if (!response.ok) {
            const message = result && result.message ? result.message : 'Server error while booking lesson.';
            showError(message);
            return;
        }

        if (result.success) {
            document.getElementById('successAlert').classList.remove('d-none');
            document.getElementById('errorAlert').classList.add('d-none');
            
            setTimeout(() => {
                window.location.href = './payment.php?booking_id=' + result.booking_id;
            }, 1200);
        } else {
            showError(result.message || 'Failed to book lesson. Please try again.');
        }
    } catch (error) {
        showError(error.message || 'Failed to book lesson. Please try again.');
        console.error('Error:', error);
    }
});

function showError(message) {
    const errorAlert = document.getElementById('errorAlert');
    const errorText = document.getElementById('errorText');
    errorText.textContent = message;
    errorAlert.classList.remove('d-none');
    window.scrollTo(0, 0);
}
</script>

<?php include './includes/user-footer.php'; ?>
