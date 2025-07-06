<?php
require_once 'functions.php';

$property = null;
if (isset($_GET['id'])) {
    $property_id = $_GET['id'];
    $property = get_property_by_id($property_id);
}

if (!$property) {
    header('Location: properties.php'); // Redirect if property not found
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title> Real Estate - <?php echo $property['location']; ?> </title>

    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <!-- START Custom CSS for Google Translate button and hiding default widget -->
    <style>
        .lang-switcher {
            text-align: right;
            margin-top: 10px;
        }

        .lang-switcher button {
            background-color: #f35525;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .lang-switcher button:hover {
            background-color: #e04a1f;
        }

        #google_translate_element {
            display: none;
        }

        .goog-te-banner-frame.skiptranslate {
            display: none !important;
        }
        body {
            top: 0px !important;
        }

        /* Specific styles for this page */
        .property-single-info ul li {
            margin-bottom: 10px;
        }
        .buy-button {
            text-align: center;
            margin-top: 30px;
        }
        .buy-button a {
            display: inline-block;
            background-color: #f35525;
            color: #fff;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
        }
        .buy-button a:hover {
            background-color: #e04a1f;
        }
    </style>
    <!-- END Custom CSS for Google Translate button and hiding default widget -->
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
        <!-- Adjusted columns for info and social links, added a new column for language switcher -->
        <div class="col-lg-7 col-md-7">
          <ul class="info">
            <li><i class="fa fa-envelope"></i> info@realestate.com</li>
            <li><i class="fa fa-map"></i> HAMA</li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3">
          <ul class="social-links">
            <li><a href="#"><i class="fab fa-facebook"></i></a></li>
            <li><a href="https://x.com/minthu" target="_blank"><i class="fab fa-twitter"></i></a></li>
            <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
          </ul>
        </div>
        <div class="col-lg-2 col-md-2">
            <div class="lang-switcher">
                <div id="google_translate_element"></div> <!-- Hidden widget div -->
                <button onclick="translatePage('ar')">العربية</button>
            </div>
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
                      <li><a href="properties.php">Properties</a></li>
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
          <span class="breadcrumb"><a href="index.php">Home</a> / <a href="properties.php">Properties</a> / Single Property</span>
          <h3><?php echo $property['location']; ?></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="single-property section">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <div class="main-image">
            <img src="<?php echo $property['image_url']; ?>" alt="<?php echo $property['type']; ?> in <?php echo $property['location']; ?>">
          </div>
          <div class="main-content">
            <span class="category"><?php echo $property['type']; ?></span>
            <h4>$<?php echo number_format($property['price'], 0, '.', ','); ?></h4>
            <p><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="info-table">
            <ul>
              <li>
                <img src="assets/images/info-icon-01.png" alt="" style="max-width: 52px;">
                <h4><?php echo $property['area']; ?>m2<br><span>Total Flat Space</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-02.png" alt="" style="max-width: 52px;">
                <h4><?php echo $property['bedrooms']; ?><br><span>Bedrooms</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-03.png" alt="" style="max-width: 52px;">
                <h4><?php echo $property['bathrooms']; ?><br><span>Bathrooms</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-04.png" alt="" style="max-width: 52px;">
                <h4><?php echo $property['floor']; ?><br><span>Floor Number</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-02.png" alt="" style="max-width: 52px;">
                <h4><?php echo $property['parking']; ?><br><span>Parking</span></h4>
              </li>
            </ul>
          </div>
          <?php if (check_login() && get_user_role() == 'buyer'): ?>
          <div class="buy-button">
              <a href="place_order.php?property_id=<?php echo $property['id']; ?>" onclick="return confirm('Are you sure you want to purchase this property?');">Confirm Purchase</a>
          </div>
          <?php elseif (!check_login()): ?>
          <div class="buy-button">
              <a href="login.php">Login to Purchase</a>
          </div>
          <?php else: ?>
          <div class="buy-button">
              <a href="#" style="background-color: #ccc; cursor: not-allowed;">Login as Buyer to Purchase</a>
          </div>
          <?php endif; ?>
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

  <!-- START Google Translate Script -->
  <script type="text/javascript">
    // Initialize Google Translate widget
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({
        pageLanguage: 'en', // Original language of the page
        includedLanguages: 'ar', // Languages available for translation (Arabic only in this case)
        layout: google.translate.TranslateElement.FloatEmpty // Hides the default dropdown
      }, 'google_translate_element');
    }

    // Function to trigger translation to Arabic
    function translatePage(lang) {
        var googleSelect = document.querySelector('#google_translate_element select');
        if (googleSelect) {
            googleSelect.value = lang;
            googleSelect.dispatchEvent(new Event('change')); // Trigger the change event
        } else {
            console.error('Google Translate element not found or not initialized yet.');
        }
    }
  </script>
  <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
  <!-- END Google Translate Script -->

  </body>
</html>