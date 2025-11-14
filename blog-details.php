<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<?php
include 'connect.php'; 

// Get blog ID
$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch blog + details using JOIN
$sql = "SELECT b.id, b.title, b.image, b.location, b.author, b.date, b.tags, b.reading_time, b.rating, b.category,
       d.content, d.gallery_images
        FROM blogs b
        LEFT JOIN blog_details d ON b.id = d.blog_id
        WHERE b.id = $blog_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $blog = $result->fetch_assoc();
} else {
    die("Blog not found.");
}
// Pehle same category ke blogs lao
$related_sql = "SELECT id, title, category, short_desc, image, tags, rating 
                FROM blogs 
                WHERE category = '" . $conn->real_escape_string($blog['category']) . "' 
                AND id != $blog_id ORDER BY RAND() LIMIT 3";
$related_blogs = $conn->query($related_sql);

// Agar same category blogs na mile, to fallback (random blogs)
if ($related_blogs->num_rows == 0) {
    $related_sql = "SELECT id, title, category, short_desc, image, tags, rating 
                    FROM blogs 
                    WHERE id != $blog_id ORDER BY RAND() LIMIT 3";
    $related_blogs = $conn->query($related_sql);
}

// Function to render star rating
function renderStars($rating) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
    $emptyStars = 5 - $fullStars - $halfStar;

    $stars = str_repeat('★', $fullStars);
    if ($halfStar) $stars .= '☆'; 
    $stars .= str_repeat('✩', $emptyStars);

    return $stars;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Read insightful travel stories, tips, and guides on Expense Voyage. Explore <?= htmlspecialchars($blog['title']); ?> and make your journeys more memorable and budget-friendly.">
<title><?= htmlspecialchars($blog['title']); ?> - Blog Detail | Expense Voyage</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html, body {
  overflow-x: hidden;
}
        .blog-detail-image {
            max-height: 450px;
            object-fit: cover;
            width: 100%;
            border-radius: 12px;
        }
        .tag-badge {
            background: #e0f7ff;
            color: #007bff;
            border-radius: 20px;
            padding: 5px 12px;
            margin: 3px;
            font-size: 0.85rem;
            display: inline-block;
        }
        .related-card img {
            height: 200px;
            object-fit: cover;
        }
        .back-btn {
            background: linear-gradient(90deg, #007bff, #00c6ff);
            color: white;
            border-radius: 6px;
            padding: 8px 14px;
            text-decoration: none;
        }
        .back-btn:hover { opacity: 0.9; }
        .rating {
            color: #FFD700;
            font-size: 1rem;
            letter-spacing: 2px;
        }
        .meta-info { font-size: 0.9rem; color: #6c757d; }
        .gallery img {
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.2s ease;
        }
        .gallery img:hover { transform: scale(1.05); }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<section class="py-5">
    <div class="container">
       <!-- Blog Title -->
       <h1 class="fw-bold mb-2" data-aos="fade-right" data-aos-delay="100">
         <?= htmlspecialchars($blog['title']); ?>
       </h1>

       <!-- Meta Info -->
       <p class="meta-info mb-2" data-aos="fade-right" data-aos-delay="200">
         <i class="fa-solid fa-user"></i> <strong><?= htmlspecialchars($blog['author']); ?></strong> | 
         <i class="fa-solid fa-calendar-days"></i> <?= date("F d, Y", strtotime($blog['date'])); ?> | 
         <i class="fa-solid fa-clock"></i> <?= htmlspecialchars($blog['reading_time'] ?? ''); ?>
       </p>

       <!-- Location -->
       <?php if (!empty($blog['location'])): ?>
         <p class="meta-info mb-2" data-aos="fade-right" data-aos-delay="250">
           <i class="fa-solid fa-map-marker-alt"></i> <?= htmlspecialchars($blog['location']); ?>
         </p>
       <?php endif; ?>

       <!-- Rating -->
       <?php if (!empty($blog['rating'])): ?>
         <div class="rating mb-3" data-aos="fade-right" data-aos-delay="300">
           <?= renderStars($blog['rating']); ?> 
           <span class="text-muted">(<?= number_format($blog['rating'], 1); ?>)</span>
         </div>
       <?php endif; ?>

       <!-- Tags -->
       <?php if (!empty($blog['tags'])): ?>
         <div class="mb-3" data-aos="fade-right" data-aos-delay="350">
           <?php foreach (explode(',', $blog['tags']) as $tag): ?>
             <span class="tag-badge"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars(trim($tag)); ?></span>
           <?php endforeach; ?>
         </div>
       <?php endif; ?>

       <!-- Main Image -->
       <img src="<?= htmlspecialchars($blog['image']); ?>" class="blog-detail-image mb-4"
            alt="<?= htmlspecialchars($blog['title']); ?>" data-aos="zoom-in-up" data-aos-delay="400">

       <!-- Content -->
       <p style="line-height: 1.8;" data-aos="fade-up" data-aos-delay="500">
         <?= nl2br(htmlspecialchars($blog['content'])); ?>
       </p>

       <!-- Gallery Images -->
       <?php if (!empty($blog['gallery_images'])): ?>
         <h2 class="mt-5 mb-3" data-aos="fade-right" data-aos-delay="600">Gallery</h2>
         <div class="row g-3 gallery">
           <?php 
           $delay = 100;
           foreach (explode(',', $blog['gallery_images']) as $gimg): 
             $gimg = trim($gimg); ?>
             <div class="col-6 col-md-4" data-aos="zoom-in" data-aos-delay="<?= $delay; ?>">
               <a href="<?= htmlspecialchars($gimg); ?>" data-lightbox="blog-gallery" data-title="<?= htmlspecialchars($blog['title']); ?>">
                 <img src="<?= htmlspecialchars($gimg); ?>" class="w-100" alt="Gallery Image">
               </a>
             </div>
           <?php $delay += 100; endforeach; ?>
         </div>
       <?php endif; ?>
    </div>
</section>

<!-- Related Blogs -->
<?php if ($related_blogs->num_rows > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <a href="blogs.php" class="back-btn mb-4 d-inline-block" data-aos="fade-right" data-aos-delay="100">
            <i class="fa-solid fa-arrow-left"></i> Back to Blogs
        </a>
        <h3 class="mb-4 fw-bold" data-aos="fade-up" data-aos-delay="150">Related Blogs</h3>
        <div class="row g-4">
            <?php 
            $relDelay = 200;
            while ($rel = $related_blogs->fetch_assoc()): ?>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= $relDelay; ?>">
                    <div class="card related-card shadow-sm h-100">
                        <img src="<?= htmlspecialchars($rel['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($rel['title']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($rel['title']); ?></h5>
                            <small class="text-muted"><?= htmlspecialchars($rel['category']); ?></small>
                            <!-- Tags -->
                            <?php if (!empty($rel['tags'])): ?>
                                <div class="mt-2">
                                    <?php foreach (explode(',', $rel['tags']) as $rtag): ?>
                                        <span class="tag-badge"><?= htmlspecialchars(trim($rtag)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <!-- Rating -->
                            <?php if (!empty($rel['rating'])): ?>
                                <div class="rating mt-2"><?= renderStars($rel['rating']); ?> (<?= number_format($rel['rating'], 1); ?>)</div>
                            <?php endif; ?>
                            <p class="card-text mt-2"><?= htmlspecialchars(substr($rel['short_desc'], 0, 80)); ?>...</p>
                            <a href="blog-details.php?id=<?= $rel['id']; ?>" class="btn btn-sm btn-grad w-100">Read More</a>
                        </div>
                    </div>
                </div>
            <?php $relDelay += 150; endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  AOS.init({ duration: 800, easing: 'ease-in-out', once: false });
</script>
</body>
</html>
