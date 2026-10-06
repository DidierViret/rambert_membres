<div class="container" >
    <div id="home_details" class="row">
        <?php if (!empty($home)): ?>
            <div class="col-lg-5 col-md-7 mb-4">
                <!-- Display home action buttons for managers and admins -->
                <?php if ($_SESSION['access_level'] >= config('\Access\Config\AccessConfig')->access_lvl_manager): ?>
                    <div class="mb-2">
                        <a href="<?= base_url('home/update/'.$home['id']) ?>" class="btn btn-outline-primary" title="<?= lang('members_lang.btn_update') ?>"><i class="bi bi-pencil" style="font-size: 20px;"></i></a>
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
                <!-- Display add person button and member status filter for managers and admins -->
                <?php if ($_SESSION['access_level'] >= config('\Access\Config\AccessConfig')->access_lvl_manager): ?>
                    <div class="row mb-2">
                        <div class="col">
                            <a href="<?= base_url('person/create/'.$home['id']) ?>" class="btn btn-outline-primary"><i class="bi bi-plus-lg"></i> <?= lang('members_lang.btn_add_person') ?></a>
                        </div>
                        <div class="col-auto d-flex align-items-center">
                            <div class="form-check form-check-inline">
                                <input type="radio" id="active" name="member_status" value="active" class="form-check-input" <?= ($member_status == 'active') ? 'checked' : '' ?>><label for="active" class="form-check-label"><?= lang('members_lang.field_active') ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" id="all" name="member_status" value="all" class="form-check-input" <?= ($member_status == 'all') ? 'checked' : '' ?>><label for="all" class="form-check-label"><?= lang('members_lang.field_all') ?></label>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Display the list of persons living in the home -->
                <div id="list_persons">
                    <?php foreach ($persons as $person): ?>
                        <?= view('Members\person_update_button', ['person' => $person]); ?>
                        <?= view('Members\person_details', ['person' => $person]); ?>
                    <?php endforeach; ?>
                </div>
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

<!-- Javascript to filter persons list after member status change -->
<script>
    $(document).ready(function() {
        var list_request = null;

        function updateList() {
            var member_status = $('input[name="member_status"]:checked').val() || 'active';
            var get_url = window.location.pathname + '?' + $.param({ms: member_status});

            // abort the previous request so that an older response can't overwrite a newer one
            if (list_request) {
                list_request.abort();
            }

            // call homeDetails controller method to update data content
            list_request = $.get(get_url, data => {
                $('#list_persons').empty();

                // replace the content of the list_persons div with the filtered data
                $('#list_persons').html($(data).find('#list_persons').html());
            });
        }

        $('input[name="member_status"]').on('change', updateList);
    });
</script>
