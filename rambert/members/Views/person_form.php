<div class="person row bg-light border-bottom border-primary pt-2 pb-2 mb-4">
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
                <!-- Display the person's personal informations fields -->
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

                <!-- Display the person's contact informations fields -->
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

                <!-- Display the person's membership informations fields -->
                <div class="form-group row">
                    <p class="col-12"><strong><?= lang('members_lang.subtitle_membership_informations') ?></strong></p>

                    <label for="membership_start" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_membership_start') ?></label>
                    <div class="col-sm-8">
                        <input type="text" id="membership_start" name="membership_start" class="form-control form-control-sm" value="<?= $person['membership_start'] ?>" />
                    </div>
                    <label for="fk_category" class="col-sm-4 col-form-label-sm"><?= lang('members_lang.field_category') ?></label>
                    <div class="col-sm-8">
                        <select id="fk_category" name="fk_category" class="custom-select custom-select-sm">
                            <?php foreach ($categories as $category): ?>
                                <?php $selected = ''; ?>
                                <?php if ($person['fk_category'] == $category['id']): ?>
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
</div>