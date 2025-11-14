<?php
include 'connect.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : "";

$where = "";
if (!empty($search)) {
    $safe = mysqli_real_escape_string($conn, $search);
    $where = "WHERE trip_name LIKE '%$safe%' OR destination LIKE '%$safe%'";
}

$sql = "SELECT id, trip_name, destination, start_date, end_date, image, budget, description 
        FROM trips $where ORDER BY id DESC LIMIT 9";
$result = mysqli_query($conn, $sql);

$html = "";
$delay = 0;

if ($result && mysqli_num_rows($result) > 0) {
    while ($trip = mysqli_fetch_assoc($result)) {
        $img = !empty($trip['image']) ? $trip['image'] : "images/default.jpg";
        $desc = !empty($trip['description']) ? $trip['description'] : 'No description available.';
        $shortDesc = strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
        $delay += 200;

        $html .= '
        <div class="col-md-4 col-sm-12 mb-4">
            <div class="trip-card shadow-lg" data-aos="fade-up" data-aos-delay="'.$delay.'" data-aos-duration="700">
                
                <div class="trip-image position-relative">
                    <img src="'.htmlspecialchars($img).'" alt="'.htmlspecialchars($trip['trip_name']).'" 
                        style="width:100%; height:250px; object-fit:cover;">
                    <span class="price-tag text-white px-3 py-1 fw-bold">
                        $'.number_format($trip['budget'], 2).'
                    </span>
                </div>

                <div class="trip-info p-3">
                    <h5>'.htmlspecialchars($trip['trip_name']).'</h5>
                    <p class="text-muted mb-1">'.htmlspecialchars($trip['destination']).'</p>
                    <p class="small text-muted mb-2">
                        '.date("M d, Y", strtotime($trip['start_date'])).' - 
                        '.date("M d, Y", strtotime($trip['end_date'])).'
                    </p>
                    <p class="trip-desc">'.htmlspecialchars($shortDesc).'</p>

                    <!-- ✅ yahan expenses button -->
                    <a href="expenses.php?trip_id='.$trip['id'].'" class="btn btn-gradient w-100 mt-2">View Expenses</a>
                </div>

            </div>
        </div>';
    }
} else {
    $html = "<p class='text-center'>No trips found.</p>";
}

echo $html;
?>
