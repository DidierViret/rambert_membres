<div class="container" >
    <div id="home_details" class="row">
        <?php if (!empty($home)): ?>
            <div class="col-lg-5 col-md-7 mb-4">
                <!-- Display the home address -->
                <div><strong><a href=""><?= lang('members_lang.col_home_address') ?></a></strong></div>
                <div><?= $home['address_title'] ?></div>
                <div><?= $home['address_name'] ?></div>
                <div><?= $home['address_line_1'] ?></div>
                <div><?= $home['address_line_2'] ?></div>
                <div><?= $home['postal_code'].' '.$home['city'] ?></div>

                <!-- Display shipments informations -->
                <div class="mt-2"><strong><?= lang('members_lang.col_shipments') ?></strong></div>
                <div>
                    <?php if (!empty($home['nb_bulletins'])): ?>
                        <?= lang('members_lang.field_nb_bulletins').' : '.$home['nb_bulletins'] ?>
                    <?php else: ?>
                        <?= lang('members_lang.field_nb_bulletins').' : 0' ?>
                    <?php endif; ?>
                </div>

                <!-- If there are comments about the home, display them -->
                <?php if (!empty($home['comments'])): ?>
                    <div class="alert alert-info mt-2"><?= $home['comments'] ?></div>
                <?php endif; ?>
            </div>

            <div class="col-lg-7 col-md-5">
                <!-- Display the list of persons living in the home -->
                <?php foreach ($persons as $person): ?>
                    <div class="row mb-4">
                        <div class="col-lg-6 mb-2">
                            <!-- Display the person's name with a link to modify this person -->
                            <strong><a href=""><?= $person['last_name'].' '.$person['first_name'] ?></a></strong>
                            
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
                            <div class="small"><?= lang('members_lang.field_birth_date').' : '.$person['birth'] ?></div>
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
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php else: ?>
            <!-- If there is no data to display, show an alert -->
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info"><?= lang('members_lang.msg_error_no_data_to_display') ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
