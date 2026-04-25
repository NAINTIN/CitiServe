<?php
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/request_form_error.log');

require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';
require_once __DIR__ . '/../app/helpers/upload.php';
require_once __DIR__ . '/../app/helpers/document_request.php';

$user = require_resident();
$data = new CitiServeData();
$dbUser = $data->findUserById((int)$user['id']);
$isVerified = ($dbUser && (int)$dbUser->is_verified === 1);

$serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
if ($serviceId <= 0 && !empty($_SESSION['document_request_draft']['service_id'])) {
    $serviceId = (int)$_SESSION['document_request_draft']['service_id'];
}

$service = $serviceId > 0 ? $data->findDocumentServiceById($serviceId) : null;
if (!$service || (int)$service['is_active'] !== 1) {
    header('Location: /CitiServe/public/request_select.php');
    exit;
}

$definition = document_request_definition_by_service_name($service['name']);
if (!$definition) {
    header('Location: /CitiServe/public/request_select.php');
    exit;
}

$errors = [];
$values = [];

if (!empty($_SESSION['document_request_draft']) && (int)$_SESSION['document_request_draft']['service_id'] === (int)$service['id']) {
    $values = isset($_SESSION['document_request_draft']['form_values']) ? (array)$_SESSION['document_request_draft']['form_values'] : [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();
    $values = [];
    foreach ($definition['fields'] as $field) {
        $name = $field['name'];
        $values[$name] = trim((string)($_POST[$name] ?? ''));
    }

    if (!$isVerified) {
        $errors[] = 'Your account must be verified before you can submit a document request.';
    }

    foreach ($definition['fields'] as $field) {
        $name = $field['name'];
        $label = $field['label'];
        $type = $field['type'];
        $value = $values[$name];

        $isRequired = !empty($field['required']);
        if (!empty($field['required_if'])) {
            $requiredIf = $field['required_if'];
            $checkField = $requiredIf['field'];
            $equals = $requiredIf['equals'];
            if (($values[$checkField] ?? '') === $equals) {
                $isRequired = true;
            }
        }

        if ($isRequired && $value === '') {
            $errors[] = $label . ' is required.';
            continue;
        }

        if ($value === '') {
            continue;
        }

        if ($type === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = $label . ' must be a valid email address.';
        } elseif ($type === 'number') {
            if (!preg_match('/^\d+$/', $value)) {
                $errors[] = $label . ' must be a valid number.';
            }
        } elseif ($type === 'date_mmddyyyy' && !document_request_is_valid_mmddyyyy($value)) {
            $errors[] = $label . ' must be in MM/DD/YYYY format.';
        } elseif ($type === 'select') {
            $options = isset($field['options']) ? $field['options'] : [];
            if (!in_array($value, $options, true)) {
                $errors[] = 'Invalid selection for ' . $label . '.';
            }
        }
    }

    $uploadedFiles = [];
    if (empty($errors)) {
        foreach ($definition['required_uploads'] as $uploadField) {
            $key = $uploadField['key'];
            $label = $uploadField['label'];
            $file = $_FILES[$key] ?? null;
            $uploadErr = $file['error'] ?? UPLOAD_ERR_NO_FILE;

            if ($uploadErr === UPLOAD_ERR_NO_FILE) {
                $errors[] = $label . ' is required.';
                continue;
            }
            if ($uploadErr === UPLOAD_ERR_INI_SIZE || $uploadErr === UPLOAD_ERR_FORM_SIZE) {
                $errors[] = $label . ' exceeds the maximum file size (5MB).';
                continue;
            }
            if ($uploadErr !== UPLOAD_ERR_OK) {
                $errors[] = 'Upload failed for ' . $label . '.';
                continue;
            }

            try {
                $path = saveDocumentRequestFile(
                    $file,
                    __DIR__ . '/uploads/request_files',
                    'uploads/request_files'
                );
                $uploadedFiles[] = [
                    'file_type' => $key,
                    'label' => $label,
                    'file_path' => $path,
                    'original_name' => isset($file['name']) ? (string)$file['name'] : null,
                ];
            } catch (Throwable $e) {
                $errors[] = $label . ': ' . $e->getMessage();
            }
        }
    }

    if (empty($errors)) {
        $_SESSION['document_request_draft'] = [
            'user_id' => (int)$user['id'],
            'service_id' => (int)$service['id'],
            'service_name' => (string)$service['name'],
            'fee' => (string)$service['price'],
            'form_values' => $values,
            'files' => $uploadedFiles,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        header('Location: /CitiServe/public/request_payment.php');
        exit;
    }
}

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Request Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CitiServe/frontend/document_request/css/barangay_clearance_form.css">
    <style>
        .top-links { margin-bottom: 12px; font-size: 13px; color: #6B7280; }
        .top-links a { color: #6B7280; text-decoration: none; margin-right: 12px; }
        .top-links a:hover { color: #E8265E; }
        .error-box { color: #B91C1C; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 12px; margin-bottom: 14px; }
        .input-like, .select-like, .textarea-like { width: 100%; border: 1.5px solid #E5E7EB; border-radius: 3px; font-size: 12.5px; padding: 10px 14px; }
        .input-like, .select-like { height: 35px; padding: 0 14px; }
        .textarea-like { min-height: 100px; resize: vertical; }
        .upload-box { background: #fff; align-items: flex-start; }
        .upload-box input[type=file] { width: 100%; border: none; padding: 0; }
    </style>
</head>
<body>
<div class="content-area">
    <div class="top-links">
        <a href="/CitiServe/public/request_select.php">Back to Selection</a>
        <a href="/CitiServe/public/dashboard.php">Dashboard</a>
    </div>

    <div class="form-breadcrumb" id="form-breadcrumb"></div>

    <h1 class="form-title"><?= h($service['name']) ?> – Request Form</h1>
    <p class="form-subtitle">Fill in all required fields accurately. Incomplete forms will not be processed.</p>
    <?php if (!$isVerified): ?>
        <div class="error-box">Your account is not yet verified. You can review this form, but submission is disabled until verification is approved.</div>
    <?php endif; ?>

    <div class="form-stepper">
        <div class="form-step">
            <div class="step-icon active-icon">
                <img src="/CitiServe/frontend/document_request/images/docu-personal-info.png" class="step-img-only" alt="">
            </div>
            <div class="step-label active-text">
                <span class="step-name">Fill out Form</span>
                <span class="step-sub">Enter your details</span>
            </div>
        </div>
        <div class="step-arrow"><img src="/CitiServe/frontend/document_request/images/docu-arrow.png" alt=""></div>
        <div class="form-step">
            <div class="step-icon inactive-icon">
                <img src="/CitiServe/frontend/document_request/images/docu-social-acc.png" class="step-img-only" alt="">
            </div>
            <div class="step-label inactive-text">
                <span class="step-name">Payment</span>
                <span class="step-sub">Complete your payment</span>
            </div>
        </div>
        <div class="step-arrow"><img src="/CitiServe/frontend/document_request/images/docu-arrow.png" alt=""></div>
        <div class="form-step">
            <div class="step-icon inactive-icon">
                <img src="/CitiServe/frontend/document_request/images/docu-payment-info.png" class="step-img-only" alt="">
            </div>
            <div class="step-label inactive-text">
                <span class="step-name">Confirmation</span>
                <span class="step-sub">Review and confirm</span>
            </div>
        </div>
    </div>

    <?php if ($errors): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= h($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-wrapper">
            <div class="form-main">
                <div class="form-card">
                    <div class="form-card-bar">
                        <img src="/CitiServe/frontend/document_request/images/docu-req-info.png" alt="">
                        Request Information
                    </div>
                    <div class="form-card-body">
                        <?php foreach ($definition['fields'] as $field): ?>
                            <?php
                            $name = $field['name'];
                            $type = $field['type'];
                            $label = $field['label'];
                            $isRequired = !empty($field['required']);
                            $value = $values[$name] ?? '';
                            $placeholder = $type === 'date_mmddyyyy' ? 'MM/DD/YYYY' : ('Enter ' . $label);
                            ?>
                            <div class="form-group" data-field-name="<?= h($name) ?>">
                                <label for="<?= h($name) ?>"><?= h($label) ?><?php if ($isRequired): ?> <span class="req">*</span><?php endif; ?></label>

                                <?php if ($type === 'textarea'): ?>
                                    <textarea class="textarea-like" id="<?= h($name) ?>" name="<?= h($name) ?>" <?= $isRequired ? 'required' : '' ?>><?= h($value) ?></textarea>
                                <?php elseif ($type === 'select'): ?>
                                    <select class="select-like" id="<?= h($name) ?>" name="<?= h($name) ?>" <?= $isRequired ? 'required' : '' ?>>
                                        <option value="">-- Select --</option>
                                        <?php foreach ((array)$field['options'] as $option): ?>
                                            <option value="<?= h($option) ?>" <?= ($value === $option) ? 'selected' : '' ?>><?= h($option === '' ? 'None' : $option) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <?php
                                    $inputType = 'text';
                                    if ($type === 'email') {
                                        $inputType = 'email';
                                    } elseif ($type === 'number') {
                                        $inputType = 'number';
                                    }
                                    ?>
                                    <input class="input-like" type="<?= h($inputType) ?>" id="<?= h($name) ?>" name="<?= h($name) ?>" value="<?= h($value) ?>" placeholder="<?= h($placeholder) ?>" <?= $isRequired ? 'required' : '' ?>>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <?php foreach ($definition['required_uploads'] as $upload): ?>
                            <div class="form-group">
                                <label for="<?= h($upload['key']) ?>"><?= h($upload['label']) ?> <span class="req">*</span></label>
                                <div class="upload-box">
                                    <input type="file" id="<?= h($upload['key']) ?>" name="<?= h($upload['key']) ?>" accept=".jpg,.jpeg,.png,.pdf,application/pdf,image/jpeg,image/png" required>
                                </div>
                                <small class="upload-note">JPG, PNG, PDF – max 5MB</small>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-btn-divider"></div>
                    <div class="form-btn-row">
                        <div class="form-btn-group">
                            <a class="form-btn form-btn-back" href="/CitiServe/public/request_select.php">
                                <img src="/CitiServe/frontend/document_request/images/docu-back.png" alt="Back">
                            </a>
                            <button class="form-btn" type="submit" <?= $isVerified ? '' : 'disabled style="opacity:.55;cursor:not-allowed;"' ?>>
                                <img src="/CitiServe/frontend/document_request/images/proceedd-to-payment.png" alt="Proceed">
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-side">
                <div class="form-card">
                    <div class="form-card-bar form-gradient">Document Summary</div>
                    <div class="form-card-body summary-body">
                        <div class="summary-row">
                            <span class="summary-label">Document</span>
                            <span class="summary-value"><?= h((string)$service['name']) ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Fee</span>
                            <span class="summary-fee">₱<?= h((string)$service['price']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="form-reminders">
                    <img src="/CitiServe/frontend/document_request/images/docu-reminders.png" alt="Reminders">
                </div>
            </div>
        </div>
    </form>

    <div class="form-logo">
        <img src="/CitiServe/frontend/document_request/images/docu-logo.png" alt="CitiServe">
        <div class="form-logo-text">
            <span class="logo-pink">CitiServe</span>
            <span class="logo-gray"> © 2026. All rights reserved.</span>
        </div>
    </div>
</div>

<?php
$conditionalWires = [];
foreach ($definition['fields'] as $field) {
    if (!empty($field['required_if'])) {
        $conditionalWires[] = [
            'source' => (string)$field['required_if']['field'],
            'equals' => (string)$field['required_if']['equals'],
            'target' => (string)$field['name'],
        ];
    }
}
?>
<script>
const trail = [
  { label: "Document Requests", href: "/CitiServe/public/request_select.php" },
  { label: "Request Document", href: "/CitiServe/public/request_select.php" },
  { label: <?= json_encode((string)$service['name'], JSON_UNESCAPED_UNICODE) ?>, href: null },
  { label: "Form", href: null }
];
(function renderBreadcrumb() {
  const el = document.getElementById("form-breadcrumb");
  el.innerHTML = trail.map((item, i) => {
    const isLast = i === trail.length - 1;
    const sep = i > 0 ? `<span class="form-sep">></span>` : "";
    if (isLast) return `${sep}<span class="form-active">${item.label}</span>`;
    if (!item.href) return `${sep}<span>${item.label}</span>`;
    return `${sep}<a href="${item.href}">${item.label}</a>`;
  }).join("");
})();

(function () {
    function toggleConditional(sourceField, expectedValue, targetField) {
        var source = document.getElementById(sourceField);
        var targetWrap = document.querySelector('[data-field-name="' + targetField + '"]');
        if (!source || !targetWrap) return;

        var targetInput = targetWrap.querySelector('input, textarea, select');
        var isShown = source.value === expectedValue;
        targetWrap.style.display = isShown ? '' : 'none';
        if (targetInput) {
            targetInput.required = isShown;
            if (!isShown) {
                targetInput.value = '';
            }
        }
    }

    function wire(sourceField, expectedValue, targetField) {
        var source = document.getElementById(sourceField);
        if (!source) return;
        source.addEventListener('change', function () {
            toggleConditional(sourceField, expectedValue, targetField);
        });
        toggleConditional(sourceField, expectedValue, targetField);
    }

    var wires = <?= json_encode($conditionalWires, JSON_UNESCAPED_UNICODE) ?>;
    for (var i = 0; i < wires.length; i++) {
        wire(wires[i].source, wires[i].equals, wires[i].target);
    }
})();
</script>
</body>
</html>
