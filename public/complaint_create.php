<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/repositories/ComplaintRepository.php';

function bytes_from_ini(string $v): int
{
    $v = trim($v);
    if ($v === '') return 0;

    $unit = strtolower(substr($v, -1));
    $num = (float)$v;

    return match ($unit) {
        'g' => (int)($num * 1024 * 1024 * 1024),
        'm' => (int)($num * 1024 * 1024),
        'k' => (int)($num * 1024),
        default => (int)$num,
    };
}

$user = require_login();
$repo = new ComplaintRepository();

$categories = $repo->getActiveCategories();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prevent 419 when request body exceeds post_max_size
    $contentLen = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
    $postMax = bytes_from_ini((string)ini_get('post_max_size'));

    if ($contentLen > 0 && $postMax > 0 && $contentLen > $postMax && empty($_POST) && empty($_FILES)) {
        $errors[] = 'Evidence file exceeds server upload limit. Please upload a smaller file (max 10MB).';
    } else {
        csrf_verify_or_die();

        $categoryId = (int)($_POST['category_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $isAnonymous = isset($_POST['is_anonymous']) && $_POST['is_anonymous'] === '1';

        if ($categoryId <= 0) $errors[] = 'Please select a complaint category.';
        if ($title === '') $errors[] = 'Title is required.';
        if ($description === '') $errors[] = 'Description is required.';

        $evidenceRelativePath = null;
        $evidenceOriginalName = null;

        if (isset($_FILES['evidence']) && (int)$_FILES['evidence']['error'] !== UPLOAD_ERR_NO_FILE) {
            $err = (int)$_FILES['evidence']['error'];

            if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                $errors[] = 'Evidence file must be 10MB or less.';
            } elseif ($err !== UPLOAD_ERR_OK) {
                $errors[] = 'Evidence upload failed (code ' . $err . ').';
            } else {
                $size = (int)($_FILES['evidence']['size'] ?? 0);
                $tmp  = $_FILES['evidence']['tmp_name'] ?? '';

                if ($size > 10 * 1024 * 1024) {
                    $errors[] = 'Evidence file must be 10MB or less.';
                } elseif ($size <= 0 || $tmp === '' || !is_uploaded_file($tmp)) {
                    $errors[] = 'Uploaded file is invalid. Please reselect the file.';
                } else {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime = (string)$finfo->file($tmp);

                    $allowedMimeToExt = [
                        'image/jpeg'      => 'jpg',
                        'image/png'       => 'png',
                        'application/pdf' => 'pdf',
                        'video/mp4'       => 'mp4',
                    ];

                    if (!isset($allowedMimeToExt[$mime])) {
                        $errors[] = 'Evidence must be jpg, jpeg, png, pdf, or mp4.';
                    } else {
                        $uploadDir = __DIR__ . '/uploads/complaint_evidence';

                        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                            $errors[] = 'Failed to create evidence upload directory.';
                        } elseif (!is_writable($uploadDir)) {
                            $errors[] = 'Evidence upload directory is not writable.';
                        } else {
                            $ext = $allowedMimeToExt[$mime];
                            $safeName = bin2hex(random_bytes(16)) . '.' . $ext;
                            $dest = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $safeName;

                            if (move_uploaded_file($tmp, $dest)) {
                                $evidenceRelativePath = 'uploads/complaint_evidence/' . $safeName;
                                $evidenceOriginalName = $_FILES['evidence']['name'] ?? 'evidence';
                            } else {
                                $errors[] = 'Evidence upload failed while saving file.';
                            }
                        }
                    }
                }
            }
        }

        if (empty($errors)) {
            $complaintId = $repo->create([
                'user_id' => (int)$user['id'],
                'category_id' => $categoryId,
                'is_anonymous' => $isAnonymous,
                'title' => $title,
                'description' => $description,
                'location' => $location !== '' ? $location : null,
            ]);

            if ($evidenceRelativePath !== null) {
                $repo->addEvidence($complaintId, $evidenceRelativePath, $evidenceOriginalName);
            }

            $success = "Complaint submitted successfully. Complaint #{$complaintId}";
            $_POST = [];
            $categories = $repo->getActiveCategories();
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Submit Complaint</title>
</head>
<body>
    <h2>Submit Complaint</h2>
    <p>
        <a href="/CitiServe/public/index.php">Home</a> |
        <a href="/CitiServe/public/my_complaints.php">My Complaints</a> |
        <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <?php if ($success): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (empty($categories)): ?>
        <p style="color:red;">No complaint categories available. Please contact admin.</p>
    <?php else: ?>
        <form method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div>
                <label>Category</label><br>
                <select name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>"
                            <?= ((int)($_POST['category_id'] ?? 0) === (int)$cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label>Title</label><br>
                <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
            </div>

            <div>
                <label>Description</label><br>
                <textarea name="description" rows="5" cols="60" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div>
                <label>Location (address or map pin text)</label><br>
                <input type="text" name="location" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
            </div>

            <div>
                <label>
                    <input type="checkbox" name="is_anonymous" value="1" <?= isset($_POST['is_anonymous']) ? 'checked' : '' ?>>
                    Submit as anonymous
                </label>
            </div>

            <div>
                <label>Evidence (optional: jpg/png/pdf/mp4, max 10MB)</label><br>
                <input type="file" name="evidence" accept=".jpg,.jpeg,.png,.pdf,.mp4,video/mp4">
            </div>

            <br>
            <button type="submit">Submit Complaint</button>
        </form>
    <?php endif; ?>
</body>
</html>