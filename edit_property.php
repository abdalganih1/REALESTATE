<?php
require_once 'functions.php';

if (!check_login() || !is_admin()) {
    header('Location: login.php');
    exit();
}

$property = null;
$message = '';
$message_type = '';

if (isset($_GET['id'])) {
    $property_id = $_GET['id'];
    $property = get_property_by_id($property_id);
    if (!$property) {
        $message = 'Property not found!';
        $message_type = 'danger';
    }
} else {
    header('Location: manage_properties.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $property) {
    $type = $_POST['type'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $area = $_POST['area'];
    $floor = $_POST['floor'];
    $parking = $_POST['parking'];
    $description = $_POST['description'];

    $image_url_to_save = $property['image_url']; // Keep existing image by default

    // Check if a new image file was uploaded
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
        $uploaded_path = upload_image($_FILES['image_upload']);
        if ($uploaded_path) {
            // If a new image is successfully uploaded, delete the old one if it's not the default
            if ($property['image_url'] != 'assets/images/default-property.jpg' && file_exists($property['image_url'])) {
                unlink($property['image_url']);
            }
            $image_url_to_save = $uploaded_path;
        } else {
            $message = 'Error uploading new image. Keeping the old image. Please ensure it\'s a JPG, PNG, or GIF file, and less than 5MB.';
            $message_type = 'warning';
        }
    } elseif (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle other upload errors even if no specific file was selected (e.g., too large from php.ini)
        $message = 'An unexpected error occurred during image upload. Keeping the old image.';
        $message_type = 'warning';
    }
    // If no file was selected or specific upload error, $image_url_to_save remains the old one.

    if (update_property($property['id'], $type, $price, $location, $bedrooms, $bathrooms, $area, $floor, $parking, $description, $image_url_to_save)) {
        // If image upload failed but property updated, combine messages
        if ($message_type == 'warning') {
            $message .= '<br>Property details updated successfully.';
            $message_type = 'success'; // Change to success if other details updated
        } else {
            $message = 'Property updated successfully!';
            $message_type = 'success';
        }
        // Refresh property data after update to show latest changes including image URL
        $property = get_property_by_id($property['id']);
    } else {
        $message = 'Error updating property details. Please try again.';
        $message_type = 'danger';
    }
}

// Get property types for the dropdown
$property_types = get_property_types();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title> Edit Property </title>
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
        .form-container {
            padding: 50px 0;
            min-height: 80vh;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #1e1e1e;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #f35525;
            border-color: #f35525;
        }
        .btn-primary:hover {
            background-color: #e04a1f;
            border-color: #e04a1f;
        }
        .image-preview {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            display: block;
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
                              <li><a href="admin_dashboard.php" class="active"><i class="fa fa-cog"></i> Admin Dashboard</a></li>
                          <?  else: ?>
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

    <div class="container form-container">
        <h2>Edit Property #<?php echo $property['id']; ?></h2>
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <?php if ($property): ?>
        <form action="edit_property.php?id=<?php echo $property['id']; ?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="type">Property Type</label>
                        <select class="form-control" id="type" name="type" required>
                            <?php foreach ($property_types as $type_option): ?>
                                <option value="<?php echo htmlspecialchars($type_option['type_name']); ?>" <?php echo ($property['type'] == $type_option['type_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type_option['type_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($property['price']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($property['location']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bedrooms">Bedrooms</label>
                        <input type="number" class="form-control" id="bedrooms" name="bedrooms" value="<?php echo htmlspecialchars($property['bedrooms']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bathrooms">Bathrooms</label>
                        <input type="number" class="form-control" id="bathrooms" name="bathrooms" value="<?php echo htmlspecialchars($property['bathrooms']); ?>" required>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="area">Area (m2)</label>
                        <input type="number" step="0.01" class="form-control" id="area" name="area" value="<?php echo htmlspecialchars($property['area']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="floor">Floor</label>
                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($property['floor']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="parking">Parking</label>
                        <input type="text" class="form-control" id="parking" name="parking" value="<?php echo htmlspecialchars($property['parking']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Current Image Preview</label>
                        <?php if ($property['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($property['image_url']); ?>" alt="Current Property Image" class="image-preview">
                        <?php else: ?>
                            <p>No image currently set.</p>
                        <?php endif; ?>
                        <label for="image_upload">Upload New Image (Optional)</label>
                        <input type="file" class="form-control" id="image_upload" name="image_upload" accept="image/*">
                        <small class="form-text text-muted">Max 5MB (JPG, PNG, GIF). Uploading a new image will replace the current one.</small>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($property['description']); ?></textarea>
                    </div>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary w-100">Update Property</button>
                </div>
            </div>
        </form>
        <?php endif; ?>
        <div class="text-center mt-3">
            <a href="manage_properties.php" class="btn btn-secondary">Back to Manage Properties</a>
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