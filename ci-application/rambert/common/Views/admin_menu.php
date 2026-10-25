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
    <div class="row">
        <div class="col">
            <?php foreach (config('\Common\Config\AdminPanelConfig')->tabs as $tab){?>
                <a href="<?=base_url($tab['pageLink'])?>" class="btn btn-primary adminnav" ><?=lang($tab['label'])?></a>
            <?php } ?>
        </div>
    </div>
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