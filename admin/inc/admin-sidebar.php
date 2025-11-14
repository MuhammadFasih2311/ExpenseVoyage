<?php 
function navItem($file, $icon, $label, $active) {
  $isActive = $active === $file ? 'active' : '';
  echo '<a href="'.$file.'" class="list-group-item list-group-item-action d-flex align-items-center '.$isActive.'">
          <i class="bi '.$icon.' me-2"></i> '.$label.'
        </a>';
}
?>
<style>
  /* Sidebar */
  .sidebar {
    background:#0b1328;
    min-height:100vh;
    position:sticky; 
    top:0;
    transition:.3s;
  }
  .sidebar-menu .list-group-item {
    color:#cbd5e1; border:0; background:transparent; font-weight:500; padding:12px 16px;
    transition:.25s ease;
  }
  .sidebar-menu .list-group-item:hover { color:#fff; background:rgba(255,255,255,.08); }
  .sidebar-menu .list-group-item.active {
    color:#fff; background:linear-gradient(90deg,var(--primary),var(--accent));
    border-radius:12px; margin:6px 10px;
  }

  @media(max-width:991px){
    .sidebar { 
      position:fixed; z-index:1000; width:220px; transform:translateX(-100%); 
    }
    .sidebar.show { transform:translateX(0); }
  }
</style>

  <!-- 🔹 Sidebar -->
   <aside id="sidebarMenu" class="col-12 col-md-3 col-lg-2 p-0 sidebar" data-aos="fade-right">
      
      <!-- ✖ Close button (only on mobile) -->
      <div class="d-flex justify-content-end d-lg-none p-3">
        <button class="btn btn-outline-light btn-sm close-sidebar rounded-circle shadow">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

     <div class="list-group list-group-flush sidebar-menu py-2">
  <?php
    navItem('dashboard.php','bi-speedometer2','Dashboard',$active);
    navItem('users.php','bi-people','Users',$active);
    navItem('admin-trips.php','bi-airplane','Trips',$active);
    navItem('admin-expenses.php','bi-wallet2','Expenses',$active);
    navItem('admin-bookings.php','bi-journal-bookmark','Bookings',$active);
    navItem('admin-blogs.php','bi-journal-text','Blogs',$active);
    navItem('messages.php','bi-envelope','Messages',$active);
  ?>
  
  <!-- Reports Modal Trigger -->
  <a href="#" 
     class="list-group-item list-group-item-action d-flex align-items-center" 
     data-bs-toggle="modal" data-bs-target="#reportsModal">
    <i class="bi bi-bar-chart me-2"></i> Reports
  </a>

  <hr class="text-secondary">
  <a href="logout.php" class="list-group-item list-group-item-action text-danger fw-bold">
    <i class="bi bi-box-arrow-right me-2"></i> Logout
  </a>
</div>

    </aside>