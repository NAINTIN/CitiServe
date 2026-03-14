<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/repositories/ComplaintRepository.php';

$user = require_login();
$repo = new ComplaintRepository();

$categories = $repo->getActiveCategories();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $categoryId = (int)($_POST['category_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $isAnonymous = isset($_POST['is_anonymous']) && $_POST['is_anonymous'] === '1';

    if ($categoryId <= 0) $errors[] = 'Please select a complaint category.';
    if ($title === '') $errors[] = 'Title is required.';
    if ($description === '') $errors[] = 'Description is required.';

    if (empty($errors)) {
        $complaintId = $repo->create([
            'user_id' => (int)$user['id'],
            'category_id' => $categoryId,
            'is_anonymous' => $isAnonymous,
            'title' => $title,
            'description' => $description,
            'location' => $location !== '' ? $location : null,
        ]);

        if (!empty($_FILES['evidence']['name'])) {
            $uploadDir = __DIR__ . '/uploads/complaint_evidence';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $tmp = $_FILES['evidence']['tmp_name'];
            $original = $_FILES['evidence']['name'];
            $size = (int)$_FILES['evidence']['size'];
            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'mp4'];

            if (!in_array($ext, $allowed, true)) {
                $errors[] = 'Evidence must be jpg, jpeg, png, pdf, or mp4.';
            } elseif ($size > 10 * 1024 * 1024) {
                $errors[] = 'Evidence file must be 10MB or less.';
            } else {
                $safeName = uniqid('cmp_', true) . '.' . $ext;
                $dest = $uploadDir . '/' . $safeName;
                if (move_uploaded_file($tmp, $dest)) {
                    $relative = 'uploads/complaint_evidence/' . $safeName;
                    $repo->addEvidence($complaintId, $relative, $original);
                } else {
                    $errors[] = 'Complaint submitted, but evidence upload failed.';
                }
            }
        }

        if (empty($errors)) {
            $success = "Complaint submitted successfully. Complaint #{$complaintId}";
            $_POST = [];
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
                <input type="file" name="evidence" accept=".jpg,.jpeg,.png,.pdf,.mp4">
            </div>

            <br>
            <button type="submit">Submit Complaint</button>
        </form>
    <?php endif; ?>
</body>
</html>