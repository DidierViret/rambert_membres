<!-- Display person update button for managers and admins -->
<?php if ($_SESSION['access_level'] >= config('\Access\Config\AccessConfig')->access_lvl_manager): ?>
    <div class="person-update-button row bg-light pt-2 pb-2" >
        <div class="col-12">
            <?php if (!empty($person['date_delete'])): ?>
                <!-- Archived person : only display a restore button -->
                <form method="post" action="<?= base_url('person/restore/'.$person['id']) ?>" class="d-inline">
                    <button type="submit" class="btn btn-outline-success" title="<?= lang('members_lang.btn_restore') ?>"><i class="bi bi-arrow-counterclockwise" style="font-size: 20px;"></i></button>
                </form>
            <?php else: ?>
                <a href="<?= base_url('person/update/'.$person['id']) ?>" class="btn btn-outline-primary" title="<?= lang('members_lang.btn_update') ?>"><i class="bi bi-pencil" style="font-size: 20px;"></i></a>
                <a href="<?= base_url('person/delete/'.$person['id']) ?>" class="btn btn-outline-danger" title="<?= lang('members_lang.btn_membership_end') ?>"><i class="bi bi-trash" style="font-size: 20px;"></i></a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>