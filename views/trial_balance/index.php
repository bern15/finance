<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Trial Balance</h1>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $_SESSION['success_message'] ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $_SESSION['error_message'] ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3>Trial Balance as of <?= date('F d, Y') ?></h3>
        </div>
        <div class="card-body">
            <form id="trial-balance-form" action="<?= URL_ROOT ?>index.php?page=trial_balance&action=store" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="trial-balance-table">
                        <thead>
                            <tr>
                                <th rowspan="2" style="vertical-align: middle;">Account</th>
                                <th colspan="2" class="text-center">Trial Balance</th>
                                <th colspan="2" class="text-center">Adjustment</th>
                                <th colspan="2" class="text-center">Adjusted Trial Balance</th>
                                <th colspan="2" class="text-center">Income Statement</th>
                                <th colspan="2" class="text-center">Balance Sheet</th>
                                <th rowspan="2" style="vertical-align: middle;">Actions</th>
                            </tr>
                            <tr>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Credit</th>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Credit</th>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Credit</th>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Credit</th>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Credit</th>
                            </tr>
                        </thead>
                        <tbody id="trial-balance-body">
                            <?php if (empty($trialBalanceData)): ?>
                                <!-- Initial empty row -->
                                <tr class="account-row">
                                    <td>
                                        <input type="text" name="accounts[]" class="form-control form-control-sm account-input" placeholder="Enter account name">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="trial_balance_debit[]" class="form-control form-control-sm tb-debit" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="trial_balance_credit[]" class="form-control form-control-sm tb-credit" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="adjustment_debit[]" class="form-control form-control-sm adj-debit" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="adjustment_credit[]" class="form-control form-control-sm adj-credit" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="adjusted_balance_debit[]" class="form-control form-control-sm atb-debit" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="adjusted_balance_credit[]" class="form-control form-control-sm atb-credit" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="income_statement_debit[]" class="form-control form-control-sm is-debit" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="income_statement_credit[]" class="form-control form-control-sm is-credit" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="balance_sheet_debit[]" class="form-control form-control-sm bs-debit" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="balance_sheet_credit[]" class="form-control form-control-sm bs-credit" placeholder="0.00">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger remove-row" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($trialBalanceData as $index => $entry): ?>
                                    <tr class="account-row">
                                        <td>
                                            <input type="text" name="accounts[]" class="form-control form-control-sm account-input" value="<?= htmlspecialchars($entry['account_name']) ?>" placeholder="Enter account name">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="trial_balance_debit[]" class="form-control form-control-sm tb-debit" value="<?= $entry['trial_balance_debit'] ?>" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="trial_balance_credit[]" class="form-control form-control-sm tb-credit" value="<?= $entry['trial_balance_credit'] ?>" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="adjustment_debit[]" class="form-control form-control-sm adj-debit" value="<?= $entry['adjustment_debit'] ?>" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="adjustment_credit[]" class="form-control form-control-sm adj-credit" value="<?= $entry['adjustment_credit'] ?>" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="adjusted_balance_debit[]" class="form-control form-control-sm atb-debit" value="<?= $entry['adjusted_balance_debit'] ?>" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="adjusted_balance_credit[]" class="form-control form-control-sm atb-credit" value="<?= $entry['adjusted_balance_credit'] ?>" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="income_statement_debit[]" class="form-control form-control-sm is-debit" value="<?= $entry['income_statement_debit'] ?>" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="income_statement_credit[]" class="form-control form-control-sm is-credit" value="<?= $entry['income_statement_credit'] ?>" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="balance_sheet_debit[]" class="form-control form-control-sm bs-debit" value="<?= $entry['balance_sheet_debit'] ?>" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="balance_sheet_credit[]" class="form-control form-control-sm bs-credit" value="<?= $entry['balance_sheet_credit'] ?>" placeholder="0.00">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-row" <?= ($index === 0 && count($trialBalanceData) === 1) ? 'disabled' : '' ?>>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td>Totals</td>
                                <td id="total-tb-debit" class="text-right">0.00</td>
                                <td id="total-tb-credit" class="text-right">0.00</td>
                                <td id="total-adj-debit" class="text-right">0.00</td>
                                <td id="total-adj-credit" class="text-right">0.00</td>
                                <td id="total-atb-debit" class="text-right">0.00</td>
                                <td id="total-atb-credit" class="text-right">0.00</td>
                                <td id="total-is-debit" class="text-right">0.00</td>
                                <td id="total-is-credit" class="text-right">0.00</td>
                                <td id="total-bs-debit" class="text-right">0.00</td>
                                <td id="total-bs-credit" class="text-right">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-3 mb-3">
                    <button type="button" id="add-row-btn" class="btn btn-info">
                        <i class="fas fa-plus"></i> Add Account Row
                    </button>
                    <button type="button" id="calculate-all-btn" class="btn btn-primary ml-2">
                        <i class="fas fa-calculator"></i> Calculate All Columns
                    </button>
                </div>
                
                <!-- Grand Total Result Display - Initially Hidden -->
                <div id="calculation-results" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4>Grand Total</h4>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <h3 id="grand-total" class="font-weight-bold">0.00</h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> Save Trial Balance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('trial-balance-form');
    const tableBody = document.getElementById('trial-balance-body');
    const addRowBtn = document.getElementById('add-row-btn');
    const calculateAllBtn = document.getElementById('calculate-all-btn');
    const calculationResults = document.getElementById('calculation-results');
    
    // Add new row
    addRowBtn.addEventListener('click', function() {
        const firstRow = tableBody.querySelector('.account-row');
        const newRow = firstRow.cloneNode(true);
        
        // Clear all inputs in the new row
        newRow.querySelectorAll('input').forEach(input => {
            input.value = '';
        });
        
        // Enable remove button for the new row
        newRow.querySelector('.remove-row').disabled = false;
        
        tableBody.appendChild(newRow);
        
        // Add event listeners to the new row
        attachRowListeners(newRow);
        
        // Update totals after adding new row
        updateTotals();
    });
    
    // Calculate all columns button - now just shows grand total without auto-filling
    calculateAllBtn.addEventListener('click', function() {
        updateTotals();
        showGrandTotal();
    });
    
    // Function to show grand total calculation
    function showGrandTotal() {
        // Show the results section
        calculationResults.style.display = 'block';
        
        // Calculate grand total by adding ALL debits and credits together
        let grandTotal = 0;
        
        // Sum all debit columns
        document.querySelectorAll('.tb-debit, .adj-debit, .atb-debit, .is-debit, .bs-debit').forEach(input => {
            grandTotal += parseFloat(input.value) || 0;
        });
        
        // Add all credit columns to the same total
        document.querySelectorAll('.tb-credit, .adj-credit, .atb-credit, .is-credit, .bs-credit').forEach(input => {
            grandTotal += parseFloat(input.value) || 0;
        });
        
        // Update the display with the combined grand total
        document.getElementById('grand-total').textContent = grandTotal.toFixed(2);
        
        // Scroll to the results section
        calculationResults.scrollIntoView({ behavior: 'smooth' });
    }
    
    // Function to attach listeners to a row
    function attachRowListeners(row) {
        const removeBtn = row.querySelector('.remove-row');
        
        // Remove row functionality
        removeBtn.addEventListener('click', function() {
            row.remove();
            updateTotals();
        });
        
        // Add input event listeners to all number fields for automatic updating
        row.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', updateTotals);
        });
    }
    
    // Update column totals
    function updateTotals() {
        const columns = [
            { inputClass: '.tb-debit', totalId: 'total-tb-debit' },
            { inputClass: '.tb-credit', totalId: 'total-tb-credit' },
            { inputClass: '.adj-debit', totalId: 'total-adj-debit' },
            { inputClass: '.adj-credit', totalId: 'total-adj-credit' },
            { inputClass: '.atb-debit', totalId: 'total-atb-debit' },
            { inputClass: '.atb-credit', totalId: 'total-atb-credit' },
            { inputClass: '.is-debit', totalId: 'total-is-debit' },
            { inputClass: '.is-credit', totalId: 'total-is-credit' },
            { inputClass: '.bs-debit', totalId: 'total-bs-debit' },
            { inputClass: '.bs-credit', totalId: 'total-bs-credit' }
        ];
        
        columns.forEach(column => {
            const inputs = document.querySelectorAll(column.inputClass);
            let total = 0;
            
            inputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            
            document.getElementById(column.totalId).textContent = total.toFixed(2);
        });
        
        // Update grand total if already visible
        if (calculationResults.style.display !== 'none') {
            showGrandTotal();
        }
    }
    
    // Attach listeners to existing rows
    document.querySelectorAll('.account-row').forEach(row => {
        attachRowListeners(row);
    });
    
    // Initial calculation of totals
    updateTotals();
});
</script>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
