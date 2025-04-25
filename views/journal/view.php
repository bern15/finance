<?php include_once ROOT_PATH . 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Journal Entry Details</h1>
        <a href="<?= URL_ROOT ?>index.php?page=journal" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Journal
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3><?= htmlspecialchars($journal['journal_title'] ?? 'Journal Entry #'.$journal['id']) ?></h3>
            <div>
                <a href="<?= URL_ROOT ?>index.php?page=journal&action=edit&id=<?= $journal['id'] ?>" 
                   class="btn btn-warning" title="Edit Entry">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Date:</strong> <?= isset($journal['date']) ? date('F d, Y', strtotime($journal['date'])) : 'Not specified' ?></p>
                </div>
            </div>
            
            <?php if (!empty($journal['description'])): ?>
                <p class="mt-3"><strong>Description:</strong> <?= htmlspecialchars($journal['description'] ?? '') ?></p>
            <?php endif; ?>
            
            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th width="15%">Date</th>
                            <th width="40%">Account</th>
                            <th width="15%">Ref</th>
                            <th width="15%" class="text-right">Debit</th>
                            <th width="15%" class="text-right">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($details) && !empty($details)): ?>
                            <?php foreach ($details as $detail): ?>
                                <tr>
                                    <td><?= date('m/d/Y', strtotime($detail['date'])) ?></td>
                                    <td>
                                        <?php if (!empty($detail['account'])): ?>
                                        <a href="<?= URL_ROOT ?>index.php?page=ledger&action=view&account=<?= urlencode($detail['account']) ?>">
                                            <?= htmlspecialchars($detail['account']) ?>
                                        </a>
                                        <?php else: ?>
                                            <em class="text-muted">No account specified</em>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= !empty($detail['reference']) ? htmlspecialchars($detail['reference']) : '-' ?></td>
                                    <td class="text-right"><?= $detail['debit'] > 0 ? number_format($detail['debit'], 2) : '-' ?></td>
                                    <td class="text-right"><?= $detail['credit'] > 0 ? number_format($detail['credit'], 2) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No details available for this journal entry</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                <p><strong>Created:</strong> <?= isset($journal['created_at']) ? date('F d, Y h:i A', strtotime($journal['created_at'])) : 'Unknown' ?></p>
                <?php if(isset($journal['updated_at']) && $journal['updated_at']): ?>
                    <p><strong>Last Updated:</strong> <?= date('F d, Y h:i A', strtotime($journal['updated_at'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once ROOT_PATH . 'includes/footer.php'; ?>
