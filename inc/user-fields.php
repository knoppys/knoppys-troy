<?php
function custom_user_profile_fields($user){
  ?>
    <h3>Extra profile information</h3>
    <table class="form-table">
        <tr>
            <th><label for="company">Job Alerts</label></th>            
            <td>
                <select name="jobalerts">
                    <option><?php echo get_user_meta($user->id, 'jobalerts', true); ?></option>
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                </select>              
            </td>
        </tr>
    </table>
  <?php
}
add_action( 'show_user_profile', 'custom_user_profile_fields' );
add_action( 'edit_user_profile', 'custom_user_profile_fields' );
add_action( "user_new_form", "custom_user_profile_fields" );

function save_custom_user_profile_fields($user_id){
    # again do this only if you can
    if(!current_user_can('manage_options'))
        return false;

    # save my custom field
    update_usermeta($user_id, 'jobalerts', $_POST['jobalerts']);
}
add_action('user_register', 'save_custom_user_profile_fields');
add_action('profile_update', 'save_custom_user_profile_fields');