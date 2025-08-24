<?php
/**
 * Form to create or update access rights
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
$update = (!empty($access) && !empty($access['id']) && $access['id'] > 0);
?>
<div class="container">
    <!-- TITLE -->
    <div class="row">
        <div class="col">
            <h1 class="title-section"><?= lang('access_lang.title_access_'.($update ? 'update' : 'new')); ?></h1>
        </div>
    </div>

    <!-- INFORMATION MESSAGE IF USER IS DISABLED -->
    <?php if ($update && $access['date_delete']) { ?>
        <div class="col-12 alert alert-info">
            <?= lang("access_lang.access_disabled_info"); ?>
        </div>
    <?php } ?>

    <!-- FORM OPEN -->
    <?php
    $attributes = array(
        'id' => 'access_form',
        'name' => 'access_form'
    );
    $hidden = array(
        'id' => $access['id'] ?? '0'
    );
    echo form_open('access/save', $attributes, $hidden);
    ?>

    <!-- ERROR MESSAGES -->
    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger" role="alert">
            <ul>
                <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <!-- ACCESS FIELDS -->
    <div class="row">
        <!-- email -->
        <div class="col-sm-6">
            <?php if ($update): ?>
                <div class="form-group was-validated">
            <?php else: ?>
                <div class="form-group">
            <?php endif ?>
                <?= form_label(lang('access_lang.field_email'), 'email', ['class' => 'form-label']); ?>
                <?= form_input('email', $access['person']['email'] ?? '', [
                    'maxlength' => config("\Access\Config\AccessConfig")->email_max_length,
                    'class' => 'form-control', 'id' => 'email', 'required' => ''
                ]); ?>
            </div>
        </div>

        <!-- access_level -->
        <div class="col-sm-6 form-group">
            <?= form_label(lang('access_lang.field_access_level'), 'access_level', ['class' => 'form-label']); ?>
            <?php
                $dropdown_options = ['class' => 'form-control', 'id' => 'access_level'];
                if($update && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $access['person']['id']){
                    $dropdown_options['disabled'] = 'disabled';
                    echo form_hidden('access_level', $access['fk_access_level'] ?? "");
                    echo "<div class=\"alert alert-info\">".lang('access_lang.access_update_level_himself')."</div>";
                }
            ?>
            <?= form_dropdown('access_level', $access_levels, $access['fk_access_level'] ?? NULL, $dropdown_options); ?>
        </div>
    </div>

    <?php if (!$update) { ?>
        <!-- PASSWORD FIELDS ONLY FOR NEW ACCESS -->
        <div class="row">
            <div class="col-sm-6 form-group">
                <?= form_label(lang('access_lang.field_password'), 'password', ['class' => 'form-label']); ?>
                <?= form_password('password', '', [
                    'class' => 'form-control', 'id' => 'password'
                ]); ?>
            </div>
            <div class="col-sm-6 form-group">
                <?= form_label(lang('access_lang.field_password_confirm'), 'password_confirm', ['class' => 'form-label']); ?>
                <?= form_password('password_confirm', '', [
                    'maxlength' => config('\Access\Config\AccessConfig')->password_max_length,
                    'class' => 'form-control', 'id' => 'password_confirm'
                ]); ?>
            </div>
        </div>
    <?php } ?>

    <?php if ($update) { ?>
        <div class="row">
            <!-- RESET PASSWORD FOR EXISTING ACCESS -->
            <div class="col-12">
                <a href="<?= base_url('access/update_password/'.$access['id']); ?>" >
                    <?= lang("access_lang.title_access_password_reset"); ?>
                </a>
            </div>
            
            <!-- ACTIVATE / DISABLE EXISTING ACCESS -->
            <?php if ($access['date_delete']) { ?>
                <div class="col-12">
                    <a href="<?= base_url('access/restore/'.$access['id']); ?>" >
                        <?= lang("access_lang.access_reactivate"); ?>
                    </a>
                </div>
                <div class="col-12">
                    <a href="<?= base_url('access/delete/'.$access['id']); ?>" class="text-danger" >
                        <?= lang("access_lang.btn_hard_delete_access"); ?>
                    </a>
                </div>
            <?php } else { ?>
                <div class="col-12">
                    <a href="<?= base_url('access/delete/'.$access['id']); ?>" class="text-danger" >
                        <?= lang("access_lang.access_delete"); ?>
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <!-- FORM BUTTONS -->
    <div class="row">
        <div class="col text-right">
            <a class="btn btn-secondary" href="<?= base_url('access'); ?>"><?= lang('common_lang.btn_cancel'); ?></a>
            <?= form_submit('save', lang('common_lang.btn_save'), ['class' => 'btn btn-primary']); ?>
        </div>
    </div>
    <?= form_close(); ?>
</div>