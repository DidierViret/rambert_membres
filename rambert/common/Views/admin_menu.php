<?php
/**
 * Display an administration menu if logged in user has administration rights.
 * 
 * This part of page is included by the BaseController display_view() method if user has administration rights.
 *
 * @author      Didier Viret
 * @link        https://github.com/DidierViret
 * @copyright   Copyright (c), Didier Viret
 */
?>

<div id="admin-menu" class="container">
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
        if (nav.href.includes(window.location)){
            nav.classList.add('active')
        }
        else{
            nav.classList.remove('active')
        }
    })
</script>