<?php
/**
 * Confirmation message to display before restoring an archived person.
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
?>

<div class="container" >
    <div id="person_restore_confirmation" class="row">
        <div class="col-12">
            <h2><?= $title ?></h2>
            <div class="alert alert-warning" role="alert"><?= $message ?></div>
            <form method="post" action="<?= $url_yes ?>">
                <a href="<?= $url_no ?>" class="btn btn-outline-secondary"><?= lang('common_lang.btn_cancel') ?></a>
                <input type="submit" class="btn btn-outline-success" value="<?= lang('common_lang.btn_validate') ?>" />
            </form>
        </div>
    </div>
</div>
