<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create New Trial Balance</h1>
        <a href="<?= URL_ROOT ?>index.php?page=trial_balance" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Trial Balance
        </a>
    </div>

    <form action="<?= URL_ROOT ?>index.php?page=trial_balance&action=store" method="post">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h3>Trial Balance as of <?= date('F d, Y') ?></h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2" class="align-middle">Account</th>
                                <th colspan="2" class="text-center">Trial Balance</th>
                                <th colspan="2" class="text-center">Adjustment</th>
                                <th colspan="2" class="text-center">Adjusted Trial Balance</th>
                                <th colspan="2" class="text-center">Income Statement</th>
                                <th colspan="2" class="text-center">Balance Sheet</th>
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
                        <tbody>
                            <?php if (empty($accounts)): ?>
                                <tr>
                                    <td colspan="11" class="text-center">No accounts found. Please add accounts to the system first.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($accounts as $index => $account): ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($account['account_name']) ?>
                                            <input type="hidden" name="account_id[<?= $index ?>]" value="<?= $account['id'] ?>">
                                            <input type="hidden" name="account_name[<?= $index ?>]" value="<?= htmlspecialchars($account['account_name']) ?>">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                                   name="trial_balance_debit[<?= $index ?>]" value="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                                   name="trial_balance_credit[<?= $index ?>]" value="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                                   name="adjustment_debit[<?= $index ?>]" value="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                                   name="adjustment_credit[<?= $index ?>]" value="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                                   name="adjusted_balance_debit[<?= $index ?>]" value="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                                   name="adjusted_balance_credit[<?= $index ?>]" value="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                                   name="income_statement_debit[<?= $index ?>]" value="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                                   name="income_statement_credit[<?= $index ?>]" value="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                                   name="balance_sheet_debit[<?= $index ?>]" value="0.00">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                                   name="balance_sheet_credit[<?= $index ?>]" value="0.00">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <!-- New account row -->
                            <tr class="table-info">
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" name="new_account_name" 
                                               placeholder="Add new account...">
                                        <div class="input-group-append">
                                            <select class="form-control form-control-sm" name="new_account_type">
                                                <option value="asset">Asset</option>
                                                <option value="liability">Liability</option>
                                                <option value="equity">Equity</option>
                                                <option value="revenue">Revenue</option>
                                                <option value="expense">Expense</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                           name="new_trial_balance_debit" value="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                           name="new_trial_balance_credit" value="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                           name="new_adjustment_debit" value="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                           name="new_adjustment_credit" value="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                           name="new_adjusted_balance_debit" value="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                           name="new_adjusted_balance_credit" value="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                           name="new_income_statement_debit" value="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                           name="new_income_statement_credit" value="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                           name="new_balance_sheet_debit" value="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                           name="new_balance_sheet_credit" value="0.00">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Trial Balance
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate adjusted balance
    const trialBalanceDebitInputs = document.querySelectorAll('input[name^="trial_balance_debit"]');
    const trialBalanceCreditInputs = document.querySelectorAll('input[name^="trial_balance_credit"]');
    const adjustmentDebitInputs = document.querySelectorAll('input[name^="adjustment_debit"]');
    const adjustmentCreditInputs = document.querySelectorAll('input[name^="adjustment_credit"]');
    const adjustedBalanceDebitInputs = document.querySelectorAll('input[name^="adjusted_balance_debit"]');
    const adjustedBalanceCreditInputs = document.querySelectorAll('input[name^="adjusted_balance_credit"]');
    
    // Function to calculate and update adjusted balance
    function updateAdjustedBalance(index) {
        const trialBalanceDebit = parseFloat(trialBalanceDebitInputs[index].value) || 0;
        const trialBalanceCredit = parseFloat(trialBalanceCreditInputs[index].value) || 0;
        const adjustmentDebit = parseFloat(adjustmentDebitInputs[index].value) || 0;
        const adjustmentCredit = parseFloat(adjustmentCreditInputs[index].value) || 0;
        
        // Calculate adjusted balance
        let adjustedDebit = trialBalanceDebit + adjustmentDebit;
        let adjustedCredit = trialBalanceCredit + adjustmentCredit;
        
        // Update the adjusted balance inputs
        adjustedBalanceDebitInputs[index].value = adjustedDebit.toFixed(2);
        adjustedBalanceCreditInputs[index].value = adjustedCredit.toFixed(2);
    }
    
    // Add event listeners to trial balance and adjustment inputs
    for (let i = 0; i < trialBalanceDebitInputs.length; i++) {
        trialBalanceDebitInputs[i].addEventListener('input', function() {
            updateAdjustedBalance(i);
        });
        
        trialBalanceCreditInputs[i].addEventListener('input', function() {
            updateAdjustedBalance(i);
        });
        
        adjustmentDebitInputs[i].addEventListener('input', function() {
            updateAdjustedBalance(i);
        });
        
        adjustmentCreditInputs[i].addEventListener('input', function() {
            updateAdjustedBalance(i);
        });
    }
    
    // Add similar behavior for the new account row
    const newTrialBalanceDebit = document.querySelector('input[name="new_trial_balance_debit"]');
    const newTrialBalanceCredit = document.querySelector('input[name="new_trial_balance_credit"]');
    const newAdjustmentDebit = document.querySelector('input[name="new_adjustment_debit"]');
    const newAdjustmentCredit = document.querySelector('input[name="new_adjustment_credit"]');
    const newAdjustedBalanceDebit = document.querySelector('input[name="new_adjusted_balance_debit"]');
    const newAdjustedBalanceCredit = document.querySelector('input[name="new_adjusted_balance_credit"]');
    
    function updateNewAccountAdjustedBalance() {
        const trialBalanceDebit = parseFloat(newTrialBalanceDebit.value) || 0;
        const trialBalanceCredit = parseFloat(newTrialBalanceCredit.value) || 0;
        const adjustmentDebit = parseFloat(newAdjustmentDebit.value) || 0;
        const adjustmentCredit = parseFloat(newAdjustmentCredit.value) || 0;
        
        // Calculate adjusted balance
        let adjustedDebit = trialBalanceDebit + adjustmentDebit;
        let adjustedCredit = trialBalanceCredit + adjustmentCredit;
        
        // Update the adjusted balance inputs
        newAdjustedBalanceDebit.value = adjustedDebit.toFixed(2);
        newAdjustedBalanceCredit.value = adjustedCredit.toFixed(2);
    }
    
    newTrialBalanceDebit.addEventListener('input', updateNewAccountAdjustedBalance);
    newTrialBalanceCredit.addEventListener('input', updateNewAccountAdjustedBalance);
    newAdjustmentDebit.addEventListener('input', updateNewAccountAdjustedBalance);
    newAdjustmentCredit.addEventListener('input', updateNewAccountAdjustedBalance);
});
</script>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
