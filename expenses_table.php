<?php
include 'connect.php';

if (isset($_GET['trip_header'])) {
    $trip_id = intval($_GET['trip_id'] ?? 0);
    if ($trip_id > 0) {
        $q = mysqli_query($conn, "SELECT trip_name, image FROM trips WHERE id=$trip_id LIMIT 1");
        if ($q && mysqli_num_rows($q) > 0) {
            $trip = mysqli_fetch_assoc($q);
            echo '<img src="'.htmlspecialchars($trip['image']).'" 
                     class="img-fluid rounded shadow" 
                     style="max-height:450px;object-fit:cover;width:100%;">';
            echo '<h3 class="mt-3">'.htmlspecialchars($trip['trip_name']).'</h3>';
        }
    }
    exit;
}

$trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

if ($trip_id <= 0) {
    echo "<p class='text-center text-muted'>⚠️ No trip selected.</p>";
    exit;
}

// Build WHERE conditions
$where = "e.trip_id = $trip_id";
if (!empty($search)) {
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $where .= " AND (e.category LIKE '%$safeSearch%' OR e.notes LIKE '%$safeSearch%')";
}

// Fetch expenses with optional image
$sql = "SELECT e.*, e.image AS expense_image 
        FROM expenses e
        WHERE $where
        ORDER BY e.expense_date DESC, e.id DESC";

$result = mysqli_query($conn, $sql);
$total = 0;
?>

<table class="table table-striped table-bordered">
  <thead class="table-dark">
    <tr>
      <th>Image</th>
      <th>Date</th>
      <th>Category</th>
      <th>Notes</th>
      <th class="text-end">Amount ($)</th>
      <th class="text-center">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
      <?php while ($exp = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td>
            <?php if (!empty($exp['expense_image'])): ?>
              <img src="<?= htmlspecialchars($exp['expense_image']); ?>" 
                   alt="Expense" 
                   style="width:60px; height:40px; object-fit:cover; border-radius:6px;">
            <?php else: ?>
              <span class="text-muted">—</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($exp['expense_date']); ?></td>
          <td><?= htmlspecialchars($exp['category']); ?></td>
          <td><?= htmlspecialchars($exp['notes']); ?></td>
          <td class="text-end"><?= number_format($exp['amount'], 2); ?></td>
          <td class="text-center">
            <!-- Edit -->
            <button class="btn btn-sm btn-warning"
              data-bs-toggle="modal"
              data-bs-target="#editModal"
              onclick="fillEditForm(
                '<?= $exp['id'] ?>',
                '<?= htmlspecialchars($exp['category'], ENT_QUOTES) ?>',
                '<?= $exp['amount'] ?>',
                '<?= $exp['expense_date'] ?>',
                '<?= htmlspecialchars($exp['notes'], ENT_QUOTES) ?>',
                '<?= htmlspecialchars($exp['expense_image'], ENT_QUOTES) ?>'
              )">
              Edit
            </button>
            <!-- Delete -->
            <button onclick="deleteExpense(<?= $exp['id'] ?>)" class="btn btn-sm btn-danger">Delete</button>
          </td>
        </tr>
        <?php $total += $exp['amount']; ?>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6" class="text-center">No expenses found.</td></tr>
    <?php endif; ?>
  </tbody>
  <tfoot class="table-light">
    <tr>
      <th colspan="4" class="text-end">Total</th>
      <th class="text-end">$<?= number_format($total, 2); ?></th>
      <th></th>
    </tr>
  </tfoot>
</table>
