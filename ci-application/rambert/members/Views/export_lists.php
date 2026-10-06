<?php
/**
 * Display a header for export lists.
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
?>

<div class="container pb-4">
    <div id="export-list-header" class="row mb-2">
        <!-- Title -->
        <div class="text-left col-12">
            <h3><?= lang('members_lang.title_export_lists') ?></h3>
        </div>

        <!-- Dropdown to select list type -->
        <div class="col-8">
            <div class="form-group">
                <select class="form-control" id="list-type">
                    <option value="postal-send" <?php if (isset($list_type) && $list_type == 'postal-send') echo 'selected'; ?>>
                        <?=lang('members_lang.export_list_type_postal_send')?>
                    </option>
                    <option value="newsletter-addresses" <?php if (isset($list_type) && $list_type == 'newsletter-addresses') echo 'selected'; ?>>
                        <?=lang('members_lang.export_list_type_newsletter_addresses')?>
                    </option>
                    <option value="no-email-address" <?php if (isset($list_type) && $list_type == 'no-email-address') echo 'selected'; ?>>
                        <?=lang('members_lang.export_list_type_no_email_address')?>
                    </option>
                    <option value="all-members" <?php if (isset($list_type) && $list_type == 'all-members') echo 'selected'; ?>>
                        <?=lang('members_lang.export_list_type_all_members')?>
                    </option>
                    <option value="all-members-with-soft-deleted" <?php if (isset($list_type) && $list_type == 'all-members-with-soft-deleted') echo 'selected'; ?>>
                        <?=lang('members_lang.export_list_type_all_members_with_soft_deleted')?>
                    </option>
                </select>
            </div>
        </div>

        <!-- Export button -->
        <div class="col-4 text-right">
            <input id="export-excel" type="button" class="btn btn-outline-success" value="<?=lang('members_lang.btn_export_excel')?>" >
        </div>
    </div>

    
    <div id="export-list-content" class="table-responsive">
        <?php if(isset($data) && !empty($data)): ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <!-- Get columns headers -->
                        <?php foreach ($data['columns'] as $column): ?>
                            <th scope="col"><?= $column ?></th>
                        <?php endforeach ?>
                    </tr>
                </thead>
                <tbody>
                    <!-- One table row for each entry -->
                    <?php foreach ($data['rows'] as $row): ?>
                    <tr>
                        <!-- Display row's properties wich should correspond to "columns" -->
                        <?php foreach ($data['columns'] as $columnKey => $column): ?>
                            <td><?= esc($row[$columnKey]) ?></td>
                        <?php endforeach ?>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        
        <?php else: ?>
            <!-- No data -->
            <div class="alert alert-info" role="alert">
                <?= lang('members_lang.msg_error_no_data_to_display') ?>
            </div>
        <?php endif ?>
    </div>
</div>

<!-- Javascript to load informations corresponding to the selected list type -->
<script>
    $(document).ready(function() {
        $('#list-type').on('change', function() {
            var list_type = $(this).val();
            var get_url = '<?= base_url(); ?>lists?list-type='+list_type;

            // call Lists controller method to update data content
            $.get(get_url, data => {
                $('#export-list-content').empty();

                // replace the content of the export-list-content div with the new datas
                $('#export-list-content').html($(data).find('#export-list-content').html());
            });
        });

        $('#export-excel').on('click', function() {
            var list_type = $('#list-type').val();
            var export_url = '<?= base_url(); ?>lists/export-excel?list-type='+list_type;

            window.location.href = export_url;
        });
    });
</script>