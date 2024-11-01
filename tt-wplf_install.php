<?php 
if ( preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF']) ) { die('You are not allowed to call this page directly.'); }
register_activation_hook( __FILE__, 'wplf_install' );
register_activation_hook( __FILE__, 'wplf_install_default_data' );

function wplf_install() {
	global $wplf_id , $wpdb ;
	$wpdb->show_errors(true);
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$followTable = $wpdb->base_prefix . 'followUser';
	$likeTable = $wpdb->base_prefix . 'postsLikes';
	
	$sql = "CREATE TABLE $followTable  (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  user_id int NOT NULL,
	  following_id int NOT NULL,
	  date datetime NOT NULL,
	  UNIQUE KEY id (id)
	);";
	
	dbDelta($sql);
	
	$likeSql = "CREATE TABLE $likeTable  (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  user_id int NOT NULL,
	  blog_id int NOT NULL,
	  post_id int NOT NULL,
	  tbl text NOT NULL,
	  date datetime NOT NULL,
	  UNIQUE KEY id (id)
	);";
	
	dbDelta($likeSql);
	
	$noLogString = htmlentities( '<h1>' . __('Uh Oh' , 'wplf') . '</h1>' . '<p>' . __('You need to be logged in order to follow or like the posts of other users.' , 'wplf') . '</p>' );
	
	update_site_option( 'tt-wplf-version' , $wplf_id );
	update_site_option( 'tt-wplf-show-default-follow-button' , '1' );
	update_site_option( 'tt-wplf-show-like-button' , '1' );
	update_site_option( 'tt-wplf-like-button-position' , 'after_content' );
	update_site_option( 'tt-wplf-show-follow-widget' , '1' );
	update_site_option( 'tt-wplf-no-logged-error' , $noLogString );
	update_site_option( 'tt-wplf-like-text' , __('Like' , 'wplf') );
	update_site_option( 'tt-wplf-unlike-text' , __('Unlike' , 'wplf') );
}

add_action( 'wp_loaded', 'wplf_update_check' );
function wplf_update_check() {
	global $wplf_id;
	if( get_site_option( 'tt-wplf-version' ) != $wplf_id ) wplf_install();
}

add_action( 'plugins_loaded' , 'wplf_setup' );
function wplf_setup() {
    load_plugin_textdomain( 'wplf' , false , dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action('wp_enqueue_scripts','wplf_scripts');
function wplf_scripts() {
	global $wplf_id;
	wp_register_style( 'wplfStyleFront' , plugins_url('css/tt-wplf.css', __FILE__) , array() , $wplf_id );
	wp_register_style( 'wplfColorboxStyle' , plugins_url('css/colorbox.css', __FILE__) , array() , '1.5.4' , 'screen' );
	
	wp_register_script( 'wplfScriptFront' , plugins_url('js/tt-wplf.js', __FILE__) , array( 'jquery' ) , $wplf_id );
	wp_register_script( 'wplfColorbox' , plugins_url('js/jquery.colorbox-min.js', __FILE__) , array( 'jquery' ) , '1.5.4' );
	
	if ( !is_admin() ) {
		wp_enqueue_script( 'wplfColorbox' );
		wp_enqueue_script( 'wplfScriptFront' );
		wp_localize_script( 'wplfScriptFront', 'ajaxObj', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_enqueue_style( 'wplfStyleFront' );
		wp_enqueue_style( 'wplfColorboxStyle' );
	}
}

add_action('admin_init','wplf_admin_init');
function wplf_admin_init() {
	wp_register_script( 'wplfAdminScript' , plugins_url( 'js/tt-wplf_back.js' , __FILE__ ) , array( 'jquery' ) );
	wp_register_style( 'wplfAdminStyle' , plugins_url( 'css/tt-wplf_back.css' , __FILE__ ) );
}

add_action('admin_menu','wplf_admin_style');
function wplf_admin_style() {
	wp_register_style( 'wplfAdminStyleBack' , plugins_url( 'css/tt-wplf_back.css' , __FILE__ ) );
	wp_enqueue_style('wplfAdminStyleBack');
}

add_action('get_footer','wplf_follow_button');
function wplf_follow_button () {
	if ( !is_admin() && get_site_option( 'tt-wplf-show-default-follow-button' ) == 1 ) {
		$fb = new WPLF();
		echo $fb->followButton('outerButton');
	}
}

add_filter('the_content','wplf_like_button');
function wplf_like_button( $content ){
	if( !is_admin() && get_site_option( 'tt-wplf-show-like-button' ) == '1' ) {
		$fb = new WPLF();
		switch( get_site_option( 'tt-wplf-like-button-position' ) ):
			case 'before_content': 
				$output = $fb->likeButton() . $content;
				break;
			case 'after_content':
				$output = $content . $fb->likeButton();
				break;
			case 'both_content':
				$output = $fb->likeButton() . $content . $fb->likeButton();
				break;
		endswitch; 
		
		return $output;
	} else {
		return $content;
	}
}

//chiamate ajax per il following/unfollowing
add_action('wp_ajax_nopriv_wplf_json', 'wplf_callback');
add_action('wp_ajax_wplf_json', 'wplf_callback');

function wplf_callback(){
	$fb = new WPLF();
	
	if( is_user_logged_in() ):
		switch( $_REQUEST['fn'] ):
			case 'follow':
				$output = $fb->insertFollower( $_REQUEST['following'] );
				break;
			case 'unfollow':
				$output = $fb->deleteFollower( $_REQUEST['following'] );
				break;
			case 'like':
				$output = $fb->insertLike( $_REQUEST['following'] );
				break;
			case 'unlike':
				$output = $fb->deleteLike( $_REQUEST['following'] );
				break;
			default:
				$output = array( 'text' => __('Error: function called is not available' , 'wplf') , 'check' => false , 'fn' => 'error' );
		endswitch;
	else:
		$output = array(
			'text' => html_entity_decode( get_site_option( 'tt-wplf-no-logged-error' ) ),
			'check' => false,
			'fn' => 'error'
		);
	endif;
	
	$output = json_encode( $output );
	if( is_array( $output ) ) { print_r( $output ); }
		else{ echo $output; }
    die;
}
?>