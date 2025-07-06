<?php
require_once 'functions.php';

if (check_login()) {
    header('Location: ' . (is_admin() ? 'admin_dashboard.php' : 'index.php'));
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = 'Passwords do not match.';
        $message_type = 'danger';
    } else {
        if (register_user($username, $password, 'buyer')) {
            $message = 'Registration successful! You can now login.';
            $message_type = 'success';
        } else {
            $message = 'Registration failed. Username might already exist.';
            $message_type = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title> Real Estate - Register </title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <style>
        .register-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background: #f8f8f8;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #1e1e1e;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #f35525;
            border-color: #f35525;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #e04a1f;
            border-color: #e04a1f;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
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
                        <li><a href="login.php" class="active"><i class="fa fa-user"></i> Login / Register</a></li>
                    </ul>   
                      <a class='menu-trigger'>
                          <span>Menu</span>
                      </a>
                  </nav>
              </div>
          </div>
      </div>
    </header>

    <div class="container">
        <div class="register-container">
            <h2>Register New Account</h2>
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
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
            // Fallback for immediate click if the widget loads slowly
            // You might want to reload the script or show a message
        }
    }
  </script>
  <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
  <!-- END Google Translate Script -->
</body>
</html>