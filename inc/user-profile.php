<?php
/**
 * User Profile Customizations
 *
 * Adds custom fields to user profiles including job title.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add social media links as WordPress contact methods.
 *
 * @param array $contactmethods Existing contact methods.
 * @return array Modified contact methods.
 */
function main_add_user_contactmethods( $contactmethods ) {
	$contactmethods['linkedin'] = __( 'LinkedIn URL', 'main' );
	$contactmethods['twitter'] = __( 'X username (without @)', 'main' );
	$contactmethods['facebook'] = __( 'Facebook URL', 'main' );
	return $contactmethods;
}
add_filter( 'user_contactmethods', 'main_add_user_contactmethods' );

/**
 * Add custom fields to user profile.
 *
 * @param WP_User $user User object.
 */
function main_add_user_profile_fields( $user ) {
	$job_title = get_user_meta( $user->ID, 'job_title', true );
	?>
	<h3><?php esc_html_e( 'Professional Information', 'main' ); ?></h3>
	<table class="form-table">
		<tr>
			<th>
				<label for="job_title"><?php esc_html_e( 'Job Title / Custom Role', 'main' ); ?></label>
			</th>
			<td>
				<input
					type="text"
					name="job_title"
					id="job_title"
					value="<?php echo esc_attr( $job_title ); ?>"
					class="regular-text"
					placeholder="<?php esc_attr_e( 'e.g., Senior Technology Writer', 'main' ); ?>"
				/>
				<p class="description">
					<?php esc_html_e( 'Enter a custom job title or role. If left empty, the default WordPress user role will be displayed on posts.', 'main' ); ?>
				</p>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'main_add_user_profile_fields' );
add_action( 'edit_user_profile', 'main_add_user_profile_fields' );

/**
 * Save custom user profile fields.
 *
 * @param int $user_id User ID.
 */
function main_save_user_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	if ( isset( $_POST['job_title'] ) ) {
		update_user_meta(
			$user_id,
			'job_title',
			sanitize_text_field( wp_unslash( $_POST['job_title'] ) )
		);
	}
}
add_action( 'personal_options_update', 'main_save_user_profile_fields' );
add_action( 'edit_user_profile_update', 'main_save_user_profile_fields' );

/**
 * Get author job title with fallback to user role.
 *
 * @param int|null $user_id User ID. If null, uses current post author.
 * @return string Job title or formatted user role.
 */
function main_get_author_job_title( $user_id = null ) {
	if ( ! $user_id ) {
		$user_id = get_the_author_meta( 'ID' );
	}

	// Get custom job title
	$job_title = get_user_meta( $user_id, 'job_title', true );

	// If custom job title exists, return it
	if ( ! empty( $job_title ) ) {
		return $job_title;
	}

	// Fallback to user role
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return '';
	}

	$roles = $user->roles;
	if ( empty( $roles ) ) {
		return '';
	}

	// Get the first role
	$role = $roles[0];

	// Get role display name
	$wp_roles = wp_roles();
	$role_names = $wp_roles->get_names();
	$role_display_name = isset( $role_names[ $role ] ) ? $role_names[ $role ] : ucfirst( $role );

	// Capitalize and format the role name
	return $role_display_name;
}

