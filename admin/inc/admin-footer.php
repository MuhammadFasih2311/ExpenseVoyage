      <!-- end row -->
    </div><!-- end container-fluid -->
  </div><!-- end body wrapper -->

  <footer class="text-center py-3 mt-4 shadow-sm" style="background:linear-gradient(90deg,var(--dark),#0f172a); color:#e2e8f0;">
    <small>© <?= date("Y"); ?> ExpenseVoyage Admin Panel</small>
  </footer>

  <!-- Reports Modal -->
<div class="modal fade" id="reportsModal" tabindex="-1" aria-labelledby="reportsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 shadow-lg border-0">
      <div class="modal-header bg-gradient text-white rounded-top-4">
        <h5 class="modal-title" id="reportsModalLabel"><i class="bi bi-graph-up"></i> Reports Center</h5>
        <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body bg-light">
        <div class="row g-4">
          <!-- Expenses Reports -->
          <div class="col-md-6">
            <a href="expenses-reports.php" class="text-decoration-none">
              <div class="card h-100 border-0 shadow-sm hover-shadow rounded-4 text-center p-4">
                <div class="card-body">
                  <div class="kpi-icon bg-warning mb-3 mx-auto"><i class="bi bi-wallet2 fs-2"></i></div>
                  <h5 class="fw-bold text-dark">Expenses Reports</h5>
                  <p class="text-muted small">View and export all user expenses.</p>
                </div>
              </div>
            </a>
          </div>

          <!-- Bookings Reports -->
          <div class="col-md-6">
            <a href="bookings-reports.php" class="text-decoration-none">
              <div class="card h-100 border-0 shadow-sm hover-shadow rounded-4 text-center p-4">
                <div class="card-body">
                  <div class="kpi-icon bg-primary mb-3 mx-auto"><i class="bi bi-journal-bookmark fs-2"></i></div>
                  <h5 class="fw-bold text-dark">Bookings Reports</h5>
                  <p class="text-muted small">Analyze bookings and payments by users & trips.</p>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>AOS.init({ duration:800 });</script>
<!-- 🔹 Sidebar Toggle JS -->
<script>
const sidebar = document.getElementById('sidebarMenu');
const openBtn = document.getElementById('openSidebarBtn');
const closeBtn = document.querySelector('.close-sidebar');

openBtn?.addEventListener('click', () => {
  sidebar.classList.add('show');
  openBtn.style.visibility = 'hidden'; // hide ☰
});
closeBtn?.addEventListener('click', () => {
  sidebar.classList.remove('show');
  setTimeout(() => { // wait for animation end
    openBtn.style.visibility = 'visible';
  }, 350);
});
window.addEventListener("load", function() {
  if (typeof AOS !== "undefined") {
    AOS.init();
    AOS.refresh(); // ensure visible elements render ho jayein
  }
});

</script>

<!-- 🔹 CSS -->
<style>
/* Sidebar base */
.sidebar {
  background:#0b1328;
  min-height:100vh;
  transition: transform 0.35s ease-in-out;
}

/* Mobile behavior */
@media(max-width: 991px){
  #sidebarMenu {
    position: fixed;
    transform: translateX(-100%);
    top: 0;
    bottom: 0;
    width: 260px;
    z-index: 1050;
    box-shadow: 3px 0 15px rgba(0,0,0,0.5);
  }
  #sidebarMenu.show {
    transform: translateX(0);
  }
}

/* ✖ button style */
.close-sidebar {
  background:rgba(255,255,255,0.1);
  border:1px solid rgba(255,255,255,0.3);
  color:#fff;
  transition:.3s;
}
.close-sidebar:hover {
  background:#dc3545;
  border-color:#dc3545;
}
</style>
</body>
</html>
