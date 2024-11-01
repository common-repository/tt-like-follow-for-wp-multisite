<?php 
if ( preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF']) ) { die('You are not allowed to call this page directly.'); }

if( !class_exists( 'followBlog' ) ) {

	// la classe principale da cui sviluppare il plugin
	class WPLF {
		private $user_ID;
		private $user_blog_ID;
		private $SQLtable;
		private $SQLtableLike;
		private $strictVals = array(
			'email-follow-notification',
			'email-like-notification'
		);
		
		public function __construct( $userID = null, $blogID = null ) {
			global $wpdb , $blog_id;
			$this->user_ID = ( is_null( $userID ) ) ? get_current_user_id() : $userID ;
			$this->user_blog_ID = ( is_null( $blogID ) ) ? $blog_id : $blogID;
			$this->SQLtable = $wpdb->base_prefix . 'followUser';
			$this->SQLtableLike = $wpdb->base_prefix . 'postsLikes';
		}
		
		/**
		 * "Follow User" button that check if the user is already following
		 *
		 * @uses WPLF::get_getFollowing()
		 * @uses WPLF::checkFollow( $follower_id )
		 *
		 * @access public
		 *
		 * @return string
		 */
		public function followButton( $class = null ) {
			$blogGetUser = get_users("blog_id=$this->user_blog_ID&orderby=registered&number=1");
			$blogUser = $blogGetUser[0]->user_nicename;
			$following = $this->getFollowing();
			$username = $blogGetUser[0]->display_name;
			$class_string = ( !is_null( $class ) && is_string( $class ) ) ? ' ' . addslashes( $class ) : '' ;
			if( $this->user_ID != $blogGetUser[0]->ID ){
				return ( !$this->checkFollow( $blogGetUser[0]->ID ) ) ? "<a class=\"followblog json-call follow-$blogUser$class_string\" data-fn=\"follow\" data-id=\"follow-$blogUser\" data-fol=\"$blogUser\" href=\"?fn=follow&following=$blogUser\">" . __("Follow $username" , "wplf") . "</a>" : "<a class=\"followblog json-call follow-$blogUser$class_string followed\" data-fn=\"unfollow\" data-id=\"follow-$blogUser\" data-fol=\"$blogUser\" href=\"?fn=unfollow&following=$blogUser\">" . __('Unfollow' , 'wplf') . "</a>";	
			}
		} 
		
		/**
		 * "Like post" button that check if the user is already liking the post
		 *
		 * @access public
		 *
		 * @return string
		 */
		public function likeButton( $args = '' ) {
			global $post;
			
			$text_like = get_site_option('tt-wplf-like-text');
			$text_unlike = get_site_option('tt-wplf-unlike-text');
			
			$defaults = array(
				'blogID' => $this->user_blog_ID,
				'postID' => $post->ID,
				'class' => null,
				'table' => 'posts'
			);
			
			$r = wp_parse_args( $args , $defaults );
			
			extract( $r );
			
			$args = array(
				'blogID' => (int) $blogID,
				'postID' => (int) $postID,
				'table' => $table 
			);
			
			$likes = $this->getLikes( $args );
			$string = ( $likes !== false ) ? ' (' . count( $likes ) . ')' : ''; 
			$class_string = ( !is_null( $class ) && is_string( $class ) ) ? ' ' . addslashes( $class ) : '' ;
			return ( !$this->checkLike( "$blogID-$postID-$table" ) ) ? "<a class=\"likepost json-call $blogID-$postID-$table$class_string\" data-fn=\"like\" data-id=\"$blogID-$postID-$table\" data-fol=\"$blogID-$postID-$table\" href=\"?fn=like&following=$blogID-$postID-$table\" >$text_like$string</a>" : "<a class=\"likepost json-call $blogID-$postID-$table$class_string followed\" data-fn=\"unlike\" data-id=\"$blogID-$postID-$table\" data-fol=\"$blogID-$postID-$table\" href=\"?fn=unlike&following=$blogID-$postID-$table\" >$text_unlike</a>";
		}
		
		/**
		 * Insert userID and the followed userID into 'followUser' table
		 *
		 * @uses WPLF::checkFollow( $follower_id )
		 *
		 * @access public
		 *
		 * @return array
		 */
		public function insertFollower( $follower ) {
			global $wpdb;
			
			$follower = addslashes( $follower );
			$fol = get_user_by( 'slug' , $follower );
			$fol = $fol->data;
			$data = array(
				'user_id' 		=> $this->user_ID,
				'following_id'	=> $fol->ID,
				'date'	=> date("Y-m-d h:i:s")
			);
			$format = array( '%d' , '%d' , '%s' );
			$result = ( !$this->checkFollow( $fol->ID ) && $this->user_ID != 0 ) ? $wpdb->insert( $this->SQLtable , $data , $format ) : false ;
			if ( !$this->checkFollow( $fol->ID ) && $this->user_ID != 0 && $result !== false ) $this->notify( $fol->ID , 'follow');
			
			return array( 'text' => __('Unfollow' , 'wplf') , 'check' => $result , 'fn' => 'unfollow' );
		}
		
		/**
		 * Insert userID, blog_id and post_id into 'postsLikes' table
		 *
		 * @uses WPLF::checkLike( $string )
		 *
		 * @access public
		 *
		 * @return array
		 */
		public function insertLike( $string ) {
			global $wpdb;
			
			$string = addslashes( $string );
			$post_array = explode( '-' , $string );
			$data = array(
				'user_id'	=> $this->user_ID,
				'blog_id'	=> $post_array[0],
				'post_id'	=> $post_array[1],
				'tbl'		=> $post_array[2],
				'date'		=> date("Y-m-d h:i:s")
			);
			
			$format = array( '%d' , '%d' , '%d' , '%s' , '%s' );
			$result = ( !$this->checkLike( $string ) ) ? $wpdb->insert( $this->SQLtableLike , $data , $format ) : false ;
			
			$user = get_users('blog_id='.$post_array[0]);
			if ( !$this->checkLike( $string ) && $result !== false ) $this->notify( $user[0]->ID , 'like');
			
			return array( 'text' => get_site_option('tt-wplf-unlike-text') , 'check' => $result , 'fn' => 'unlike' );
		}
		
		/**
		 * delete userID from 'followUser' table
		 *
		 * @uses WPLF::checkFollow( $follower_id )
		 *
		 * @access public
		 *
		 * @return array
		 */
		public function deleteFollower( $follower ) {
			global $wpdb;
			
			$follower = addslashes( $follower );
			$fol = get_user_by( 'slug' , $follower );
			$fol = $fol->data;
			
			$result = ( $this->checkFollow( $fol->ID ) && $this->user_ID != 0 ) ? $wpdb->query( $wpdb->prepare( "DELETE FROM $this->SQLtable WHERE user_id = %d AND following_id = %d" , $this->user_ID , $fol->ID ) ) : false ;
			
			return array ( 'text' => __("Follow $fol->display_name" , 'wplf') , 'check' => $result , 'fn' => 'follow' );
		}
		
		/**
		 * Delete userID, blog_id and post_id into 'postsLikes' table
		 *
		 * @uses WPLF::checkLike( $string )
		 *
		 * @access public
		 *
		 * @return array
		 */
		public function deleteLike( $string ) {
			global $wpdb;
			
			$string = addslashes( $string );
			$post_array = explode( '-' , $string );
			
			$result = ( $this->checkLike( $string ) ) ? $wpdb->query( $wpdb->prepare( "DELETE FROM $this->SQLtableLike WHERE user_id = %d AND blog_id = %d AND post_id = %d AND tbl = %s" , $this->user_ID , $post_array[0] , $post_array[1] , $post_array[2] ) ) : false;
			
			$args = array(
				'blogID' => (int) $post_array[0],
				'postID' => (int) $post_array[1],
				'table' => $post_array[2]
			);
			
			$likes = $this->getLikes( $args );
			$string_like = ( $likes !== false ) ? ' (' . count( $likes ) . ')' : ''; 
			
			return array( 'text' => get_site_option('tt-wplf-like-text') . $string_like , 'check' => $result , 'fn' => 'like' );
		}
		
		/**
		 * Check if the user is following a specific user
		 *
		 * @access public
		 *
		 * @return boolean
		 */
		public function checkFollow( $follower_id ) {
			global $wpdb;
			
			$follower_id = addslashes( $follower_id );
			$check = $wpdb->get_results("SELECT id FROM $this->SQLtable WHERE user_id = '$this->user_ID' AND following_id = '$follower_id'");
			
			return ( count( $check ) > 0 ) ? true : false ;
		}
		
		/**
		 * Check if the user has already liked a specific post
		 *
		 * @access public
		 *
		 * @return boolean
		 */
		public function checkLike( $string ) {
			global $wpdb;
			
			$post_array = explode('-', $string);
			$blogID = $post_array[0];
			$postID = $post_array[1];
			$table = $post_array[2];
			
			$check = $wpdb->get_results("SELECT id FROM $this->SQLtableLike WHERE user_id = '$this->user_ID' AND blog_id = '$blogID'  AND post_id = '$postID' AND tbl = '$table'");
			
			return ( count( $check ) > 0 ) ? true : false ;
		}
				
		/**
		 * Get the followers
		 *
		 * @access public
		 *
		 * @return dynamic
		 */
		public function getFollowers( $args = '' ) {
			global $wpdb;
			
			$defaults = array(
				'page'	=> 1,
				'limit'	=> false,
				'output_type' => 'OBJECT'
			);
			
			$r = wp_parse_args( $args , $defaults );
			
			$paging = $this->wplfPage( $page , $limit );
			$limit = ( $limit !== false ) ? "LIMIT $paging,".addslashes( $limit ) : '';
			$followers = $wpdb->get_results( "SELECT user_id AS ID , date AS insert_date  FROM $this->SQLtable WHERE following_id = '$this->user_ID' $limit" , $output_type );
			
			return ( count( $followers ) > 0 ) ? $followers : false ;
		}
		
		/**
		 * Get the the likes
		 *
		 * @access public
		 *
		 * @return dynamic
		 */
		public function getLikes( $args = '' ) {
			global $wpdb;
			
			$defaults = array(
				'blogID' => false,
				'postID' => false,
				'page'   => 1,
				'table' => 'posts',
				'limit'  => false,
				'output_type' => 'OBJECT',
				'exclude' => false
			);
			
			$r = wp_parse_args( $args , $defaults );
			extract( $r );
			
			$paging = $this->wplfPage( $page , $limit );
			$limit = ( $limit !== false && is_int( $limit ) ) ? "LIMIT $paging,$limit" : '';
			$blog = ( $blogID !== false && is_int( $blogID ) ) ? "blog_id = $blogID" : "blog_id = $this->user_blog_ID";
			$single = ( ( $postID !== false && is_int( $postID ) ) && ( $blogID !== false && is_int( $blogID ) ) ) ? "AND post_id = $postID" : '';
			$tblString = "AND tbl = $table";
			$exclude = ( $exclude !== false && is_bool( $exclude ) ) ? "AND user_id != $this->user_ID" : '' ;
			
			$likes = $wpdb->get_results( "SELECT user_id AS ID , post_id , date AS insert_date FROM $this->SQLtableLike WHERE $blog $single $tblString $exclude $limit" , $output_type );
			
			return ( count( $likes ) > 0 ) ? $likes : false;
		}
		
		/**
		 * Get the users that a user is following
		 *
		 * @access public
		 *
		 * @return dynamic
		 */
		public function getFollowing( 
			$page = 1 , 
			$limit = false , 
			$output_type = 'OBJECT' 
		) {
			global $wpdb;
			
			$paging = $this->wplfPage( $page , $limit );
			$limit = ( $limit ) ? 'LIMIT 0,'.addslashes($limit) : '';
			$following = $wpdb->get_results( "SELECT following_id AS ID , date AS insert_date FROM $this->SQLtable WHERE user_id = '$this->user_ID' $limit" , $output_type );
			
			return ( count( $following ) > 0 ) ? $following : false ;
		}
		
		/**
		 * Get the the post liked by the user
		 *
		 * @access public
		 *
		 * @return dynamic
		 */
		public function getLiked( $args ) {
			global $wpdb;
			
			$defaults = array(
				'blogID' => false,
				'page' => 1,
				'limit' => false,
				'table' => false,
				'output_type' => 'OBJECT'
			);
			
			$r = wp_parse_args( $args , $defaults );
			extract( $r );
			
			$paging = $this->wplfPage( $page , $limit );
			$limit = ( $limit !== false && is_int( $limit ) ) ? "LIMIT $paging,$limit" : '';
			$blog = ( $blogID !== false && is_int( $blogID ) ) ? "AND blog_id = $blogID" : '';
			$tblString = ( $table !== false && is_string( $table ) ) ? "AND tbl = $table" : '';
			
			$likes = $wpdb->get_results( "SELECT post_id , blog_id , date AS insert_date FROM $this->SQLtableLike WHERE user_id = $this->user_ID $blog $tblString $limit" , $output_type );
			
			return ( count( $likes ) > 0 ) ? $likes : false;
		}
		
		/**
		 * Gets the posts of users followed by NOTE: this function is compatible with Post Indexer (http://premium.wpmudev.org/project/post-indexer/)
		 * 
		 * @uses WPLF::getFollowing()
		 * @uses Network_Query
		 *
		 * @access public
		 *
		 * @return dynamic
		 */
		public function getFollowingPosts( 
			$page = 1 , 
			$limit = 10
		) {
			$users = $this->getFollowing();

			if( !function_exists( 'network_query_posts' ) ) {
				if( $users !== false ):
					
					global $wpdb;
					$output = array();
					
					$string = 'SELECT u.* FROM (';
					$cnt = 0;
					$paging = $this->wplfPage( $page , $limit );
					
					foreach( $users as $u ):
					
						$blogs = get_blogs_of_user( $u->ID );
						$blogTable = $wpdb->base_prefix . $blogs[ $u->ID ]->userblog_id . '_posts';
						if( $cnt > 0 ) $string.= ' UNION ';
						$string.= "(SELECT * FROM $blogTable WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date DESC)";
						$cnt++;
						
					endforeach;
					
					$string.= ") AS u ORDER BY u.post_date DESC LIMIT $paging,$limit";
					
					$netPosts = $wpdb->get_results( $string );
					
					foreach( $netPosts as $nP ):
						$postObj = new stdClass;
						$userObj = get_user_by( 'id' , $nP->post_author );
						$blogs = get_blogs_of_user( $nP->post_author );
						
						switch_to_blog( $blogs[ $nP->post_author ]->userblog_id );
						
						global $blog_id;
						$the_permalink = get_permalink( $nP->ID );
						
						$postObj->BLOG_ID	 			= $blog_id;
						$postObj->ID 					= $nP->ID;
						$postObj->post_author 			= $nP->post_author;
						$postObj->post_author_name 		= $userObj->display_name;;
						$postObj->post_date 			= $nP->post_date;
						$postObj->post_date_gmt 		= $nP->post_date_gmt;
						$postObj->post_content 			= $nP->post_content;
						$postObj->post_title 			= $nP->post_title;
						$postObj->post_excerpt 			= $nP->post_excerpt;
						$postObj->post_status 			= $nP->post_status;
						$postObj->comment_status 		= $nP->comment_status;
						$postObj->ping_status 			= $nP->ping_status;
						$postObj->post_password 		= $nP->post_password;
						$postObj->post_name 			= $nP->post_name;
						$postObj->to_ping 				= $nP->to_ping;
						$postObj->pinged 				= $nP->pinged;
						$postObj->post_modified 		= $nP->post_modified;
						$postObj->post_modified_gmt 	= $nP->post_modified_gmt;
						$postObj->post_content_filtered = $nP->post_content_filtered;
						$postObj->post_parent 			= $nP->post_parent;
						$postObj->guid 					= $nP->guid;
						$postObj->permalink				= $the_permalink;
						$postObj->menu_order 			= $nP->menu_order;
						$postObj->post_type 			= $nP->post_type;
						$postObj->post_mime_type 		= $nP->post_mime_type;
						$postObj->comment_count 		= $nP->comment_count;
						
						restore_current_blog();
						
						$output[] = $postObj;
					endforeach;
					
					return $output;
					
				else:
					
					return __('You\'re not following anyone. No posts for you, sorry.' , 'wplf');
				
				endif;
				
			} else {
					
				$netPost = new Network_Query();
				$uA = array();
				$output = array();
				
				if( $users !== false ):
					foreach ( $users as $u ) {
						$uA[] = $u->ID;
					}
					$args = array(
						'author' => implode( ',' , $uA ),
						'paged' => $page,
						'posts_per_page' => $limit
					);
					
					$netPost->query( $args );
					
					foreach( $netPost->posts as $nP ):
						$postObj = new stdClass;
						$userObj = get_user_by( 'id' , $nP->post_author );
						$the_permalink = network_get_permalink( $nP->BLOG_ID , $nP->ID );
						
						$postObj->BLOG_ID	 			= $nP->BLOG_ID;
						$postObj->ID 					= $nP->ID;
						$postObj->post_author 			= $nP->post_author;
						$postObj->post_author_name 		= $userObj->display_name;;
						$postObj->post_date 			= $nP->post_date;
						$postObj->post_date_gmt 		= $nP->post_date_gmt;
						$postObj->post_content 			= $nP->post_content;
						$postObj->post_title 			= $nP->post_title;
						$postObj->post_excerpt 			= $nP->post_excerpt;
						$postObj->post_status 			= $nP->post_status;
						$postObj->comment_status 		= $nP->comment_status;
						$postObj->ping_status 			= $nP->ping_status;
						$postObj->post_password 		= $nP->post_password;
						$postObj->post_name 			= $nP->post_name;
						$postObj->to_ping 				= $nP->to_ping;
						$postObj->pinged 				= $nP->pinged;
						$postObj->post_modified 		= $nP->post_modified;
						$postObj->post_modified_gmt 	= $nP->post_modified_gmt;
						$postObj->post_content_filtered = $nP->post_content_filtered;
						$postObj->post_parent 			= $nP->post_parent;
						$postObj->guid 					= $nP->guid;
						$postObj->permalink				= $the_permalink;
						$postObj->menu_order 			= $nP->menu_order;
						$postObj->post_type 			= $nP->post_type;
						$postObj->post_mime_type 		= $nP->post_mime_type;
						$postObj->comment_count 		= $nP->comment_count;
						
						$output[] = $postObj;
					endforeach;
					
					return $output;
				else:
				
					return __('You\'re not following anyone. No posts for you, sorry.' , 'wplf');
				
				endif;
				
			}
		}
		
		/**
		 * function to get and sort the activity of likes and follows from a given user
		 * 
		 * @uses WPLF::getFollowing()
		 * @uses WPLF::getLikes()
		 * @uses WPLF::backToObj()
		 *
		 * @access public
		 *
		 */
		public function getActivity( 
			$page = 1 , 
			$limit = false
		) {
			
			function dateDescSort( $a, $b ) { 
			  if(  $a['insert_date'] ==  $b['insert_date'] ) return 0 ;
			  return ( $a['insert_date'] > $b['insert_date'] ) ? -1 : 1;
			}
			
			$likes = $this->getLikes( false , false , $page , $limit , 'ARRAY_A' , true );
			$followers = $this->getFollowers( $page , $limit , 'ARRAY_A' );
			$results = array_merge( ( $likes !== false ) ? $likes : array() , ( $followers !== false ) ? $followers : array() );
			
			usort( $results , "dateDescSort" );
			$this->backToObj( $results );
			
			return $results;
		}
		
		/**
		 * function to save wplf-related usermeta values. NOTE: wplf only values are permitted.
		 * 
		 * @access public
		 *
		 * @return boolean
		 */
		public function saveUserOptions( 
			$optionName , 
			$optionValue 
		) {
			$save = ( in_array( $optionName , $this->strictVals ) ) ? update_user_meta( $this->user_ID , 'tt-wplf-' . $optionName , $optionValue ) : false ;
			return ( $save !== false ) ? true : false ;
		}
		
		/**
		 * function to get wplf-related usermeta values. NOTE: wplf only values are permitted.
		 * 
		 * @access public
		 *
		 * @return string
		 */
		public function getUserOptions( $optionName ) {
			$get = ( in_array( $optionName , $this->strictVals ) ) ? get_user_meta( $this->user_ID , 'tt-wplf-' . $optionName , true ) : 'not set' ;
			return $get;
		}
		
		/**
		 * function to transform nested arrays into object (NOTE: works only with first level of array)
		 * transform the given array and return a boolean
		 *
		 * @access private
		 *
		 * @return boolean
		 */
		private function backToObj( &$array ) {
			$obj = array();
			if( $array !== false || count( $array ) > 0 ):
				foreach( $array as $b ):
					if( is_array( $b ) ) $obj[] = ( object ) $b;
				endforeach;
			endif;
			$array = ( object ) $obj;
			
			return ( $array !== false || count( $array ) > 0 ) ? true : false;
		}
		
		/**
		 * Notify the user
		 *
		 * @access private
		 *
		 * @return boolean
		 */
		private function notify( $userID , $type ) {
			$sU = get_user_by( 'id' , $userID );
			$mU = get_user_by( 'id' , $this->user_ID );
			$title = $mU->display_name;
			$string =  __('Hello ' , 'wplf') . $sU->display_name . ',<br><br> <strong>' . $mU->display_name . '</strong> ';
			
			if( $type == 'follow' ) {
				$title .= __(' is now your follower!' , 'wplf');
				$string .= __('started following you.' , 'wplf');
			} elseif( $type == 'like' ) {
				$title .= __(' like your post!' , 'wplf');
				$string .= __('liked your post.' , 'wplf');
			}
			
			$string .= __('<br><br>Have a good day!' , 'wplf');
			
			$sendMail = wp_mail( $sU->user_email , $title , $message );
			
			return $sendMail;
		}
		
		/**
		 * Paginating function
		 * 
		 * @access private
		 * 
		 * @return integer
		 */
		 private function wplfPage( $page = 1 , $limit = 10 ) {
			return ( $limit !== false ) ? ( $page - 1 ) * $limit : ( $page - 1 ) * 10 ;
		 }
	}
	
}

?>