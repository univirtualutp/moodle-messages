<?php
require_once 'config/config.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Cargar idioma
$lang = isset($_POST['lang']) && in_array($_POST['lang'], ['es', 'en']) ? $_POST['lang'] : 'es';
$translations = require_once "lang/$lang.php";

// Tamaño máximo permitido por archivo (5 MB en bytes)
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5,000,000 bytes

// Validar entrada
$courseid = filter_input(INPUT_POST, 'courseid', FILTER_VALIDATE_INT);
$userid = filter_input(INPUT_POST, 'userid', FILTER_VALIDATE_INT);
$recipients = $_POST['recipients'] ?? [];
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');
$from_name = trim($_POST['from_name'] ?? '');

if (!$courseid || !$userid || empty($recipients) || !$subject || !$message || !$from_name) {
    $error = $translations['error'];
    header("Location: index.php?courseid=$courseid&userid=$userid&lang=$lang&error=" . urlencode($error));
    exit;
}

// Conexión a la base de datos
try {
    $pdo = new PDO("pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = $translations['error'] . ': ' . $e->getMessage();
    header("Location: index.php?courseid=$courseid&userid=$userid&lang=$lang&error=" . urlencode($error));
    exit;
}

// Obtener correos de destinatarios
$emails = [];
if (in_array('all', $recipients)) {
    $stmt = $pdo->prepare("
        SELECT DISTINCT u.email
        FROM " . MOODLE_PREFIX . "user u
        JOIN " . MOODLE_PREFIX . "user_enrolments ue ON u.id = ue.userid
        JOIN " . MOODLE_PREFIX . "enrol e ON ue.enrolid = e.id
        JOIN " . MOODLE_PREFIX . "context ctx ON e.courseid = ctx.instanceid
        JOIN " . MOODLE_PREFIX . "role_assignments ra ON u.id = ra.userid AND ra.contextid = ctx.id
        WHERE e.courseid = :courseid AND ra.roleid != 9 AND ctx.contextlevel = 50
    ");
    $stmt->execute(['courseid' => $courseid]);
    $emails = array_column($stmt->fetchAll(PDO::FETCH_OBJ), 'email');
} else {
    $placeholders = implode(',', array_fill(0, count($recipients), '?'));
    $stmt = $pdo->prepare("
        SELECT email
        FROM " . MOODLE_PREFIX . "user
        WHERE id IN ($placeholders)
    ");
    $stmt->execute($recipients);
    $emails = array_column($stmt->fetchAll(PDO::FETCH_OBJ), 'email');
}

if (empty($emails)) {
    $error = $translations['no_recipients'];
    header("Location: index.php?courseid=$courseid&userid=$userid&lang=$lang&error=" . urlencode($error));
    exit;
}

// Validar tamaño de archivos adjuntos
if (!empty($_FILES['attachments']['name'][0])) {
    foreach ($_FILES['attachments']['size'] as $key => $size) {
        if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK && $size > MAX_FILE_SIZE) {
            $error = sprintf($translations['error'] . ': El archivo "%s" excede el tamaño máximo de 5 MB.', $_FILES['attachments']['name'][$key]);
            header("Location: index.php?courseid=$courseid&userid=$userid&lang=$lang&error=" . urlencode($error));
            exit;
        }
    }
}

// Configurar PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = SMTP_PORT;

    $mail->setFrom(FROM_EMAIL, $from_name);
    foreach ($emails as $email) {
        $mail->addBCC($email);
    }

    // Adjuntos
    if (!empty($_FILES['attachments']['name'][0])) {
        foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                $mail->addAttachment($tmp_name, $_FILES['attachments']['name'][$key]);
            }
        }
    }

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = strip_tags($message);

    $mail->send();
    header("Location: index.php?courseid=$courseid&userid=$userid&lang=$lang&success=" . urlencode($translations['success']));
} catch (Exception $e) {
    $error = $translations['error'] . ': ' . $mail->ErrorInfo;
    header("Location: index.php?courseid=$courseid&userid=$userid&lang=$lang&error=" . urlencode($error));
}
?>