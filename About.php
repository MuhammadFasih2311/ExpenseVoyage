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
<meta name="description" content="Discover Expense Voyage – learn about our journey, vision, and commitment to making your travel planning, expense tracking, and trip bookings easier than ever.">
<title>About | Expense Voyage</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="images/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
html, body {
  overflow-x: hidden;
}
/* Card hover effect */
  .mission-card {
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    cursor: default;
  }

  .mission-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 30px rgba(0,0,0,0.25);
  }

  /* Icon gradient */
  .mission-card .icon-wrapper i {
    background: linear-gradient(135deg, #20c997, #6610f2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  /* Responsive padding */
  @media (max-width: 768px) {
    .mission-card {
      padding: 3rem 2rem !important;
    }
  }
  .mission-vision-section {
  background: linear-gradient(to right, #f8f9fa, #eef2f7);
}
</style>
<body>
<?php include 'header.php'; ?>
<?php include 'connect.php'; ?>
    
<!-- ABOUT HERO -->
<section class="about-hero d-flex align-items-center justify-content-center text-center position-relative" 
         style="background-image: url('images/about.jpg'); background-attachment: fixed; background-size: cover; background-position: center; height: 350px;">
  
  <!-- Dark Overlay -->
  <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5);"></div>

  <!-- Text Content -->
  <div class="container position-relative text-white" data-aos="zoom-in">
    <h1 class="fw-bold">About Us</h1>
    <p class="lead">Your trusted partner in planning unforgettable journeys</p>
  </div>

</section>

<!-- OUR STORY -->
<section class="py-5 our-story-section">
  <div class="container">
    <div class="row align-items-center flex-column-reverse flex-md-row">
      
      <!-- Image -->
      <div class="col-md-6 mt-3" data-aos="fade-right">
        <div class="story-image-wrapper shadow-lg rounded overflow-hidden">
          <img src="images/about-1.jpg" alt="Our Story" class="img-fluid w-100">
        </div>
      </div>

      <!-- Text -->
      <div class="col-md-6 text-center" data-aos="fade-left">
        <h2 class="fw-bold mb-3 section-heading">Our Story</h2>
        <p class="lead text-muted">
          ExpenseVoyage was founded with a mission to make travel planning and expense tracking simple, fun, and stress-free. 
        </p>
        <p>
          We believe that every journey should be an adventure, and managing budgets should never get in the way of exploring the world. From solo travelers to families, our platform helps you plan trips, manage itineraries, and track every penny along the way.
        </p>
        <a href="contact.php" class="btn btn-gradient mt-3">
          Get in Touch <i class="fas fa-arrow-right ms-2"></i>
        </a>
      </div>

    </div>
  </div>
</section>

<!-- MISSION & VISION -->
<section class="py-5 mission-vision-section">
  <div class="container text-center">
    <div class="row g-4">

      <!-- Mission -->
      <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="p-5 rounded-4 shadow-lg bg-white mission-card position-relative overflow-hidden">
          <div class="icon-wrapper mb-3">
            <i class="fa-solid fa-bullseye fs-1"></i>
          </div>
          <h4 class="fw-bold mb-3">Our Mission</h4>
          <p class="text-muted">To empower travelers with tools that make trip planning and expense management effortless.</p>
        </div>
      </div>

      <!-- Vision -->
      <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="p-5 rounded-4 shadow-lg bg-white mission-card position-relative overflow-hidden">
          <div class="icon-wrapper mb-3">
            <i class="fa-solid fa-eye fs-1"></i>
          </div>
          <h4 class="fw-bold mb-3">Our Vision</h4>
          <p class="text-muted">To become the world’s most trusted travel companion, inspiring millions to explore without limits.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- OUR TEAM -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold section-title" data-aos="fade-up">Meet Our Team</h2>
      <p class="text-muted" data-aos="fade-up" data-aos-delay="200">
        Passionate travelers & tech enthusiasts behind ExpenseVoyage
      </p>
    </div>
    <div class="row g-4">
      
      <!-- Member 1 -->
      <div class="col-md-4 text-center" data-aos="fade-up">
        <div class="team-card p-3 rounded shadow-sm bg-white">
          <div class="team-img mb-3">
            <img src="images/member1.jpg" alt="Team Member" class="rounded-circle">
          </div>
          <h5 class="fw-bold border-bottom pb-1">Ali Raza</h5>
          <small class="">CEO & Founder</small>
        </div>
      </div>

      <!-- Member 2 -->
      <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="200">
        <div class="team-card p-3 rounded shadow-sm bg-white">
          <div class="team-img mb-3">
            <img src="images/member3.png" alt="Team Member" class="rounded-circle">
          </div>
          <h5 class="fw-bold border-bottom pb-1">Hamza Ahmed</h5>
          <small class="">Lead Developer</small>
        </div>
      </div>

      <!-- Member 3 -->
      <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="400">
        <div class="team-card p-3 rounded shadow-sm bg-white">
          <div class="team-img mb-3">
            <img src="images/member2.png" alt="Team Member" class="rounded-circle">
          </div>
          <h5 class="fw-bold border-bottom pb-1">Ayesha Khan</h5>
          <small class="">Marketing Head</small>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- FAQ / Accordion Section -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold section-title" data-aos="fade-up">Frequently Asked Questions</h2>
      <p class="text-muted" data-aos="fade-up" data-aos-delay="200">
        Answers to common queries about ExpenseVoyage
      </p>
    </div>

    <div class="accordion custom-accordion shadow-lg rounded-3 overflow-hidden" id="faqAccordion" data-aos="fade-up" data-aos-delay="300">

      <!-- Item 1 -->
      <div class="accordion-item border-0">
        <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button collapsed fw-bold d-flex align-items-center" type="button"
                  data-bs-toggle="collapse" data-bs-target="#collapseOne"
                  aria-expanded="false" aria-controls="collapseOne">
            <span>How do I plan a trip using ExpenseVoyage?</span>
            <span class="icon ms-auto">+</span>
          </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse"
             aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Simply sign up, create a new trip, add destinations, and our platform will help you organize expenses, activities, and itineraries easily.
          </div>
        </div>
      </div>

      <!-- Item 2 -->
      <div class="accordion-item border-0">
        <h2 class="accordion-header" id="headingTwo">
          <button class="accordion-button collapsed fw-bold d-flex align-items-center" type="button"
                  data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                  aria-expanded="false" aria-controls="collapseTwo">
            <span>Can I share my trip plan with friends?</span>
            <span class="icon ms-auto">+</span>
          </button>
        </h2>
        <div id="collapseTwo" class="accordion-collapse collapse"
             aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes! You can invite friends to view or even edit your travel plan, making group trips super easy to manage.
          </div>
        </div>
      </div>

      <!-- Item 3 -->
      <div class="accordion-item border-0">
        <h2 class="accordion-header" id="headingThree">
          <button class="accordion-button collapsed fw-bold d-flex align-items-center" type="button"
                  data-bs-toggle="collapse" data-bs-target="#collapseThree"
                  aria-expanded="false" aria-controls="collapseThree">
            <span>Is ExpenseVoyage free to use?</span>
            <span class="icon ms-auto">+</span>
          </button>
        </h2>
        <div id="collapseThree" class="accordion-collapse collapse"
             aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Absolutely! Basic features are free. Premium plans are available for advanced tools and customization.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Travel Tips / Top Destinations Preview -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <h2 class="fw-bold section-title">From Our Travel Journal</h2>
      <p class="text-muted" data-aos-delay="200">Stories & insights straight from our latest adventures</p>
    </div>

   <div class="row g-4 mt-3">
  <?php
  $sqlPreview = "SELECT * FROM blogs ORDER BY date ASC LIMIT 3";
  $previewBlogs = $conn->query($sqlPreview);

  if ($previewBlogs->num_rows > 0):
    $delay = 0;
    while ($blog = $previewBlogs->fetch_assoc()):
  ?>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= $delay; ?>" data-aos-duration="800">
      <div class="blog-card shadow-sm h-100">
        <img src="<?= htmlspecialchars($blog['image']); ?>" 
             class="img-fluid rounded-top" 
             alt="<?= htmlspecialchars($blog['title']); ?>">
        <div class="p-3">
          <h5 class="fw-bold"><?= htmlspecialchars($blog['title']); ?></h5>
          <small class="text-muted d-block mb-2">
            <?= htmlspecialchars($blog['category']); ?> | <?= date("F d, Y", strtotime($blog['date'])); ?>
          </small>
          <p class="text-muted"><?= htmlspecialchars($blog['short_desc']); ?></p>
          <a href="blog-details.php?id=<?= $blog['id']; ?>" class="btn btn-grad btn-sm w-100">Read More</a>
        </div>
      </div>
    </div>
  <?php
      $delay += 200; // har card me 200ms delay add hoga (staggered effect)
    endwhile;
  else:
    echo "<p class='text-center text-muted'>No blogs available.</p>";
  endif;
  ?>
</div>

<div class="text-center mt-4" data-aos="zoom-in" data-aos-duration="800">
  <a href="blogs.php" class="btn btn-gradient px-4">View All Blogs</a>
</div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  AOS.init({
    duration: 800,   // thoda slow aur elegant
    easing: 'ease-in-out', 
    once: false,      // scroll karte hi dobara animation chale
    offset: 120       // thoda neeche scroll pe start ho
  });
</script>
<script>
document.querySelectorAll('.custom-accordion .accordion-button').forEach(button => {
  button.addEventListener('click', function (e) {
    const targetId = this.getAttribute('data-bs-target');
    const target = document.querySelector(targetId);
    const collapse = bootstrap.Collapse.getInstance(target) || new bootstrap.Collapse(target, { toggle: false });

    // agar already open hai -> close manually
    if (!this.classList.contains('collapsed')) {
      e.preventDefault();
      collapse.hide();
    }
  });
});

// icon toggle
const accordions = document.querySelectorAll('.custom-accordion .accordion-collapse');
accordions.forEach(acc => {
  acc.addEventListener('show.bs.collapse', e => {
    e.target.previousElementSibling.querySelector('.icon').textContent = '×';
  });
  acc.addEventListener('hide.bs.collapse', e => {
    e.target.previousElementSibling.querySelector('.icon').textContent = '+';
  });
});

</script>
</body>
</html>