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