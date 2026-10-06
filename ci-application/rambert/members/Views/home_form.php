<?php helper('form'); ?>
<?php $homeId = $home['id'] ?? 0; ?>

<div class="container" >
    <?php if (empty($home)): ?>
        <div id="message" class="row" >
            <div class="col-12">
                <div class="alert alert-warning"><?= lang('members_lang.msg_warning_new_home') ?></div>
            </div>
        </div>
    <?php endif; ?>

    <div id="home_form" class="row">
        <div class="col-lg-5 col-md-7 mb-4">
            <?= form_open('home/save/'.$homeId) ?>
                <!-- Display action buttons -->
                <?php if (empty($home)): ?>
                    <!-- New home, cancel goes to home list -->
                    <a href="<?= base_url() ?>" class="btn btn-outline-secondary"><?= lang('members_lang.btn_cancel') ?></a>
                <?php else: ?>
                    <!-- Existing home, cancel goes to home details -->
                    <a href="<?= base_url('home/'.$homeId) ?>" class="btn btn-outline-secondary"><?= lang('members_lang.btn_cancel') ?></a>
                <?php endif; ?>
                <input type="submit" class="btn btn-outline-primary" value="<?= lang('members_lang.btn_save') ?>" />

                <!-- Display the home address fields -->
                <div><strong><?= lang('members_lang.col_home_address') ?></strong></div>
                <div class="form-group row">
                    <label for="address_title" class="col-sm-4 col-form-label"><?= lang('members_lang.field_address_title') ?></label>
                    <div class="col-sm-8">
                        <input type="text" name="address_title" class="form-control" value="<?= $home['address_title'] ?? '' ?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_name" class="col-sm-4 col-form-label"><?= lang('members_lang.field_address_name') ?></label>
                    <div class="col-sm-8">
                        <input type="text" name="address_name" class="form-control" value="<?= $home['address_name'] ?? '' ?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_line_1" class="col-sm-4 col-form-label"><?= lang('members_lang.field_address_line_1') ?></label>
                    <div class="col-sm-8">
                        <input type="text" name="address_line_1" class="form-control" value="<?= $home['address_line_1'] ?? '' ?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_line_2" class="col-sm-4 col-form-label"><?= lang('members_lang.field_address_line_2') ?></label>
                    <div class="col-sm-8">
                        <input type="text" name="address_line_2" class="form-control" value="<?= $home['address_line_2'] ?? '' ?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="postal_code" class="col-sm-4 col-form-label"><?= lang('members_lang.field_postal_code') ?></label>
                    <div class="col-sm-8">
                        <input type="text" name="postal_code" class="form-control" value="<?= $home['postal_code'] ?? '' ?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="city" class="col-sm-4 col-form-label"><?= lang('members_lang.field_city') ?></label>
                    <div class="col-sm-8">
                        <input type="text" name="city" class="form-control" value="<?= $home['city'] ?? '' ?>" />
                    </div>
                </div>

                <!-- Display the home shipments fields -->
                <div class="mt-2"><strong><?= lang('members_lang.col_shipments') ?></strong></div>
                <div class="form-group row">
                    <label for="nb_bulletins" class="col-sm-4 col-form-label"><?= lang('members_lang.field_nb_bulletins') ?></label>
                    <div class="col-sm-8">
                        <input type="text" name="nb_bulletins" class="form-control" value="<?= $home['nb_bulletins'] ?? '' ?>" />
                    </div>
                </div>

                <!-- Display the home comments field -->
                <div class="mt-2"><strong><?= lang('members_lang.field_comments') ?></strong></div>
                <div class="form-group">
                    <textarea name="comments" class="form-control" rows="5"><?= $home['comments'] ?? '' ?></textarea>
                </div>
            <?= form_close() ?>
        </div>

        <div class="col-lg-7 col-md-5">
            <!-- Display the list of persons living in the home -->
            <?php foreach ($persons as $person): ?>
                <?= view('Members\person_details', ['person' => $person]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
