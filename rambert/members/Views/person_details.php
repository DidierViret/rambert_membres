<div class="person row bg-light border-bottom border-primary pt-2 pb-2 mb-4">

    <!-- If the person has access rights, display a badge for each access_level -->
    <?php if (!empty($person['access_levels'])): ?>
        <div class="col-12 mb-2">
            <span class="small" ><strong><?= lang('members_lang.col_access_levels') ?> : </strong></span>
            <?php foreach ($person['access_levels'] as $access_level): ?>
                <span class="badge badge-warning"><?= $access_level['name'] ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="col-lg-6 mb-2">
        <?= $person['title'] ?><br />
        <!-- Display the person's name -->
        <span class="text-primary"><strong><?= $person['last_name'].' '.$person['first_name'] ?></strong></span>

        <!-- Display the person's contact informations -->
        <div><a href="mailto:<?= $person['email'] ?>"><?= $person['email'] ?></a></div>
        <div><?= $person['phone_1'] ?></div>
        <div><?= $person['phone_2'] ?></span></div>

        <!-- If there are comments about the person, display them -->
        <?php if (!empty($person['comments'])): ?>
            <div class="small alert alert-info"><?= $person['comments'] ?></div>
        <?php endif; ?>
    </div>
    <div class="col-lg-6 mb-2">
        <!-- Display the person's details -->
        <div class="small"><?= lang('members_lang.field_birth').' : '.$person['birth'] ?></div>
        <div class="small"><?= lang('members_lang.field_membership_start').' : '.$person['membership_start'] ?></div>
        <?php if (!empty($person['membership_end'])): ?>   
            <div class="small"><?= lang('members_lang.field_membership_end').' : '.$person['membership_end'] ?></div>
            <div class="small"><?= lang('members_lang.field_membership_end_reason').' : '.$person['membership_end_reason'] ?></div>
        <?php endif; ?>
        <?php if (!empty($person['profession'])): ?>
            <div class="small"><?= lang('members_lang.field_profession').' : '.$person['profession'] ?></div>
        <?php endif; ?>
        <?php if (!empty($person['godfathers'])): ?>
            <div class="small"><?= lang('members_lang.field_godfathers').' :</br>'.$person['godfathers'] ?></div>
        <?php endif; ?>
    </div>

    <!-- Display the person's membership category -->
    <div class="col-lg-6 mb-2">
        <div><strong><?= lang('members_lang.col_category') ?></strong></div>
        <div><?= $person['category']['name'] ?></div>
    </div>

    <!-- Display the person's newsletter subscriptions -->
    <div class="col-lg-6 mb-2">
        <div><strong><?= lang('members_lang.col_newsletter_subscriptions') ?></strong></div>
        <?php $no_subscription = true; ?>
        <?php foreach ($person['newsletters'] as $newsletter): ?>
            <?php if ($newsletter['subscribed']): ?>
                <?php $no_subscription = false; ?>
                <span class="badge badge-primary"><?= $newsletter['title'] ?></span><br />
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if($no_subscription): ?>
            <div class="small"><?= lang('members_lang.no_subscription') ?></div>
        <?php endif; ?>
    </div>

    <!-- Display the person's contributions to the club -->
    <div class="col-lg-6 mb-2">
        <div><strong><?= lang('members_lang.col_contributions') ?></strong></div>
        <!-- If user has manager or admin access, display the update button -->
        <?php if ($_SESSION['access_level'] >= config('\Access\Config\AccessConfig')->access_lvl_manager): ?>
            <div class="contributions-update-button row bg-light pt-2 pb-2" >
                <div class="col-12">
                    <a href="<?= base_url('contributions/'.$person['id']) ?>" class="btn btn-outline-primary btn-sm"><?= lang('members_lang.btn_update') ?></a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($person['contributions'])): ?> 
            <!-- List contributions still active -->
            <div>
            <?php foreach ($person['contributions'] as $contribution): ?>
                <div class="small">
                    <?php if (empty($contribution['date_end'])): ?>
                        <?php if (!empty($contribution['role']['team'])): ?>
                            <strong><?= $contribution['role']['team']['name'] ?></strong> :
                        <?php endif; ?>
                        
                        <?= $contribution['role']['name'].' ('.strtolower(lang('members_lang.since')).' '.$contribution['date_begin'].')' ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
            <!-- List contributions which are not active anymore -->
            <div class="mt-2 text-muted">
            <?php foreach ($person['contributions'] as $contribution): ?>
                <div class="small">
                    <?php if (!empty($contribution['date_end'])): ?>
                        <?php if (!empty($contribution['role']['team'])): ?>
                            <strong><?= $contribution['role']['team']['name'] ?></strong> :
                        <?php endif; ?>
                        
                        <?= $contribution['role']['name'].' ('.$contribution['date_begin'].'-'.$contribution['date_end'].')' ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>