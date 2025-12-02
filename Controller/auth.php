<?php

require_once __DIR__ . '/../Controller/config.php';

// Login
if (isset($_POST['login'])) {

    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Bitte alle Felder ausfüllen';
        redirect('/index.php');
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['success'] = 'Erfolgreich angemeldet!';
        redirect('/index.php');
    } else {
        $_SESSION['error'] = 'Ungültige Anmeldedaten';
        redirect('/index.php');
    }
}

// Registrierung
if (isset($_POST['register'])) {

    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    $errors = [];

    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = 'Alle Felder sind erforderlich';
    }

    if (strlen($username) < 3) {
        $errors[] = 'Benutzername muss mindestens 3 Zeichen lang sein';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Ungültige E-Mail-Adresse';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Passwort muss mindestens 8 Zeichen lang sein';
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Passwort muss mindestens einen Großbuchstaben enthalten';
    }

    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Passwort muss mindestens einen Kleinbuchstaben enthalten';
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Passwort muss mindestens eine Ziffer enthalten';
    }

    if ($password !== $password_confirm) {
        $errors[] = 'Passwörter stimmen nicht überein';
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        $errors[] = 'Benutzername oder E-Mail bereits vergeben';
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
        redirect('/index.php');
    }

    try {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'customer')");

        if ($stmt->execute([$username, $email, $password_hash])) {
            $_SESSION['success'] = 'Registrierung erfolgreich! Bitte melden Sie sich an.';
            redirect('/index.php');
        } else {
            throw new Exception('Database insert failed');
        }
    } catch (Exception $e) {
        logError('Registration failed', ['error' => $e->getMessage(), 'username' => $username]);
        $_SESSION['error'] = 'Registrierung fehlgeschlagen';
        redirect('/index.php');
    }
}

// Logout
if (isset($_POST['logout'])) {

    session_destroy();
    redirect('/index.php');
}

// Passwort ändern
if (isset($_POST['change_password'])) {
    if (!isLoggedIn()) {
        redirect('/index.php');
    }

    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $new_password_confirm = $_POST['new_password_confirm'];

    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!password_verify($old_password, $user['password_hash'])) {
        $_SESSION['error'] = 'Aktuelles Passwort ist falsch';
        redirect('/profile.php');
    }

    if (strlen($new_password) < 8) {
        $_SESSION['error'] = 'Neues Passwort muss mindestens 8 Zeichen lang sein';
        redirect('/profile.php');
    }

    if (!preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $_SESSION['error'] = 'Neues Passwort muss mindestens einen Großbuchstaben, einen Kleinbuchstaben und eine Ziffer enthalten';
        redirect('/profile.php');
    }

    if ($new_password !== $new_password_confirm) {
        $_SESSION['error'] = 'Neue Passwörter stimmen nicht überein';
        redirect('/profile.php');
    }

    try {
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");

        if ($stmt->execute([$new_password_hash, $_SESSION['user_id']])) {
            $_SESSION['success'] = 'Passwort erfolgreich geändert';
            redirect('/profile.php');
        } else {
            throw new Exception('Database update failed');
        }
    } catch (Exception $e) {
        logError('Password change failed', ['error' => $e->getMessage(), 'user_id' => $_SESSION['user_id']]);
        $_SESSION['error'] = 'Passwortänderung fehlgeschlagen';
        redirect('/profile.php');
    }
}
