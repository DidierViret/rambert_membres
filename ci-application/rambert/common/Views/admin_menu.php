<?php
/**
 * Display an administration menu if logged in user has administration rights.
 * 
 * This part of page is included by the BaseController display_view() method if user has administration rights.
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
?>

<div id="admin-menu" class="container pb-4">
    <!-- Tabs are displayed in a line on medium and large screens, in a burger menu on small screens -->
    <nav class="navbar navbar-expand-md navbar-light p-0">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#admin-menu-tabs"
                aria-controls="admin-menu-tabs" aria-expanded="false" aria-label="<?=lang('common_lang.btn_admin')?>">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="admin-menu-tabs">
            <div class="navbar-nav">
                <?php foreach (config('\Common\Config\AdminPanelConfig')->tabs as $tab){?>
                    <a href="<?=base_url($tab['pageLink'])?>" class="btn btn-primary adminnav mt-2 mt-md-0 mr-md-1" ><?=lang($tab['label'])?></a>
                <?php } ?>
            </div>
        </div>
    </nav>
</div>
<script defer>
    document.querySelectorAll('.adminnav').forEach((nav)=>{
        // Tab is active if the current page is the tab's page or one of its sub-pages
        if (window.location.href === nav.href || window.location.href.startsWith(nav.href + '/')){
            nav.classList.add('active')
        }
        else{
            nav.classList.remove('active')
        }
    })
</script>