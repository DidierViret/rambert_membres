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
        <div class="col-12 col-md-8">
            <h3><?= lang('members_lang.title_teams_list') ?></h3>
        </div>
    </div>

    <div class="team row bg-light border-bottom border-primary pt-2 pb-2 mb-4">

        <!-- Team selection -->
        <div class="col-12 col-md-8 mb-2">
            <label for="team_select"><?= lang('members_lang.field_team') ?></label>
        </div>
        <div class="col-12 col-md-4 text-md-right mb-2">
            <label class="form-check-label" for="with_archived"><?= lang('common_lang.btn_show_disabled') ?></label>
            <?= form_checkbox('with_archived', '1', $with_archived, ['class' => 'ml-1 align-middle', 'id' => 'with_archived']) ?>
        </div>
        <div class="col-12 col-md-6 mb-2">
            <?= form_dropdown('team_select', $teams, $team['id'] ?? 0, ['class' => 'form-control', 'id' => 'team_select']) ?>
        </div>

        <!-- Actions on the selected team -->
        <div class="col-12 col-md-6 d-flex align-items-start flex-wrap">
            <a href="<?= base_url('teams/create') ?>" class="btn btn-outline-primary mr-2 mb-2"><i class="bi bi-plus-lg"></i> <?= lang('members_lang.btn_add_team') ?></a>
            <?php if (!empty($team['date_delete'])): ?>
                <!-- Archived team : it can only be restored -->
                <a href="<?= base_url('teams/restore/'.$team['id']) ?>" class="btn btn-outline-success mb-2" title="<?= lang('common_lang.btn_restore') ?>"><i class="bi bi-arrow-counterclockwise"></i></a>
            <?php elseif (!empty($team)): ?>
                <a href="<?= base_url('teams/update/'.$team['id']) ?>" class="btn btn-outline-secondary mr-2 mb-2" title="<?= lang('members_lang.btn_update') ?>"><i class="bi bi-pencil"></i></a>
                <a href="<?= base_url('teams/delete/'.$team['id']) ?>" class="btn btn-outline-danger mb-2" title="<?= lang('members_lang.btn_delete') ?>"><i class="bi bi-trash"></i></a>
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
    // Display the selected team's roles, with or without the archives
    const displayTeam = (teamId) => {
        const withArchived = document.getElementById('with_archived').checked;
        window.location.href = '<?= base_url('teams') ?>/' + teamId + (withArchived ? '?with_archived=1' : '');
    };

    document.getElementById('team_select').addEventListener('change', (e) => {
        displayTeam(e.currentTarget.value);
    });

    // When hiding the archives while an archived team is selected, display the roles without team
    document.getElementById('with_archived').addEventListener('change', (e) => {
        <?php if (!empty($team['date_delete'])): ?>
            displayTeam(e.currentTarget.checked ? <?= $team['id'] ?> : 0);
        <?php else: ?>
            displayTeam(document.getElementById('team_select').value);
        <?php endif ?>
    });
</script>
