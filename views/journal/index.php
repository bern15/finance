<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Journal Entries</h1>
        <div>
            <a href="<?= URL_ROOT ?>index.php?page=journal&action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Entry
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $success_message ?>
                </div>
            <?php endif; ?>
            
            <div id="journal-entries-container">
                <?php if(empty($entries)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i> No journal entries to display. Create a new entry by clicking the "New Entry" button.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="12%">Date</th>
                                    <th width="35%">Account Title</th>
                                    <th width="10%">Ref</th>
                                    <th width="15%" class="text-right">DR</th>
                                    <th width="15%" class="text-right">CR</th>
                                    <th width="13%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($entries as $entry): ?>
                                    <?php if(!empty($entry['details'])): ?>
                                        <?php 
                                        // Display only the first row with date and actions
                                        $firstDetail = $entry['details'][0];
                                        ?>
                                        <tr>
                                            <td rowspan="<?= count($entry['details']) ?>">
                                                <?= date('m/d/Y', strtotime($entry['date'])) ?>
                                            </td>
                                            <td><?= htmlspecialchars($firstDetail['account'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($firstDetail['reference'] ?? '') ?></td>
                                            <td class="text-right"><?= $firstDetail['debit'] > 0 ? number_format($firstDetail['debit'], 2) : '&mdash;' ?></td>
                                            <td class="text-right"><?= $firstDetail['credit'] > 0 ? number_format($firstDetail['credit'], 2) : '&mdash;' ?></td>
                                            <td rowspan="<?= count($entry['details']) ?>" class="text-center">
                                                <div class="btn-group">
                                                    <a href="<?= URL_ROOT ?>index.php?page=journal&action=view&id=<?= $entry['id'] ?>" 
                                                       class="btn btn-sm btn-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= URL_ROOT ?>index.php?page=journal&action=edit&id=<?= $entry['id'] ?>" 
                                                       class="btn btn-sm btn-warning" title="Edit Entry">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= URL_ROOT ?>index.php?page=journal&action=delete&id=<?= $entry['id'] ?>" 
                                                       class="btn btn-sm btn-danger delete-btn" 
                                                       data-id="<?= $entry['id'] ?>"
                                                       onclick="return confirm('Are you sure you want to delete journal entry #<?= $entry['id'] ?>?\nThis action cannot be undone.')"
                                                       title="Delete Entry">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php 
                                        // Display additional rows without date and actions
                                        for($i = 1; $i < count($entry['details']); $i++): 
                                            $detail = $entry['details'][$i];
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($detail['account'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($detail['reference'] ?? '') ?></td>
                                                <td class="text-right"><?= $detail['debit'] > 0 ? number_format($detail['debit'], 2) : '&mdash;' ?></td>
                                                <td class="text-right"><?= $detail['credit'] > 0 ? number_format($detail['credit'], 2) : '&mdash;' ?></td>
                                            </tr>
                                        <?php endfor; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td><?= date('m/d/Y', strtotime($entry['date'])) ?></td>
                                            <td colspan="3"><?= htmlspecialchars($entry['journal_title'] ?? 'Journal Entry #'.$entry['id']) ?></td>
                                            <td class="text-right">&mdash;</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="<?= URL_ROOT ?>index.php?page=journal&action=view&id=<?= $entry['id'] ?>" 
                                                       class="btn btn-sm btn-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= URL_ROOT ?>index.php?page=journal&action=edit&id=<?= $entry['id'] ?>" 
                                                       class="btn btn-sm btn-warning" title="Edit Entry">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= URL_ROOT ?>index.php?page=journal&action=delete&id=<?= $entry['id'] ?>" 
                                                       class="btn btn-sm btn-danger delete-btn" 
                                                       data-id="<?= $entry['id'] ?>"
                                                       onclick="return confirm('Are you sure you want to delete journal entry #<?= $entry['id'] ?>?\nThis action cannot be undone.')"
                                                       title="Delete Entry">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
