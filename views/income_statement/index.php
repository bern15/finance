<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Income Statement</h1>
        <div>
            <a href="<?= URL_ROOT ?>index.php?page=trial_balance" class="btn btn-secondary">
                <i class="fas fa-file-alt"></i> Trial Balance
            </a>
        </div>
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
            <h3>Income Statement</h3>
        </div>
        <div class="card-body">
            <form id="income-statement-form" action="<?= URL_ROOT ?>index.php?page=income_statement&action=store" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered" id="income-statement-table">
                        <thead>
                            <tr>
                                <th width="50%">Description</th>
                                <th width="50%">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="income-statement-body">
                            <?php 
                            // Determine how many rows to display
                            $displayRows = 10; // Default to 10 rows
                            
                            // If there's existing data, use that count but ensure at least 10 rows
                            if (!empty($incomeStatementData)) {
                                $displayRows = max(10, count($incomeStatementData));
                            }
                            
                            // Generate rows
                            for ($i = 0; $i < $displayRows; $i++): 
                                // Get data for this row if available
                                $description = isset($incomeStatementData[$i]) ? $incomeStatementData[$i]['description'] : '';
                                $amount = isset($incomeStatementData[$i]) ? $incomeStatementData[$i]['amount'] : '';
                            ?>
                                <tr class="statement-row">
                                    <td>
                                        <input type="text" name="descriptions[]" class="form-control form-control-sm description-input" value="<?= htmlspecialchars($description) ?>" placeholder="Enter description">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="amounts[]" class="form-control form-control-sm amount-input" value="<?= $amount ?>" placeholder="0.00">
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td class="text-right">Total:</td>
                                <td id="total-amount" class="text-right">0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Action Buttons -->
                <div class="text-center mt-3 mb-3">
                    <button type="button" id="add-row-btn" class="btn btn-info">
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                    <button type="button" id="calculate-btn" class="btn btn-primary ml-2">
                        <i class="fas fa-calculator"></i> Calculate Total
                    </button>
                </div>
                
                <!-- Calculation Results Section - Initially Hidden -->
                <div id="calculation-results" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4>Calculation Results</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th class="text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="results-body">
                                        <!-- Results will be added here dynamically -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold bg-light">
                                            <td class="text-right">Total Amount:</td>
                                            <td id="result-total" class="text-right">0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> Save Income Statement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('income-statement-form');
    const tableBody = document.getElementById('income-statement-body');
    const addRowBtn = document.getElementById('add-row-btn');
    const calculateBtn = document.getElementById('calculate-btn');
    const calculationResults = document.getElementById('calculation-results');
    const resultsBody = document.getElementById('results-body');
    const totalDisplay = document.getElementById('total-amount');
    const resultTotal = document.getElementById('result-total');
    
    // Add new row
    addRowBtn.addEventListener('click', function() {
        const firstRow = tableBody.querySelector('.statement-row');
        const newRow = firstRow.cloneNode(true);
        
        // Clear input values
        newRow.querySelectorAll('input').forEach(input => {
            input.value = '';
        });
        
        // Add row to table
        tableBody.appendChild(newRow);
        
        // Add event listeners to new row's amount input
        const amountInput = newRow.querySelector('.amount-input');
        amountInput.addEventListener('input', updateTotal);
    });
    
    // Calculate button - calculate and show result
    calculateBtn.addEventListener('click', function() {
        let total = calculateTotal();
        
        // Clear previous results
        resultsBody.innerHTML = '';
        
        // Add each row with description and amount to results
        let rowsAdded = 0;
        document.querySelectorAll('.statement-row').forEach(row => {
            const description = row.querySelector('.description-input').value.trim();
            const amount = parseFloat(row.querySelector('.amount-input').value) || 0;
            
            // Only add rows that have either description or amount
            if (description || amount > 0) {
                const resultRow = document.createElement('tr');
                resultRow.innerHTML = `
                    <td>${description || '<em class="text-muted">No description</em>'}</td>
                    <td class="text-right">${amount.toFixed(2)}</td>
                `;
                resultsBody.appendChild(resultRow);
                rowsAdded++;
            }
        });
        
        // If no rows were added, add a message
        if (rowsAdded === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="2" class="text-center">No data entered</td>
            `;
            resultsBody.appendChild(emptyRow);
        }
        
        // Update total in results
        resultTotal.textContent = total.toFixed(2);
        
        // Show calculation results
        calculationResults.style.display = 'block';
        
        // Scroll to results
        calculationResults.scrollIntoView({ behavior: 'smooth' });
    });
    
    // Function to calculate and update total
    function calculateTotal() {
        let total = 0;
        
        // Sum all amount inputs
        document.querySelectorAll('.amount-input').forEach(input => {
            const value = parseFloat(input.value) || 0;
            total += value;
        });
        
        // Update the total display
        totalDisplay.textContent = total.toFixed(2);
        return total;
    }
    
    // Function to update total whenever an amount changes
    function updateTotal() {
        calculateTotal();
    }
    
    // Add event listeners to all initial amount inputs
    document.querySelectorAll('.amount-input').forEach(input => {
        input.addEventListener('input', updateTotal);
    });
    
    // Initial calculation of total
    calculateTotal();
});
</script>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
