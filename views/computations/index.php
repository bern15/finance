<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Accounts Title</h1>
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
            <h3>Accounts Title</h3>
        </div>
        <div class="card-body">
            <form id="computation-form" action="<?= URL_ROOT ?>index.php?page=computations&action=save" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered" id="computation-table">
                        <thead>
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
                        <tbody id="computation-tbody">
                            <!-- Initial row -->
                            <tr class="computation-row">
                                <td><input type="date" name="debit[date][]" class="form-control form-control-sm debit-date" placeholder="mm/dd/yyyy"></td>
                                <td><input type="text" name="debit[explanation][]" class="form-control form-control-sm debit-explanation"></td>
                                <td><input type="text" name="debit[f][]" class="form-control form-control-sm debit-f"></td>
                                <td><input type="number" step="0.01" min="0" name="debit[amount][]" class="form-control form-control-sm dr-amount" placeholder="0.00"></td>
                                <td><input type="date" name="credit[date][]" class="form-control form-control-sm credit-date" placeholder="mm/dd/yyyy"></td>
                                <td><input type="text" name="credit[explanation][]" class="form-control form-control-sm credit-explanation"></td>
                                <td><input type="text" name="credit[f][]" class="form-control form-control-sm credit-f"></td>
                                <td><input type="number" step="0.01" min="0" name="credit[amount][]" class="form-control form-control-sm cr-amount" placeholder="0.00"></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Table Total</th>
                                <th id="dr-total" class="text-end">0.00</th>
                                <th colspan="3" class="text-end">Table Total</th>
                                <th id="cr-total" class="text-end">0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Add Row Button -->
                <div class="text-center mt-2 mb-3">
                    <button type="button" id="add-row-btn" class="btn btn-info">
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>
                
                <!-- Action Buttons -->
                <div class="text-center mt-3">
                    <button type="button" id="clear-button" class="btn btn-warning btn-lg">
                        <i class="fas fa-eraser mr-2"></i> Clear
                    </button>
                    <button type="button" id="compute-button" class="btn btn-primary btn-lg ml-2">
                        <i class="fas fa-calculator mr-2"></i> Compute
                    </button>
                    <button type="submit" id="save-computation-button" class="btn btn-success btn-lg ml-2" style="display: none;">
                        <i class="fas fa-save mr-2"></i> Save Computation
                    </button>
                </div>

                <!-- Hidden fields for saving computation -->
                <input type="hidden" name="dr_value" id="dr_value" value="0">
                <input type="hidden" name="cr_value" id="cr_value" value="0">
                <input type="hidden" name="result" id="result_value" value="0">
            </form>
            
            <!-- Computation History -->
            <div id="computation-history" class="mt-4" style="display: none;">
                <h4>Computation History</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">DEBIT SIDE</th>
                                <th class="text-center">CREDIT SIDE</th>
                                <th class="text-center">RESULT</th>
                            </tr>
                        </thead>
                        <tbody id="history-body">
                            <!-- Computation history will be added here by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Saved Computations -->
            <?php if(!empty($computations)): ?>
            <div class="mt-4">
                <h4>Saved Computations</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">DEBIT SIDE</th>
                                <th class="text-center">CREDIT SIDE</th>
                                <th class="text-center">RESULT</th>
                                <th class="text-center">DATE</th>
                                <th class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($computations as $comp): ?>
                            <tr>
                                <td class="text-center"><?= number_format($comp['dr_value'], 2) ?></td>
                                <td class="text-center"><?= number_format($comp['cr_value'], 2) ?></td>
                                <td class="text-center"><?= number_format($comp['result'], 2) ?></td>
                                <td class="text-center"><?= date('m/d/Y H:i', strtotime($comp['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="<?= URL_ROOT ?>index.php?page=computations&action=delete&id=<?= $comp['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this computation?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('computation-form');
    const computeButton = document.getElementById('compute-button');
    const clearButton = document.getElementById('clear-button');
    const addRowBtn = document.getElementById('add-row-btn');
    const saveComputationButton = document.getElementById('save-computation-button');
    const historyTable = document.getElementById('computation-history');
    const historyBody = document.getElementById('history-body');
    const drValueInput = document.getElementById('dr_value');
    const crValueInput = document.getElementById('cr_value');
    const resultValueInput = document.getElementById('result_value');
    const tbody = document.getElementById('computation-tbody');
    
    // Add row functionality
    addRowBtn.addEventListener('click', function() {
        const firstRow = document.querySelector('.computation-row');
        const newRow = firstRow.cloneNode(true);
        
        // Clear all inputs in the new row
        newRow.querySelectorAll('input').forEach(input => {
            input.value = '';
        });
        
        tbody.appendChild(newRow);
        
        // Add event listeners to the new row's inputs
        newRow.querySelectorAll('.dr-amount, .cr-amount').forEach(input => {
            input.addEventListener('input', updateTotals);
            
            // Format number when focus is lost
            input.addEventListener('blur', function() {
                if (this.value.trim() !== '') {
                    const value = parseFloat(this.value);
                    if (!isNaN(value)) {
                        this.value = value.toFixed(2);
                    }
                }
                updateTotals();
            });
        });
        
        // Update totals
        updateTotals();
    });
    
    // Clear button functionality
    clearButton.addEventListener('click', function() {
        // Clear all input fields
        document.querySelectorAll('.dr-amount, .cr-amount').forEach(input => {
            input.value = '';
        });
        
        // Update totals after clearing
        updateTotals();

        // Hide save button and history table
        saveComputationButton.style.display = 'none';
        historyTable.style.display = 'none';
    });
    
    // Function to calculate and update totals
    function updateTotals() {
        let drTotal = 0;
        let crTotal = 0;
        
        // Calculate DR total
        document.querySelectorAll('.dr-amount').forEach(input => {
            const value = parseFloat(input.value) || 0;
            drTotal += value;
        });
        
        // Calculate CR total
        document.querySelectorAll('.cr-amount').forEach(input => {
            const value = parseFloat(input.value) || 0;
            crTotal += value;
        });
        
        // Update totals display
        document.getElementById('dr-total').textContent = drTotal.toFixed(2);
        document.getElementById('cr-total').textContent = crTotal.toFixed(2);
        
        return { dr: drTotal, cr: crTotal };
    }
    
    // Add event listeners to all amount inputs
    document.querySelectorAll('.dr-amount, .cr-amount').forEach(input => {
        input.addEventListener('input', updateTotals);
        
        // Format number when focus is lost
        input.addEventListener('blur', function() {
            if (this.value.trim() !== '') {
                const value = parseFloat(this.value);
                if (!isNaN(value)) {
                    this.value = value.toFixed(2);
                }
            }
            updateTotals();
        });
    });
    
    // Initial calculation of totals
    updateTotals();
    
    // Handle compute button click
    computeButton.addEventListener('click', function() {
        // Get the current totals
        const totals = updateTotals();
        
        // Find highest and lowest values
        const highest = Math.max(totals.dr, totals.cr);
        const lowest = Math.min(totals.dr, totals.cr);
        const difference = highest - lowest;
        
        // Set values in hidden inputs for form submission
        drValueInput.value = totals.dr.toFixed(2);
        crValueInput.value = totals.cr.toFixed(2);
        resultValueInput.value = difference.toFixed(2);
        
        // Add to history
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td class="text-center">${totals.dr.toFixed(2)}</td>
            <td class="text-center">${totals.cr.toFixed(2)}</td>
            <td class="text-center">${difference.toFixed(2)}</td>
        `;
        
        historyBody.innerHTML = ''; // Clear previous results
        historyBody.appendChild(newRow);
        
        // Show history section and save button
        historyTable.style.display = 'block';
        saveComputationButton.style.display = 'inline-block';
    });
});
</script>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
