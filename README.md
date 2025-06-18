Moodle Messages Module
A standalone PHP application to send messages to enrolled users in a specific Moodle course, excluding users with role ID 9. Supports TinyMCE for rich text editing, file attachments, and two languages (Spanish and English).
Features

Send emails to one, multiple, or all course participants.
Excludes users with role ID 9.
Uses TinyMCE for rich text editing (loaded locally).
Supports file attachments via PHPMailer (max 5 MB per file).
Bilingual support (Spanish and English).
Captures course ID and user ID automatically from URL parameters.
Compatible with PostgreSQL database.
Secure and suitable for public GitHub repository.

Installation

Clone the repository to /data/htdocs/moodle-messages:git clone https://github.com/yourusername/moodle-messages.git /data/htdocs/moodle-messages


Install PHPMailer:composer require phpmailer/phpmailer

Or download it from https://github.com/PHPMailer/PHPMailer and place it in lib/PHPMailer/.
Install TinyMCE:
Download TinyMCE from https://www.tiny.cloud/download/.
Extract the ZIP and copy the tinymce folder to lib/tinymce/ in the project.


Create config/config.php based on the example below:<?php
define('DB_HOST', 'your_host');
define('DB_PORT', '5432');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database');
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your_email@gmail.com');
define('SMTP_PASS', 'your_app_password');
define('SMTP_PORT', 587);
define('FROM_EMAIL', 'your_email@gmail.com');
define('MOODLE_PREFIX', 'mdl_');
?>


Configure PHP settings in php.ini:
Set upload_max_filesize = 5M
Set post_max_size = 20M (to allow multiple attachments)
Ensure the pdo_pgsql extension is enabled.
Restart your web server after making changes.


Configure your web server to point to /data/htdocs/moodle-messages, accessible as https://aulavirtual.utp.edu.co/moodle-messages/.
In Moodle (located at /data/htdocs/campusunivirtual/moodle), add an HTML block with a link like:<a href="https://aulavirtual.utp.edu.co/moodle-messages/index.php?courseid=<?php echo $COURSE->id; ?>&userid=<?php echo $USER->id; ?>">Send Messages to Course</a>



Usage

Access the form via the link in the Moodle course.
Select recipients (individual users or all participants).
Enter subject and message using TinyMCE.
Attach files (up to 5 MB each).
Send the email.

License
MIT License
