<?php
// ajax_expenses.php
header('Content-Type: application/json');
session_start();   // ✅ ensure session
include 'connect.php';

// ---------- helpers ----------
function respond($ok, $msg = '', $extra = []) {
    echo json_encode(array_merge(['success' => $ok, 'message' => $msg], $extra));
    exit;
}

function ensure_upload_dir($dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    if (!is_dir($dir) || !is_writable($dir)) {
        respond(false, "Upload folder not writable: $dir");
    }
}

function save_image_upload($field_name, $old_path = null) {
    if (!isset($_FILES[$field_name]) || !is_array($_FILES[$field_name]) || $_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE) {
        return $old_path; // no new file
    }

    $file = $_FILES[$field_name];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        respond(false, "Image upload error (code {$file['error']})");
    }

    // Validations
    $allowed_ext = ['jpg','jpeg','png','gif','webp'];
    $max_bytes   = 5 * 1024 * 1024; // 5MB

    if ($file['size'] > $max_bytes) {
        respond(false, "Image too large. Max 5MB allowed.");
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        respond(false, "Invalid image type. Allowed: " . implode(', ', $allowed_ext));
    }

    // MIME check
    $finfo = @finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo) {
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (strpos($mime, 'image/') !== 0) {
            respond(false, "Invalid file; not an image.");
        }
    }

    // Destination
    $upload_dir = __DIR__ . '/uploads/expenses';
    ensure_upload_dir($upload_dir);

    // Unique filename
    $safeBase = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
    $new_name = time() . '_' . bin2hex(random_bytes(4)) . '_' . $safeBase;
    $dest_abs = $upload_dir . '/' . $new_name;

    if (!move_uploaded_file($file['tmp_name'], $dest_abs)) {
        respond(false, "Failed to move uploaded image.");
    }

    // Delete old
    if (!empty($old_path)) {
        $old_abs = __DIR__ . '/' . ltrim($old_path, '/');
        if (is_file($old_abs)) { @unlink($old_abs); }
    }

    return 'uploads/expenses/' . $new_name; // relative path for DB
}

// ---------- router ----------
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $trip_id      = intval($_POST['trip_id'] ?? 0);
    $category     = mysqli_real_escape_string($conn, $_POST['category'] ?? '');
    $amount       = floatval($_POST['amount'] ?? 0);
    $expense_date = mysqli_real_escape_string($conn, $_POST['expense_date'] ?? '');
    $notes        = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
    $user_id      = $_SESSION['user_id'] ?? 0;   // ✅ logged in user

    if ($trip_id <= 0 || $category === '' || $expense_date === '' || $user_id <= 0) {
        respond(false, "Missing required fields.");
    }

    $image_path = save_image_upload('image', null);

    $cols = "trip_id, user_id, category, amount, expense_date, notes";
    $vals = "$trip_id, $user_id, '$category', $amount, '$expense_date', '$notes'";

    if (!empty($image_path)) {
        $cols .= ", image";
        $vals .= ", '" . mysqli_real_escape_string($conn, $image_path) . "'";
    }

    $sql = "INSERT INTO expenses ($cols) VALUES ($vals)";
    $ok  = mysqli_query($conn, $sql);

    respond((bool)$ok, $ok ? "Added" : "Insert failed");

} elseif ($action === 'update') {
    $id           = intval($_POST['id'] ?? 0);
    $category     = mysqli_real_escape_string($conn, $_POST['category'] ?? '');
    $amount       = floatval($_POST['amount'] ?? 0);
    $expense_date = mysqli_real_escape_string($conn, $_POST['expense_date'] ?? '');
    $notes        = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');

    if ($id <= 0 || $category === '' || $expense_date === '') {
        respond(false, "Missing required fields.");
    }

    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM expenses WHERE id=$id LIMIT 1"));
    $old_image = $row ? ($row['image'] ?? null) : null;

    $new_image = save_image_upload('image', $old_image);

    $set = "category='$category', amount=$amount, expense_date='$expense_date', notes='$notes'";
    if ($new_image !== $old_image) {
        $set .= ", image='" . mysqli_real_escape_string($conn, $new_image) . "'";
    }

    $ok  = mysqli_query($conn, "UPDATE expenses SET $set WHERE id=$id");

    respond((bool)$ok, $ok ? "Updated" : "Update failed");

} elseif ($action === 'delete') {
    $id      = intval($_POST['id'] ?? 0);
    $trip_id = intval($_POST['trip_id'] ?? 0);

    if ($id <= 0 || $trip_id <= 0) {
        respond(false, "Invalid request.");
    }

    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM expenses WHERE id=$id AND trip_id=$trip_id LIMIT 1"));
    if ($row && !empty($row['image'])) {
        $imgPath = $row['image'];
        if (strpos($imgPath, 'uploads/expenses/') === 0) {
            $abs = __DIR__ . '/' . ltrim($imgPath, '/');
            if (is_file($abs)) { @unlink($abs); }
        }
    }

    $ok = mysqli_query($conn, "DELETE FROM expenses WHERE id=$id AND trip_id=$trip_id");
    respond((bool)$ok, $ok ? "Deleted" : "Delete failed");

} elseif ($action === 'use_template') {
    $trip_id = intval($_POST['trip_id'] ?? 0);
    $user_id = $_SESSION['user_id'] ?? 0;  // ✅ template bhi user ke naam pe save ho

    if ($trip_id <= 0 || $user_id <= 0) {
        respond(false, "Invalid trip id or user.");
    }

    $tpl_result = mysqli_query($conn, "SELECT category, amount, expense_date, notes, image FROM expenses_templates WHERE trip_id=$trip_id");
    if (!$tpl_result || mysqli_num_rows($tpl_result) === 0) {
        respond(false, "No templates found for this trip.");
    }

    $ok_all = true;
    while ($tpl = mysqli_fetch_assoc($tpl_result)) {
        $cat = mysqli_real_escape_string($conn, $tpl['category']);
        $amt = floatval($tpl['amount']);
        $dt  = mysqli_real_escape_string($conn, $tpl['expense_date']);
        $nt  = mysqli_real_escape_string($conn, $tpl['notes']);
        $im  = mysqli_real_escape_string($conn, $tpl['image'] ?? '');

        $cols = "trip_id, user_id, category, amount, expense_date, notes";
        $vals = "$trip_id, $user_id, '$cat', $amt, '$dt', '$nt'";

        if (!empty($im)) {
            $cols .= ", image";
            $vals .= ", '$im'";
        }

        $ok = mysqli_query($conn, "INSERT INTO expenses ($cols) VALUES ($vals)");
        $ok_all = $ok_all && (bool)$ok;
    }

    respond($ok_all, $ok_all ? "Template applied" : "Some template rows failed");

} else {
    respond(false, "Invalid action");
}
