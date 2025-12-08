<?php
/**
 * Login form
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
?>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-sm-10 well">
            <!-- Page title -->
            <h2><?= lang('access_lang.title_login'); ?></h2>

            <!-- Error message -->
            <?php if(!empty($error_message)) { ?>
                    <div id="error" class="alert alert-danger text-center"><?= $error_message ?></div>
            <?php } ?>
            
            <!-- Login form -->
            <?php
                $attributes = array("class" => "form-horizontal",
                                    "id" => "loginform",
                                    "name" => "loginform");
                echo form_open("login", $attributes);
            ?>
            
            <fieldset>
                <div class="form-group">
                    <div class="row colbox">
                        <div class="col-sm-4">
                            <label for="email" class="control-label"><?= lang('access_lang.field_email'); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <input class="form-control" id="email" name="email" type="email" value="<?= set_value('email'); ?>" />
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row colbox">
                        <div class="col-sm-4">
                            <label for="password" class="control-label"><?= lang('access_lang.field_password'); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <input class="form-control" id="password" name="password" type="password" value="<?= set_value('password'); ?>" />
                        </div>
                    </div>
                </div>
                                    
                <div class="form-group">
                    <div class="col-sm-12 text-right">
                        <a id="btn_cancel" class="btn btn-secondary" href="<?= base_url(); ?>"><?= lang('common_lang.btn_cancel'); ?></a>
                        <input id="btn_login" name="btn_login" type="submit" class="btn btn-primary" value="<?= lang('access_lang.btn_login'); ?>" />
                    </div>
                </div>
            </fieldset>
            <?= form_close(); ?>
        </div>
    </div>
</div>
