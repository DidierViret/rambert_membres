<?php
/**
 * Form to create or update a role
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
helper('form');
?>

<div class="container" >
    <div class="role row bg-light border-bottom border-primary pt-2 pb-2 mb-4">
        <!-- Display the form title -->
        <div class="col-12 mb-2">
            <h3><?= $title ?></h3>
        </div>

        <!-- Display the role form -->
        <?= form_open('teams/role/save/'.$role['id'], ['class' => 'col-12', 'id' => 'role_form']) ?>

            <!-- Error messages -->
            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger" role="alert">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

            <!-- Display the role fields -->
            <div class="row mb-2">
                <div class="col-sm-6">
                    <label for="fk_team"><?= lang('members_lang.field_team') ?></label>
                    <?= form_dropdown('fk_team', $teams, $role['fk_team'] ?? 0, ['class' => 'form-control', 'id' => 'fk_team']) ?>
                </div>
                <div class="col-sm-6">
                    <label for="name"><?= lang('members_lang.col_name') ?></label>
                    <input type="text" name="name" id="name" class="form-control" maxlength="100" value="<?= esc($role['name']) ?>" required />
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <label for="description"><?= lang('members_lang.col_description') ?></label>
                    <input type="text" name="description" id="description" class="form-control" maxlength="255" value="<?= esc($role['description'] ?? '') ?>" />
                </div>
            </div>

            <!-- Display action buttons -->
            <div class="row mb-2">
                <div class="col-12">
                    <a href="<?= base_url('teams/'.($role['fk_team'] ?? 0)) ?>" class="btn btn-outline-secondary"><?= lang('members_lang.btn_cancel') ?></a>
                    <input type="submit" class="btn btn-outline-success" value="<?= lang('members_lang.btn_save') ?>" />
                </div>
            </div>
        <?= form_close() ?>
    </div>
</div>
