<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="View complete details of your selected trip on Expense Voyage – itinerary, expenses, and booking information all in one place.">
<title>Trip Details - Expense Voyage</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<style>
    html, body {
  overflow-x: hidden;
}
.trip-img {
    width: 100%;
    height: 350px;
    object-fit: cover;
    border-radius: 8px;
}
</style>
<body>

<?php
include 'connect.php';
include 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container py-5'><p class='text-danger'>Invalid Trip ID.</p></div>";
    include 'footer.php';
    exit;
}

$trip_id = intval($_GET['id']);

// Fetch trip details
$trip_sql = "SELECT * FROM trips WHERE id = $trip_id";
$trip_result = mysqli_query($conn, $trip_sql);

if (!$trip_result || mysqli_num_rows($trip_result) == 0) {
    echo "<div class='container py-5'><p class='text-danger'>Trip not found.</p></div>";
    include 'footer.php';
    exit;
}

$trip = mysqli_fetch_assoc($trip_result);

// Fetch itinerary
$itinerary_sql = "SELECT * FROM expenses_templates WHERE trip_id = $trip_id ORDER BY day ASC";
$itinerary_result = mysqli_query($conn, $itinerary_sql);
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-6" data-aos="fade-right" data-aos-delay="50">
            <img src="<?= !empty($trip['image']) ? htmlspecialchars($trip['image']) : 'img/trip-placeholder.jpg'; ?>" 
                 alt="<?= htmlspecialchars($trip['trip_name']); ?>" 
                 class="img-fluid rounded shadow trip-img">
        </div>
        <div class="col-md-6" >
            <h2 data-aos="fade-left" data-aos-delay="50"><?= htmlspecialchars($trip['trip_name']); ?></h2>
            <p class="text-muted mb-1" data-aos="fade-left" data-aos-delay="150">
                <i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($trip['destination']); ?>
            </p>
            <p class="small text-muted">
                <div data-aos="fade-left" data-aos-delay="200"><strong>Start:</strong> <?= date("M d, Y", strtotime($trip['start_date'])); ?></div>
                <div data-aos="fade-left" data-aos-delay="250"><strong data-aos="fade-left" data-aos-delay="250">End:</strong> <?= date("M d, Y", strtotime($trip['end_date'])); ?></div>
            </p>
            <h4 class="text-primary" data-aos="fade-left" data-aos-delay="300">Budget: $<?= number_format($trip['budget'], 2); ?></h4>
            <p class="mt-3" data-aos="fade-left" data-aos-delay="350"><?= nl2br(htmlspecialchars($trip['description'])); ?></p>
            <div data-aos="fade-left" data-aos-delay="400">
            <a href="expenses.php?trip_id=<?= $trip['id']; ?>" class="btn btn-gradient mt-3" data-aos="zoom-in" data-aos-delay="350">
                View Expenses
            </a>
            <a href="book-trip.php?trip_id=<?= $trip['id']; ?>" 
            class="btn btn-gradient mt-3" 
            data-aos="zoom-in" data-aos-delay="350">
            Book Trip
            </a>
        </div>
        </div>
    </div>

    <!-- Itinerary Section -->
    <h3 class="mt-5 mb-4 fw-bold" data-aos="fade-right" data-aos-delay="250">Itinerary</h3>
    <div class="row g-4">
        <?php
        if ($itinerary_result && mysqli_num_rows($itinerary_result) > 0) {
            while ($day = mysqli_fetch_assoc($itinerary_result)) {
                $imgPath = !empty($day['image']) ? $day['image'] : 'img/itinerary-placeholder.jpg';
                ?>
                <div class="col-md-6" data-aos="flip-left">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?= htmlspecialchars($imgPath) ?>" class="card-img-top" alt="Day <?= $day['day']; ?>">
                        <div class="card-body">
                            <h5 class="card-title">
                                Day <?= $day['day']; ?> - <?= htmlspecialchars($day['category']); ?>
                            </h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($day['notes'])); ?></p>
                            <p class="small text-muted mb-0">
                                <?= date("M d, Y", strtotime($day['expense_date'])); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="col-12 text-center py-5" data-aos="fade-up">
                <img src="img/no-itinerary.png" alt="No itinerary" style="max-width:200px;" class="mb-3">
                <h5 class="text-muted">Itinerary not planned yet</h5>
                <p class="text-muted">Plan your trip day-by-day to make the most of it!</p>
                <a href="add-itinerary.php?trip_id=<?= $trip['id']; ?>" class="btn btn-primary">
                    Add Itinerary
                </a>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<!-- Related Trips -->
<?php
$related_sql = "SELECT * FROM trips 
                WHERE id != " . intval($trip['id']) . " 
                ORDER BY RAND() LIMIT 3";
$related_trips = mysqli_query($conn, $related_sql);
?>

<?php if ($related_trips && $related_trips->num_rows > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <!-- Back Button -->
        <a href="trips.php" class="back-btn d-inline-block mb-4 text-decoration-none" data-aos="fade-right">
            <i class="fa-solid fa-arrow-left"></i> Back to Trips
        </a>
        <h3 class="mb-4 fw-bold" data-aos="fade-up">Related Trips</h3>
        <div class="row g-4">
            <?php while ($rel = $related_trips->fetch_assoc()): ?>
                <?php 
                    $imagePath = !empty($rel['image']) ? htmlspecialchars($rel['image']) : 'img/trip-placeholder.jpg';
                    $shortDesc = strlen($rel['description']) > 80 
                                ? substr($rel['description'], 0, 80) . '...' 
                                : $rel['description'];
                ?>
                <div class="col-md-4 col-sm-12" data-aos="zoom-in">
                    <div class="trip-card shadow-lg h-100">
                        <div class="trip-image position-relative">
                            <img src="<?= $imagePath; ?>" 
                                 alt="<?= htmlspecialchars($rel['trip_name']); ?>" 
                                 style="width:100%; height:250px; object-fit:cover;">
                            <span class="price-tag text-white px-3 py-1 fw-bold">
                                $<?= number_format($rel['budget'], 2); ?>
                            </span>
                        </div>
                        <div class="trip-info p-3">
                            <h5><?= htmlspecialchars($rel['trip_name']); ?></h5>
                            <p class="text-muted mb-1"><?= htmlspecialchars($rel['destination']); ?></p>
                            <p class="small text-muted mb-2">
                                <?= date("M d, Y", strtotime($rel['start_date'])); ?> - 
                                <?= date("M d, Y", strtotime($rel['end_date'])); ?>
                            </p>
                            <p class="trip-desc"><?= htmlspecialchars($shortDesc); ?></p>
                            <a href="trip-details.php?id=<?= $rel['id']; ?>" class="btn btn-gradient w-100 mt-2">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>

<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: false,
    mirror: true
  });
</script>
</body>
</html>
