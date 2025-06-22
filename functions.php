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