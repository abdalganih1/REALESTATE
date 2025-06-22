تمام، سأقوم بتصميم مشروعك بالكامل بناءً على طلبك، مع توفير جميع الأكواد في رد واحد. سنعتمد على تعديل الملفات الموجودة وإضافة ملفات PHP جديدة للوظائف الديناميكية.

**ملاحظات هامة قبل البدء:**

1.  **بيئة XAMPP:** تأكد من تشغيل Apache و MySQL في XAMPP.
2.  **مسار المشروع:** قم بإنشاء مجلد لمشروعك (مثلاً `realestate`) داخل مجلد `htdocs` الخاص بـ XAMPP (عادةً `C:\xampp\htdocs\realestate`).
3.  **الملفات الحالية:** قم بنقل ملفاتك `index.html`, `properties.html`, `contact.html` بالإضافة إلى مجلدات `vendor` و `assets` إلى هذا المجلد الجديد.
4.  **الأمان:** هذا الكود مصمم للوظائف الأساسية والسرعة في التنفيذ كما طلبت. في بيئة الإنتاج الحقيقية، ستحتاج إلى تعزيز الأمان (مثل استخدام Prepared Statements بدلاً من `mysqli_real_escape_string` لجميع الاستعلامات، وتحسين التحقق من صحة المدخلات، وإدارة الجلسات بشكل أكثر قوة).
5.  **مسار الصور:** في ملف `add_property.php`، عند إضافة عقار جديد، ستطلب إدخال اسم ملف الصورة (مثلاً `property-07.jpg`). يجب أن تتأكد من وجود هذا الملف يدويًا في `assets/images/` حتى يظهر في الموقع. لم يتم تضمين وظيفة رفع الصور الحقيقية لتسهيل الكود كما طلبت.

---

## 1. ملف Powershell لإنشاء هيكل المشروع

قم بحفظ هذا الكود كـ `setup_project.ps1` داخل مجلد المشروع (مثلاً `C:\xampp\htdocs\realestate\setup_project.ps1`) ثم شغله عبر PowerShell.

```powershell
# Get the directory where the script is located
$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Definition

Write-Host "Creating project structure in: $projectRoot"

# Create SQL directory and file
$sqlDir = Join-Path $projectRoot "sql"
New-Item -ItemType Directory -Path $sqlDir -Force
$sqlFilePath = Join-Path $sqlDir "realestate_db.sql"

$sqlContent = @"
-- Create Database
CREATE DATABASE IF NOT EXISTS `realestate_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `realestate_db`;

-- Table: `users`
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'buyer') NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Table: `properties`
CREATE TABLE IF NOT EXISTS `properties` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(100) NOT NULL,
    `price` DECIMAL(15, 2) NOT NULL,
    `location` VARCHAR(255) NOT NULL,
    `bedrooms` INT NOT NULL,
    `bathrooms` INT NOT NULL,
    `area` DECIMAL(10, 2) NOT NULL,
    `floor` VARCHAR(50) NOT NULL,
    `parking` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `image_url` VARCHAR(255) DEFAULT 'assets/images/default-property.jpg'
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Table: `orders`
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `property_id` INT NOT NULL,
    `buyer_id` INT NOT NULL,
    `order_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('Pending', 'Rejected', 'Sold') NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Table: `contact_messages`
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255),
    `message` TEXT NOT NULL,
    `received_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Insert initial data into `users` table
-- Passwords are 'adminpass' and 'buyerpass' respectively (hashed)
INSERT IGNORE INTO `users` (`username`, `password`, `role`) VALUES
('admin', '\$2y\$10\$Y.z/o/a.k.F.u.1.A.n.g.l.e.C.r.y.p.t.e.d.P.a.s.s.w.o.r.d.H.e.r.e.F.o.r.A.d.m.i.n', 'admin'), -- replace with actual hash for 'adminpass'
('buyer', '\$2y\$10\$X.y/o/b.k.F.u.1.B.n.g.l.e.C.r.y.p.t.e.d.P.a.s.s.w.o.r.d.H.e.r.e.F.o.r.B.u.y.e.r', 'buyer'); -- replace with actual hash for 'buyerpass'
-- To generate actual hashes, you can run a temporary PHP script:
-- <?php echo password_hash('adminpass', PASSWORD_BCRYPT); ?>
-- <?php echo password_hash('buyerpass', PASSWORD_BCRYPT); ?>

-- Insert initial data into `properties` table
INSERT IGNORE INTO `properties` (`type`, `price`, `location`, `bedrooms`, `bathrooms`, `area`, `floor`, `parking`, `description`, `image_url`) VALUES
('Luxury Villa', 2264000.00, 'HAMA,ALBRNAWE', 8, 8, 545.00, '3', '6 spots', 'A luxurious villa located in the prestigious Al-Barnawi area of Hama, offering ample space and modern amenities.', 'assets/images/property-01.jpg'),
('Luxury Villa', 1180000.00, 'DAMASCUS,YAFOUR', 6, 5, 450.00, '3', '8 spots', 'An elegant villa in Yaafour, Damascus, perfect for families seeking comfort and style.', 'assets/images/property-02.jpg'),
('Luxury Villa', 1460000.00, 'HAMA,ALMADINEH', 5, 4, 225.00, '3', '10 spots', 'A beautiful villa in the heart of Hama, Al-Madineh, combining classic design with modern living.', 'assets/images/property-03.jpg'),
('Apartment', 584500.00, 'ALEPPO,MERDIAN', 4, 3, 125.00, '25th', '2 cars', 'A modern apartment in Aleppo, Meridian, offering stunning city views from the 25th floor.', 'assets/images/property-04.jpg'),
('Penthouse', 925600.00, 'HOMS,ALHDARA', 4, 4, 180.00, '38th', '2 cars', 'Experience luxury living in this spacious penthouse in Al-Hadara, Homs, with panoramic views.', 'assets/images/property-05.jpg'),
('Modern Condo', 450000.00, 'HAMA,ALKSOUR', 3, 2, 165.00, '26th', '3 cars', 'A stylish and comfortable modern condo in Al-Kassour, Hama, ideal for urban living.', 'assets/images/property-06.jpg'),
('Luxury Villa', 980000.00, 'DAMASCUS,MAZEH', 8, 8, 550.00, '3', '12 spots', 'Grand villa in Mezzeh, Damascus, offering expansive living areas and privacy.', 'assets/images/property-03.jpg'),
('Luxury Villa', 1520000.00, 'DAMASCUS,ALSHALAN', 12, 15, 380.00, '3', '14 spots', 'Exquisite villa in Al-Shaalan, Damascus, known for its opulent design and amenities.', 'assets/images/property-02.jpg'),
('Luxury Villa', 3145000.00, 'DAMASCUS,ALMAZRA', 10, 12, 860.00, '3', '10 spots', 'A massive, luxurious villa in Al-Mazraa, Damascus, suitable for grand living and entertaining.', 'assets/images/property-01.jpg');

-- Insert initial data into `contact_messages` table
INSERT IGNORE INTO `contact_messages` (`name`, `email`, `subject`, `message`) VALUES
('John Doe', 'john.doe@example.com', 'Inquiry about Villa', 'I am interested in property-01. Can you provide more details?'),
('Jane Smith', 'jane.smith@example.com', 'General Question', 'What are your working hours?');
"@

Set-Content -Path $sqlFilePath -Value $sqlContent -Encoding UTF8

Write-Host "SQL script created at: $sqlFilePath"
Write-Host "Please import this SQL file into your MySQL database using phpMyAdmin or MySQL client."

# PHP files to create
$phpFiles = @(
    "db_connect.php",
    "functions.php",
    "login.php",
    "register.php",
    "logout.php",
    "admin_dashboard.php",
    "add_property.php",
    "manage_properties.php",
    "edit_property.php",
    "delete_property.php",
    "property_details.php",
    "place_order.php",
    "my_orders.php",
    "view_orders.php",
    "view_messages.php",
    "contact_process.php"
)

# Create each PHP file with basic content
foreach ($file in $phpFiles) {
    $filePath = Join-Path $projectRoot $file
    New-Item -ItemType File -Path $filePath -Force | Out-Null
    Set-Content -Path $filePath -Value "<?php // Content for $file ?>" -Encoding UTF8
    Write-Host "Created file: $filePath"
}

Write-Host "`nPowerShell script finished. Please proceed with manually editing the HTML and PHP files."

# Renaming existing HTML files to PHP
$htmlFilesToRename = @(
    "index.html",
    "properties.html",
    "contact.html"
)

foreach ($oldName in $htmlFilesToRename) {
    $oldPath = Join-Path $projectRoot $oldName
    $newName = $oldName -replace '\.html$', '.php'
    $newPath = Join-Path $projectRoot $newName

    if (Test-Path $oldPath) {
        Rename-Item -Path $oldPath -NewName $newName -Force
        Write-Host "Renamed $oldName to $newName"
    } else {
        Write-Warning "File not found: $oldPath. Skipping rename."
    }
}
```

**بعد تشغيل السكربت:**
1.  **اذهب إلى phpMyAdmin** (عادةً `http://localhost/phpmyadmin/`).
2.  أنشئ قاعدة بيانات جديدة باسم `realestate_db`.
3.  اضغط على `realestate_db`، ثم اذهب إلى تبويب `Import` (استيراد).
4.  اختر ملف `realestate_db.sql` الذي تم إنشاؤه في مجلد `sql` داخل مشروعك وقم باستيراده.

---

## 2. أكواد PHP الأساسية للربط والدوال

هذه الملفات ستكون أساس مشروعك.

### `db_connect.php`

```php
<?php
$servername = "localhost";
$username = "root"; // اسم مستخدم MySQL الافتراضي لـ XAMPP
$password = "";     // كلمة مرور MySQL الافتراضية لـ XAMPP (فارغة)
$dbname = "realestate_db";

// إنشاء اتصال بقاعدة البيانات
$conn = mysqli_connect($servername, $username, $password, $dbname);

// التحقق من الاتصال
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// تعيين ترميز الاتصال لضمان دعم اللغة العربية
mysqli_set_charset($conn, "utf8mb4");
?>
```

### `functions.php`

```php
<?php
session_start();
require_once 'db_connect.php';

// Function to sanitize input
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// User Authentication Functions
function register_user($username, $password, $role) {
    global $conn;
    $username = sanitize_input($username);
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);
    $role = sanitize_input($role);

    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password_hashed', '$role')";
    if (mysqli_query($conn, $sql)) {
        return true;
    } else {
        return false;
    }
}

function login_user($username, $password) {
    global $conn;
    $username = sanitize_input($username);

    $sql = "SELECT id, username, password, role FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            return true;
        }
    }
    return false;
}

function check_login() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function get_user_role() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// Property Management Functions (Admin)
function add_property($type, $price, $location, $bedrooms, $bathrooms, $area, $floor, $parking, $description, $image_url) {
    global $conn;
    $type = sanitize_input($type);
    $price = (float)$price;
    $location = sanitize_input($location);
    $bedrooms = (int)$bedrooms;
    $bathrooms = (int)$bathrooms;
    $area = (float)$area;
    $floor = sanitize_input($floor);
    $parking = sanitize_input($parking);
    $description = sanitize_input($description);
    $image_url = sanitize_input($image_url);

    $sql = "INSERT INTO properties (type, price, location, bedrooms, bathrooms, area, floor, parking, description, image_url)
            VALUES ('$type', $price, '$location', $bedrooms, $bathrooms, $area, '$floor', '$parking', '$description', '$image_url')";
    return mysqli_query($conn, $sql);
}

function get_properties($search_type = '', $min_price = null, $max_price = null, $location_search = '') {
    global $conn;
    $sql = "SELECT * FROM properties WHERE 1=1";

    if (!empty($search_type) && $search_type != 'Show All') {
        $search_type = sanitize_input($search_type);
        $sql .= " AND type = '$search_type'";
    }
    if ($min_price !== null && is_numeric($min_price)) {
        $min_price = (float)$min_price;
        $sql .= " AND price >= $min_price";
    }
    if ($max_price !== null && is_numeric($max_price)) {
        $max_price = (float)$max_price;
        $sql .= " AND price <= $max_price";
    }
    if (!empty($location_search)) {
        $location_search = sanitize_input($location_search);
        $sql .= " AND location LIKE '%$location_search%'";
    }

    $sql .= " ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    $properties = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $properties[] = $row;
    }
    return $properties;
}

function get_property_by_id($id) {
    global $conn;
    $id = (int)$id;
    $sql = "SELECT * FROM properties WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

function update_property($id, $type, $price, $location, $bedrooms, $bathrooms, $area, $floor, $parking, $description, $image_url) {
    global $conn;
    $id = (int)$id;
    $type = sanitize_input($type);
    $price = (float)$price;
    $location = sanitize_input($location);
    $bedrooms = (int)$bedrooms;
    $bathrooms = (int)$bathrooms;
    $area = (float)$area;
    $floor = sanitize_input($floor);
    $parking = sanitize_input($parking);
    $description = sanitize_input($description);
    $image_url = sanitize_input($image_url);

    $sql = "UPDATE properties SET
            type = '$type',
            price = $price,
            location = '$location',
            bedrooms = $bedrooms,
            bathrooms = $bathrooms,
            area = $area,
            floor = '$floor',
            parking = '$parking',
            description = '$description',
            image_url = '$image_url'
            WHERE id = $id";
    return mysqli_query($conn, $sql);
}

function delete_property($id) {
    global $conn;
    $id = (int)$id;
    $sql = "DELETE FROM properties WHERE id = $id";
    return mysqli_query($conn, $sql);
}

// Order Management Functions
function add_order($property_id, $buyer_id) {
    global $conn;
    $property_id = (int)$property_id;
    $buyer_id = (int)$buyer_id;
    $sql = "INSERT INTO orders (property_id, buyer_id) VALUES ($property_id, $buyer_id)";
    return mysqli_query($conn, $sql);
}

function get_user_orders($buyer_id) {
    global $conn;
    $buyer_id = (int)$buyer_id;
    $sql = "SELECT o.*, p.location, p.type, p.price, p.image_url FROM orders o JOIN properties p ON o.property_id = p.id WHERE o.buyer_id = $buyer_id ORDER BY o.order_date DESC";
    $result = mysqli_query($conn, $sql);
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    return $orders;
}

function get_all_orders() {
    global $conn;
    $sql = "SELECT o.*, p.location, p.type, p.price, u.username AS buyer_username
            FROM orders o
            JOIN properties p ON o.property_id = p.id
            JOIN users u ON o.buyer_id = u.id
            ORDER BY o.order_date DESC";
    $result = mysqli_query($conn, $sql);
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    return $orders;
}

function update_order_status($order_id, $status) {
    global $conn;
    $order_id = (int)$order_id;
    $status = sanitize_input($status);
    $sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    return mysqli_query($conn, $sql);
}

// Contact Messages Functions
function add_contact_message($name, $email, $subject, $message) {
    global $conn;
    $name = sanitize_input($name);
    $email = sanitize_input($email);
    $subject = sanitize_input($subject);
    $message = sanitize_input($message);

    $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    return mysqli_query($conn, $sql);
}

function get_contact_messages() {
    global $conn;
    $sql = "SELECT * FROM contact_messages ORDER BY received_at DESC";
    $result = mysqli_query($conn, $sql);
    $messages = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }
    return $messages;
}
?>
```

---

## 3. أكواد PHP والـ HTML المعدلة/المضافة

الآن، سنقوم بتعديل ملفات HTML الحالية وإضافة ملفات PHP جديدة لتطبيق الوظائف المطلوبة.

**تنبيه هام:**
للتسهيل، قمت بتضمين الأجزاء المشتركة (Preloader, Sub-header, Header) في كل ملف بدلاً من فصلها في ملفات `header.php` و `footer.php`. هذا يتبع طلبك بعدم التعقيد في التنظيم.

### 3.1 `index.php` (كان `index.html`)

قم بتغيير اسم `index.html` إلى `index.php` واستبدل محتواه بهذا:

```php
<?php
require_once 'functions.php';
// يمكنك إضافة منطق خاص بالصفحة الرئيسية هنا إذا لزم الأمر
$properties = get_properties(); // جلب العقارات لعرضها في قسم Properties
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title> Real Estate </title>

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
                      <li><a href="index.php" class="active">Home</a></li>
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
  

  <div class="main-banner">
    <div class="owl-carousel owl-banner">
      <div class="item item-1">
        <div class="header-text">
          <span class="category">HAMA, <em>SYRIA</em></span>
          <h2>Hurry!<br>Get the Best Villa for you</h2>
        </div>
      </div>
      <div class="item item-2">
        <div class="header-text">
          <span class="category">HOMS, <em>SYRIA</em></span>
          <h2>Be Quick!<br>Get the best villa in town</h2>
        </div>
      </div>
      <div class="item item-3">
        <div class="header-text">
          <span class="category">DAMASCUS, <em>SYRIA</em></span>
          <h2>Act Now!<br>Get the highest level penthouse</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="featured section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4">
          <div class="left-image">
            <img src="assets/images/featured.jpg" alt="">
            <a href="property-details.php?id=1"><img src="assets/images/featured-icon.png" alt="" style="max-width: 60px; padding: 0px;"></a>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="section-heading">
            <h6>| Featured</h6>
            <h2>Best Apartment &amp; Sea view</h2>
          </div>
          <div class="accordion" id="accordionExample">
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Why choose our properties?
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                 <strong>We offer prime locations and competitive prices.</strong> Our listings are carefully curated to ensure the best quality for our clients.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                How do we operate?
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                   <strong>We connect buyers with sellers directly.</strong> Our platform simplifies the real estate process, from browsing to closing deals securely.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                What are our future plans?
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                   <strong>Expanding to new cities and introducing more property types.</strong> We aim to be the leading real estate agency in the region.
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="info-table">
            <ul>
              <li>
                <img src="assets/images/info-icon-01.png" alt="" style="max-width: 52px;">
                <h4>250 m2<br><span>Total Flat Space</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-02.png" alt="" style="max-width: 52px;">
                <h4>Contract<br><span>Contract Ready</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-03.png" alt="" style="max-width: 52px;">
                <h4>Payment<br><span>Payment Process</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-04.png" alt="" style="max-width: 52px;">
                <h4>Safety<br><span>24/7 Under Control</span></h4>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="video section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 offset-lg-4">
          <div class="section-heading text-center">
            <h6>| Video View</h6>
            <h2>Get Closer View & Different Feeling</h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="video-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 offset-lg-1">
          <div class="video-frame">
            <img src="assets/images/video-frame.jpg" alt="">
            <a href="https://youtube.com" target="_blank"><i class="fa fa-play"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="fun-facts">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="wrapper">
            <div class="row">
              <div class="col-lg-4">
                <div class="counter">
                  <h2 class="timer count-title count-number" data-to="34" data-speed="1000"></h2>
                   <p class="count-text ">Buildings<br>Finished Now</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="counter">
                  <h2 class="timer count-title count-number" data-to="12" data-speed="1000"></h2>
                  <p class="count-text ">Years<br>Experience</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="counter">
                  <h2 class="timer count-title count-number" data-to="24" data-speed="1000"></h2>
                  <p class="count-text ">Awards<br>Won 2023</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section best-deal">
    <div class="container">
      <div class="row">
        <div class="col-lg-4">
          <div class="section-heading">
            <h6>| Best Deal</h6>
            <h2>Find Your Best Deal Right Now!</h2>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="tabs-content">
            <div class="row">
              <div class="nav-wrapper ">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="appartment-tab" data-bs-toggle="tab" data-bs-target="#appartment" type="button" role="tab" aria-controls="appartment" aria-selected="true">Apartment</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="villa-tab" data-bs-toggle="tab" data-bs-target="#villa" type="button" role="tab" aria-controls="villa" aria-selected="false">Villa House</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="penthouse-tab" data-bs-toggle="tab" data-bs-target="#penthouse" type="button" role="tab" aria-controls="penthouse" aria-selected="false">Penthouse</button>
                  </li>
                </ul>
              </div>              
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="appartment" role="tabpanel" aria-labelledby="appartment-tab">
                  <div class="row">
                    <div class="col-lg-3">
                      <div class="info-table">
                        <ul>
                          <li>Total Flat Space <span>185 m2</span></li>
                          <li>Floor number <span>26th</span></li>
                          <li>Number of rooms <span>4</span></li>
                          <li>Parking Available <span>Yes</span></li>
                          <li>Payment Process <span>Bank</span></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <img src="assets/images/deal-01.jpg" alt="">
                    </div>
                    <div class="col-lg-3">
                      <h4>Luxury Apartment</h4>
                      <p>This exquisite apartment offers breathtaking city views and is situated in a vibrant neighborhood with easy access to amenities. Ideal for modern urban living.<br><br>Contact us today to schedule a viewing and discover your dream home.</p>
                      <div class="icon-button">
                        <a href="contact.php"><i class="fa fa-calendar"></i> Schedule a Visit</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="villa" role="tabpanel" aria-labelledby="villa-tab">
                  <div class="row">
                    <div class="col-lg-3">
                      <div class="info-table">
                        <ul>
                          <li>Total Flat Space <span>250 m2</span></li>
                          <li>Floor number <span>26th</span></li>
                          <li>Number of rooms <span>5</span></li>
                          <li>Parking Available <span>Yes</span></li>
                          <li>Payment Process <span>Bank</span></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <img src="assets/images/deal-02.jpg" alt="">
                    </div>
                    <div class="col-lg-3">
                      <h4>Spacious Villa House</h4>
                      <p>Discover this magnificent villa, designed for comfort and luxury. Featuring expansive living areas and a beautiful garden, it's perfect for family life and entertaining guests. Located in a tranquil, upscale community.
                      <br><br>Don't miss the opportunity to own this exceptional property. Call us now!</p>
                      <div class="icon-button">
                        <a href="contact.php"><i class="fa fa-calendar"></i> Schedule a Visit</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="penthouse" role="tabpanel" aria-labelledby="penthouse-tab">
                  <div class="row">
                    <div class="col-lg-3">
                      <div class="info-table">
                        <ul>
                          <li>Total Flat Space <span>320 m2</span></li>
                          <li>Floor number <span>34th</span></li>
                          <li>Number of rooms <span>6</span></li>
                          <li>Parking Available <span>Yes</span></li>
                          <li>Payment Process <span>Bank</span></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <img src="assets/images/deal-03.jpg" alt="">
                    </div>
                    <div class="col-lg-3">
                      <h4>Luxurious Penthouse</h4>
                      <p>Elevate your lifestyle with this stunning penthouse, offering unparalleled panoramic views and sophisticated design. Enjoy exclusive amenities and spacious interiors in a prime urban location. <br><br>This is an investment in ultimate comfort and prestige. Inquire today!</p>
                      <div class="icon-button">
                        <a href="contact.php"><i class="fa fa-calendar"></i> Schedule a Visit</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="properties section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 offset-lg-4">
          <div class="section-heading text-center">
            <h6>| Properties</h6>
            <h2>We Provide The Best Property You Like</h2>
          </div>
        </div>
      </div>
      <div class="row">
        <?php if (!empty($properties)): ?>
            <?php foreach ($properties as $property): ?>
                <div class="col-lg-4 col-md-6">
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
                <p>No properties found.</p>
            </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="contact section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 offset-lg-4">
          <div class="section-heading text-center">
            <h6>| Contact Us</h6>
            <h2>Get In Touch With Our Agents</h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="contact-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-7">
          <div class="row">
            <div class="col-lg-6">
              <div class="item phone">
                <img src="assets/images/phone-icon.png" alt="" style="max-width: 52px;">
                <h6>0993837156<br><span>Phone Number</span></h6>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="item email">
                <img src="assets/images/email-icon.png" alt="" style="max-width: 52px;">
                <h6>info@villa.com<br><span>Business Email</span></h6>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <form id="contact-form" action="contact_process.php" method="post">
            <div class="row">
              <div class="col-lg-12">
                <fieldset>
                  <label for="name">Full Name</label>
                  <input type="name" name="name" id="name" placeholder="Your Name..." autocomplete="on" required>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <label for="email">Email Address</label>
                  <input type="text" name="email" id="email" pattern="[^ @]*@[^ @]*" placeholder="Your E-mail..." required="">
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <label for="subject">Subject</label>
                  <input type="subject" name="subject" id="subject" placeholder="Subject..." autocomplete="on" >
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <label for="message">Message</label>
                  <textarea name="message" id="message" placeholder="Your Message"></textarea>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <button type="submit" id="form-submit" class="orange-button">Send Message</button>
                </fieldset>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <div class="container">
      <div class="col-lg-8">
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
```

### 3.2 `properties.php` (كان `properties.html`)

قم بتغيير اسم `properties.html` إلى `properties.php` واستبدل محتواه بهذا:

```php
<?php
require_once 'functions.php';

$search_type = $_GET['type'] ?? '';
$min_price = $_GET['min_price'] ?? null;
$max_price = $_GET['max_price'] ?? null;
$location_search = $_GET['location'] ?? '';

$properties = get_properties($search_type, $min_price, $max_price, $location_search);
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
                            <option value="Apartment" <?php echo ($search_type == 'Apartment') ? 'selected' : ''; ?>>Apartment</option>
                            <option value="Villa House" <?php echo ($search_type == 'Villa House') ? 'selected' : ''; ?>>Villa House</option>
                            <option value="Penthouse" <?php echo ($search_type == 'Penthouse') ? 'selected' : ''; ?>>Penthouse</option>
                            <option value="Luxury Villa" <?php echo ($search_type == 'Luxury Villa') ? 'selected' : ''; ?>>Luxury Villa</option>
                            <option value="Modern Condo" <?php echo ($search_type == 'Modern Condo') ? 'selected' : ''; ?>>Modern Condo</option>
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
        <li>
          <a href="properties.php?type=Apartment" data-filter=".adv">Apartment</a>
        </li>
        <li>
          <a href="properties.php?type=Villa House" data-filter=".str">Villa House</a>
        </li>
        <li>
          <a href="properties.php?type=Penthouse" data-filter=".rac">Penthouse</a>
        </li>
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
```

### 3.3 `contact.php` (كان `contact.html`)

قم بتغيير اسم `contact.html` إلى `contact.php` واستبدل محتواه بهذا:

```php
<?php
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title> Real Estate - Contact </title>

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
                      <li><a href="properties.php">Properties</a></li>
                     
                      <li><a href="contact.php" class="active">Contact Us</a></li>
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
          <span class="breadcrumb"><a href="#">Home</a>  /  Contact Us</span>
          <h3>Contact Us</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="contact-page section">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <div class="section-heading">
            <h6>| Contact Us</h6>
            <h2>Get In Touch With Our Agents</h2>
          </div>
          <p>Feel free to reach out to us with any inquiries, property viewing requests, or general questions. Our dedicated team is here to assist you in finding your ideal real estate solution.</p>
          <div class="row">
            <div class="col-lg-12">
              <div class="item phone">
                <img src="assets/images/phone-icon.png" alt="" style="max-width: 52px;">
                <h6>0993837156<br><span>Phone Number</span></h6>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="item email">
                <img src="assets/images/email-icon.png" alt="" style="max-width: 52px;">
                <h6>info@villa.co<br><span>Business Email</span></h6>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <form id="contact-form" action="contact_process.php" method="post">
            <div class="row">
              <div class="col-lg-12">
                <fieldset>
                  <label for="name">Full Name</label>
                  <input type="name" name="name" id="name" placeholder="Your Name..." autocomplete="on" required>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <label for="email">Email Address</label>
                  <input type="text" name="email" id="email" pattern="[^ @]*@[^ @]*" placeholder="Your E-mail..." required="">
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <label for="subject">Subject</label>
                  <input type="subject" name="subject" id="subject" placeholder="Subject..." autocomplete="on" >
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <label for="message">Message</label>
                  <textarea name="message" id="message" placeholder="Your Message"></textarea>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <button type="submit" id="form-submit" class="orange-button">Send Message</button>
                </fieldset>
              </div>
            </div>
          </form>
        </div>
        <div class="col-lg-12">
          <div id="map">
            <!-- Google Maps Embed Code -->
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d102078.68305786576!2d36.657519642006765!3d35.15833058866164!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x152277d32c53a653%3A0x63359d997a44f54e!2sHama%2C%20Syria!5e0!3m2!1sen!2sus!4v1701234567890!5m2!1sen!2sus" width="100%" height="500px" frameborder="0" style="border:0; border-radius: 10px; box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.15);" allowfullscreen=""></iframe>
          </div>
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
```

### 3.4 `login.php` (جديد)

```php
<?php
require_once 'functions.php';

if (check_login()) {
    header('Location: ' . (is_admin() ? 'admin_dashboard.php' : 'index.php'));
    exit();
}

$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (login_user($username, $password)) {
        header('Location: ' . (is_admin() ? 'admin_dashboard.php' : 'index.php'));
        exit();
    } else {
        $error_message = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title> Real Estate - Login </title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <style>
        .login-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background: #f8f8f8;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .login-container h2 {
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
        <div class="login-container">
            <h2>Login to Your Account</h2>
            <?php if ($error_message): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a></p>
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
```

### 3.5 `register.php` (جديد - لتسجيل المشترين)

```php
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
</body>
</html>
```

### 3.6 `logout.php` (جديد)

```php
<?php
session_start();
session_unset();
session_destroy();
header('Location: index.php');
exit();
?>
```

### 3.7 `admin_dashboard.php` (جديد)

```php
<?php
require_once 'functions.php';

if (!check_login() || !is_admin()) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title> Admin Dashboard </title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <style>
        .dashboard-container {
            padding: 50px 0;
            min-height: 70vh;
        }
        .dashboard-menu {
            list-style: none;
            padding: 0;
        }
        .dashboard-menu li {
            margin-bottom: 15px;
        }
        .dashboard-menu li a {
            display: block;
            padding: 15px 20px;
            background-color: #f35525;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            text-align: center;
        }
        .dashboard-menu li a:hover {
            background-color: #e04a1f;
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
            color: #1e1e1e;
        }
    </style>
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

    <div class="container dashboard-container">
        <h2 class="dashboard-header">Admin Dashboard</h2>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <ul class="dashboard-menu">
                    <li><a href="add_property.php"><i class="fa fa-plus-circle"></i> Add New Property</a></li>
                    <li><a href="manage_properties.php"><i class="fa fa-home"></i> Manage Properties</a></li>
                    <li><a href="view_orders.php"><i class="fa fa-clipboard-list"></i> View All Orders</a></li>
                    <li><a href="view_messages.php"><i class="fa fa-envelope"></i> View Contact Messages</a></li>
                </ul>
            </div>
            <div class="col-lg-8 col-md-6">
                <div class="alert alert-info" role="alert">
                    Welcome, Admin <?php echo $_SESSION['username']; ?>! Use the menu on the left to manage the website.
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
```

### 3.8 `add_property.php` (جديد - لإضافة عقار)

```php
<?php
require_once 'functions.php';

if (!check_login() || !is_admin()) {
    header('Location: login.php');
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $area = $_POST['area'];
    $floor = $_POST['floor'];
    $parking = $_POST['parking'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url']; // Assuming user inputs image file name/path directly

    if (add_property($type, $price, $location, $bedrooms, $bathrooms, $area, $floor, $parking, $description, $image_url)) {
        $message = 'Property added successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error adding property. Please try again.';
        $message_type = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title> Add New Property </title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <style>
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
    </style>
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

    <div class="container form-container">
        <h2>Add New Property</h2>
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="add_property.php" method="POST">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="type">Property Type</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="Luxury Villa">Luxury Villa</option>
                            <option value="Apartment">Apartment</option>
                            <option value="Penthouse">Penthouse</option>
                            <option value="Villa House">Villa House</option>
                            <option value="Modern Condo">Modern Condo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="e.g., HAMA,ALBRNAWE" required>
                    </div>
                    <div class="form-group">
                        <label for="bedrooms">Bedrooms</label>
                        <input type="number" class="form-control" id="bedrooms" name="bedrooms" required>
                    </div>
                    <div class="form-group">
                        <label for="bathrooms">Bathrooms</label>
                        <input type="number" class="form-control" id="bathrooms" name="bathrooms" required>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="area">Area (m2)</label>
                        <input type="number" step="0.01" class="form-control" id="area" name="area" required>
                    </div>
                    <div class="form-group">
                        <label for="floor">Floor</label>
                        <input type="text" class="form-control" id="floor" name="floor" placeholder="e.g., 3 or 25th" required>
                    </div>
                    <div class="form-group">
                        <label for="parking">Parking</label>
                        <input type="text" class="form-control" id="parking" name="parking" placeholder="e.g., 6 spots or 2 cars" required>
                    </div>
                    <div class="form-group">
                        <label for="image_url">Image File Name (e.g., assets/images/property-07.jpg)</label>
                        <input type="text" class="form-control" id="image_url" name="image_url" value="assets/images/default-property.jpg" required>
                        <small class="form-text text-muted">Ensure this file exists in your assets/images folder.</small>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                    </div>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary w-100">Add Property</button>
                </div>
            </div>
        </form>
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
</body>
</html>
```

### 3.9 `manage_properties.php` (جديد - إدارة العقارات)

```php
<?php
require_once 'functions.php';

if (!check_login() || !is_admin()) {
    header('Location: login.php');
    exit();
}

$properties = get_properties();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title> Manage Properties </title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <style>
        .manage-container {
            padding: 50px 0;
            min-height: 70vh;
        }
        .manage-container h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #1e1e1e;
        }
        .table-responsive {
            margin-top: 30px;
        }
        .action-buttons a {
            margin-right: 5px;
            color: #f35525;
        }
        .action-buttons a:hover {
            color: #e04a1f;
        }
    </style>
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

    <div class="container manage-container">
        <h2>Manage Properties</h2>
        <div class="text-right mb-4">
            <a href="add_property.php" class="btn btn-primary">Add New Property</a>
        </div>
        <?php if (!empty($properties)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Location</th>
                        <th>Beds</th>
                        <th>Baths</th>
                        <th>Area</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $property): ?>
                    <tr>
                        <td><?php echo $property['id']; ?></td>
                        <td><?php echo $property['type']; ?></td>
                        <td>$<?php echo number_format($property['price'], 0, '.', ','); ?></td>
                        <td><?php echo $property['location']; ?></td>
                        <td><?php echo $property['bedrooms']; ?></td>
                        <td><?php echo $property['bathrooms']; ?></td>
                        <td><?php echo $property['area']; ?>m2</td>
                        <td class="action-buttons">
                            <a href="edit_property.php?id=<?php echo $property['id']; ?>" class="btn btn-sm btn-info text-white">Edit</a>
                            <a href="delete_property.php?id=<?php echo $property['id']; ?>" class="btn btn-sm btn-danger text-white" onclick="return confirm('Are you sure you want to delete this property?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="alert alert-info">No properties found.</div>
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
</body>
</html>
```

### 3.10 `edit_property.php` (جديد - لتعديل العقار)

```php
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
    $image_url = $_POST['image_url'];

    if (update_property($property['id'], $type, $price, $location, $bedrooms, $bathrooms, $area, $floor, $parking, $description, $image_url)) {
        $message = 'Property updated successfully!';
        $message_type = 'success';
        // Refresh property data after update
        $property = get_property_by_id($property['id']);
    } else {
        $message = 'Error updating property. Please try again.';
        $message_type = 'danger';
    }
}
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
    <style>
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
    </style>
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

    <div class="container form-container">
        <h2>Edit Property #<?php echo $property['id']; ?></h2>
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <?php if ($property): ?>
        <form action="edit_property.php?id=<?php echo $property['id']; ?>" method="POST">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="type">Property Type</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="Luxury Villa" <?php echo ($property['type'] == 'Luxury Villa') ? 'selected' : ''; ?>>Luxury Villa</option>
                            <option value="Apartment" <?php echo ($property['type'] == 'Apartment') ? 'selected' : ''; ?>>Apartment</option>
                            <option value="Penthouse" <?php echo ($property['type'] == 'Penthouse') ? 'selected' : ''; ?>>Penthouse</option>
                            <option value="Villa House" <?php echo ($property['type'] == 'Villa House') ? 'selected' : ''; ?>>Villa House</option>
                            <option value="Modern Condo" <?php echo ($property['type'] == 'Modern Condo') ? 'selected' : ''; ?>>Modern Condo</option>
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
                        <label for="image_url">Image File Name (e.g., assets/images/property-07.jpg)</label>
                        <input type="text" class="form-control" id="image_url" name="image_url" value="<?php echo htmlspecialchars($property['image_url']); ?>" required>
                        <small class="form-text text-muted">Ensure this file exists in your assets/images folder.</small>
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
</body>
</html>
```

### 3.11 `delete_property.php` (جديد - لحذف العقار)

```php
<?php
require_once 'functions.php';

if (!check_login() || !is_admin()) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $property_id = $_GET['id'];
    if (delete_property($property_id)) {
        // Optional: Add a success message to session
        $_SESSION['message'] = 'Property deleted successfully!';
        $_SESSION['message_type'] = 'success';
    } else {
        // Optional: Add an error message to session
        $_SESSION['message'] = 'Error deleting property.';
        $_SESSION['message_type'] = 'danger';
    }
}
header('Location: manage_properties.php');
exit();
?>
```

### 3.12 `property_details.php` (جديد - لعرض تفاصيل العقار)

```php
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
    <style>
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

  </body>
</html>
```

### 3.13 `place_order.php` (جديد - صفحة الطلبية)

```php
<?php
require_once 'functions.php';

if (!check_login() || get_user_role() != 'buyer') {
    header('Location: login.php');
    exit();
}

$message = '';
$message_type = '';

if (isset($_GET['property_id'])) {
    $property_id = $_GET['property_id'];
    $buyer_id = $_SESSION['user_id'];

    if (add_order($property_id, $buyer_id)) {
        $message = 'Your order has been placed successfully! We will review it shortly.';
        $message_type = 'success';
    } else {
        $message = 'Failed to place order. Please try again.';
        $message_type = 'danger';
    }
} else {
    $message = 'No property specified for order.';
    $message_type = 'danger';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title> Place Order </title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <style>
        .order-status-container {
            padding: 50px 0;
            min-height: 70vh;
            text-align: center;
        }
        .order-status-container h2 {
            margin-bottom: 30px;
            color: #1e1e1e;
        }
        .order-status-container .alert {
            max-width: 600px;
            margin: 0 auto 30px;
        }
    </style>
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
                        <li><a href="properties.php">Properties</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                        <?php if (!check_login()): ?>
                          <li><a href="login.php"><i class="fa fa-user"></i> Login / Register</a></li>
                        <?php else: ?>
                          <?php if (is_admin()): ?>
                              <li><a href="admin_dashboard.php"><i class="fa fa-cog"></i> Admin Dashboard</a></li>
                          <?php else: ?>
                              <li><a href="my_orders.php" class="active"><i class="fa fa-list"></i> My Orders</a></li>
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

    <div class="container order-status-container">
        <h2>Order Placement Status</h2>
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <a href="my_orders.php" class="btn btn-primary">View My Orders</a>
        <a href="properties.php" class="btn btn-secondary">Continue Browsing Properties</a>
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
```

### 3.14 `my_orders.php` (جديد - طلبات المشتري)

```php
<?php
require_once 'functions.php';

if (!check_login() || get_user_role() != 'buyer') {
    header('Location: login.php');
    exit();
}

$buyer_id = $_SESSION['user_id'];
$orders = get_user_orders($buyer_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title> My Orders </title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <style>
        .orders-container {
            padding: 50px 0;
            min-height: 70vh;
        }
        .orders-container h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #1e1e1e;
        }
        .order-item {
            background-color: #f8f8f8;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
        }
        .order-item img {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 20px;
        }
        .order-item-details {
            flex-grow: 1;
        }
        .order-item-details h4 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.3rem;
            color: #f35525;
        }
        .order-item-details p {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        .order-item-details .status {
            font-weight: bold;
            color: #198754; /* Green for Pending */
        }
        .order-item-details .status.Rejected {
            color: #dc3545; /* Red for Rejected */
        }
        .order-item-details .status.Sold {
            color: #0d6efd; /* Blue for Sold */
        }
    </style>
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
                        <li><a href="properties.php">Properties</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                        <?php if (!check_login()): ?>
                          <li><a href="login.php"><i class="fa fa-user"></i> Login / Register</a></li>
                        <?php else: ?>
                          <?php if (is_admin()): ?>
                              <li><a href="admin_dashboard.php"><i class="fa fa-cog"></i> Admin Dashboard</a></li>
                          <?php else: ?>
                              <li><a href="my_orders.php" class="active"><i class="fa fa-list"></i> My Orders</a></li>
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
        <h2>My Property Orders</h2>
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-item">
                    <img src="<?php echo $order['image_url']; ?>" alt="<?php echo $order['type']; ?>">
                    <div class="order-item-details">
                        <h4><a href="property_details.php?id=<?php echo $order['property_id']; ?>"><?php echo $order['type']; ?> in <?php echo $order['location']; ?></a></h4>
                        <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
                        <p><strong>Price:</strong> $<?php echo number_format($order['price'], 0, '.', ','); ?></p>
                        <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
                        <p><strong>Status:</strong> <span class="status <?php echo htmlspecialchars($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></span></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                You have no orders yet. <a href="properties.php">Browse properties</a> to make a purchase!
            </div>
        <?php endif; ?>
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
```

### 3.15 `view_orders.php` (جديد - لمدير النظام لعرض الطلبات)

```php
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
    <style>
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
</body>
</html>
```

### 3.16 `view_messages.php` (جديد - لمدير النظام لعرض رسائل الاتصال)

```php
<?php
require_once 'functions.php';

if (!check_login() || !is_admin()) {
    header('Location: login.php');
    exit();
}

$messages = get_contact_messages();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title> View Contact Messages </title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <style>
        .messages-container {
            padding: 50px 0;
            min-height: 70vh;
        }
        .messages-container h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #1e1e1e;
        }
        .table-responsive {
            margin-top: 30px;
        }
    </style>
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

    <div class="container messages-container">
        <h2>Contact Messages</h2>
        <?php if (!empty($messages)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Received At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?php echo $message['id']; ?></td>
                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                        <td><?php echo htmlspecialchars($message['subject']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($message['message'])); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($message['received_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="alert alert-info">No contact messages found.</div>
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
</body>
</html>
```

### 3.17 `contact_process.php` (جديد - لمعالجة نموذج الاتصال)

```php
<?php
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    if (!empty($name) && !empty($email) && !empty($message)) {
        if (add_contact_message($name, $email, $subject, $message)) {
            echo "<script>alert('Your message has been sent successfully!'); window.location.href='contact.php';</script>";
        } else {
            echo "<script>alert('Failed to send message. Please try again.'); window.location.href='contact.php';</script>";
        }
    } else {
        echo "<script>alert('Please fill in all required fields.'); window.location.href='contact.php';</script>";
    }
} else {
    header('Location: contact.php');
    exit();
}
?>
```

---

**خطوات إضافية لتشغيل المشروع:**

1.  **ضع الملفات:** تأكد من أن جميع الملفات والمسارات الجديدة (التي تم إنشاؤها وتعديلها) موجودة بشكل صحيح في مجلد مشروعك داخل `htdocs` في XAMPP.
2.  **تشغيل XAMPP:** تأكد من أن Apache و MySQL قيد التشغيل.
3.  **الوصول إلى المشروع:** افتح متصفحك واذهب إلى `http://localhost/your_project_folder_name/index.php` (استبدل `your_project_folder_name` باسم المجلد الذي أنشأته، مثلاً `realestate`).

**بيانات الدخول الأولية:**

*   **للمدير (Admin):**
    *   اسم المستخدم: `admin`
    *   كلمة المرور: `adminpass`
*   **للمشتري (Buyer):**
    *   اسم المستخدم: `buyer`
    *   كلمة المرور: `buyerpass`

يمكنك تغيير كلمات المرور في SQL Script إذا أردت، بعد توليد هاش جديد لها باستخدام `password_hash('yournewpassword', PASSWORD_BCRYPT);` في PHP.

هذا يغطي جميع المتطلبات التي ذكرتها. آمل أن يكون مفيدًا لمشروعك!