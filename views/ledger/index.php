<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>General Ledger</h1>
        <div>
            <a href="<?= URL_ROOT ?>index.php?page=journal" class="btn btn-secondary">
                <i class="fas fa-file-alt"></i> Journal Entries
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
    
    <!-- Assets and Liabilities Table -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h3>Assets and Liabilities</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover ledger-table">
                    <thead class="thead-dark">
                        <tr>
                            <th colspan="4" class="text-center border-right">DEBIT SIDE</th>
                            <th colspan="4" class="text-center">CREDIT SIDE</th>
                        </tr>
                        <tr>
                            <th width="10%">Date</th>
                            <th width="27%">Explanation</th>
                            <th width="5%">F</th>
                            <th width="8%" class="text-right border-right">DR</th>
                            <th width="10%">Date</th>
                            <th width="27%">Explanation</th>
                            <th width="5%">F</th>
                            <th width="8%" class="text-right">CR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Cash -->
                        <tr>
                            <td colspan="8" class="bg-light font-weight-bold text-center account-header">Cash</td>
                        </tr>
                        <tr>
                            <td>01/15/2023</td>
                            <td>Initial Investment</td>
                            <td class="text-center">J1</td>
                            <td class="text-right border-right">250,000.00</td>
                            <td>02/28/2023</td>
                            <td>Rent Payment</td>
                            <td class="text-center">J5</td>
                            <td class="text-right">2,500.00</td>
                        </tr>
                        <tr>
                            <td>03/10/2023</td>
                            <td>Service Revenue</td>
                            <td class="text-center">J7</td>
                            <td class="text-right border-right">8,500.00</td>
                            <td>04/15/2023</td>
                            <td>Utilities Payment</td>
                            <td class="text-center">J9</td>
                            <td class="text-right">750.00</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="border-right"></td>
                            <td>05/02/2023</td>
                            <td>Office Supplies</td>
                            <td class="text-center">J12</td>
                            <td class="text-right">1,200.00</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="border-right"></td>
                            <td>05/10/2023</td>
                            <td>Equipment Purchase</td>
                            <td class="text-center">J15</td>
                            <td class="text-right">9,300.00</td>
                        </tr>
                        
                        <!-- Accounts Receivable -->
                        <tr>
                            <td colspan="8" class="bg-light font-weight-bold text-center account-header">Accounts Receivable</td>
                        </tr>
                        <tr>
                            <td>04/30/2023</td>
                            <td>Client Invoice #1082</td>
                            <td class="text-center">J11</td>
                            <td class="text-right border-right">3,500.00</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        
                        <!-- Office Equipment -->
                        <tr>
                            <td colspan="8" class="bg-light font-weight-bold text-center account-header">Office Equipment</td>
                        </tr>
                        <tr>
                            <td>03/10/2023</td>
                            <td>Computer Equipment</td>
                            <td class="text-center">J6</td>
                            <td class="text-right border-right">5,700.00</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>05/10/2023</td>
                            <td>Office Furniture</td>
                            <td class="text-center">J15</td>
                            <td class="text-right border-right">9,300.00</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        
                        <!-- Accounts Payable -->
                        <tr>
                            <td colspan="8" class="bg-light font-weight-bold text-center account-header">Accounts Payable</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="border-right"></td>
                            <td>04/15/2023</td>
                            <td>Vendor Invoice #5423</td>
                            <td class="text-center">J8</td>
                            <td class="text-right">2,130,031.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Income & Expenses Table -->
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h3>Income & Expenses</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover ledger-table">
                    <thead class="thead-dark">
                        <tr>
                            <th colspan="4" class="text-center border-right">DEBIT SIDE</th>
                            <th colspan="4" class="text-center">CREDIT SIDE</th>
                        </tr>
                        <tr>
                            <th width="10%">Date</th>
                            <th width="27%">Explanation</th>
                            <th width="5%">F</th>
                            <th width="8%" class="text-right border-right">DR</th>
                            <th width="10%">Date</th>
                            <th width="27%">Explanation</th>
                            <th width="5%">F</th>
                            <th width="8%" class="text-right">CR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Service Revenue -->
                        <tr>
                            <td colspan="8" class="bg-light font-weight-bold text-center account-header">Service Revenue</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="border-right"></td>
                            <td>03/10/2023</td>
                            <td>Consulting Services</td>
                            <td class="text-center">J7</td>
                            <td class="text-right">8,500.00</td>
                        </tr>
                        
                        <!-- Rent Expense -->
                        <tr>
                            <td colspan="8" class="bg-light font-weight-bold text-center account-header">Rent Expense</td>
                        </tr>
                        <tr>
                            <td>02/28/2023</td>
                            <td>Monthly Office Rent</td>
                            <td class="text-center">J5</td>
                            <td class="text-right border-right">2,134,631.00</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        
                        <!-- Salaries Expense -->
                        <tr>
                            <td colspan="8" class="bg-light font-weight-bold text-center account-header">Salaries Expense</td>
                        </tr>
                        <tr>
                            <td>04/30/2023</td>
                            <td>Monthly Payroll</td>
                            <td class="text-center">J10</td>
                            <td class="text-right border-right">187,681.00</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        
                        <!-- Supplies Expense -->
                        <tr>
                            <td colspan="8" class="bg-light font-weight-bold text-center account-header">Supplies Expense</td>
                        </tr>
                        <tr>
                            <td>04/15/2023</td>
                            <td>Office Supplies</td>
                            <td class="text-center">J8</td>
                            <td class="text-right border-right">2,312,312.00</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        
                        <!-- Utilities Expense -->
                        <tr>
                            <td colspan="8" class="bg-light font-weight-bold text-center account-header">Utilities Expense</td>
                        </tr>
                        <tr>
                            <td>04/15/2023</td>
                            <td>Monthly Utilities</td>
                            <td class="text-center">J9</td>
                            <td class="text-right border-right">750.00</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
