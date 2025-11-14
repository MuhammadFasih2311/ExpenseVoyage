<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Welcome to Expense Voyage – your ultimate travel companion for booking trips, managing expenses, and exploring the world stress-free.">
<title>Home - Expense Voyage</title>
  <link rel="icon" href="images/logo.png" type="image/png">
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <style>
    html, body {
      overflow-x: hidden;
    }
    .hero-section .swiper-slide {
      position: relative;
      height: 100vh;
    }
    .hero-section .overlay {
      z-index: 1;
    }
    .hero-section .content {
      z-index: 2;
    }
    .hero-section h1, 
    .hero-section p {
      text-shadow: 2px 2px 5px rgba(0,0,0,0.7);
    }

    /* ✅ Swiper Pagination Styling */
    .swiper-pagination-bullet {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: linear-gradient(45deg, #20c997, #0dcaf0); /* theme colors */
      opacity: 0.5;
      transition: all 0.3s ease;
    }
    .swiper-pagination-bullet-active {
      opacity: 1;
      transform: scale(1.3);
      box-shadow: 0 0 10px rgba(32, 201, 151, 0.7);
    }

    /* ✅ Gradient Button */
    .btn-gradient {
      background: linear-gradient(45deg, #20c997, #0dcaf0);
      border: none;
      color: #fff;
      font-weight: 500;
      padding: 8px 20px;
      border-radius: 30px;
      transition: all 0.3s ease;
    }
    .btn-gradient:hover {
      background: linear-gradient(45deg, #0dcaf0, #20c997);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
/* Default (Desktop / Tablet) */
.carousel-form-overlay {
  position: absolute;
  top: 70%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 80%;
  max-width: 900px;
  z-index: 10;
}

/* ✅ Extra small screens (<576px) */
@media (max-width: 585.98px) {
  .hero-section {
    height: auto !important; /* text + form ke liye jagah khud adjust hogi */
    padding-bottom: 20px;
  }
  .carousel-form-overlay {
    position: static;   /* ab overlay nahi, normal flow me aa jayega */
    transform: none;
    margin: 15px auto 0;
    width: 95%;
  }
}

  </style>
</head>
<body>
  <?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<?php include 'header.php'; ?>
<?php include 'connect.php'; ?>


<!-- HERO SECTION -->
<section class="position-relative hero-section" style="height: 100vh; overflow: hidden;">
  
  <!-- Swiper Container -->
  <div class="swiper hero-swiper w-100 h-100 mb-5">
    <div class="swiper-wrapper">

      <!-- Slide 1 -->
      <div class="swiper-slide d-flex align-items-center justify-content-center text-center text-white" style="background: url('images/hero-back.jpg') center/cover no-repeat;">
        <div class="overlay position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5);"></div>
        <div class="content position-relative" >
          <h1 class="fw-bold mb-3" data-aos="fade-up">Plan Your Next Adventure</h1>
          <p data-aos="fade-up">Discover amazing destinations and track your travel expenses easily</p>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="swiper-slide d-flex align-items-center justify-content-center text-center text-white" style="background: url('images/her0-back 2.jpg') center/cover no-repeat;">
        <div class="overlay position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5);"></div>
        <div class="content position-relative">
          <h1 class="fw-bold mb-3" data-aos="fade-up">Explore the World</h1>
          <p data-aos="fade-up">Make memories without worrying about expenses</p>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="swiper-slide d-flex align-items-center justify-content-center text-center text-white" style="background: url('images/hero-3.jpg') center/cover no-repeat;">
        <div class="overlay position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5);"></div>
        <div class="content position-relative">
          <h1 class="fw-bold mb-3" data-aos="fade-up">Travel Smart</h1>
          <p data-aos="fade-up">Budget-friendly trip planning at your fingertips</p>
        </div>
      </div>

    </div>

    <!-- Pagination (dots) -->
    <div class="swiper-pagination"></div>
  </div>

   <!-- ✅ Form Overlay -->
  <div class="carousel-form-overlay">
    <form action="home-fetch.php" method="get" class="hero-search p-3 rounded bg-white shadow-lg" data-aos="zoom-in">
      <div class="row g-2">
        <!-- col-12 = mobile full width, col-md-x = desktop split -->
        <div class="col-12 col-md-4 col-sm-4 col-lg-3">
          <input type="text" name="home_destination" class="form-control" placeholder="Destination" minlength="3" maxlength="30" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
        </div>
        <div class="col-12 col-md-4 col-sm-4 col-lg-3">
          <input type="date" name="home_date" class="form-control" id="homeDate">
        </div>
        <div class="col-12 col-md-4 col-sm-4 col-lg-3">
          <input type="number" name="home_budget" class="form-control" placeholder="Max Budget" maxlength="99999">
        </div>
        <div class="col-12 col-md-12 col-sm-12 col-lg-3">
          <button type="submit" class="btn btn-gradient w-100">Search</button>
        </div>
      </div>
    </form>
  </div>
</section>

<!-- date restrict -->
<script>
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0');
  const dd = String(today.getDate()).padStart(2, '0');
  const formattedToday = `${yyyy}-${mm}-${dd}`;
  document.getElementById("homeDate").setAttribute("min", formattedToday);
</script>

</section>

<?php
// 3 random trips fetch karna
$sql = "SELECT * FROM trips ORDER BY RAND() LIMIT 3";
$result = $conn->query($sql);
?>
<!-- FEATURED TRIPS -->
<section class="featured-trips py-5">
  <div class="container" data-aos="fade-up">
    <div class="text-center mb-5">
      <h2 class="fw-bold section-title">Featured Trips</h2>
      <p class="text-muted">Our top travel picks just for you</p>
    </div>
    <div class="row g-4">
  <?php if ($result->num_rows > 0): ?>
    <?php $delay = 300; // starting delay ?>
    <?php while($trip = $result->fetch_assoc()): ?>
      <div class="col-md-4 col-sm-12" data-aos="fade-up" data-aos-delay="<?= $delay; ?>">
        <div class="trip-card shadow-lg">
          <div class="trip-image">
            <img src="<?= $trip['image']; ?>" alt="<?= $trip['trip_name']; ?>">
            <span class="price-tag">$<?= number_format($trip['budget']); ?></span>
          </div>
          <div class="trip-info p-3">
            <h5><?= $trip['trip_name']; ?></h5>
            <p class="text-muted">
              <?= date("M d", strtotime($trip['start_date'])); ?> - 
              <?= date("M d, Y", strtotime($trip['end_date'])); ?><br>
              <?= $trip['destination']; ?>
            </p>
            <a href="trip-details.php?id=<?= $trip['id']; ?>" class="btn btn-gradient w-100">View Details</a>
          </div>
        </div>
      </div>
      <?php $delay += 100; // increase delay for next card ?>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-center">No trips found!</p>
  <?php endif; ?>
</div>
      <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="400">
        <a href="trips.php" class="btn btn-gradient px-4">View All Trips</a>
      </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-it-works py-5 bg-light">
  <div class="container" data-aos="fade-up">
    <div class="text-center mb-5">
      <h2 class="fw-bold section-title">How It Works</h2>
      <p class="text-muted">Plan your trip in just 3 simple steps</p>
    </div>

    <div class="row g-4 text-center">
      <!-- Step 1 -->
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="step-card p-4 shadow-sm h-100">
          <div class="icon mb-3">
            <i class="fa-solid fa-map-location-dot"></i>
          </div>
          <h5 class="fw-bold mb-2">Plan Your Trip</h5>
          <p class="text-muted">Choose your destination, dates, and budget easily.</p>
        </div>
      </div>

      <!-- Step 2 -->
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="step-card p-4 shadow-sm h-100">
          <div class="icon mb-3">
            <i class="fa-solid fa-coins"></i>
          </div>
          <h5 class="fw-bold mb-2">Track Your Expenses</h5>
          <p class="text-muted">Log and monitor your spending in real time.</p>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
        <div class="step-card p-4 shadow-sm h-100">
          <div class="icon mb-3">
            <i class="fa-solid fa-plane-departure"></i>
          </div>
          <h5 class="fw-bold mb-2">Enjoy Your Travel</h5>
          <p class="text-muted">Focus on fun while we handle your budget tracking.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- POPULAR DESTINATIONS -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <h2 class="fw-bold section-title">Popular Destinations</h2>
      <p class="text-muted">Most loved travel spots by our community</p>
    </div>

    <div id="destinationsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
      <div class="carousel-inner">
        <!-- Carousel Slide 1 -->
        <div class="carousel-item active">
          <div class="row g-4">
            <div class="col-md-4" data-aos="flip-left" data-aos-delay="200">
              <div class="card shadow-sm h-100 border-0 rounded-4">
                <img src="images/paris.jpg" class="card-img-top rounded-top-4" alt="Paris">
                <div class="card-body">
                  <h5 class="fw-bold">Paris, France</h5>
                  <p class="text-muted">The city of lights and love.</p>
                </div>
              </div>
            </div>
            <div class="col-md-4" data-aos="flip-left" data-aos-delay="250">
              <div class="card shadow-sm h-100 border-0 rounded-4">
                <img src="images/maldives.jpg" class="card-img-top rounded-top-4" alt="Maldives">
                <div class="card-body">
                  <h5 class="fw-bold">Maldives</h5>
                  <p class="text-muted">Paradise with turquoise waters.</p>
                </div>
              </div>
            </div>
            <div class="col-md-4" data-aos="flip-left" data-aos-delay="300">
              <div class="card shadow-sm h-100 border-0 rounded-4">
                <img src="images/rome.jpg" class="card-img-top rounded-top-4" alt="Rome">
                <div class="card-body">
                  <h5 class="fw-bold">Rome, Italy</h5>
                  <p class="text-muted">Historic beauty at every corner.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Carousel Slide 2 -->
        <div class="carousel-item">
          <div class="row g-4">
            <div class="col-md-4" data-aos="flip-left" data-aos-delay="200"> 
              <div class="card shadow-sm h-100 border-0 rounded-4">
                <img src="images/bali.jpg" class="card-img-top rounded-top-4" alt="Bali">
                <div class="card-body">
                  <h5 class="fw-bold">Bali, Indonesia</h5>
                  <p class="text-muted">Tropical paradise with scenic beaches.</p>
                </div>
              </div>
            </div>
            <div class="col-md-4" data-aos="flip-left" data-aos-delay="250">
              <div class="card shadow-sm h-100 border-0 rounded-4">
                <img src="images/newyork.jpg" class="card-img-top rounded-top-4" alt="New York">
                <div class="card-body">
                  <h5 class="fw-bold">New York, USA</h5>
                  <p class="text-muted">The city that never sleeps.</p>
                </div>
              </div>
            </div>
            <div class="col-md-4" data-aos="flip-left" data-aos-delay="300">
              <div class="card shadow-sm h-100 border-0 rounded-4">
                <img src="images/tokyo.jpg" class="card-img-top rounded-top-4" alt="Tokyo">
                <div class="card-body">
                  <h5 class="fw-bold">Tokyo, Japan</h5>
                  <p class="text-muted">A perfect mix of tradition & modernity.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Carousel Controls -->
      <button class="carousel-control-prev custom-btn" type="button" data-bs-target="#destinationsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next custom-btn" type="button" data-bs-target="#destinationsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
  </div>
</section>

<!-- STATS COUNTER -->
<section class="stats-section py-5 text-white" style="background: linear-gradient(90deg, #0855ca, #ff7a00);">
  <div class="container">
    <div class="row text-center" data-aos="zoom-in">
      <div class="col-md-3 mb-4">
        <h2 class="fw-bold counter" data-target="250">0</h2>
        <p>Trips Planned</p>
      </div>
      <div class="col-md-3 mb-4">
        <h2 class="fw-bold counter" data-target="500">0</h2>
        <p>Happy Travelers</p>
      </div>
      <div class="col-md-3 mb-4">
        <h2 class="fw-bold counter" data-target="150">0</h2>
        <p>Destinations Covered</p>
      </div>
      <div class="col-md-3 mb-4">
        <h2 class="fw-bold counter" data-target="120">0</h2>
        <p>Travel Guides</p>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials py-5" style="background-image: url('images/background1.jpg');">
  <div class="overlay"></div>
  <div class="container position-relative" >
    <div class="text-center mb-5 text-white">
      <h2 class="fw-bold" data-aos="fade-up">What Our Travelers Say</h2>
      <p data-aos="fade-up" data-aos-delay="100">Real experiences from our happy customers</p>
    </div>

    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-aos="fade-up" data-aos-delay="200">
      <div class="carousel-inner">

        <!-- Testimonial 1 -->
        <div class="carousel-item active text-center">
          <div class="testimonial-card p-4 mx-auto">
            <img src="images\traveler1.jpg" class="rounded-circle mb-3" alt="User 1">
            <p class="mb-3">"ExpenseVoyage made my Bali trip stress-free. Tracking my expenses was super easy!"</p>
            <h6 class="fw-bold">Yasir Ali</h6>
            <small class="text-muted">Bali, Indonesia</small>
          </div>
        </div>

        <!-- Testimonial 2 -->
        <div class="carousel-item text-center">
          <div class="testimonial-card p-4 mx-auto">
            <img src="images\traveller 3.jpg" class="rounded-circle mb-3" alt="User 2">
            <p class="mb-3">"The best tool for planning and managing trips. I used it for Switzerland and it was perfect!"</p>
            <h6 class="fw-bold">Hira Khan</h6>
            <small class="text-muted">Swiss Alps</small>
          </div>
        </div>

        <!-- Testimonial 3 -->
        <div class="carousel-item text-center">
          <div class="testimonial-card p-4 mx-auto">
            <img src="images\traveller 2.jpg" class="rounded-circle mb-3" alt="User 3">
            <p class="mb-3">"No more budget headaches. ExpenseVoyage kept my Dubai vacation on track."</p>
            <h6 class="fw-bold">Ahmed Faraz</h6>
            <small class="text-muted">Dubai, UAE</small>
          </div>
        </div>

      </div>

      <!-- Carousel Controls -->
      <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
  </div>
</section>

<!-- CALL TO ACTION -->
<section class="cta-section py-5">
  <div class="container text-center text-white" data-aos="zoom-in">
    <h2 class="fw-bold mb-3">Ready to Plan Your Next Adventure?</h2>
    <p class="mb-4" data-aos="zoom-in" data-aos-delay="100">Sign up now and start organizing your dream trip with ExpenseVoyage.</p>
    <a href="expenses.php" class="btn btn-light btn-lg me-2" data-aos="fade-up" data-aos-delay="200">Get Started</a>
    <a href="trips.php" class="btn btn-outline-light btn-lg" data-aos="fade-up" data-aos-delay="300">Browse Trips</a>
  </div>
</section>

<!-- LATEST TRAVEL BLOGS -->
<section class="latest-blogs py-5 bg-light" id="get">
  <div class="container" data-aos="fade-up">
    <div class="text-center mb-5" data-aos="zoom-in" data-aos-delay="100">
      <h2 class="fw-bold section-title">Latest Travel Tips & Stories</h2>
      <p class="text-muted">Inspiration and guidance for your next trip</p>
    </div>

    <div class="row g-4">
      <?php
      $blogs = $conn->query("SELECT * FROM blogs ORDER BY date DESC LIMIT 3");

      if ($blogs->num_rows > 0) {
        $delay = 100; // animation delay increment
        while ($blog = $blogs->fetch_assoc()) {
      ?>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
          <div class="blog-card shadow-sm h-100">
            <div class="blog-img" data-aos="zoom-in" data-aos-delay="<?php echo $delay + 100; ?>">
              <img src="<?php echo htmlspecialchars($blog['image']); ?>" 
                   class="img-fluid rounded-top" 
                   alt="<?php echo htmlspecialchars($blog['title']); ?>">
            </div>
            <div class="p-3" data-aos="fade-right" data-aos-delay="<?php echo $delay + 200; ?>">
              <h5 class="fw-bold"><?php echo htmlspecialchars($blog['title']); ?></h5>
              <div class="underline mb-2"></div>
              <p class="text-muted"><?php echo htmlspecialchars($blog['short_desc']); ?></p>
              <a href="blog-details.php?id=<?php echo $blog['id']; ?>" 
                 class="btn btn-grad btn-sm" 
                 data-aos="zoom-in" 
                 data-aos-delay="<?php echo $delay + 200; ?>">
                 Read More
              </a>
            </div>
          </div>
        </div>
      <?php
          $delay += 200; // next card thoda late animate hoga
        }
      } else {
        echo "<p class='text-center text-muted'>No blogs available right now.</p>";
      }
      ?>
      
      <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="600">
        <a href="blogs.php" class="btn btn-gradient px-4">View All Blogs</a>
      </div>
    </div>
  </div>
</section>

<!-- NEWSLETTER -->
<section class="newsletter-section py-5 bg-light">
  <div class="container text-center" data-aos="fade-up">
    <h3 class="fw-bold section-title mb-3">Stay Updated!</h3>
    <p class="text-muted mb-4">Get the latest travel tips & offers directly to your inbox</p>
    <form class="row g-2 justify-content-center">
      <div class="col-md-4 col-sm-8">
        <input type="email" class="form-control" placeholder="Enter your email" required>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-gradient px-4">Subscribe</button>
      </div>
    </form>
  </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  AOS.init({
    duration: 800, // animation ka time (ms) slow aur smooth ke liye
    easing: 'ease-in-out', // smooth easing
    once: false, // baar-baar animation chalay
  });
</script>
<script>
document.querySelectorAll('.counter').forEach(counter => {
  let target = +counter.getAttribute('data-target');
  let count = 0;
  let step = target / 100;
  function updateCounter() {
    if(count < target){
      count += step;
      counter.innerText = Math.ceil(count);
      requestAnimationFrame(updateCounter);
    } else {
      counter.innerText = target;
    }
  }
  updateCounter();
});

 const swiper = new Swiper('.hero-swiper', {
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    grabCursor: true,
    pagination: {
      el: '.swiper-pagination',
      clickable: true
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev'
    }
  });
</script>
</body>
</html>