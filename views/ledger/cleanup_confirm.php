<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirm Ledger Cleanup</h2>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <h4>Warning! This action cannot be undone.</h4>
                <p>You are about to remove all test/dummy accounts from your general ledger. This operation will:</p>
                <ul>
                    <li>Keep only standard accounts (Cash, Accounts Receivable, etc.)</li>
                    <li>Delete all other accounts (including those with numeric names or random characters)</li>
                    <li>Any transactions associated with deleted accounts may become orphaned</li>
                </ul>
                <p>The following standard accounts will be kept:</p>
                <ul>
                    <li>Cash</li>
                    <li>Accounts Receivable</li>
                    <li>Office Equipment</li>
                    <li>Office Supplies</li>
                    <li>Accounts Payable</li>
                    <li>Notes Payable</li>
                    <li>Common Stock</li>
                    <li>Retained Earnings</li>
                    <li>Service Revenue</li>
                    <li>Rent Expense</li>
                    <li>Supplies Expense</li>
                    <li>Salaries Expense</li>
                    <li>Utilities Expense</li>
                </ul>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="<?= URL_ROOT ?>index.php?page=ledger" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <a href="<?= URL_ROOT ?>index.php?page=ledger&action=cleanup" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Proceed with Cleanup
                </a>
            </div>
        </div>
    </div>
</div>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
