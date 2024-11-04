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
    <!-- Bootstrap javascript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>

    <!-- Application styles -->
    <link rel="stylesheet" href="<?= base_url("css/MY_styles.css"); ?>" />
</head>
<body>
    <?php
        if (ENVIRONMENT != 'production') {
            echo '<div class="alert alert-warning text-center">CodeIgniter environment variable is set to '.strtoupper(ENVIRONMENT).'. You can change it in .env file.</div>';
        }
    ?>