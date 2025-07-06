<?php
require_once 'functions.php';

if (!check_login() || !is_admin()) {
    header('Location: login.php');
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    update_order_status($order_id, $new_status);
    header('Location: view_orders.php'); // Redirect to refresh the page
    exit();
}

$orders = get_all_orders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title> View All Orders </title>
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
        .orders-container {
            padding: 50px 0;
            min-height: 70vh;
        }
        .orders-container h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #1e1e1e;
        }
        .table-responsive {
            margin-top: 30px;
        }
        .status-badge {
            padding: .35em .65em;
            border-radius: .25rem;
            font-size: .75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            display: inline-block;
        }
        .status-Pending { background-color: #ffc107; color: #000; }
        .status-Rejected { background-color: #dc3545; color: #fff; }
        .status-Sold { background-color: #0d6efd; color: #fff; }
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
                              <li><a href="admin_dashboard.php" class="active"><i class="fa fa-cog"></i> Admin Dashboard</a></li>
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

    <div class="container orders-container">
        <h2>All Customer Orders</h2>
        <?php if (!empty($orders)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Property</th>
                        <th>Buyer</th>
                        <th>Price</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><a href="property_details.php?id=<?php echo $order['property_id']; ?>"><?php echo $order['type'] . ' - ' . $order['location']; ?></a></td>
                        <td><?php echo $order['buyer_username']; ?></td>
                        <td>$<?php echo number_format($order['price'], 0, '.', ','); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                        <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span></td>
                        <td>
                            <form action="view_orders.php" method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="form-control form-control-sm d-inline-block w-auto">
                                    <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Rejected" <?php echo ($order['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                    <option value="Sold" <?php echo ($order['status'] == 'Sold') ? 'selected' : ''; ?>>Sold</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="alert alert-info">No orders found.</div>
        <?php endif; ?>
        <div class="text-center mt-3">
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
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