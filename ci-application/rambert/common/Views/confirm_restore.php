<?php
/**
 * Common confirmation message to display before restoring an archived item
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
?>

<div class="container" >
    <div id="restore_confirmation" class="row">
        <div class="col-12">
            <h3><?= $title ?></h3>
            <div class="alert alert-warning" role="alert"><?= $message ?></div>
            <form method="post" action="<?= $url_yes ?>">
                <a href="<?= $url_no ?>" class="btn btn-outline-secondary"><?= lang('common_lang.btn_cancel') ?></a>
                <input type="submit" class="btn btn-outline-success" value="<?= lang('common_lang.btn_restore') ?>" />
            </form>
        </div>
    </div>
</div>
