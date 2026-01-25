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
    <div class="row">
        <div class="col">
            <?php foreach (config('\Common\Config\NavMenuConfig')->tabs as $tab){?>
                <a href="<?=base_url($tab['pageLink'])?>" class="btn btn-primary navmenu" ><?=lang($tab['label'])?></a>
            <?php } ?>
        </div>
    </div>
</div>
<script defer>
    document.querySelectorAll('.navmenu').forEach((nav)=>{
        if (nav.href.includes(window.location)){
            nav.classList.add('active')
        }
        else{
            nav.classList.remove('active')
        }
    })
</script>