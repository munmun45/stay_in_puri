<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar" style="margin: 0px;padding: 0px;     box-shadow: unset;">
    <div class="sidebar-brand p-4 text-center" style="height: 70px; padding: 0px !important; background-color: #ffffff;">
        <a href="dashboard.php" class="d-inline-block " style="    width: 100%;">
            <img src="img/somas-logo.png" alt="Somas Fleet" height="40" >
            
        </a>
    </div>
    <hr style="margin-top: 0px;">
    <ul class="sidebar-menu" style="    padding: 0px 15px;">
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
 
        
        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>" href="users.php">
                <i class="fas fa-users-cog"></i>
                <span>Users</span>
            </a>
        </li>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'sponsors.php' ? 'active' : ''; ?>" href="sponsors.php">
                <i class="fas fa-star"></i>
                <span>Sponsors</span>
            </a>
        </li>
        <?php endif; ?>
        

        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'vehicles.php' ? 'active' : ''; ?>" href="vehicles.php">
                <i class="fas fa-car"></i>
                <span>Vehicles</span>
            </a>
        </li>
        
        
      


        

        

       
    </ul>
</aside>

<script>
// Toggle sidebar on mobile
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            document.body.classList.toggle('sidebar-open');
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 991.98) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        }
    });
    
    // Prevent dropdown from closing when clicking inside
    document.querySelectorAll('.dropdown-menu').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});
</script>

<style>
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        background: #49467b;
        color: white;
        padding: 20px;
        z-index: 100000000;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        color: white;
        text-decoration: none;
        font-weight: bold;
    }

    .sidebar-logo {
        height: 30px;
        margin-right: 10px;
    }

    .sidebar-nav {
        margin-bottom: 30px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
    }

    .nav-link.active {
        color: white;
        background: rgba(255, 255, 255, 0.2);
    }

    .nav-link i {
        width: 20px;
        text-align: center;
    }

    .sidebar-footer {
        position: absolute;
        bottom: 20px;
        width: 100%;
    }
</style>
