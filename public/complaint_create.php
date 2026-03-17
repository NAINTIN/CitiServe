<?php
// Don't show errors on the page (for security), but still log them
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Include all the files we need
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/repositories/ComplaintRepository.php';

// This function converts PHP ini values like "8M" or "2G" into bytes
function bytes_from_ini($value)
{
    $value = trim($value);
    if ($value === '') {
        return 0;
    }

    // Get the last character (the unit: g, m, or k)
    $unit = strtolower(substr($value, -1));
    $number = (float)$value;

    // Convert based on the unit
    if ($unit === 'g') {
        return (int)($number * 1024 * 1024 * 1024);
    } elseif ($unit === 'm') {
        return (int)($number * 1024 * 1024);
    } elseif ($unit === 'k') {
        return (int)($number * 1024);
    } else {
        return (int)$number;
    }
}

// Make sure the user is a resident
$user = require_resident();

// Create a ComplaintRepository and get the available categories
$repo = new ComplaintRepository();
$categories = $repo->getActiveCategories();

// Variables for errors and success message
$errors = [];
$success = '';
$canSubmitComplaint = ((int)$user['is_verified'] === 1 && $user['residency_verification_status'] === 'approved');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canSubmitComplaint) {

    // Check if the uploaded data is too large (server limit)
    $contentLen = 0;
    if (isset($_SERVER['CONTENT_LENGTH'])) {
        $contentLen = (int)$_SERVER['CONTENT_LENGTH'];
    }
    $postMaxSetting = (string)ini_get('post_max_size');
    $postMax = bytes_from_ini($postMaxSetting);

    if ($contentLen > 0 && $postMax > 0 && $contentLen > $postMax && empty($_POST) && empty($_FILES)) {
        $errors[] = 'Only files less than or equal to 10MB are allowed.';
    } else {
        // Verify the CSRF token
        csrf_verify_or_die();

        // Get form values
        $categoryId = 0;
        if (isset($_POST['category_id'])) {
            $categoryId = (int)$_POST['category_id'];
        }

        $title = '';
        if (isset($_POST['title'])) {
            $title = trim($_POST['title']);
        }

        $description = '';
        if (isset($_POST['description'])) {
            $description = trim($_POST['description']);
        }

        $location = '';
        if (isset($_POST['location'])) {
            $location = trim($_POST['location']);
        }

        // Check if the user wants to submit anonymously
        $isAnonymous = false;
        if (isset($_POST['is_anonymous']) && $_POST['is_anonymous'] === '1') {
            $isAnonymous = true;
        }

        // Validate the required fields
        if ($categoryId <= 0) {
            $errors[] = 'Please select a complaint category.';
        }
        if ($title === '') {
            $errors[] = 'Title is required.';
        }
        if ($description === '') {
            $errors[] = 'Description is required.';
        }

        // Handle the evidence file upload (optional)
        $evidenceRelativePath = null;
        $evidenceOriginalName = null;

        if (isset($_FILES['evidence']) && (int)$_FILES['evidence']['error'] !== UPLOAD_ERR_NO_FILE) {
            $err = (int)$_FILES['evidence']['error'];

            if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                $errors[] = 'Only files less than or equal to 10MB are allowed.';
            } elseif ($err !== UPLOAD_ERR_OK) {
                $errors[] = 'Evidence upload failed (code ' . $err . ').';
            } else {
                // Get the file size and temp path
                $size = 0;
                if (isset($_FILES['evidence']['size'])) {
                    $size = (int)$_FILES['evidence']['size'];
                }
                $tmp = '';
                if (isset($_FILES['evidence']['tmp_name'])) {
                    $tmp = $_FILES['evidence']['tmp_name'];
                }

                // Check file size (max 10 MB)
                if ($size > 10 * 1024 * 1024) {
                    $errors[] = 'Only files less than or equal to 10MB are allowed.';
                } elseif ($size <= 0 || $tmp === '' || !is_uploaded_file($tmp)) {
                    $errors[] = 'Uploaded file is invalid. Please reselect the file.';
                } else {
                    // Check the file type using MIME detection
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime = (string)$finfo->file($tmp);

                    // These are the file types we allow
                    $allowedMimeToExt = [
                        'image/jpeg'      => 'jpg',
                        'image/png'       => 'png',
                        'application/pdf' => 'pdf',
                        'video/mp4'       => 'mp4',
                    ];

                    if (!isset($allowedMimeToExt[$mime])) {
                        $errors[] = 'Evidence must be jpg, jpeg, png, pdf, or mp4.';
                    } else {
                        // Create the upload directory if it doesn't exist
                        $uploadDir = __DIR__ . '/uploads/complaint_evidence';

                        if (!is_dir($uploadDir)) {
                            $created = mkdir($uploadDir, 0775, true);
                            if (!$created && !is_dir($uploadDir)) {
                                $errors[] = 'Failed to create evidence upload directory.';
                            }
                        }

                        // Check if the directory is writable
                        if (empty($errors) && !is_writable($uploadDir)) {
                            $errors[] = 'Evidence upload directory is not writable.';
                        }

                        // Save the file with a random name
                        if (empty($errors)) {
                            $ext = $allowedMimeToExt[$mime];
                            $safeName = bin2hex(random_bytes(16)) . '.' . $ext;
                            $dest = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $safeName;

                            if (move_uploaded_file($tmp, $dest)) {
                                $evidenceRelativePath = 'uploads/complaint_evidence/' . $safeName;
                                $evidenceOriginalName = isset($_FILES['evidence']['name']) ? $_FILES['evidence']['name'] : 'evidence';
                            } else {
                                $errors[] = 'Evidence upload failed while saving file.';
                            }
                        }
                    }
                }
            }
        }

        // If there are no errors, create the complaint
        if (empty($errors)) {
            // Set location to null if empty
            $locationForDb = null;
            if ($location !== '') {
                $locationForDb = $location;
            }

            $complaintId = $repo->create([
                'user_id' => (int)$user['id'],
                'category_id' => $categoryId,
                'is_anonymous' => $isAnonymous,
                'title' => $title,
                'description' => $description,
                'location' => $locationForDb,
            ]);

            // If evidence was uploaded, add it to the complaint
            if ($evidenceRelativePath !== null) {
                $repo->addEvidence($complaintId, $evidenceRelativePath, $evidenceOriginalName);
            }

            $success = "Complaint submitted successfully. Complaint #{$complaintId}";
            $_POST = [];
            // Reload categories in case they changed
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

    <?php if (!$canSubmitComplaint): ?>
        <p style="color: red;">
            Your account is not verified yet. You must submit proof of residency before filing a complaint.
            <a href="/CitiServe/public/residency_verification.php">Submit proof of residency</a>
        </p>
        <p>Complaint submission is locked until your residency proof is approved.</p>
    <?php elseif (empty($categories)): ?>
        <p style="color:red;">No complaint categories available. Please contact admin.</p>
    <?php else: ?>
        <form method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div>
                <label>Category</label><br>
                <select name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <?php
                        // Check if this category was previously selected
                        $selectedCatId = 0;
                        if (isset($_POST['category_id'])) {
                            $selectedCatId = (int)$_POST['category_id'];
                        }
                        $isSelected = ($selectedCatId === (int)$cat['id']) ? 'selected' : '';
                        ?>
                        <option value="<?= (int)$cat['id'] ?>" <?= $isSelected ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label>Title</label><br>
                <?php
                $previousTitle = '';
                if (isset($_POST['title'])) {
                    $previousTitle = $_POST['title'];
                }
                ?>
                <input type="text" name="title" value="<?= htmlspecialchars($previousTitle) ?>" required>
            </div>

            <div>
                <label>Description</label><br>
                <?php
                $previousDesc = '';
                if (isset($_POST['description'])) {
                    $previousDesc = $_POST['description'];
                }
                ?>
                <textarea name="description" rows="5" cols="60" required><?= htmlspecialchars($previousDesc) ?></textarea>
            </div>

            <div>
                <label>Location (address or map pin text)</label><br>
                <?php
                $previousLocation = '';
                if (isset($_POST['location'])) {
                    $previousLocation = $_POST['location'];
                }
                ?>
                <input type="text" name="location" value="<?= htmlspecialchars($previousLocation) ?>">
            </div>

            <div>
                <label>
                    <input type="checkbox" name="is_anonymous" value="1" <?php if (isset($_POST['is_anonymous'])) { echo 'checked'; } ?>>
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
