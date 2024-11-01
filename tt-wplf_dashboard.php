<?php

if ( preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF']) ) { die('You are not allowed to call this page directly.'); }

add_action( 'wp_dashboard_setup' , 'wplf_dashboard_widgets' );

function wplf_dashboard_widgets() {
	wp_add_dashboard_widget( 'wplf_widget' , __('News from who you\'re following' , 'wplf') , 'wplf_dashboard' );
	wp_add_dashboard_widget( 'wplf_activity' , __('Activity' , 'wplf') , 'wplf_activity_dashboard' );
	
	global $wp_meta_boxes;
	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	$news_widget_backup = array('wplf_widget' => $normal_dashboard['wplf_widget']);
	$activity_widget_backup = array('wplf_activity' => $normal_dashboard['wplf_activity']);
	unset($normal_dashboard['wplf_widget']);
	unset($normal_dashboard['wplf_activity']);
	
	$sorted_dashboard = array_merge($news_widget_backup, $activity_widget_backup, $normal_dashboard);
	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
}

function wplf_dashboard() {
	$wplf = new WPLF();
	$netPost = $wplf->getFollowingPosts( 1 , 10 );
	
	if( is_object( $netPost ) || is_array( $netPost ) ):
		
		if( count( $netPost ) > 0 ):
			echo '<ul>';
			foreach( $netPost as $nP ):
				
				echo '<li class="wplf-news">';
				echo '<a href="' . $nP->permalink . '">' . get_avatar( $nP->post_author , 75 , '' ) . '</a>';
				echo '<h2><a href="' . $nP->permalink . '">' . substr( $nP->post_title, 0, 75 ) . '</a></h2>';
				echo '<span class="wplf-author">'.__("by $nP->post_author_name" , 'wplf').'</span><br />';
				echo substr( strip_tags( $nP->post_content ), 0, 250 ) . '... <a href="' . $nP->permalink . '">' . __('[read more]' , 'wplf') . '</a>';
				echo '<div class="clearfix"></div>';
				echo '</li>';
				
			endforeach;
			echo '</ul>';
		endif;
		
	else:
		echo $netPost;
	endif;
}

function wplf_activity_dashboard() {
	$wplf = new WPLF();
	$results = $wplf->getActivity( 1 , 10 );
	
	if( count( $results ) > 0 ):
		foreach( $results as $a ):
			$avatar = get_avatar( $a->ID , 25 , '' );
			$user = get_user_by( 'id' , $a->ID );

			if( isset( $a->post_id ) ) {
				$link = get_permalink( $a->post_id );
				$post = get_post( $a->post_id );
				$string = $a->insert_date . __(": <strong>$user->display_name</strong> liked your post: <strong>$post->post_title</strong>" , 'wplf');
			} else {
				$blog = get_blogs_of_user( $a->ID ); 
				$link = $blog[$a->ID]->siteurl;
				$string = $a->insert_date . __(": <strong>$user->display_name</strong> started following you." , 'wplf');
			}
			
			echo "<a href=\"$link\" class=\"wplf_activity\">";
			echo $avatar . $string;
			echo '<div class="clearfix"></div>';
			echo '</a>';
		endforeach;
	else:
		_e('No activity, for now ;)');
	endif;
}

?>