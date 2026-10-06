<!-- Display person update button for managers and admins -->
<?php if ($_SESSION['access_level'] >= config('\Access\Config\AccessConfig')->access_lvl_manager): ?>
    <div class="person-update-button row bg-light pt-2 pb-2" >
        <div class="col-12 d-flex align-items-center">
            <?php if (!empty($person['date_delete'])): ?>
                <!-- Archived person : only display a restore button and a resigned label aligned on the right -->
                <a href="<?= base_url('person/restore/'.$person['id']) ?>" class="btn btn-outline-success" title="<?= lang('members_lang.btn_restore') ?>"><i class="bi bi-arrow-counterclockwise" style="font-size: 20px;"></i></a>
                <span class="ml-auto text-danger"><?= lang('members_lang.field_resigned') ?></span>
            <?php else: ?>
                <a href="<?= base_url('person/update/'.$person['id']) ?>" class="btn btn-outline-primary mr-2" title="<?= lang('members_lang.btn_update') ?>"><i class="bi bi-pencil" style="font-size: 20px;"></i></a>
                <a href="<?= base_url('person/delete/'.$person['id']) ?>" class="btn btn-outline-danger" title="<?= lang('members_lang.btn_membership_end') ?>"><i class="bi bi-trash" style="font-size: 20px;"></i></a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>