<?php
/**
 * Form to let an admin change another user's password
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */


?>
<div class="container">
    <div class="row">
        <div class="col-md-10 well">
            <?php
            $attributes = array("class" => "form-horizontal",
                                "id" => "update_password",
                                "name" => "update_password");
            echo form_open("access/update_password/".$access['id'], $attributes);
            ?>
            <fieldset>
                <legend><?= $title ?></legend>

                <!-- ERROR MESSAGES -->
                <?php if (!empty($errors)) { ?>
                    <div id="error" class="alert alert-danger" role="alert">
                    <?php foreach ($errors as $error) { ?>
                        <?= $error ?><br />
                    <?php } ?>
                    </div>
                <?php } ?>
                
                <div class="form-group">
                    <div class="row colbox">
                        <div class="col-md-4">
                            <label for="new_password" class="control-label"><?= lang('access_lang.field_new_password'); ?></label>
                        </div>
                        <div class="col-md-8">
                            <input id="new_password" name="new_password" type="password" class="form-control" placeholder="<?= lang('access_lang.field_new_password'); ?>" value="<?= set_value('new_password'); ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row colbox">
                        <div class="col-md-4">
                            <label for="password_confirm" class="control-label"><?= lang('access_lang.field_password_confirm'); ?></label>
                        </div>
                        <div class="col-md-8">
                            <input id="password_confirm" name="password_confirm" type="password" class="form-control" placeholder="<?= lang('access_lang.field_password_confirm'); ?>" value="<?= set_value('password_confirm'); ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 text-right">
                        <a id="btn_cancel" class="btn btn-secondary" href="<?= base_url('access'); ?>"><?= lang('common_lang.btn_cancel'); ?></a>
                        <input id="btn_update_password" name="btn_update_password" type="submit" class="btn btn-primary" value="<?= lang('common_lang.btn_save'); ?>" />
                    </div>
                </div>
            </fieldset>
            <?= form_close(); ?>
        </div>
    </div>
</div>