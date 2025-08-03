<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}



?>

<?php require_once 'includes/header.php'; ?>

<div class="container-fluid bg-light min-vh-100 p-4">
    <div class="row">
        <?php include 'includes/menu.php'; ?>

        
        <main class="col">

        <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="mb-0 fw-bold">Dashboard</h2>
                </div>


                
            </div>


            <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
            
        </main>

    </div>
</div>





<?php require_once 'includes/footer.php'; ?>


