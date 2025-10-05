<?php
/**
 * Confirmation message to display before deletion of a person.
 * Lets the manager confirm the cancellation of a membership. Admin are allowed to delete a person from the database.
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
?>

<div class="container" >
    <div id="person_delete_confirmation" class="row">
        <div class="col-12">
            <h2><?= $title ?></h2>
            <div class="alert alert-danger" role="alert"><?= $message ?></div>
            <form method="post" class="col-12" action="<?= $url_yes ?>">
                <div class="form-group row">
                    <label for="membership_end" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_membership_end') ?></label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" name="membership_end" value="<?= date("Y") ?>" />
                    </div>

                    <label for="membership_end_reason" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_membership_end_reason') ?></label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" name="membership_end_reason" value="" />
                    </div>
                </div>

                <a href="<?= $url_no ?>" class="btn btn-outline-secondary"><?= lang('common_lang.btn_cancel') ?></a>
                <input type="submit" class="btn btn-outline-danger" value="<?= lang('common_lang.btn_validate') ?>" />
            </form>
        </div>
    </div>
</div>
