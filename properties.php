<?php
require_once 'functions.php';

$search_type = $_GET['type'] ?? '';
$min_price = $_GET['min_price'] ?? null;
$max_price = $_GET['max_price'] ?? null;
$location_search = $_GET['location'] ?? '';

$properties = get_properties($search_type, $min_price, $max_price, $location_search);

// Get property types for the filter dropdown and list
$property_types = get_property_types();
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title> Real Estate - Properties </title>

    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>

  </head>

<body>

  <div id="js-preloader" class="js-preloader">
    <div class="preloader-inner">
      <span class="dot"></span>
      <div class="dots">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </div>

  <div class="sub-header">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-8">
          <ul class="info">
            <li><i class="fa fa-envelope"></i> info@realestate.com</li>
            <li><i class="fa fa-map"></i> HAMA</li>
          </ul>
        </div>
        <div class="col-lg-4 col-md-4">
          <ul class="social-links">
            <li><a href="#"><i class="fab fa-facebook"></i></a></li>
            <li><a href="https://x.com/minthu" target="_blank"><i class="fab fa-twitter"></i></a></li>
            <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                   
                    <a href="index.php" class="logo">
                        <h1>Villa</h1>
                    </a>
                    
                    <ul class="nav">
                      <li><a href="index.php">Home</a></li>
                      <li><a href="properties.php" class="active">Properties</a></li>
                     
                      <li><a href="contact.php">Contact Us</a></li>
                      <?php if (!check_login()): ?>
                        <li><a href="login.php"><i class="fa fa-user"></i> Login / Register</a></li>
                      <?php else: ?>
                        <?php if (is_admin()): ?>
                            <li><a href="admin_dashboard.php"><i class="fa fa-cog"></i> Admin Dashboard</a></li>
                        <?php else: ?>
                            <li><a href="my_orders.php"><i class="fa fa-list"></i> My Orders</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                      <?php endif; ?>
                  </ul>   
                    <a class='menu-trigger'>
                        <span>Menu</span>
                    </a>
                    
                </nav>
            </div>
        </div>
    </div>
  </header>
 

  <div class="page-heading header-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <span class="breadcrumb"><a href="#">Home</a> / Properties</span>
          <h3>Properties</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="section properties">
    <div class="container">
        <!-- Smart Search Form -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <form action="properties.php" method="GET" class="row gx-3 gy-2 align-items-center">
                    <div class="col-sm-3">
                        <label class="visually-hidden" for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="Search by Location" value="<?php echo htmlspecialchars($location_search); ?>">
                    </div>
                    <div class="col-sm-3">
                        <label class="visually-hidden" for="type">Property Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="Show All" <?php echo ($search_type == 'Show All' || $search_type == '') ? 'selected' : ''; ?>>Show All</option>
                            <?php foreach ($property_types as $type_option): ?>
                                <option value="<?php echo htmlspecialchars($type_option['type_name']); ?>" <?php echo ($search_type == $type_option['type_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type_option['type_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="visually-hidden" for="min_price">Min Price</label>
                        <input type="number" class="form-control" id="min_price" name="min_price" placeholder="Min Price" value="<?php echo htmlspecialchars($min_price); ?>">
                    </div>
                    <div class="col-sm-2">
                        <label class="visually-hidden" for="max_price">Max Price</label>
                        <input type="number" class="form-control" id="max_price" name="max_price" placeholder="Max Price" value="<?php echo htmlspecialchars($max_price); ?>">
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Smart Search Form -->

      <ul class="properties-filter">
        <li>
          <a class="is_active" href="properties.php" data-filter="*">Show All</a>
        </li>
        <?php foreach ($property_types as $type_option): ?>
            <li>
                <a href="properties.php?type=<?php echo urlencode($type_option['type_name']); ?>" 
                   data-filter=".<?php echo strtolower(str_replace(' ', '', $type_option['type_name'])); ?>">
                    <?php echo htmlspecialchars($type_option['type_name']); ?>
                </a>
            </li>
        <?php endforeach; ?>
      </ul>
      <div class="row properties-box">
        <?php if (!empty($properties)): ?>
            <?php foreach ($properties as $property): ?>
                <div class="col-lg-4 col-md-6 align-self-center mb-30 properties-items col-md-6 <?php echo strtolower(str_replace(' ', '', $property['type'])); ?>">
                  <div class="item">
                    <a href="property_details.php?id=<?php echo $property['id']; ?>"><img src="<?php echo $property['image_url']; ?>" alt=""></a>
                    <span class="category"><?php echo $property['type']; ?></span>
                    <h6>$<?php echo number_format($property['price'], 0, '.', ','); ?></h6>
                    <h4><a href="property_details.php?id=<?php echo $property['id']; ?>"><?php echo $property['location']; ?></a></h4>
                    <ul>
                      <li>Bedrooms: <span><?php echo $property['bedrooms']; ?></span></li>
                      <li>Bathrooms: <span><?php echo $property['bathrooms']; ?></span></li>
                      <li>Area: <span><?php echo $property['area']; ?>m2</span></li>
                      <li>Floor: <span><?php echo $property['floor']; ?></span></li>
                      <li>Parking: <span><?php echo $property['parking']; ?></span></li>
                    </ul>
                    <div class="main-button">
                      <a href="property_details.php?id=<?php echo $property['id']; ?>">Schedule a Visit</a>
                    </div>
                  </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-lg-12">
                <p>No properties found matching your criteria.</p>
            </div>
        <?php endif; ?>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <!-- You can implement dynamic pagination here based on actual number of results -->
          <ul class="pagination">
            <li><a href="#">1</a></li>
            <li><a class="is_active" href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">>></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <div class="container">
      <div class="col-lg-12">
        <p>Copyright © 2024 Real Estate Co., Ltd. All rights reserved. 
        </p>
      </div>
    </div>
  </footer>

 
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/counter.js"></script>
  <script src="assets/js/custom.js"></script>

  </body>
</html>