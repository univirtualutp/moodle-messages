<?php
require_once 'config/config.php';

// Cargar idioma
$lang = isset($_GET['lang']) && in_array($_GET['lang'], ['es', 'en']) ? $_GET['lang'] : 'es';
$translations = require_once "lang/$lang.php";

// Validar courseid
$courseid = filter_input(INPUT_GET, 'courseid', FILTER_VALIDATE_INT);
if (!$courseid) {
    $error = $translations['invalid_course'];
    include 'templates/form.php';
    exit;
}

// Validar userid
$userid = filter_input(INPUT_GET, 'userid', FILTER_VALIDATE_INT);
if (!$userid) {
    $error = $translations['error'] . ': ID de usuario no válido';
    include 'templates/form.php';
    exit;
}

// Conexión a la base de datos
try {
    $pdo = new PDO("pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = $translations['error'] . ': ' . $e->getMessage();
    include 'templates/form.php';
    exit;
}

// Obtener nombre del usuario autenticado
$stmt = $pdo->prepare("
    SELECT firstname, lastname
    FROM " . MOODLE_PREFIX . "user
    WHERE id = :userid
");
$stmt->execute(['userid' => $userid]);
$user = $stmt->fetch(PDO::FETCH_OBJ);
if (!$user) {
    $error = $translations['error'] . ': Usuario no encontrado';
    include 'templates/form.php';
    exit;
}
$from_name = $user->firstname . ' ' . $user->lastname;

// Obtener usuarios matriculados (excluyendo rol 9)
$stmt = $pdo->prepare("
    SELECT DISTINCT u.id, u.firstname, u.lastname, u.email
    FROM " . MOODLE_PREFIX . "user u
    JOIN " . MOODLE_PREFIX . "user_enrolments ue ON u.id = ue.userid
    JOIN " . MOODLE_PREFIX . "enrol e ON ue.enrolid = e.id
    JOIN " . MOODLE_PREFIX . "context ctx ON e.courseid = ctx.instanceid
    JOIN " . MOODLE_PREFIX . "role_assignments ra ON u.id = ra.userid AND ra.contextid = ctx.id
    WHERE e.courseid = :courseid AND ra.roleid != 9 AND ctx.contextlevel = 50
    ORDER BY u.firstname, u.lastname
");
$stmt->execute(['courseid' => $courseid]);
$users = $stmt->fetchAll(PDO::FETCH_OBJ);

if (empty($users)) {
    $error = $translations['no_recipients'];
    include 'templates/form.php';
    exit;
}

// URL de regreso a Moodle
$moodle_url = "https://aulaunivirtual.utp.edu.co/course/view.php?id=$courseid";

include 'templates/form.php';
?>