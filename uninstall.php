<?php
	if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) exit();
	
	global $wpdb;
	
	$followTable = $wpdb->base_prefix . 'followUser';
	$likeTable = $wpdb->base_prefix . 'postsLikes';

	$wplf_follow = $wpdb->query( "DROP TABLE $followTable" );
	$wplf_like = $wpdb->query( "DROP TABLE $likeTable" );
	
	delete_site_option( 'tt-wplf-version' );
	delete_site_option( 'tt-wplf-show-default-follow-button' );
	delete_site_option( 'tt-wplf-show-like-button' );
	delete_site_option( 'tt-wplf-like-button-position' );
	delete_site_option( 'tt-wplf-show-follow-widget' );
	delete_site_option( 'tt-wplf-no-logged-error' );
	
?>