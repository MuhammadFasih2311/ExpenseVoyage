<!-- FOOTER -->
<footer class="footer mt-5 pt-5">
  <div class="container">
    <div class="row gy-4">

  <!-- Logo & About -->
  <div class="col-md-3" data-aos="fade-right" data-aos-duration="1100">
    <h5>
      <a href="index.php">
        <img src="images/logo.png" alt="Expense Voyage Logo" width="40">
        <span style="color:#ff7a00">Expense</span>
        <span style="color:#0d6efd;">Voyage</span>
      </a>
    </h5>
    <p>Plan your trips, manage your expenses, and travel smart with ExpenseVoyage.</p>
  </div>

  <!-- Quick Links -->
  <div class="col-md-3" data-aos="zoom-in" data-aos-duration="1100">
    <h6>Quick Links</h6>
    <ul class="list-unstyled">
      <li><a href="index.php">Home</a></li>
      <li><a href="trips.php">Trips</a></li>
      <li><a href="expenses.php">Expenses</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="blogs.php">Blogs</a></li>
      <li><a href="contact.php">Contact</a></li>
    </ul>
  </div>

  <!-- Useful Links -->
  <div class="col-md-3" data-aos="zoom-in" data-aos-duration="1100">
    <h6>Useful Links</h6>
    <ul class="list-unstyled">
      <li><a href="book-trip.php">Bookings</a></li>
      <li><a href="expenses.php">Expenses</a></li>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="my-bookings.php">My Bookings</a></li>
    </ul>
  </div>

  <!-- Contact Info -->
  <div class="col-md-3" data-aos="fade-left" data-aos-duration="1100">
    <h6>Get in Touch</h6>
    <p>Email: <a href="mailto:support@expensevoyage.com">support@expensevoyage.com</a></p>
    <p>Phone: +92-300-0000000</p>
    <div class="social-icons">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
  </div>

    <hr>
    <div class="text-center pb-3">
      <small>© <?php echo date('Y'); ?> ExpenseVoyage. All rights reserved.</small>
    </div>
  </div>
</footer>
<!-- SCROLL TO TOP BUTTON -->
<button id="scrollTopBtn" class="btn btn-grad rounded-circle">
  ↑
</button>

<style>
#scrollTopBtn {
  position: fixed;
  bottom: 20px;
  right: 20px;
  display: none;
  z-index: 999;
}
</style>

<script>
let scrollTopBtn = document.getElementById("scrollTopBtn");
window.onscroll = function() {
  if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
    scrollTopBtn.style.display = "block";
  } else {
    scrollTopBtn.style.display = "none";
  }
};
scrollTopBtn.addEventListener("click", function() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>


<!-- JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  AOS.init({ once: true });
</script>

