<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-warning text-white">
            <h2>Edit Journal Entry</h2>
        </div>
        <div class="card-body">
            <form id="journal-entry-form" action="<?= URL_ROOT ?>index.php?page=journal&action=update" method="post">
                <input type="hidden" name="journal_id" value="<?= $journal['id'] ?>">
                <input type="hidden" name="journal_title" value="Journal Entry #<?= $journal['id'] ?>">
                
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" id="add-new-table" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add New Entry Table
                    </button>
                </div>
                
                <div id="journal-tables-container">
                    <!-- Journal entry table -->
                    <div class="journal-table-section mb-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" style="max-width: 950px; margin: 0 auto;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="15%">Date</th>
                                        <th width="30%">Account Title</th>
                                        <th width="15%">Ref</th>
                                        <th width="15%">DR</th>
                                        <th width="15%">CR</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (isset($journal_details) && count($journal_details) > 0):
                                        foreach($journal_details as $index => $detail): 
                                    ?>
                                        <tr class="entry-row">
                                            <td>
                                                <input type="date" name="tables[0][entry_dates][]" class="form-control form-control-sm date-input" 
                                                       value="<?= $detail['date'] ?>">
                                            </td>
                                            <td>
                                                <input type="text" name="tables[0][accounts][]" class="form-control form-control-sm account-input" 
                                                       value="<?= htmlspecialchars($detail['account']) ?>" placeholder="Enter account title">
                                            </td>
                                            <td>
                                                <input type="text" name="tables[0][references][]" class="form-control form-control-sm reference-input" 
                                                       value="<?= htmlspecialchars($detail['reference'] ?? '') ?>" placeholder="Ref #">
                                            </td>
                                            <td>
                                                <input type="text" name="tables[0][debits][]" class="form-control form-control-sm debit-amount" 
                                                       value="<?= $detail['debit'] > 0 ? $detail['debit'] : '' ?>" placeholder="0.00">
                                            </td>
                                            <td>
                                                <input type="text" name="tables[0][credits][]" class="form-control form-control-sm credit-amount" 
                                                       value="<?= $detail['credit'] > 0 ? $detail['credit'] : '' ?>" placeholder="0.00">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-row" <?= $index === 0 && count($journal_details) === 1 ? 'disabled' : '' ?>>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php 
                                        endforeach;
                                    else: 
                                    ?>
                                        <!-- Empty row if no details exist -->
                                        <tr class="entry-row">
                                            <td>
                                                <input type="date" name="tables[0][entry_dates][]" class="form-control form-control-sm date-input" 
                                                       value="<?= $journal['date'] ?>">
                                            </td>
                                            <td>
                                                <input type="text" name="tables[0][accounts][]" class="form-control form-control-sm account-input" 
                                                       placeholder="Enter account title">
                                            </td>
                                            <td>
                                                <input type="text" name="tables[0][references][]" class="form-control form-control-sm reference-input" 
                                                       placeholder="Ref #">
                                            </td>
                                            <td>
                                                <input type="text" name="tables[0][debits][]" class="form-control form-control-sm debit-amount" 
                                                       placeholder="0.00">
                                            </td>
                                            <td>
                                                <input type="text" name="tables[0][credits][]" class="form-control form-control-sm credit-amount" 
                                                       placeholder="0.00">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-row" disabled>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right mt-2">
                            <button type="button" class="btn btn-sm btn-danger remove-table" disabled>Remove Table</button>
                        </div>
                    </div>
                </div>
                
                <div class="form-group text-center mt-4">
                    <button type="submit" id="submit-button" class="btn btn-warning">Update Journal Entry</button>
                    <a href="<?= URL_ROOT ?>index.php?page=journal" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const journalForm = document.getElementById('journal-entry-form');
    const tablesContainer = document.getElementById('journal-tables-container');
    const addTableBtn = document.getElementById('add-new-table');
    
    // Add new table functionality
    addTableBtn.addEventListener('click', function() {
        const firstTableSection = tablesContainer.querySelector('.journal-table-section');
        const newTableSection = firstTableSection.cloneNode(true);
        
        // Clear input values in the new table
        newTableSection.querySelectorAll('input').forEach(input => input.value = '');
        
        // Enable remove table button
        newTableSection.querySelector('.remove-table').disabled = false;
        
        tablesContainer.appendChild(newTableSection);
        
        // Attach event listeners to the new table
        attachTableEventListeners(newTableSection);
    });
    
    // Attach event listeners to initial tables
    document.querySelectorAll('.journal-table-section').forEach(tableSection => {
        attachTableEventListeners(tableSection);
    });
    
    // Function to attach necessary event listeners to a table section
    function attachTableEventListeners(tableSection) {
        const removeTableBtn = tableSection.querySelector('.remove-table');
        const addRowBtn = tableSection.querySelector('.add-account-row');
        const tbody = tableSection.querySelector('tbody');
        
        // Remove table functionality
        removeTableBtn.addEventListener('click', function() {
            tableSection.remove();
        });
        
        // Add new row to the table
        addRowBtn.addEventListener('click', function() {
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);
            
            // Clear input values in the new row
            newRow.querySelectorAll('input').forEach(input => input.value = '');
            
            // Enable remove row button
            newRow.querySelector('.remove-row').disabled = false;
            
            tbody.appendChild(newRow);
            
            // Attach event listeners to the new row
            attachRowEventListeners(newRow);
        });
        
        // Attach event listeners to initial rows
        tbody.querySelectorAll('.entry-row').forEach(row => {
            attachRowEventListeners(row);
        });
    }
    
    // Function to attach necessary event listeners to a row
    function attachRowEventListeners(row) {
        const removeBtn = row.querySelector('.remove-row');
        
        // Remove row functionality
        removeBtn.addEventListener('click', function() {
            row.remove();
        });
    }
    
    // Form validation - removed account title requirement
    journalForm.addEventListener('submit', function(e) {
        // No validation required - allow blank fields
        return true;
    });
});
</script>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
