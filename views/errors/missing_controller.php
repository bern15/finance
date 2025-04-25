<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="alert alert-danger">
        <h2><i class="fas fa-exclamation-triangle"></i> Controller Not Found</h2>
        <p>The requested controller could not be found. This could be due to an incomplete installation or missing files.</p>
        <p>Please ensure all application files are properly installed.</p>
        <p>
            <a href="<?= URL_ROOT ?>" class="btn btn-primary">Return to Home</a>
            <a href="<?= URL_ROOT ?>setup.php" class="btn btn-warning">Run Setup</a>
        </p>
    </div>
    
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h3>Available Options</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fa-3x mb-3 text-primary"></i>
                            <h5>Journal Entries</h5>
                            <p>Manage your journal entries</p>
                            <a href="<?= URL_ROOT ?>index.php?page=journal" class="btn btn-primary">Go to Journal</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <i class="fas fa-book fa-3x mb-3 text-success"></i>
                            <h5>Computations</h5>
                            <p>View and perform financial computations</p>
                            <a href="<?= URL_ROOT ?>index.php?page=computations" class="btn btn-success">Go to Computations</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
