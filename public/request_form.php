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
<html>
<head>
    <meta charset="utf-8">
    <title>Document Request Form</title>
</head>
<body>
    <h2>Step 2: Fill Up Form - <?= h($service['name']) ?></h2>
    <p>
        <a href="/CitiServe/public/request_select.php">Back to Selection</a> |
        <a href="/CitiServe/public/dashboard.php">Dashboard</a>
    </p>

    <?php if ($errors): ?>
        <div style="color:red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= h($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <?php foreach ($definition['fields'] as $field): ?>
            <?php
            $name = $field['name'];
            $type = $field['type'];
            $label = $field['label'];
            $required = !empty($field['required']) ? 'required' : '';
            $value = $values[$name] ?? '';
            ?>
            <div style="margin-bottom:10px;" data-field-name="<?= h($name) ?>">
                <label for="<?= h($name) ?>"><?= h($label) ?></label><br>

                <?php if ($type === 'textarea'): ?>
                    <textarea id="<?= h($name) ?>" name="<?= h($name) ?>" rows="4" cols="50" <?= $required ?>><?= h($value) ?></textarea>
                <?php elseif ($type === 'select'): ?>
                    <select id="<?= h($name) ?>" name="<?= h($name) ?>" <?= $required ?>>
                        <option value="">-- Select --</option>
                        <?php foreach ((array)$field['options'] as $option): ?>
                            <option value="<?= h($option) ?>" <?= ($value === $option) ? 'selected' : '' ?>><?= h($option === '' ? 'None' : $option) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input id="<?= h($name) ?>" name="<?= h($name) ?>" value="<?= h($value) ?>" <?= $required ?>>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <h3>Required Uploads (JPG, PNG, PDF. Max 5MB each)</h3>
        <?php foreach ($definition['required_uploads'] as $upload): ?>
            <div style="margin-bottom:10px;">
                <label for="<?= h($upload['key']) ?>"><?= h($upload['label']) ?></label><br>
                <input type="file" id="<?= h($upload['key']) ?>" name="<?= h($upload['key']) ?>" accept=".jpg,.jpeg,.png,.pdf,application/pdf,image/jpeg,image/png" required>
            </div>
        <?php endforeach; ?>

        <button type="submit">Continue to Payment</button>
    </form>

    <script>
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

            wire('business_nature', 'others', 'business_nature_other');
            wire('solo_parent_reason', 'others', 'solo_parent_reason_other');
        })();
    </script>
</body>
</html>
