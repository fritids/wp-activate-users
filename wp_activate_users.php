<?php
/*
Plugin Name: WP Activate Users
Plugin URI: http://countingrows.com
Description: Shows users who have not activated their account
Version: 1.0
Author: Matthew Price
Author URI: http://countingrows.com
License: GPL2
*/

if($_GET['action']); {

	switch ($_GET['action']) {
		
	case 'activate-user': activate_user();
	
	break;

	}
}	

function add_to_head() {
?>
<!-- SHOW-HIDE MULTIPLE SCRIPT --> 
<script type="text/javascript">
function expandCollapse() {
for (var i=0; i<expandCollapse.arguments.length; i++) {
var element = document.getElementById(expandCollapse.arguments[i]);
element.style.display = (element.style.display == "none") ? "block" : "none";
}
}
</script>
<script type="text/javascript">
function readOnlyCheckBox() {
   return false;
}
</script>
}
<?
}

function wp_activate_users() {
global $wpdb;
$users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users WHERE user_status = '2' ORDER BY user_registered DESC");
?>
<div class="wrap">
<h2>Users Who Have Not Activated Their Account</h2>
Total: <? echo $wpdb->num_rows; ?><br><br>	

<? if ($_GET['userid'] != '') { 
$uid = $_GET['userid'];
$user_activated = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users WHERE ID = '$uid'");
?>
<div id="message" class="updated">User: <? echo $user_activated[0]->display_name; ?> has been activated.</div>
<? } ?>
<table class="widefat" cellspacing="0">
	<thead>
		<tr class="thead">
			<th scope="col" id="user-registered" class="manage-column">Registration Date</th>
			<th scope="col" id="user-login" class="manage-column">Login</th>
			<th scope="col" id="user-email" class="manage-column">Email</th>
			<th scope="col" id="display-name" class="manage-column">Display Name</th>
			<th scope="col" id="activate" class="manage-column">Activate</th>
		</tr>
	</thead>
<?
$stripe = 1;				
	foreach ($users as $user) {
	echo $stripe % 2 == 0 ? '<tr>' : '<tr class="alternate">';
	echo "<td>" . $user->user_registered . "</td><td>" . $user->user_login . "</td><td><a href=\"mailto:" . $user->user_email . "\">" . $user->user_email . "</a></td><td>" . $user->display_name . "</td>";
	?>
	<td><img src="<? bloginfo('template_directory'); ?>/images/activate.png" style="cursor: pointer;" onclick="javascript: expandCollapse('activator<? echo $user->ID; ?>');"><br>
	<div id="activator<? echo $user->ID; ?>" style="display: none;">
		<form action="?action=activate-user" method="post">
			<input type="radio" value="0" name="activate">Activate <input type="radio" value="2" name="activate"> Cancel<br>
			<input type="hidden" name="id" value="<? echo $user->ID; ?>">
			<input type="submit" value="Confirm">
		</form>
	</div>
	<?
	echo "</tr>";
	$stripe++;
	}
echo '</tbody></table>';
echo "</div>";
}

function activate_user() {
global $wpdb;
$uid = $_POST['id'];
$activate = $_POST['activate'];

$wpdb->query("UPDATE " . $wpdb->prefix . "users SET user_status = '$activate' WHERE ID = '$uid'");
header("Location: " . $_SERVER['PHP_SELF'] . "?page=wp-activate-users&userid=" . $uid);

}

function add_to_users_menu() {
add_users_page(__('WP Activate Users','wp activate users'), __('WP Activate Users','wp activate users'), 'manage_options', 'wp-activate-users', 'wp_activate_users', '', '4');
}

add_action( 'admin_menu', 'add_to_users_menu' );
add_action( 'admin_head', 'add_to_head' );
?>