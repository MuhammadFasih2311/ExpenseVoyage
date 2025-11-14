<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<?php include 'connect.php'; ?>

<?php
// SETTINGS
$blogsPerPage = 6;

// Get filters from URL
$categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : '';

// Current page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $blogsPerPage;

// Base query
$sql = "SELECT * FROM blogs WHERE 1=1";

// Only category filter
if (!empty($categoryFilter)) {
    $sql .= " AND category = '" . $conn->real_escape_string($categoryFilter) . "'";
}

// Count total blogs for pagination
$countResult = $conn->query($sql);
$totalBlogs = $countResult->num_rows;
$totalPages = ceil($totalBlogs / $blogsPerPage);

// Add ordering & limit
$sql .= " ORDER BY date DESC LIMIT $offset, $blogsPerPage";
$blogs = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Explore the Expense Voyage travel blog – tips, guides, and inspiring stories to help you plan affordable and unforgettable trips.">
<title>Blogs - Expense Voyage</title>
  <link rel="icon" href="images/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    html, body {
  overflow-x: hidden;
}
   .category-tabs {
  display: flex;
  flex-wrap: wrap; /* ✅ Allows wrapping on small screens */
  justify-content: center;
  gap: 10px;
  list-style: none;
  padding: 0;
}

.category-tabs li {
  flex: 0 0 auto; /* ✅ Keeps each button sized naturally */
}

.category-tabs a {
  padding: 8px 16px;
  border-radius: 20px;
  text-decoration: none;
  border: 1px solid #ddd;
  color: #555;
  transition: 0.3s;
  white-space: nowrap; /* ✅ Prevents text from breaking mid-word */
}

.category-tabs a.active,
.category-tabs a:hover {
  background: linear-gradient(90deg, #007bff, #00c6ff);
  color: #fff !important;
  border-color: transparent;
}
.blog-card img {
  height: 200px;
  object-fit: cover;
}
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="page-hero d-flex align-items-center justify-content-center text-center position-relative"
    style="background-image: url('images/blogs.jpg'); background-attachment: fixed; background-size: cover; background-position: center; height: 350px;">

    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5);"></div>
    <div class="container position-relative text-white" data-aos="zoom-in">
        <h1 class="fw-bold">Travel Blog</h1>
        <p class="lead">Stories, tips & guides from our travel experts</p>
    </div>
</section>

<section class="py-5">
  <div class="container" data-aos="fade-up">
    <div class="text-center mb-5">
      <h2 class="fw-bold section-title">Latest Travel Stories</h2>
      <p class="lead">Explore guides, tips & inspiring journeys</p>
    </div>

    <!-- Category Tabs -->
<ul class="category-tabs mb-4 flex-wrap">
  <?php
  $categories = ["", "Trip Guides", "Budget Tips", "Packing Checklists", "Top Destinations"];
  $delay = 0;
  foreach ($categories as $cat):
    $active = ($categoryFilter === $cat) ? 'active' : '';
    $label = $cat === "" ? "All" : $cat;
    $url = "blogs.php?category=" . urlencode($cat);
  ?>
    <li data-aos="fade-right" data-aos-delay="<?= $delay ?>">
      <a class="<?= $active ?>" href="<?= $url ?>"><?= $label ?></a>
    </li>
  <?php 
    $delay += 150; // ✅ har next button 0.15s delay ke sath
  endforeach; ?>
</ul>

<!-- Blogs Grid -->
<div class="row g-4 mt-3" id="blogsContainer">
  <?php if ($blogs->num_rows > 0): ?>
    <?php 
      $delay = 0;
      while ($blog = $blogs->fetch_assoc()): 
    ?>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
        <div class="blog-card shadow-sm h-100">
          <img src="<?= htmlspecialchars($blog['image']); ?>" class="img-fluid rounded-top" alt="<?= htmlspecialchars($blog['title']); ?>">
          <div class="p-3">
            <h5 class="fw-bold"><?= htmlspecialchars($blog['title']); ?></h5>
            <small class="text-muted d-block mb-2"><?= htmlspecialchars($blog['category']); ?> | <?= date("F d, Y", strtotime($blog['date'])); ?></small>
            <p class="text-muted"><?= htmlspecialchars($blog['short_desc']); ?></p>
            <a href="blog-details.php?id=<?= $blog['id']; ?>" class="btn btn-grad btn-sm w-100">Read More</a>
          </div>
        </div>
      </div>
    <?php 
      $delay += 200; // ✅ har card 0.2s delay ke sath
      endwhile; 
    ?>
  <?php else: ?>
    <p class='text-center text-muted'>No blogs available.</p>
  <?php endif; ?>
</div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <nav class="mt-5" id="paginationContainer" data-aos="fade-up" data-aos-delay="700">
        <ul class="pagination justify-content-center">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i; ?>&category=<?= urlencode($categoryFilter); ?>"><?= $i; ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>

  </div>
</section>

<?php include 'footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
AOS.init({
  duration: 800,
  easing: 'ease-in-out',
  once: false
});
</script>
<script>
function loadBlogs(page = 1, category = "") {
    $.ajax({
        url: "blogs_fetch.php",
        type: "GET",
        data: { page: page, category: category },
        success: function(data) {
            let res = JSON.parse(data);
            $("#blogsContainer").html(res.blogs);
            $("#paginationContainer").html(res.pagination);
            AOS.refresh(); // ✅ animations reinit
        }
    });
}

$(document).ready(function(){
    // initial load
    loadBlogs();

    // category click
    $(".category-tabs a").on("click", function(e){
        e.preventDefault();
        $(".category-tabs a").removeClass("active");
        $(this).addClass("active");
        let cat = $(this).text() === "All" ? "" : $(this).text();
        loadBlogs(1, cat);
    });

    // pagination click
    $(document).on("click", ".blog-page", function(e){
        e.preventDefault();
        let page = $(this).data("page");
        let cat = $(".category-tabs a.active").text();
        cat = (cat === "All") ? "" : cat;
        loadBlogs(page, cat);
    });
});
</script>
</body>
</html>
