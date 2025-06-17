Moodle Messages Module
A standalone PHP application to send messages to enrolled users in a specific Moodle course, excluding users with role ID 9. Supports TinyMCE for rich text editing, file attachments, and two languages (Spanish and English).
Features

Send emails to one, multiple, or all course participants.
Excludes users with role ID 9.
Uses TinyMCE for rich text editing.
Supports file attachments via PHPMailer.
Bilingual support (Spanish and English).
Captures course ID automatically from URL parameter.
Secure and suitable for public GitHub repository.

Installation

Clone the repository:git clone https://github.com/yourusername/moodle-messages.git


Install PHPMailer:composer require phpmailer/phpmailer

Or download it and place it in lib/PHPMailer/.
Create config/config.php based on the example below:<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'moodle_user');
define('DB_PASS', 'your_password');
define('DB_NAME', 'moodle_db');
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your_email@gmail.com');
define('SMTP_PASS', 'your_app_password');
define('SMTP_PORT', 587);
define('FROM_EMAIL', 'your_email@gmail.com');
define('FROM_NAME', 'Moodle Messages');
define('MOODLE_PREFIX', 'mdl_');


Configure your web server to point to the moodle-messages directory.
In Moodle, add an HTML block with a link like:<a href="https://yourdomain.com/moodle-messages/index.php?courseid=123">Send Messages to Course</a>



Usage

Access the form via the link in the Moodle course.
Select recipients (individual users or all participants).
Enter subject and message using TinyMCE.
Attach files if needed.
Send the email.

License
MIT License
