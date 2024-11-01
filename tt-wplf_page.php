<?php 
if ( preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF']) ) { die('You are not allowed to call this page directly.'); }

// pagina network admin
add_action('network_admin_menu', 'wplf_network_admin_page');
function wplf_network_admin_page() {
	$page = add_menu_page( 'TT WPLF Network Administration' , 'TT WPLF Network' , 'administrator' , 'tt-wplf-network-page' , 'wplf_network_page_template' , plugin_dir_url( __FILE__ ).'/css/TT_logo_page_menu.png' );
    add_action( 'admin_print_scripts-' . $page , 'wplf_admin_scripts' );
    add_action( 'admin_print_styles-' . $page , 'wplf_admin_style' );
}

function wplf_network_page_template () {
	if( isset( $_POST['tt-wplf-save'] ) ):
		foreach ( $_POST as $k => $v ):
			if( strpos( $k, 'tt-wplf-' ) === false ) {
				if( $k == 'no-logged-error' ) {
					update_site_option( 'tt-wplf-' . $k , htmlentities( $v ) );
				} else {
					update_site_option( 'tt-wplf-' . $k , $v );
				}
			}
		endforeach;
	endif;
	
	$wplfVersion = get_site_option('tt-wplf-version');
	$wplf_dfb = get_site_option('tt-wplf-show-default-follow-button');
	$wplf_dlb = get_site_option('tt-wplf-show-like-button');
	$wplf_lbp = get_site_option('tt-wplf-like-button-position');
	$wplf_fbw = get_site_option('tt-wplf-show-follow-widget');
	$wplf_nlt = get_site_option('tt-wplf-no-logged-error');
	$wplf_ltx = get_site_option('tt-wplf-like-text');
	$wplf_utx = get_site_option('tt-wplf-unlike-text');

	?>
	<div class="wrap">
		<div id="icon-wplf" class="icon32"></div>
		<h2>TT Like &amp; Follow WPMU Plugin <?php echo $wplfVersion ?> Network Administration </h2>
		<h3><?php _e('Appearance options' , 'wplf'); ?></h3>
		<form name="tt-wplf-network-options" method="post">
			<table class="form-table wplf">
				<tr>
					<th scope="col">
						<?php _e('Show default "Follow" button' , 'wplf'); ?>
					</th>
					<td>
						<label for="follow_button_yes"><?php _e('Yes' , 'wplf'); ?></label>
						<input name="show-default-follow-button" <?php echo ( $wplf_dfb == 1 ) ? 'checked="checked"' : ''; ?> id="follow_button_yes" class="toggleRadio" type="radio" value="1">
						&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="follow_button_no"><?php _e('No' , 'wplf'); ?></label>
						<input name="show-default-follow-button" <?php echo ( $wplf_dfb == 0 ) ? 'checked="checked"' : ''; ?> id="follow_button_no" class="toggleRadio" type="radio" value="0">
						<div class="follow_button<?php echo ( $wplf_dfb == 1 ) ? ' snippet' : ''; ?>">
							<?php _e('If you want to insert the "follow" button manually, paste this code:' , 'wplf'); ?><br>
							<code>
								$wplf = new WPLF();<br>
								echo $tt-wplf->followButton();
							</code>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="col">
						<?php _e('Show default "Like" button' , 'wplf'); ?>
					</th>
					<td>
						<label for="like_button_yes"><?php _e('Yes' , 'wplf'); ?></label>
						<input name="show-like-button" <?php echo ( $wplf_dlb == 1 ) ? 'checked="checked"' : ''; ?> id="like_button_yes" class="toggleRadio" type="radio" value="1">
						&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="like_button_no"><?php _e('No' , 'wplf'); ?></label>
						<input name="show-like-button" <?php echo ( $wplf_dlb == 0 ) ? 'checked="checked"' : ''; ?> id="like_button_no" class="toggleRadio" type="radio" value="0">
						<div class="like_button<?php echo ( $wplf_dlb == 1 ) ? ' snippet' : ''; ?>">
							<?php _e('If you want to insert the "like" button manually, paste this code inside the post loop:' , 'wplf'); ?><br>
							<code>
								$wplf = new WPLF();<br>
								echo $wplf->likeButton();
							</code>
						</div>
					</td>
				</tr>
				<tr>
					<th class="like_button_no<?php echo ( $wplf_dlb == 0 ) ? ' snippet' : ''; ?>" scope="col">
						<?php _e('Position of "Like" button' , 'wplf'); ?>
					</th>
					<td class="like_button_no<?php echo ( $wplf_dlb == 0 ) ? ' snippet' : ''; ?>">
						<select name="like-button-position">
							<option <?php echo ( $wplf_lbp == 'before_content' ) ? 'selected' : ''; ?> value="before_content"><?php _e('before the post' , 'wplf'); ?></option>
							<option <?php echo ( $wplf_lbp == 'after_content' ) ? 'selected' : ''; ?> value="after_content"><?php _e('after the post' , 'wplf'); ?></option>
							<option <?php echo ( $wplf_lbp == 'both_content' ) ? 'selected' : ''; ?> value="both_content"><?php _e('before and after the post' , 'wplf'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="col">
						<?php _e('"Follow" button\'s Widget' , 'wplf'); ?>
					</th>
					<td>
						<label for="widget_button_yes"><?php _e('Yes','wplf'); ?></label>
						<input name="show-follow-widget" <?php echo ( $wplf_fbw == 1 ) ? 'checked="checked"' : ''; ?> id="widget_button_yes" type="radio" value="1">
						&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="widget_button_no"><?php _e('No','wplf'); ?></label>
						<input name="show-follow-widget" <?php echo ( $wplf_fbw == 0 ) ? 'checked="checked"' : ''; ?> id="widget_button_no" type="radio" value="0">
					</td>
				</tr>
				<tr>
					<th scope="col">
						<?php _e('"Like" text','wplf'); ?>
					</th>
					<td>
						<input type="text" name="like-text" value="<?php echo $wplf_ltx; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="col">
						<?php _e('"Unlike" text','wplf'); ?>
					</th>
					<td>
						<input type="text" name="unlike-text" value="<?php echo $wplf_utx; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="col">
						<?php _e('"Not logged in" pop up text','wplf'); ?>
					</th>
					<td>
						<textarea name="no-logged-error"><?php echo $wplf_nlt; ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="col">
						<input class="button-primary" type="submit" name="tt-wplf-save" value="<?php _e('Save Options' , 'wplf'); ?>" id="submitbutton">
					</th>
				</tr>
			</table>
		</form>
		<p><?php _e( 'Tailored by' , 'wplf'); ?> <a target="_blank" href="http://themetailors.com">Theme Tailors</a></p>
	</div>
	<?php
}

add_action('admin_menu','wplf_add_options_page');
function wplf_add_options_page() {
	$page = add_menu_page( 'TT Like & Follow' , 'TT Like & Follow' , 'administrator' , 'tt-wplf-page' , 'wplf_page_template' , plugin_dir_url( __FILE__ ).'/css/TT_logo_page_menu.png' );
    add_action( 'admin_print_scripts-' . $page , 'wplf_admin_scripts' );
}

function wplf_admin_scripts() {
	wp_enqueue_script('wplfAdminScript');
}

function wplf_page_template() {
	$wplfVersion = get_site_option('tt-wplf-version');
	$fb = new WPLF();
	
	if( isset( $_POST['wplf-save'] ) ):
		foreach ( $_POST as $k => $v ):
			if( strpos( $k, 'tt-wplf-' ) === false ) { $fb->saveUserOptions( $k , $v ); }
		endforeach;
	endif;
	
	$mailFollowNotify = ( $fb->getUserOptions('email-follow-notification') !== 'not set' ) ? (bool) $fb->getUserOptions('email-follow-notification') : $fb->saveUserOptions( 'email-follow-notification' , true );
	$mailLikeNotify = ( $fb->getUserOptions('email-like-notification') !== 'not set' ) ? (bool) $fb->getUserOptions('email-like-notification') : $fb->saveUserOptions( 'email-like-notification' , true );
?>
	<div class="wrap">
		<div id="icon-wplf" class="icon32"></div>
		<h2>TT Like &amp; Follow WPMU Plugin <?php echo $wplfVersion ?></h2>
		<h3><?php _e( 'Main Options' , 'wplf' ); ?></h3>
		<form name="tt-wplf-options" method="post">
			<table class="form-table">
				<tr valign="top">
					<th scope="col">
						<h3><?php _e( 'Get notified via mail:' , 'wplf' ); ?></h3>
						
						<p><?php _e('When a user is following you' , 'wplf'); ?></p>
						<input type="radio" id="tt-wplf-mail-follow-yes" name="email-follow-notification" <?php echo ( $mailFollowNotify == 1 ) ? 'checked="checked" ' : '' ; ?>value="1" />
						<label for="tt-wplf-mail-follow-yes"><?php _e('Yes' , 'wplf'); ?></label>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" id="tt-wplf-mail-follow-no" name="email-follow-notification" <?php echo ( $mailFollowNotify == 0 ) ? 'checked="checked" ' : '' ;  ?>value="0" />
						<label for="tt-wplf-mail-follow-no"><?php _e('No' , 'wplf'); ?></label>
						
						<p><?php _e('When a user like your post' , 'wplf'); ?></p>
						<input type="radio" id="tt-wplf-mail-like-yes" name="email-like-notification" <?php echo ( $mailLikeNotify == 1 ) ? 'checked="checked" ' : '' ; ?>value="1" />
						<label for="tt-wplf-mail-like-yes"><?php _e('Yes' , 'wplf'); ?></label>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" id="tt-wplf-mail-like-no" name="email-like-notification" <?php echo ( $mailLikeNotify == 0 ) ? 'checked="checked" ' : '' ;  ?>value="0" />
						<label for="tt-wplf-mail-like-no"><?php _e('No' , 'wplf'); ?></label>
					</th>
				</tr>
				
				<tr valign="top">
					<th scope="row">
						<input class="button-primary" type="submit" name="tt-wplf-save" value="<?php _e('Save Options','wplf'); ?>" id="submitbutton">
					</th>
				</tr>
				
			</table>
		</form>
		<br><br>
		<?php 
		$f = $fb->getFollowers();
		$g = $fb->getFollowing();
		
		?> <h3><?php _e('Followers' , 'wplf'); ?></h3>
		<?php if(!$f): 
			_e('No Followers, sorry :(' , 'wplf');
		else: ?>
		<table class="wp-list-table widefat fixed">
			<thead>
				<tr valign="top">
					<th>User</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $f as $i ): ?>
				<?php 
					$u = get_user_by( 'id' , $i->ID ); 
					$blogs = get_blogs_of_user( $i->ID );
					$u = $u->data;
				?>
				<tr>
					<td>
						<span class="<?php echo 'userlink-' . $i->ID; ?>"><?php echo $u->display_name; ?></span>
						<br><a data-id="<?php echo $i->ID; ?>" title="<?php printf( __("Visit %s's site" , 'wplf') , $u->data->display_name ); ?>" href="<?php echo $blogs[$i->ID]->siteurl; ?>" target="_blank"><?php printf( __("Visit %s's site" , 'wplf') , $u->data->display_name ); ?></a> | <a class="json-call" data-fn="follow" data-fol="<?php echo $u->user_nicename; ?>" href="#">Follow</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr valign="top">
					<th>User</th>
				</tr>
			</tfoot>
		</table>
		<?php endif; ?>
			
		<h3><?php _e('Following') ?></h3>
		<?php if(!$g): 
			_e('You\'re not following anyone.' , 'wplf');
		else: ?>
		<table class="wp-list-table widefat fixed">
			<thead>
				<tr valign="top">
					<th>User</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $g as $e ): ?>
				<?php 
					$u = get_user_by( 'id' , $e->ID ); 
					$blogs = get_blogs_of_user( $e->ID );
					$u = $u->data;
				?>
				<tr>
					<td>
						<span class="<?php echo 'userlink-' . $e->ID; ?>"><?php echo $u->display_name; ?></span>
						<br><a data-id="<?php echo $e->ID; ?>" title="<?php printf( __("Visit %s's site" , 'wplf') , $u->data->display_name ); ?>" href="<?php echo $blogs[$e->ID]->siteurl; ?>" target="_blank"><?php printf( __("Visit %s's site" , 'wplf') , $u->data->display_name ); ?></a> | <a class="json-call" data-fn="unfollow" data-fol="<?php echo $u->user_nicename; ?>" href="#">Unfollow</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr valign="top">
					<th>User</th>
				</tr>
			</tfoot>
		</table>
		<?php endif; ?>
		<p><?php _e('Tailored by','wplf'); ?> <a target="_blank" href="http://themetailors.com">Theme Tailors</a></p>
	</div>
<?php
}
?>