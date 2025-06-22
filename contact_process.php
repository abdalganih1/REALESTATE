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