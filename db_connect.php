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