<!-- Admin Header Component -->
<?php
// Start session if not started
if (!isset($_SESSION)) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin-login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DriveEasy Admin Dashboard">
    <title><?php echo isset($page_title) ? $page_title . ' | Admin Dashboard' : 'Admin Dashboard - DriveEasy'; ?></title>
    
    <!-- MDBootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet"/>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js for graphs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo isset($css_path) ? $css_path : '../assets/css/admin-style.css'; ?>">
    
    <style>
        :root {
            --primary-color: #1e63d9;
            --secondary-color: #ffc107;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --sidebar-bg: #2c3e50;
            --sidebar-text: #ecf0f1;
        }
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--light-color);
            color: var(--dark-color);
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            padding: 0;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            overflow-y: auto;
            z-index: 999;
        }
        
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
        }
        
        .sidebar-brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--secondary-color);
        }
        
        .sidebar-nav .nav-item {
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .sidebar-nav .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
            border-left-color: var(--secondary-color);
        }
        
        .sidebar-nav .nav-item.active {
            background-color: rgba(255, 193, 7, 0.1);
            border-left-color: var(--secondary-color);
        }
        
        .sidebar-nav .nav-link {
            color: var(--sidebar-text);
            padding: 12px 20px;
            display: block;
        }
        
        .topbar {
            background-color: white;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .admin-content {
            padding: 30px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
        
        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-car me-2"></i> DriveEasy Admin
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'active' : ''; ?>">
            <a class="nav-link" href="./dashboard.php">
                <i class="fas fa-chart-line me-2"></i> Dashboard
            </a>
        </div>
        
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'active' : ''; ?>">
            <a class="nav-link" href="./users.php">
                <i class="fas fa-users me-2"></i> Manage Users
            </a>
        </div>
        
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'courses') !== false ? 'active' : ''; ?>">
            <a class="nav-link" href="./courses.php">
                <i class="fas fa-book me-2"></i> Manage Courses
            </a>
        </div>
        
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'instructors') !== false ? 'active' : ''; ?>">
            <a class="nav-link" href="./instructors.php">
                <i class="fas fa-chalkboard-user me-2"></i> Instructors
            </a>
        </div>
        
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'bookings') !== false ? 'active' : ''; ?>">
            <a class="nav-link" href="./bookings.php">
                <i class="fas fa-calendar-check me-2"></i> Bookings
            </a>
        </div>
        
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'reviews') !== false ? 'active' : ''; ?>">
            <a class="nav-link" href="./reviews.php">
                <i class="fas fa-star me-2"></i> Reviews
            </a>
        </div>
        
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'messages') !== false ? 'active' : ''; ?>">
            <a class="nav-link" href="./messages.php">
                <i class="fas fa-envelope me-2"></i> Messages
            </a>
        </div>
        
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'active' : ''; ?>">
            <a class="nav-link" href="./settings.php">
                <i class="fas fa-cog me-2"></i> Settings
            </a>
        </div>
        
        <hr style="border-color: rgba(255, 255, 255, 0.1);">
        
        <div class="nav-item">
            <a class="nav-link" href="../api/admin-logout.php">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </div>
    </nav>
</div>

<!-- Main Content Area -->
<div class="main-content">
    <!-- Top Bar -->
    <div class="topbar">
        <h4 class="mb-0"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h4>
        <div class="d-flex align-items-center">
            <span class="me-3">
                <i class="fas fa-user-circle me-2"></i>
                <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
            </span>
            <a href="../api/admin-logout.php" class="btn btn-danger btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </a>
        </div>
    </div>
    
    <div class="admin-content">
