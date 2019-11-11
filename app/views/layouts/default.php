<!DOCTYPE html>
<html dir="ltr">
    <head>
        <meta charset="utf-8">
        <title><?php echo $title ?></title>
        <!-- <link rel="stylesheet" href="<?php echo $_SERVER['DOCUMENT_ROOT'].'/public/styles/main.css' ?>"> -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <!-- <link rel="shortcut icon" href="/test/logfile.png" type="img/png"> -->
        <style>
            <?php echo file_get_contents($_SERVER['DOCUMENT_ROOT'].'/public/styles/main.css'); ?>
        </style>
    </head>
    <body>
        <?php echo $content ?? "<h1>Empty page</h1>" ?>
    </body>
</html>
