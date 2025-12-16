<?php
/**
 * Common confirmation message to display before deletion
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
?>

<div class="container" >
    <div id="delete_confirmation" class="row">
        <div class="col-12">
            <div class="alert alert-danger" role="alert"><?= $message ?></div>
            <form method="post" action="<?= $url_yes ?>">
                <a href="<?= $url_no ?>" class="btn btn-outline-secondary"><?= lang('common_lang.no') ?></a>
                <input type="submit" class="btn btn-outline-danger" value="<?= lang('common_lang.yes') ?>" />
            </form>
        </div>
    </div>
</div>
