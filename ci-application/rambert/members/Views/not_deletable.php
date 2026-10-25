<?php
/**
 * Information message to display when an item can't be deleted because it is still in use
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
?>

<div class="container" >
    <div id="not_deletable" class="row">
        <div class="col-12">
            <div class="alert alert-warning" role="alert"><?= $message ?></div>
            <a href="<?= $url_back ?>" class="btn btn-outline-secondary"><?= lang('common_lang.btn_back') ?></a>
        </div>
    </div>
</div>
