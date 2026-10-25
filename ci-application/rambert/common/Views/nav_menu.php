<?php
/**
 * Display a navigation menu.
 * 
 * This part of page is included by the BaseController display_view() method.
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
?>

<div id="nav-menu" class="container pb-4">
    <!-- Tabs are displayed in a line on medium and large screens, in a burger menu on small screens -->
    <nav class="navbar navbar-expand-md navbar-light p-0">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#nav-menu-tabs"
                aria-controls="nav-menu-tabs" aria-expanded="false" aria-label="<?=lang('common_lang.btn_menu')?>">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav-menu-tabs">
            <div class="navbar-nav">
                <?php foreach (config('\Common\Config\NavMenuConfig')->tabs as $tab){?>
                    <a href="<?=base_url($tab['pageLink'])?>" class="btn btn-primary navmenu mt-2 mt-md-0 mr-md-1" ><?=lang($tab['label'])?></a>
                <?php } ?>
            </div>
        </div>
    </nav>
</div>
<script defer>
    document.querySelectorAll('.navmenu').forEach((nav)=>{
        if (nav.href.includes(window.location.pathname)){
            nav.classList.add('active')
        }
        else{
            nav.classList.remove('active')
        }
    })
</script>