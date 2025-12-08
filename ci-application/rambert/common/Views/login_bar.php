<?php
/**
 * A view containing a login bar with application logo, title
 * and links for login/logout/change password/administration functionnalities.
 * The links are related with the "access" module. They depend of the user access level.
 * 
 * This part of page is included in all pages by using the BaseController display_view() method.
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
?>

<div id="login-bar" class="container" >
  <div class="row xs-center">

    <!-- Logo -->
    <div class="col-5 col-md-3">
      <a href="<?php echo base_url(); ?>" ><img id="app-logo" class="img-fluid" src="<?php echo base_url("images/logo.png"); ?>" ></a>
    </div>

    <!-- Title -->
    <div class="col-7 col-md-6">
      <h1 id="app-title"><a href="<?php echo base_url(); ?>" class="text-dark text-decoration-none"><?php echo lang('common_lang.app_title'); ?></a></h1>
    </div>

    <!-- Links depending of the user access level -->
    <div class="col-sm-12 col-md-3 text-right" >
      <div id="login-menu" class="nav flex-column">
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) { ?>
          
          <!-- ADMIN ACCESS ONLY -->
          <?php if ($_SESSION['access_level'] >= config('\Access\Config\AccessConfig')->access_lvl_admin) { ?>
              <!-- Link to the first administration tab defined in Common\Config\AdminPanelConfig -->
              <a href="<?php echo base_url(config('\Common\Config\AdminPanelConfig')->tabs[0]['pageLink']); ?>" ><?php echo lang('common_lang.btn_admin'); ?></a>
          <?php } ?>
          <!-- END OF ADMIN ACCESS -->

          <!-- For logged in users, display a "change password" button -->
          <a href="<?php echo base_url("change_my_password"); ?>" ><?php echo lang('access_lang.btn_change_my_password'); ?></a>
          <!-- And a "logout" button -->
          <a href="<?php echo base_url("logout"); ?>" ><?php echo lang('access_lang.btn_logout'); ?></a>

        <?php } else { ?>
          <!-- For not logged in users, display a "login" button -->
          <a id="login_button" href="<?php echo base_url("login".(isset($after_login_redirect) ? '?after_login_redirect='.$after_login_redirect : '') ); ?>">
            <?php echo lang('access_lang.btn_login'); ?>
          </a>
        <?php } ?>
      </div>
    </div>
  </div>
</div>