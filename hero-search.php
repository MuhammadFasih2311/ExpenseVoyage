    <form action="home-fetch.php" method="get" class="hero-search p-3 rounded bg-white shadow-lg" data-aos="zoom-in">
      <div class="row g-2">
        <!-- col-12 = mobile full width, col-md-x = desktop split -->
        <div class="col-md-4 col-lg-3">
          <input type="text" name="home_destination" class="form-control" placeholder="Destination" maxlength="30" minlength="3">
        </div>
        <div class="col-md-4  col-lg-3">
          <input type="date" name="home_date" class="form-control" id="homeDate">
        </div>
        <div class="col-md-4 col-lg-3" >
          <input type="number" name="home_budget" class="form-control" placeholder="Max Budget">
        </div>
        <div class="col-md-12 col-lg-3">
          <button type="submit" class="btn btn-gradient w-100">Search</button>
        </div>
      </div>
    </form>

<!-- date restrict -->
<script>
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0');
  const dd = String(today.getDate()).padStart(2, '0');

  const formattedToday = `${yyyy}-${mm}-${dd}`;
  document.getElementById("homeDate").setAttribute("min", formattedToday);
</script>