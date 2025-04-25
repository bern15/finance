<?php require_once ROOT_PATH . 'views/includes/header.php'; ?>

<div class="container mt-4">
    <h1><?php echo $data['title']; ?></h1>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><?php echo $data['account_name'] ?? 'Account'; ?></h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th colspan="4" class="text-center">DEBIT SIDE</th>
                        <th colspan="4" class="text-center">CREDIT SIDE</th>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <th>Explanation</th>
                        <th>F</th>
                        <th>DR</th>
                        <th>Date</th>
                        <th>Explanation</th>
                        <th>F</th>
                        <th>CR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($data['rows']) && $data['rows'] > 0): ?>
                        <?php for ($i = 0; $i < $data['rows']; $i++): ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php endfor; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No data to display</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th></th>
                        <th colspan="3" class="text-end">Total</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/includes/footer.php'; ?>
