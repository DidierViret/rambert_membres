<div class="container" >
    <div id="list_persons" >
        <?php if (!empty($persons)): ?>
            <?php foreach ($persons as $person): ?>
                <div class="person row bg-light border-bottom border-primary pt-2 pb-2 mb-4">
                    <div class="col-lg-4 col-md-6">
                        <div>
                            <strong><a href=""><?= $person['last_name'].' '.$person['first_name'] ?></a></strong>
                            <!-- If the personn has access rights, display a badge for each access_level -->
                            <?php if (!empty($person['access_levels'])): ?>
                                <?php foreach ($person['access_levels'] as $access_level): ?>
                                    <span class="badge badge-warning"><?= $access_level['name'] ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="small"><a href=""><?= $person['email'] ?></a></div>
                        <div class="small"><?= $person['phone_1'] ?></div>
                        <div class="small"><?= $person['phone_2'] ?></span></div>
                        <?php if (!empty($person['comments'])): ?>
                            <div class="small alert alert-info"><?= $person['comments'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="small"><strong><a href=""><?= lang('members_lang.col_home_address') ?></a></strong></div>
                        <div class="small"><?= $person['home']['address_title'] ?></div>
                        <div class="small"><?= $person['home']['address_name'] ?></div>
                        <div class="small"><?= $person['home']['address_line_1'] ?></div>
                        <div class="small"><?= $person['home']['address_line_2'] ?></div>
                        <div class="small"><?= $person['home']['postal_code'].' '.$person['home']['city'] ?></div>
                        <?php if (!empty($person['home']['comments'])): ?>
                            <div class="small alert alert-info"><?= $person['home']['comments'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="small"><strong><a href=""><?= lang('members_lang.col_category') ?></a></strong></div>
                        <div class="small"><?= $person['category']['name'] ?></div>
                        <?php if (!empty($person['roles'])): ?>
                            <div class="small"><strong><a href=""><?= lang('members_lang.col_roles') ?></a></strong></div>
                            <?php foreach ($person['roles'] as $role): ?>
                                <div class="small">
                                    <?php if (!empty($role['team'])): ?>
                                        <strong><?= $role['team']['name'] ?></strong> :
                                    <?php endif; ?>
                                    <?= $role['name'] ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info"><?= lang('members_lang.msg_error_no_data_to_display') ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
