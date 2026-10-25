<?php
/**
 * Team selection with the actions on the selected team.
 * Displayed above the list of the selected team's roles.
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
helper('form');
?>

<div class="container" >
    <!-- Page title -->
    <div class="row mb-2">
        <div class="col-12">
            <h3><?= lang('members_lang.title_teams_list') ?></h3>
        </div>
    </div>

    <div class="team row bg-light border-bottom border-primary pt-2 pb-2 mb-4">

        <!-- Team selection -->
        <div class="col-md-6 mb-2">
            <label for="team_select"><?= lang('members_lang.field_team') ?></label>
            <?= form_dropdown('team_select', $teams, $team['id'] ?? 0, ['class' => 'form-control', 'id' => 'team_select']) ?>
        </div>

        <!-- Actions on the selected team -->
        <div class="col-md-6 mb-2 d-flex align-items-end flex-wrap">
            <a href="<?= base_url('teams/create') ?>" class="btn btn-outline-primary mr-2 mt-2"><i class="bi bi-plus-lg"></i> <?= lang('members_lang.btn_add_team') ?></a>
            <?php if (!empty($team)): ?>
                <a href="<?= base_url('teams/update/'.$team['id']) ?>" class="btn btn-outline-secondary mr-2 mt-2"><i class="bi bi-pencil"></i> <?= lang('members_lang.btn_update') ?></a>
                <a href="<?= base_url('teams/delete/'.$team['id']) ?>" class="btn btn-outline-danger mt-2"><i class="bi bi-trash"></i> <?= lang('members_lang.btn_delete') ?></a>
            <?php endif ?>
        </div>

        <!-- Description of the selected team -->
        <?php if (!empty($team['description'])): ?>
            <div class="col-12">
                <?= esc($team['description']) ?>
            </div>
        <?php endif ?>
    </div>
</div>

<script>
    // Display the roles of the newly selected team
    document.getElementById('team_select').addEventListener('change', (e) => {
        window.location.href = '<?= base_url('teams') ?>/' + e.currentTarget.value;
    });
</script>
