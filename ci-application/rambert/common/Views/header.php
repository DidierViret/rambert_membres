<?php
/**
 * Common header used for all views.
 * 
 * This part of page is included in all pages by using the BaseController display_view() method.
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Copied from Bootstrap model https://getbootstrap.com/docs/4.6/getting-started/introduction/) -->

    <title>
        <?php
        if (!isset($title) || is_null($title) || $title == '') {
            echo lang('common_lang.page_prefix');
        } else {
            echo lang('common_lang.page_prefix').' - '.$title;
        }
        ?>
    </title>

    <!-- Icon -->
    <link rel="icon" type="image/png" href="<?= base_url("images/favicon.png"); ?>" />
    <link rel="shortcut icon" type="image/png" href="<?= base_url("images/favicon.png"); ?>" />

    <!-- Bootstrap  -->
    <!-- Rambert Bootstrap CSS personalized with https://bootstrap.build/app -->
    <link rel="stylesheet" href="<?= base_url("css/rambert_bootstrap.min.css"); ?>" />
    <!-- Bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
     <!-- Include Bootstrap and jQuery javascript -->
    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous">
    </script>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
        crossorigin="anonymous">
    </script>

    <!-- Application styles -->
    <link rel="stylesheet" href="<?= base_url("css/MY_styles.css"); ?>" />
</head>
<body>
    <?php
        if (ENVIRONMENT != 'production') {
            echo '<div class="alert alert-warning text-center">CodeIgniter environment variable is set to '.strtoupper(ENVIRONMENT).'. You can change it in .env file.</div>';
        }
    ?>