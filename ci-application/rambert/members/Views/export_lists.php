<?php
/**
 * Display a header for export lists.
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
?>

<div id="export-list-header" class="container pb-4">
    <div class="row mb-2">
        <div class="text-left col-12">
            <!-- Display list title if defined defined -->
            <?= isset($list_title) ? '<h3>'.esc($list_title).'</h3>' : '' ?>
        </div>
        <div class="col-sm-6 text-left">
            <!-- Display the "create" button if url_create is defined -->
            <?php if(isset($url_create)): ?>
                <a class="btn btn-primary" href="<?= site_url(esc($url_create)) ?>"><?= esc($btn_create_label) ?></a>
            <?php endif ?>
        </div>
        <div class="col-sm-6 text-right">
            <!-- Display the "with_deleted" checkbox if with_deleted and url_getView variables are defined -->
            <?php if (isset($with_deleted) && isset($url_getView)): ?>
                <label class="form-check-label" for="toggle_deleted">
                    <?= lang($display_deleted_label); ?>
                </label>
                <?= form_checkbox('toggle_deleted', '', $with_deleted, ['id' => 'toggle_deleted']); ?>
            <?php endif ?>
        </div>
    </div>
    <div class="row mb-2">
        <!-- Title -->
        <div class="text-left col-12">
            <h3><?= lang('members_lang.title_export_lists') ?></h3>
        </div>

        <!-- List choice and export button -->
        <div class="col-8">
            <div class="form-group">
                <select class="form-control" id="list-type">
                    <option value="postal-send"><?=lang('members_lang.export_list_type_postal_send')?></option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                </select>
            </div>
        </div>
        <div class="col-4 text-right">
            <input type="button" class="btn btn-outline-success" value="<?=lang('members_lang.btn_export_excel')?>" >
        </div>
    </div>
</div>