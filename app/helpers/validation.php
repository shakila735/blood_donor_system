<?php

function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function is_valid_phone($phone) {
    return preg_match('/^(01[3-9]\d{8})$/', $phone);
}

function is_valid_email($email) {
    if ($email === '') return true;
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_valid_role($role) {
    $allowed = ['donor', 'requester', 'hospital'];
    return in_array($role, $allowed);
}

function is_valid_blood_group($blood_group) {
    $allowed = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    return in_array($blood_group, $allowed);
}

function validate_registration($data) {

    $errors = [];

    if (trim($data['name'] ?? '') === '') {
        $errors[] = "Name is required.";
    }

    if (trim($data['phone'] ?? '') === '') {
        $errors[] = "Phone number is required.";
    } elseif (!is_valid_phone($data['phone'])) {
        $errors[] = "Invalid phone number.";
    }

    if (!is_valid_email($data['email'] ?? '')) {
        $errors[] = "Invalid email address.";
    }

    if (($data['password'] ?? '') === '') {
        $errors[] = "Password is required.";
    } elseif (strlen($data['password']) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (!is_valid_role($data['role'] ?? '')) {
        $errors[] = "Invalid role selected.";
    }

    if (trim($data['location'] ?? '') === '') {
        $errors[] = "Location is required.";
    }

    if (($data['role'] ?? '') === 'donor') {

        if (($data['blood_group'] ?? '') === '') {
            $errors[] = "Blood group required for donor.";
        } elseif (!is_valid_blood_group($data['blood_group'])) {
            $errors[] = "Invalid blood group.";
        }
    }

    return $errors;
}

function validate_login($data) {

    $errors = [];

    if (trim($data['phone'] ?? '') === '') {
        $errors[] = "Phone number is required.";
    }

    if (($data['password'] ?? '') === '') {
        $errors[] = "Password is required.";
    }

    return $errors;
}
?>