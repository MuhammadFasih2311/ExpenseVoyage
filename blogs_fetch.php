<?php
include 'connect.php';

$blogsPerPage = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : "";
$offset = ($page - 1) * $blogsPerPage;

// WHERE condition
$where = [];
if (!empty($categoryFilter)) {
    $where[] = "category = '" . mysqli_real_escape_string($conn, $categoryFilter) . "'";
}
$whereSql = count($where) ? " WHERE " . implode(" AND ", $where) : "";

// total blogs
$countSql = "SELECT COUNT(*) AS total FROM blogs $whereSql";
$countResult = mysqli_query($conn, $countSql);
$totalBlogs = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalBlogs / $blogsPerPage);

// fetch blogs
$sql = "SELECT * FROM blogs $whereSql ORDER BY date DESC LIMIT $offset, $blogsPerPage";
$result = mysqli_query($conn, $sql);

// blogs HTML
$blogsHtml = "";
if ($result && mysqli_num_rows($result) > 0) {
    $delay = 0;
    while ($blog = mysqli_fetch_assoc($result)) {
        $delay += 200;
        $blogsHtml .= '
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="'.$delay.'">
          <div class="blog-card shadow-sm h-100">
            <img src="'.htmlspecialchars($blog['image']).'" class="img-fluid rounded-top" alt="'.htmlspecialchars($blog['title']).'">
            <div class="p-3">
              <h5 class="fw-bold">'.htmlspecialchars($blog['title']).'</h5>
              <small class="text-muted d-block mb-2">'.htmlspecialchars($blog['category']).' | '.date("F d, Y", strtotime($blog['date'])).'</small>
              <p class="text-muted">'.htmlspecialchars($blog['short_desc']).'</p>
              <a href="blog-details.php?id='.$blog['id'].'" class="btn btn-grad btn-sm w-100">Read More</a>
            </div>
          </div>
        </div>';
    }
} else {
    $blogsHtml = "<p class='text-center text-muted'>No blogs available.</p>";
}

// pagination HTML
$paginationHtml = '<ul class="pagination justify-content-center">';
if ($page > 1) {
    $paginationHtml .= '<li class="page-item"><a class="page-link blog-page" href="#" data-page="'.($page - 1).'">Previous</a></li>';
}
for ($i = 1; $i <= $totalPages; $i++) {
    $active = $i == $page ? 'active' : '';
    $paginationHtml .= '<li class="page-item '.$active.'"><a class="page-link blog-page" href="#" data-page="'.$i.'">'.$i.'</a></li>';
}
if ($page < $totalPages) {
    $paginationHtml .= '<li class="page-item"><a class="page-link blog-page" href="#" data-page="'.($page + 1).'">Next</a></li>';
}
$paginationHtml .= '</ul>';

echo json_encode([
    "blogs" => $blogsHtml,
    "pagination" => $paginationHtml
]);
