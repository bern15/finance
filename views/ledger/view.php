<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Ledger Account: <?= $account_name ?></h1>
        <a href="<?= URL_ROOT ?>index.php?page=ledger" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Ledger
        </a>
    </div>
    
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3>T-Account</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th colspan="2" class="text-center"><?= $account_name ?></th>
                    </tr>
                    <tr>
                        <th class="text-center w-50">Debit</th>
                        <th class="text-center w-50">Credit</th>
                    </tr>
                    <tr>
                        <td class="align-top">
                            <?php foreach($ledger_entries as $entry): ?>
                                <?php if($entry['debit'] > 0): ?>
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <div><?= date('M d', strtotime($entry['date'])) ?></div>
                                    <div><?= number_format($entry['debit'], 2) ?></div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </td>
                        <td class="align-top">
                            <?php foreach($ledger_entries as $entry): ?>
                                <?php if($entry['credit'] > 0): ?>
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <div><?= date('M d', strtotime($entry['date'])) ?></div>
                                    <div><?= number_format($entry['credit'], 2) ?></div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </table>
            </div>
            
            <h4 class="mt-4">Account Transactions</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Credit</th>
                            <th class="text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($ledger_entries as $entry): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($entry['date'])) ?></td>
                                <td><?= $entry['description'] ?></td>
                                <td class="text-right"><?= number_format($entry['debit'], 2) ?></td>
                                <td class="text-right"><?= number_format($entry['credit'], 2) ?></td>
                                <td class="text-right"><?= number_format($entry['balance'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
