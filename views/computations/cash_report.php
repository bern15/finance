<?php require_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container mt-4">
    <h1><?php echo $data['title']; ?></h1>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Cash Account Summary</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Total Increases (Debits)</h5>
                            <h3 class="text-success">$<?php echo number_format($data['totalDebits'], 2); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Total Decreases (Credits)</h5>
                            <h3 class="text-danger">$<?php echo number_format($data['totalCredits'], 2); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Current Balance</h5>
                            <h3 class="<?php echo $data['finalBalance'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                $<?php echo number_format($data['finalBalance'], 2); ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Cash Transactions</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th class="text-end">Increases (Debits)</th>
                        <th class="text-end">Decreases (Credits)</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['transactions'] as $transaction): ?>
                        <tr>
                            <td><?php echo date('m/d/Y', strtotime($transaction['date'])); ?></td>
                            <td><?php echo htmlspecialchars($transaction['reference']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                            <td class="text-end"><?php echo $transaction['debit'] > 0 ? '$' . number_format($transaction['debit'], 2) : ''; ?></td>
                            <td class="text-end"><?php echo $transaction['credit'] > 0 ? '$' . number_format($transaction['credit'], 2) : ''; ?></td>
                            <td class="text-end">$<?php echo number_format($transaction['balance'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-dark">
                        <th colspan="3">Totals</th>
                        <th class="text-end">$<?php echo number_format($data['totalDebits'], 2); ?></th>
                        <th class="text-end">$<?php echo number_format($data['totalCredits'], 2); ?></th>
                        <th class="text-end">$<?php echo number_format($data['finalBalance'], 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
