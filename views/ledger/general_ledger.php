<?php require_once ROOT_PATH . 'views/includes/header.php'; ?>

<div class="container mt-4">
    <h1>General Ledger</h1>
    
    <h3>Assets and Liabilities</h3>
    <div class="table-responsive">
        <table class="table table-bordered">
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
            <tbody>
                <!-- Cash -->
                <tr>
                    <td colspan="8" class="bg-light font-weight-bold">Cash</td>
                </tr>
                <tr>
                    <td>01/15/2023</td>
                    <td>Initial Investment</td>
                    <td>J1</td>
                    <td>250,000.00</td>
                    <td>02/28/2023</td>
                    <td>Rent Payment</td>
                    <td>J5</td>
                    <td>2,500.00</td>
                </tr>
                <tr>
                    <td>03/10/2023</td>
                    <td>Service Revenue</td>
                    <td>J7</td>
                    <td>8,500.00</td>
                    <td>04/15/2023</td>
                    <td>Utilities Payment</td>
                    <td>J9</td>
                    <td>750.00</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>05/02/2023</td>
                    <td>Office Supplies</td>
                    <td>J12</td>
                    <td>1,200.00</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>05/10/2023</td>
                    <td>Equipment Purchase</td>
                    <td>J15</td>
                    <td>9,300.00</td>
                </tr>
                
                <!-- Accounts Receivable -->
                <tr>
                    <td colspan="8" class="bg-light font-weight-bold">Accounts Receivable</td>
                </tr>
                <tr>
                    <td>04/30/2023</td>
                    <td>Client Invoice #1082</td>
                    <td>J11</td>
                    <td>3,500.00</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <!-- Office Equipment -->
                <tr>
                    <td colspan="8" class="bg-light font-weight-bold">Office Equipment</td>
                </tr>
                <tr>
                    <td>03/10/2023</td>
                    <td>Computer Equipment</td>
                    <td>J6</td>
                    <td>5,700.00</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>05/10/2023</td>
                    <td>Office Furniture</td>
                    <td>J15</td>
                    <td>9,300.00</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <!-- Accounts Payable -->
                <tr>
                    <td colspan="8" class="bg-light font-weight-bold">Accounts Payable</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>04/15/2023</td>
                    <td>Vendor Invoice #5423</td>
                    <td>J8</td>
                    <td>2,130,031.00</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <h3>Income & Expenses</h3>
    <div class="table-responsive">
        <table class="table table-bordered">
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
            <tbody>
                <!-- Service Revenue -->
                <tr>
                    <td colspan="8" class="bg-light font-weight-bold">Service Revenue</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>03/10/2023</td>
                    <td>Consulting Services</td>
                    <td>J7</td>
                    <td>8,500.00</td>
                </tr>
                
                <!-- Rent Expense -->
                <tr>
                    <td colspan="8" class="bg-light font-weight-bold">Rent Expense</td>
                </tr>
                <tr>
                    <td>02/28/2023</td>
                    <td>Monthly Office Rent</td>
                    <td>J5</td>
                    <td>2,134,631.00</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <!-- Salaries Expense -->
                <tr>
                    <td colspan="8" class="bg-light font-weight-bold">Salaries Expense</td>
                </tr>
                <tr>
                    <td>04/30/2023</td>
                    <td>Monthly Payroll</td>
                    <td>J10</td>
                    <td>187,681.00</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <!-- Supplies Expense -->
                <tr>
                    <td colspan="8" class="bg-light font-weight-bold">Supplies Expense</td>
                </tr>
                <tr>
                    <td>04/15/2023</td>
                    <td>Office Supplies</td>
                    <td>J8</td>
                    <td>2,312,312.00</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <!-- Utilities Expense -->
                <tr>
                    <td colspan="8" class="bg-light font-weight-bold">Utilities Expense</td>
                </tr>
                <tr>
                    <td>04/15/2023</td>
                    <td>Monthly Utilities</td>
                    <td>J9</td>
                    <td>750.00</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/includes/footer.php'; ?>
