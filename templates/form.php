<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $translations['title']; ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.tinymce.com/4/tinymce.min.js"></script>
    <script src="js/scripts.js"></script>
</head>
<body>
    <div class="lang-switch">
        <a href="?courseid=<?php echo $courseid; ?>&userid=<?php echo $userid; ?>&lang=es"><?php echo $translations['lang_es']; ?></a> |
        <a href="?courseid=<?php echo $courseid; ?>&userid=<?php echo $userid; ?>&lang=en"><?php echo $translations['lang_en']; ?></a>
    </div>
    <h1><?php echo $translations['title']; ?></h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="send.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="courseid" value="<?php echo $courseid; ?>">
        <input type="hidden" name="userid" value="<?php echo $userid; ?>">
        <input type="hidden" name="lang" value="<?php echo $lang; ?>">
        <input type="hidden" name="from_name" value="<?php echo htmlspecialchars($from_name); ?>">
        <div class="form-group">
            <label for="recipients"><?php echo $translations['select_recipients']; ?></label>
            <select name="recipients[]" id="recipients" multiple size="10">
                <option value="all"><?php echo $translations['all_participants']; ?></option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user->id; ?>"><?php echo htmlspecialchars($user->firstname . ' ' . $user->lastname . ' (' . $user->email . ')'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="subject"><?php echo $translations['subject']; ?></label>
            <input type="text" name="subject" id="subject" required>
        </div>
        <div class="form-group">
            <label for="message"><?php echo $translations['message']; ?></label>
            <textarea name="message" id="message" required></textarea>
        </div>
        <div class="form-group">
            <label for="attachments"><?php echo $translations['attachments']; ?></label>
            <input type="file" name="attachments[]" id="attachments" multiple>
        </div>
        <button type="submit"><?php echo $translations['send']; ?></button>
        <a href="<?php echo $moodle_url; ?>"><?php echo $translations['back']; ?></a>
    </form>
</body>
</html>