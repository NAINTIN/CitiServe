<?php
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/request_confirm_error.log');

require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';
require_once __DIR__ . '/../app/helpers/document_request.php';

$user = require_resident();
$data = new CitiServeData();
$db = $data->getPdo();

$draft = isset($_SESSION['document_request_draft']) ? $_SESSION['document_request_draft'] : null;
if (!$draft || (int)$draft['user_id'] !== (int)$user['id']) {
    header('Location: /CitiServe/public/request_select.php');
    exit;
}

if (empty($draft['form_values']) || empty($draft['files']) || empty($draft['payment_method']) || empty($draft['payment_reference']) || empty($draft['payment_proof_path'])) {
    header('Location: /CitiServe/public/request_payment.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $action = trim((string)($_POST['action'] ?? ''));
    if ($action === 'cancel') {
        unset($_SESSION['document_request_draft']);
        header('Location: /CitiServe/public/request_select.php');
        exit;
    }

    if ($action !== 'submit') {
        $errors[] = 'Invalid action.';
    }

    if (empty($errors)) {
        $purpose = null;
        $values = (array)$draft['form_values'];
        foreach (['purpose', 'purpose_of_request', 'purpose_of_clearance'] as $purposeField) {
            if (!empty($values[$purposeField])) {
                $purpose = trim((string)$values[$purposeField]);
                break;
            }
        }

        $db->beginTransaction();
        try {
            $requestId = $data->createDocumentRequest([
                'user_id' => (int)$user['id'],
                'document_service_id' => (int)$draft['service_id'],
                'purpose' => $purpose,
                'payment_method' => (string)$draft['payment_method'],
                'payment_reference' => (string)$draft['payment_reference'],
                'payment_proof_path' => (string)$draft['payment_proof_path'],
                'form_data_json' => json_encode($values, JSON_UNESCAPED_UNICODE),
            ]);

            foreach ((array)$draft['files'] as $f) {
                $data->addDocumentRequestFile(
                    $requestId,
                    (string)$f['file_type'],
                    (string)$f['file_path'],
                    isset($f['original_name']) ? (string)$f['original_name'] : null
                );
            }

            $admins = $data->getUsersByRole('admin');
            foreach ($admins as $admin) {
                $data->createNotification(
                    (int)$admin['id'],
                    'New Document Request',
                    'A resident submitted a document request.',
                    '/CitiServe/public/admin/requests.php'
                );
            }

            $db->commit();

            $_SESSION['document_request_success'] = [
                'request_id' => $requestId,
                'service_name' => $draft['service_name'],
                'fee' => $draft['fee'],
                'payment_method' => $draft['payment_method'],
                'status' => 'received',
                'submitted_at' => date('Y-m-d H:i:s'),
            ];

            unset($_SESSION['document_request_draft']);
            header('Location: /CitiServe/public/request_success.php?request_id=' . (int)$requestId);
            exit;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log('REQUEST CONFIRM ERROR: ' . $e->getMessage());
            $errors[] = 'Failed to submit request. Please try again.';
        }
    }
}

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirm Request</title>
</head>
<body>
    <h2>Step 4: Confirm Submission</h2>

    <?php if ($errors): ?>
        <div style="color:red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= h($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <p><strong>Service:</strong> <?= h($draft['service_name']) ?></p>
    <p><strong>Amount:</strong> ₱<?= h($draft['fee']) ?></p>
    <p><strong>Payment Method:</strong> <?= h($draft['payment_method']) ?></p>
    <p><strong>Payment Reference:</strong> <?= h($draft['payment_reference']) ?></p>

    <h3>Form Details</h3>
    <ul>
        <?php foreach ((array)$draft['form_values'] as $key => $value): ?>
            <?php if ($value === ''): continue; endif; ?>
            <li><strong><?= h(document_request_readable_field_name($key)) ?>:</strong> <?= nl2br(h($value)) ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Uploaded Files</h3>
    <ul>
        <?php foreach ((array)$draft['files'] as $file): ?>
            <li><?= h($file['label']) ?> - <a href="/CitiServe/public/<?= h($file['file_path']) ?>" target="_blank">View</a></li>
        <?php endforeach; ?>
        <li>Payment Proof Screenshot - <a href="/CitiServe/public/<?= h($draft['payment_proof_path']) ?>" target="_blank">View</a></li>
    </ul>

    <form method="post" onsubmit="return confirm('Submit this document request?');">
        <?= csrf_field() ?>
        <button type="submit" name="action" value="submit">Submit Request</button>
        <button type="submit" name="action" value="cancel" onclick="return confirm('Cancel this request?');">Cancel</button>
    </form>
</body>
</html>
