<div class="container" >
    <div id="home_details" class="row">
        <?php if (!empty($home)): ?>
            <div class="col-lg-5 col-md-7 mb-4">
                <!-- Display home action buttons for managers and admins -->
                <?php if ($_SESSION['access_level'] >= config('\Access\Config\AccessConfig')->access_lvl_manager): ?>
                    <div class="mb-2">
                        <a href="<?= base_url('home/update/'.$home['id']) ?>" class="btn btn-outline-primary"><i class="bi bi-pencil" style="font-size: 20px;"></i></a>
                        <a href="<?= base_url('home/delete/'.$home['id']) ?>" class="btn btn-outline-danger"><i class="bi bi-trash" style="font-size: 20px;"></i></a>
                    </div>
                <?php endif; ?>

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
                    <?= view('Members\person_update_button', ['person' => $person]); ?>
                    <?= view('Members\person_details', ['person' => $person]); ?>
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
