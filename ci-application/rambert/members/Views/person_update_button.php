<!-- Display person update button for managers and admins -->
<?php if ($_SESSION['access_level'] >= config('\Access\Config\AccessConfig')->access_lvl_manager): ?>
    <div class="person-update-button row bg-light pt-2 pb-2" >
        <div class="col-12">
            <a href="<?= base_url('person/update/'.$person['id']) ?>" class="btn btn-outline-primary"><?= lang('members_lang.btn_update') ?></a>
        </div>
    </div>
<?php endif; ?>