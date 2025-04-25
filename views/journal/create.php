<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2>Create Journal Entry</h2>
        </div>
        <div class="card-body">
            <form id="journal-entry-form" action="<?= URL_ROOT ?>index.php?page=journal&action=store" method="post">
                
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" id="add-new-table" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add New Entry Table
                    </button>
                </div>
                
                <div id="journal-tables-container">
                    <!-- First journal entry table -->
                    <div class="journal-table-section mb-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" style="max-width: 950px; margin: 0 auto;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="15%">Date</th>
                                        <th width="30%">Account Title</th>
                                        <th width="15%">Ref</th>
                                        <th width="20%">DR</th>
                                        <th width="20%">CR</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Initial empty row -->
                                    <tr class="entry-row">
                                        <td>
                                            <input type="date" name="tables[0][entry_dates][]" class="form-control form-control-sm date-input" value="<?= date('Y-m-d') ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="tables[0][accounts][]" class="form-control form-control-sm account-input" placeholder="Enter account title">
                                        </td>
                                        <td>
                                            <input type="text" name="tables[0][references][]" class="form-control form-control-sm reference-input" placeholder="Ref #">
                                        </td>
                                        <td>
                                            <input type="text" name="tables[0][debits][]" class="form-control form-control-sm debit-amount" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="text" name="tables[0][credits][]" class="form-control form-control-sm credit-amount" placeholder="0.00">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-row" disabled><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>

                                    <tr class="entry-row">
                                        <td>
                                            
                                        </td>
                                        <td>
                                            <input type="text" name="tables[0][accounts][]" class="form-control form-control-sm account-input" placeholder="Enter account title">
                                        </td>
                                        <td>
                                            <input type="text" name="tables[0][references][]" class="form-control form-control-sm reference-input" placeholder="Ref #">
                                        </td>
                                        <td>
                                            <input type="text" name="tables[0][debits][]" class="form-control form-control-sm debit-amount" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="text" name="tables[0][credits][]" class="form-control form-control-sm credit-amount" placeholder="0.00">
                                        </td>
                                        <td>
                                            
                                        </td>
                                    </tr>

                                    <tr class="entry-row">
                                        <td>
                                            
                                        </td>
                                        <td>
                                            <input type="text" name="tables[0][accounts][]" class="form-control form-control-sm account-input" placeholder="Enter account title">
                                        </td>
                                        <td>
                                            <input type="text" name="tables[0][references][]" class="form-control form-control-sm reference-input" placeholder="Ref #">
                                        </td>
                                        <td>
                                            <input type="text" name="tables[0][debits][]" class="form-control form-control-sm debit-amount" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="text" name="tables[0][credits][]" class="form-control form-control-sm credit-amount" placeholder="0.00">
                                        </td>
                                        <td>
                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right mt-2">
                            <button type="button" class="btn btn-sm btn-danger remove-table" disabled>Remove Table</button>
                        </div>
                    </div>
                </div>
                
                <div class="form-group text-center mt-4">
                    <button type="submit" id="submit-button" class="btn btn-primary">Create Journal Entry</button>
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
    
    // Add new table to the journal tables container
    addTableBtn.addEventListener('click', function() {
        const tableSections = tablesContainer.querySelectorAll('.journal-table-section');
        const newTableIndex = tableSections.length;
        const firstTableSection = tableSections[0];
        const newTableSection = firstTableSection.cloneNode(true);
        
        // Update name attributes for the new table
        newTableSection.querySelectorAll('input').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const updatedName = name.replace(/\[0\]/, `[${newTableIndex}]`);
                input.setAttribute('name', updatedName);
            }
            // Set default date on date fields
            if (input.classList.contains('date-input')) {
                input.value = new Date().toISOString().split('T')[0];
            } else {
                input.value = '';
            }
        });
        
        // Enable remove button for the new table
        newTableSection.querySelector('.remove-table').disabled = false;
        
        tablesContainer.appendChild(newTableSection);
        
        // Attach event listeners to the new table section
        attachTableEventListeners(newTableSection);
    });
    
    // Format number inputs to show two decimal places
    document.querySelectorAll('.debit-amount, .credit-amount').forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() !== '') {
                const value = parseFloat(this.value.replace(/,/g, ''));
                if (!isNaN(value)) {
                    this.value = value.toFixed(2);
                }
            }
        });
    });
    
    // Attach event listeners to initial table section
    document.querySelectorAll('.journal-table-section').forEach(section => {
        attachTableEventListeners(section);
    });
    
    // Function to attach necessary event listeners to a table section
    function attachTableEventListeners(section) {
        const addRowBtn = section.querySelector('.add-account-row');
        const removeTableBtn = section.querySelector('.remove-table');
        const tbody = section.querySelector('tbody');
        
        // Add new row to the table
        addRowBtn.addEventListener('click', function() {
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);
            
            // Clear input values
            newRow.querySelectorAll('input').forEach(input => input.value = '');
            
            // Enable remove button for the new row
            newRow.querySelector('.remove-row').disabled = false;
            
            tbody.appendChild(newRow);
            
            // Attach event listeners to the new row
            attachRowEventListeners(newRow);
        });
        
        // Remove table functionality
        removeTableBtn.addEventListener('click', function() {
            section.remove();
        });
        
        // Attach event listeners to initial rows in the table
        section.querySelectorAll('.entry-row').forEach(row => {
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
    
    // Form validation - ensure at least one row has data
    journalForm.addEventListener('submit', function(e) {
        let hasData = false;
        
        // Check if at least one row has account data
        const accountInputs = document.querySelectorAll('.account-input');
        accountInputs.forEach(input => {
            if (input.value.trim() !== '') {
                hasData = true;
            }
        });
        
        // If no data, prevent form submission
        if (!hasData) {
            e.preventDefault();
            alert('Please enter at least one account entry before submitting.');
            return false;
        }
        
        return true;
    });
});
</script>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
