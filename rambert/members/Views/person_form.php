<?php helper('form'); ?>

<div class="container" >
    <div id="home_details" class="row">
        <?php if (!empty($home)): ?>
            <div class="col-lg-5 col-md-7 mb-4">

                <!-- Display the home address -->
                <div><strong><?= lang('members_lang.col_home_address') ?></strong></div>
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
                    <div class="person row bg-light border-bottom border-primary pt-2 pb-2 mb-4">
                        <!-- if this is the person to update, display a form to update his informations -->
                        <?php if ($person['id'] == $person_to_update): ?>
                            <!-- Display the person's name -->
                            <div class="col-12 mb-2">
                                <span class="text-primary"><strong><?= $person['last_name'].' '.$person['first_name'] ?></strong></span>
                            </div>

                            <?= form_open('person/save/'.$person['id'], ['class' => 'col-12', 'id' => 'person_form']) ?>
                                <!-- Display action buttons -->
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <a href="<?= base_url('home/'.$home['id']) ?>" class="btn btn-outline-secondary"><?= lang('members_lang.btn_cancel') ?></a>
                                        <input type="submit" class="btn btn-outline-success" value="<?= lang('members_lang.btn_save') ?>" />
                                    </div>
                                </div>

                                <!-- If logged user has admin rights, give the possibility to update the person's access rights -->
                                <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && $_SESSION['access_level'] >= config('\Access\Config\AccessConfig')->access_lvl_admin): ?>
                                    <div class="row mb-2">
                                        <label for="access_level" class="col-sm-6 col-form-label-sm" ><strong><?= lang('members_lang.col_access_levels') ?> : </strong></label>
                                        <div class="col-sm-6">
                                            <select id="access_level" name="access_level" class="custom-select custom-select-sm">
                                                <?php if (empty($person['access_levels'])): ?>
                                                    <option value="0" selected ><?= lang('members_lang.no_access_level') ?></option>
                                                <?php else: ?>
                                                    <option value="0"><?= lang('members_lang.no_access_level') ?></option>
                                                <?php endif; ?>

                                                <?php foreach ($access_levels as $access_level): ?>
                                                    <?php $selected = ''; ?>
                                                    <?php if (!empty($person['access_levels'])): ?>
                                                        <?php foreach ($person['access_levels'] as $person_access_level): ?>
                                                            <?php if ($person_access_level['id'] == $access_level['id']): ?>
                                                                <?php $selected = 'selected'; ?>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                    <option value="<?= $access_level['id'] ?>" <?= $selected ?>><?= $access_level['name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Display fields to update the person's informations -->
                                <div class="row mb-2">
                                    <div class="col-12 mb-2">
                                        <div class="form-group row">
                                            <p class="col-12"><strong><?= lang('members_lang.subtitle_person_informations') ?></strong></p>

                                            <label for="title" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_title') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" id="title" name="title" class="form-control form-control-sm" value="<?= $person['title'] ?>" />
                                            </div>
                                            <label for="last_name" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_last_name') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" id="last_name" name="last_name" class="form-control form-control-sm" value="<?= $person['last_name'] ?>" />
                                            </div>
                                            <label for="first_name" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_first_name') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" id="first_name" name="first_name" class="form-control form-control-sm" value="<?= $person['first_name'] ?>" />
                                            </div>
                                            <label for="birth" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_birth') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" id="birth" name="birth" class="form-control form-control-sm" value="<?= $person['birth'] ?>" />
                                            </div>
                                            <label for="profession" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_profession') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" id="profession" name="profession" class="form-control form-control-sm" value="<?= $person['profession'] ?>" />
                                            </div>
                                            <label for="comments" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_comments') ?></label>
                                            <div class="col-sm-8">
                                                <textarea id="comments" name="comments" class="form-control form-control-sm" rows="3"><?= $person['comments'] ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <p class="col-12"><strong><?= lang('members_lang.subtitle_contact_informations') ?></strong></p>

                                            <label for="email" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_email') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" id="email" name="email" class="form-control form-control-sm" value="<?= $person['email'] ?>" />
                                            </div>
                                            <label for="phone_1" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_phone_1') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" id="phone_1" name="phone_1" class="form-control form-control-sm" value="<?= $person['phone_1'] ?>" />
                                            </div>
                                            <label for="phone_2" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_phone_2') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" id="phone_2" name="phone_2" class="form-control form-control-sm" value="<?= $person['phone_2'] ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <p class="col-12"><strong><?= lang('members_lang.subtitle_membership_informations') ?></strong></p>

                                            <label for="membership_start" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_membership_start') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" id="membership_start" name="membership_start" class="form-control form-control-sm" value="<?= $person['membership_start'] ?>" />
                                            </div>
                                            <label for="category" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_category') ?></label>
                                            <div class="col-sm-8">
                                                <select id="category" name="category" class="custom-select custom-select-sm">
                                                    <?php foreach ($categories as $category): ?>
                                                        <?php $selected = ''; ?>
                                                        <?php if ($person['category']['id'] == $category['id']): ?>
                                                            <?php $selected = 'selected'; ?>
                                                        <?php endif; ?>
                                                        <option value="<?= $category['id'] ?>" <?= $selected ?>><?= $category['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <label for="godfathers" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_godfathers') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" id="godfathers" name="godfathers" class="form-control form-control-sm" value="<?= $person['godfathers'] ?>" />
                                            </div>
                                            <label for="membership_end" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_membership_end') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" id="membership_end" name="membership_end" class="form-control form-control-sm" value="<?= $person['membership_end'] ?>" />
                                            </div>
                                            <label for="membership_end_reason" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_membership_end_reason') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" id="membership_end_reason" name="membership_end_reason" class="form-control form-control-sm" value="<?= $person['membership_end_reason'] ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            <?= form_close() ?>

                        <!-- if this is NOT the person to update, just display his informations -->
                        <?php else: ?>
                            
                            <!-- If the personn has access rights, display a badge for each access_level -->
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
                                <?php if (!empty($person['newsletter_subscriptions'])): ?>
                                <ul>
                                    <?php foreach ($person['newsletter_subscriptions'] as $subscription): ?>
                                        <li>
                                            <?= $subscription['newsletter']['title'] ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php else: ?>
                                    <div class="small"><?= lang('members_lang.no_subscription') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Display the person's contributions to the club -->
                            <?php if (!empty($person['contributions'])): ?> 
                                <div class="col-lg-6 mb-2">
                                    <div><strong><?= lang('members_lang.col_contributions') ?></strong></div>
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
                        <?php endif; ?>
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
