<?php
include 'connect.php';

$trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;
if ($trip_id <= 0) {
    echo "<p class='text-muted text-center'>⚠️ No trip selected.</p>";
    exit;
}

$q = mysqli_query($conn, "SELECT trip_name, image FROM trips WHERE id=$trip_id LIMIT 1");
if ($q && mysqli_num_rows($q) > 0) {
    $trip = mysqli_fetch_assoc($q);
    ?>
    <img src="<?= htmlspecialchars($trip['image']); ?>" 
         alt="<?= htmlspecialchars($trip['trip_name']); ?>" 
         class="img-fluid rounded shadow"
         style="max-height:450px; object-fit:cover; width:100%; border-radius:8px;">
    <h3 class="mt-3"><?= htmlspecialchars($trip['trip_name']); ?></h3>

    <!-- ✅ Recommended Plan Button -->
    <button class="btn btn-gradient mb-3" onclick="useTemplate(<?= $trip_id ?>)">Use Recommended Plan</button>
    <?php
} else {
    echo "<p class='text-center text-danger'>Trip not found.</p>";
}
?>
