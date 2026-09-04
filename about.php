<?php
session_start();
require_once 'config/constants.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - DriveEasy Driving School</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet"/>
    <link href="assets/css/style.css" rel="stylesheet"/>
    <style>
        :root {
            --primary-color: #1e63d9;
            --secondary-color: #ffc107;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .about-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1249b3 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .about-hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .about-hero p {
            font-size: 1.25rem;
            opacity: 0.9;
        }

        .mission-section {
            padding: 80px 0;
            background-color: #f8f9fa;
        }

        .mission-card {
            background: white;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .mission-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .mission-card i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .mission-card h3 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .mission-card p {
            color: #666;
            line-height: 1.6;
        }

        .team-section {
            padding: 80px 0;
        }

        .team-section h2 {
            text-align: center;
            font-size: 2.5rem;
            color: var(--dark-color);
            margin-bottom: 60px;
            font-weight: 700;
        }

        .team-member {
            text-align: center;
            margin-bottom: 30px;
        }

        .team-member-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .team-member h4 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 5px;
        }

        .team-member .position {
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 10px;
        }

        .team-member p {
            color: #666;
            font-size: 0.95rem;
        }

        .stats-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1249b3 100%);
            color: white;
            padding: 60px 0;
            margin: 60px 0;
        }

        .stat-item {
            text-align: center;
            margin-bottom: 30px;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .why-choose {
            padding: 80px 0;
            background-color: #f8f9fa;
        }

        .why-choose h2 {
            text-align: center;
            font-size: 2.5rem;
            color: var(--dark-color);
            margin-bottom: 50px;
            font-weight: 700;
        }

        .reason-item {
            display: flex;
            margin-bottom: 30px;
            align-items: flex-start;
        }

        .reason-icon {
            width: 50px;
            height: 50px;
            background: var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 25px;
            flex-shrink: 0;
        }

        .reason-icon i {
            color: var(--dark-color);
            font-size: 1.5rem;
        }

        .reason-content h4 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .reason-content p {
            color: #666;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .cta-section {
            background: white;
            padding: 60px 0;
            text-align: center;
        }

        .cta-section h2 {
            color: var(--dark-color);
            font-size: 2rem;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .btn-primary-custom {
            background: var(--primary-color);
            color: white;
            padding: 12px 40px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease;
            display: inline-block;
            margin: 10px;
        }

        .btn-primary-custom:hover {
            background: #1249b3;
            color: white;
            text-decoration: none;
        }

        .btn-secondary-custom {
            background: var(--secondary-color);
            color: var(--dark-color);
            padding: 12px 40px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease;
            display: inline-block;
            margin: 10px;
        }

        .btn-secondary-custom:hover {
            background: #e0a800;
            color: var(--dark-color);
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .about-hero h1 {
                font-size: 2rem;
            }

            .about-hero p {
                font-size: 1rem;
            }

            .mission-section,
            .team-section,
            .why-choose {
                padding: 50px 0;
            }

            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/user-header.php'; ?>

    <main>
        <!-- About Hero Section -->
        <section class="about-hero">
            <div class="container">
                <h1>About DriveEasy</h1>
                <p>Your Trusted Partner for Safe and Confident Driving</p>
            </div>
        </section>

        <!-- Mission, Vision, Values -->
        <section class="mission-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mission-card">
                            <i class="fas fa-bullseye"></i>
                            <h3>Our Mission</h3>
                            <p>To provide high-quality, accessible driving education that empowers individuals with the skills and confidence to drive safely on any road.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mission-card">
                            <i class="fas fa-eye"></i>
                            <h3>Our Vision</h3>
                            <p>To be the leading driving school in the region, recognized for excellence in instruction, student outcomes, and road safety promotion.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mission-card">
                            <i class="fas fa-heart"></i>
                            <h3>Our Values</h3>
                            <p>Safety, integrity, professionalism, and student success guide every decision we make and every lesson we teach.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics -->
        <section class="stats-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-item">
                            <div class="stat-number">15+</div>
                            <div class="stat-label">Years of Experience</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-item">
                            <div class="stat-number">10,000+</div>
                            <div class="stat-label">Students Trained</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-item">
                            <div class="stat-number">98%</div>
                            <div class="stat-label">Pass Rate</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-item">
                            <div class="stat-number">50+</div>
                            <div class="stat-label">Expert Instructors</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="why-choose">
            <div class="container">
                <h2>Why Choose DriveEasy?</h2>
                <div class="row">
                    <div class="col-md-6">
                        <div class="reason-item">
                            <div class="reason-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="reason-content">
                                <h4>Certified Instructors</h4>
                                <p>All our instructors are certified, experienced, and trained in the latest teaching methods and safety protocols.</p>
                            </div>
                        </div>

                        <div class="reason-item">
                            <div class="reason-icon">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="reason-content">
                                <h4>Modern Fleet</h4>
                                <p>We maintain a fleet of well-maintained, modern vehicles equipped with dual controls for maximum safety during training.</p>
                            </div>
                        </div>

                        <div class="reason-item">
                            <div class="reason-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="reason-content">
                                <h4>Personalized Training</h4>
                                <p>Each student receives customized instruction tailored to their learning pace and specific driving challenges.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="reason-item">
                            <div class="reason-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="reason-content">
                                <h4>Flexible Scheduling</h4>
                                <p>Book lessons at times that work best for you, with options for weekdays, evenings, and weekends.</p>
                            </div>
                        </div>

                        <div class="reason-item">
                            <div class="reason-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="reason-content">
                                <h4>Comprehensive Curriculum</h4>
                                <p>Our courses cover everything from basic traffic rules to defensive driving techniques and real-world scenarios.</p>
                            </div>
                        </div>

                        <div class="reason-item">
                            <div class="reason-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="reason-content">
                                <h4>High Success Rate</h4>
                                <p>With a 98% pass rate, our students consistently exceed expectations and pass their driving tests on the first attempt.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Team Highlights -->
        <section class="team-section">
            <div class="container">
                <h2>Meet Our Leadership Team</h2>
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="team-member">
                            <div class="team-member-img">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4>John Smith</h4>
                            <div class="position">Founder & CEO</div>
                            <p>With 20+ years of driving experience, John founded DriveEasy with a mission to revolutionize driver education.</p>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="team-member">
                            <div class="team-member-img">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4>Sarah Johnson</h4>
                            <div class="position">Head Instructor</div>
                            <p>Sarah brings 15 years of professional instruction and has trained over 3,000 students successfully.</p>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="team-member">
                            <div class="team-member-img">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4>Michael Chen</h4>
                            <div class="position">Safety Coordinator</div>
                            <p>Michael ensures all training meets the highest safety standards and uses the latest defensive driving techniques.</p>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="team-member">
                            <div class="team-member-img">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4>Emily Rodriguez</h4>
                            <div class="position">Student Relations</div>
                            <p>Emily manages student success programs and ensures every learner achieves their driving goals.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="cta-section">
            <div class="container">
                <h2>Ready to Start Your Journey?</h2>
                <p style="font-size: 1.1rem; color: #666; margin-bottom: 30px;">Join thousands of confident drivers who started with DriveEasy</p>
                <a href="courses.php" class="btn-primary-custom">
                    <i class="fas fa-book"></i> Explore Our Courses
                </a>
                <a href="contact.php" class="btn-secondary-custom">
                    <i class="fas fa-envelope"></i> Get in Touch
                </a>
            </div>
        </section>
    </main>

    <?php include 'includes/user-footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
