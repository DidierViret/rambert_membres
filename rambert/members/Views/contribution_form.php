<?php helper('form'); ?>

<div class="container" >
    <div class="contribution row bg-light border-bottom border-primary pt-2 pb-2 mb-4">
        <!-- Display the person's name -->
        <div class="col-12 mb-2">
            <h3><?= $title ?></h3>
        </div>

        <?= form_open('contribution/save/'.$contribution['id'], ['class' => 'col-12', 'id' => 'contribution_form']) ?>
            <!-- Display the contribution fields -->
            <div class="row mb-2">
                <div class="col-sm-6">
                    <label for="team"><?= lang('members_lang.field_team') ?></label>
                    <select name="team" id="team" class="form-control" required >
                        <?php foreach ($teams as $team): ?>
                            <option value="<?= $team['id'] ?>" <?= ($contribution['role']['team']['id'] == $team['id']) ? 'selected' : '' ?>><?= $team['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                </div>
                <div class="col-sm-6">
                    <label for="role"><?= lang('members_lang.field_role') ?></label>
                    <select name="role" id="role" class="form-control" required >
                        <!-- Field will be filled with the roles of the selected team by javascript -->
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3">
                    <label for="contribution_start"><?= lang('members_lang.field_contribution_start') ?></label>
                    <input type="number" name="contribution_start" id="contribution_start" class="form-control" value="<?= $contribution['date_begin'] ?>" required />
                </div>
                <div class="col-sm-3">
                    <label for="contribution_end"><?= lang('members_lang.field_contribution_end') ?></label>
                    <input type="number" name="contribution_end" id="contribution_end" class="form-control" value="<?= $contribution['date_end'] ?>" />
                </div>
            </div>
            
            <!-- Display action buttons -->
            <div class="row mb-2">
                <div class="col-12">
                    <a href="<?= base_url('contributions/'.$contribution['person']['id']) ?>" class="btn btn-outline-secondary"><?= lang('members_lang.btn_cancel') ?></a>
                    <input type="submit" class="btn btn-outline-success" value="<?= lang('members_lang.btn_save') ?>" />
                </div>
            </div>
        <?= form_close() ?>
    </div>
</div>

<script>
    // Convert PHP array to JavaScript object
    const roles = <?= json_encode($roles) ?>;
    const selectedRoleId = <?= json_encode($contribution['role']['id']) ?>;

    // Function to update the roles dropdown based on the selected team
    function updateRoles() {
        const teamId = document.getElementById('team').value;
        const roleSelect = document.getElementById('role');
        
        // Clear existing options
        roleSelect.innerHTML = '';

        // Filter roles for the selected team
        const teamRoles = roles.filter(role => role.fk_team == teamId);

        // Populate dropdown with filtered roles
        teamRoles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.id;
            option.textContent = role.name;
            option.selected = (role.id === selectedRoleId);
            roleSelect.appendChild(option);
        });
    }

    // Add event listener to the team select element
    document.getElementById('team').addEventListener('change', updateRoles);
    // Initialize roles on page load
    document.addEventListener('DOMContentLoaded', updateRoles);
</script>