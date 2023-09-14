<?php

/**
 * When administrators edit a user, we want to show all groups that they manage,
 * including showing the group ID, the level ID for the group, the number of seats in the group,
 * and a link to manage the group if the "Manage Group" page is set.
 *
 * We also want to show a table of all groups that the user is a member of, including
 * links to the group owner, the level that they claimed with the group, and the group member status.
 *
 * @since TBD
 *
 * @param WP_User $user The user object being edited.
 */
function pmprogroupacct_after_membership_level_profile_fields( $user ) {
    // Get all groups that the user manages.
	$group_query_args = array(
		'group_parent_user_id' => (int)$user->ID,
	);
	$groups = PMProGroupAcct_Group::get_groups( $group_query_args );

	// Get all groups that the user is a member of.
	$group_member_query_args = array(
		'group_child_user_id' => (int)$user->ID,
	);
	$group_members = PMProGroupAcct_Group_Member::get_group_members( $group_member_query_args );

	// Show the UI.
	?>
	<hr>
	<h2><?php esc_html_e( 'PMPro Group Accounts Add On', 'pmpro-group-accounts' ); ?></h2>
	<h3><?php esc_html_e( 'Manage Groups', 'pmpro-group-accounts' ); ?></h3>
	<?php
	if ( empty( $groups ) ) {
		echo '<p>' . esc_html__( 'This user does not manage any groups.', 'pmpro-group-accounts' ) . '</p>';
	} else {
		// Show the groups that the user manages.
		?>
		<table>
			<thead>
				<tr>
					<th><?php esc_html_e( 'Group ID', 'pmpro-group-accounts' ); ?></th>
					<th><?php esc_html_e( 'Level ID', 'pmpro-group-accounts' ); ?></th>
					<th><?php esc_html_e( 'Seats', 'pmpro-group-accounts' ); ?></th>
					<th><?php esc_html_e( 'Manage Group', 'pmpro-group-accounts' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $groups as $group ) {
					?>
					<tr>
						<td><?php echo esc_html( $group->id ); ?></td>
						<td><?php echo esc_html( $group->group_parent_level_id ); ?></td>
						<td><?php echo esc_html( $group->get_active_members( true ) ) . '/' . esc_html( $group->group_total_seats ); ?></td>
						<td>
							<?php
							$manage_group_url = pmpro_url( 'pmprogroupacct_manage_group' );
							if ( ! empty( $manage_group_url ) ) {
								?>
								<a href="<?php echo esc_url( add_query_arg( 'pmprogroupacct_group_id', $group->id, $manage_group_url ) ); ?>"><?php esc_html_e( 'Manage Group', 'pmpro-group-accounts' ); ?></a>
								<?php
							} else {
								esc_html_e( 'Page not set.', 'pmpro-group-accounts' );
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table> 
		<?php
	}
	?>
	<h3><?php esc_html_e( 'Manage Child Memberships', 'pmpro-group-accounts' ); ?></h3>
	<?php
	if ( empty( $group_members ) ) {
		echo '<p>' . esc_html__( 'This user has not been a member of any groups.', 'pmpro-group-accounts' ) . '</p>';
	} else {
		// Show the groups that the user is a member of.
		?>
		<table>
			<thead>
				<tr>
					<th><?php esc_html_e( 'Group ID', 'pmpro-group-accounts' ); ?></th>
					<th><?php esc_html_e( 'Group Owner', 'pmpro-group-accounts' ); ?></th>
					<th><?php esc_html_e( 'Level ID', 'pmpro-group-accounts' ); ?></th>
					<th><?php esc_html_e( 'Status', 'pmpro-group-accounts' ); ?></th>
					<th><?php esc_html_e( 'Manage Group', 'pmpro-group-accounts' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $group_members as $group_member ) {
					$group            = new PMProGroupAcct_Group( (int)$group_member->group_id );
					$parent_user      = get_userdata( $group->group_parent_user_id );
					$parent_user_link = empty( $parent_user ) ? esc_html( $group->group_parent_user_id ) : '<a href="' . esc_url( add_query_arg( 'user_id', $parent_user->ID, admin_url( 'user-edit.php' ) ) ) . '">' . esc_html( $parent_user->user_login ) . '</a>';
					?>
					<tr>
						<td><?php echo esc_html( $group->id ); ?></td>
						<td><?php echo $parent_user_link ?></td>
						<td><?php echo esc_html( $group_member->group_child_level_id ); ?></td>
						<td><?php echo esc_html( $group_member->group_child_status ); ?></td>
						<td>
							<?php
							$manage_group_url = pmpro_url( 'pmprogroupacct_manage_group' );
							if ( ! empty( $manage_group_url ) ) {
								?>
								<a href="<?php echo esc_url( add_query_arg( 'pmprogroupacct_group_id', $group->id, $manage_group_url ) ); ?>"><?php esc_html_e( 'Manage Group', 'pmpro-group-accounts' ); ?></a>
								<?php
							} else {
								esc_html_e( 'Page not set.', 'pmpro-group-accounts' );
							}
							?>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table> 
		<?php
	}
}
add_action( 'pmpro_after_membership_level_profile_fields', 'pmprogroupacct_after_membership_level_profile_fields' );