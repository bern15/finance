<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="alert alert-danger">
        <h2><i class="fas fa-exclamation-triangle"></i> Missing View File</h2>
        <p>The requested view file could not be found. This could be due to an incomplete installation or missing files.</p>
        <p>Please ensure all application files are properly installed.</p>
        <p>
            <a href="<?= URL_ROOT ?>" class="btn btn-primary">Return to Dashboard</a>
            <a href="<?= URL_ROOT ?>setup.php" class="btn btn-warning">Run Setup</a>
        </p>
    </div>
</div>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
