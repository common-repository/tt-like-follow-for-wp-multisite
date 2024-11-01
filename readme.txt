=== TT Like & Follow for WP-Multisite  ===
Contributors: paoltaia, stiofan
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=payments@nomaddevs.com&item_name=Donation+for+WPLF
Tags: multisite, network, follow button, like button, social network
Requires at least: 3.0.1
Tested up to: 3.8.3
Stable tag: 1.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

TT Like & Follow for WP-Multisite gives you the chance to add basic Social Network features to your installation.

== Description ==

<p>ThemeTailors Like & Follow Plugin for Wordpress Multisite gives you the chance to add basic Social Network features to your WP multisite installation.</p>

<p>It include 2 features:</p>

<ul>
<li> A like button can be added to all network's posts and registered users can "Like" the posts;</li>
<li> A follow button can be added to all network's site and registered users can "Follow" any sites in the network.</li>
</ul>

<p>Site's admins will see in their dashboard the latest posts from people they are following.</p> 

<p>The panel will display the latest 4 posts, from every followed user, in chronological order.</p>

<p>Every post will show the linked title of the post, the author's name, an excerpt of the post and the author's avatar.</p>

<p>TT Like & Follow Plugin comes with a handful sets of functions that you can insert in your theme.</p>

<p>These functions are called by a class that can be extended and improved.</p>

Tailored by <a href="http://www.themetailors.com">theme tailors</a>

== Installation ==

= Automatic installation =
<p>Automatic installation is the easiest option. To do an automatic install of TT Like &amp; Follow for WP-Multisite, log in to your WordPress Multisite dashboard, navigate to the Plugins menu and click Add New.</p>
<p>In the search field type TT Like &amp; Follow for WP-Multisite and click Search Plugins. Once you've found our plugin you install it by simply clicking Install Now.</p>

= Manual installation =

<p>The manual installation method involves downloading TT Like & Follow for WP-Multisite plugin and uploading it to your webserver via your favourite FTP application. </p>

<p>The WordPress codex will tell you more [here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).</p>

= Updating =

<p>Automatic updates should seamlessly work. We always suggest to backup up your website before performing any automated update to avoid exceptions.</p>

= Using TT WPLF in your own theme = 

<p>Like & Follow Wordpress Plugin comes with a handful sets of functions that you can insert in your theme. These functions are called by a class that can be extended and improved.</p>
<p>Please find a list of the functions inside the WPLF class in the plugin FAQ.</p>

== Frequently Asked Questions ==

= Q : How do I create an instance of WPLF class =
= A : To create the instance of the WPLF class: =

<p>$foo = new WPLF( userID , blogID );</p>
<p>userID ( integer )</p>
<p>The ID of a desired user. If the value is null, the current user ID will be used with the help of the WP function get_current_user_id().</p>
<p>Default value: null</p>
<p>blogID ( integer )</p>
<p>The ID of a desired blog, If the value in null, the current blog ID will be used with the help of the global variable $blog_id.</p>
<p>Default value: null</p>

= Q : Where can I find a list of functions in WPLF class =
= A : Here's a list of the functions inside the WPLF class: =

= 1) followButton =

<p>As its name says, this function creates the "Follow" button. You can call it everywhere in the code like this:</p>
<p>echo $foo-&gt;followButton( class );</p>
<p>class ( string )</p>
<p>custom classnames that you can insert in the button to modify its default style.</p>
<p>Default value: null</p>
<p>Return value: a string that contains the formatted HTML "Follow" button.</p>

= 2) likeButton =

<p>this function creates the "Like" button.</p>
<p>echo $foo-&gt;likeButton( args );</p>
<p>args ( mixed values )</p>
<p>The function uses the same pattern used in WordPress to define the multiple variables inside. You can use arrays or a string to set the following variables:</p>
<p>blogID ( integer )</p>
<p>The ID of the blog you want to target the button.</p>
<p>Default value: the blogID set when the class's instance is created</p>
<p>postID ( integer )</p>
<p>The ID of the post you want to target.</p>
<p>Default value: $post-&gt;ID from global variable $post. Remember, if this value is set to default, you must use the function inside the loop.<br>
class ( string )</p>
<p>The custom classnames that you can insert in the button to modify its style.</p>
<p>Defaul value: null</p>
<p>table ( string )</p>
<p>It refers to the table located in the WP database. It targets a specified table that contain the target post.</p>
<p>If you're using a custom type post, you don't need to modify this parameter.</p>
<p>Default value: ‘posts'</p>
<p>Return value: a string that contains the formatted HTML "Like" button.</p>

= 3) insertFollower =

<p>This function insert the selected user into the "following" list of a target user. It's the function that is called when clicking on the "Follow" button.</p>
<p>$foo-&gt;insertFollower( follower );</p>
<p>follower ( integer )</p>
<p>The follower's ID. There's no default value, this variable is mandatory.</p>
<p>Return value: an associative array that contains the following values:</p>
<p>‘text' = "Unfollow"</p>
<p>‘check' = if the insertion fails, the value will be false</p>
<p>‘fn' = "unfollow"</p>

= 4) insertLike =

<p>Insert a target post into the like list. It's the function called after you press on the "Like" button.</p>
<p>$foo-&gt;insertLike( comingString );</p>
<p>comingString ( string )</p>
<p>The string that's coming from the "Like" button. It's formed as follows:</p>
<p>blog_id-post_id-table</p>
<p>The string is split by the function and processed.</p>
<p>Return value: an associative array with the following values:</p>
<p>‘text' = the value set in the admin backend</p>
<p>‘check' = if the insertion fails, the value will be false</p>
<p>‘fn' = "unlike"</p>

= 5) deleteFollower =

<p>Remove a selected user from the "following" list from the target user. It's called when you press the "Unfollow" button.</p>
<p>$foo-&gt;deleteFollower( follower );</p>
<p>follower ( string )</p>
<p>The user's slug name that will be removed from the list. It's a mandatory variable.</p>
<p>Return value: an associative array with the following values:</p>
<p>‘text' = "Follow [USERNAME]"</p>
<p>‘check' = if the deletion fails, the value will be false</p>
<p>‘fn' = "follow"</p>

= 6) deleteLike =

<p>Remove a selected post ( or a custom type ) from the "Like" list of the target user. It's the function called when you press on the "Unlike" button.</p>
<p>$foo-&gt;deleteLike( deleteString );</p>
<p>deleteString ( string )</p>
<p>The string that's coming from the "Unlike" button. Like the insertLike function, it follows the same pattern:</p>
<p>blog_id-post_id-table</p>
<p>Return value: an associative array with the following values:</p>
<p>‘text' = the value set in the admin backend</p>
<p>‘check' = if the deletion fails, the value will be false</p>
<p>‘fn' = "follow"</p>

= 7) checkFollow =

<p>This function checks if the user is already followed.</p>
<p>$foo-&gt;checkFollow( follower_id );</p>
<p>follower_id ( integer )</p>
<p>The ID of the target user. This variable is mandatory</p>
<p>Return value: ( boolean ) if the user is already followed, the function will return true</p>

= 8) checkLike =

<p>Checks if the post is already liked by a target user.</p>
<p>$foo-&gt;checkLike( likeString );</p>
<p>likeString ( string )</p>
<p>String that will be processed by the function. Like insertLike and deleteLike functions, it uses the same pattern:</p>
<p>blog_id-post_id-table</p>
<p>Return value: ( boolean ) if the post is altready liked, the function will return true</p>

= 9) getFollowers =

<p>Return an array that contains all the users that follows the target user.</p>
<p>$foo-&gt;getFollowers( args );</p>
<p>args ( mixed values )</p>
<p>the arguments used for the pagination and to define the output type. You can insert the values as an associative array or as a query string, just like the function get_results in the wpdb class.</p>
<p>page ( integer )</p>
<p>The current page defined fro the pagination.</p>
<p>Default value: 1</p>
<p>limit ( integer )</p>
<p>The number of results that will be displayed per page.</p>
<p>Default value: false</p>
<p>output_type ( string )</p>
<p>Sets the type of the resulting array. To set a different type of array, check the wpdb class on the WordPress Codex Page.</p>
<p>Default value: OBJECT</p>
<p>Return value: An array with the result of the query.</p>

= 10) getLikes =

<p>Return an array that contains the likes got from the target user.</p>
<p>$foo-&gt;getLikes( args );</p>
<p>args ( mixed values )</p>
<p>The arguments used to filter and paginate the results. You can insert the values as an associative array or as a query string, just like the function get_results in the wpdb class.</p>
<p>blogID ( integer )</p>
<p>The ID of a target blog in the network. if the value is false the function will return all the likes of the target user.</p>
<p>Default value: false</p>
<p>postID ( integer )</p>
<p>The ID of the post. To target a post, you must also enter the blogID.</p>
<p>Default value: false</p>
<p>page ( integer )</p>
<p>The current page defined for pagination.</p>
<p>Default value: 1</p>
<p>limit ( integer )</p>
<p>The number of results that will be displayed per page.</p>
<p>Default value: false</p>
<p>table ( string )</p>
<p>It refers to the table located in the WP database. It targets a specified table that contain the target post.</p>
<p>Default value: ‘posts'</p>
<p>output_type ( string )</p>
<p>Sets the type of the resulting array. To set a different type of array, check the wpdb class on the WordPress Codex Page.</p>
<p>Default value: ‘OBJECT'</p>
<p>exclude ( boolean )</p>
<p>You can exclude the target user from the results. Setting true this parameter, the query will return only the likes that other user gave to the selected post.</p>
<p>Default value: false</p>
<p>Return value: an array with the result of the query.</p>

= 11) getFollowing =

<p>Return the users that the target user is following.</p>
<p>$foo-&gt;getFollowing( args );</p>
<p>args ( mixed values )</p>
<p>Like the "getFollowers" function, the args used in this function are for the pagination and to define the output type on the resulting query.</p>
<p>page ( integer )</p>
<p>The current page defined fro the pagination.</p>
<p>Default value: 1</p>
<p>limit ( integer )</p>
<p>The number of results that will be displayed per page.</p>
<p>Default value: false</p>
<p>output_type ( string )</p>
<p>Sets the type of the resulting array. To set a different type of array, check the wpdb class on the WordPress Codex Page.</p>
<p>Default value: OBJECT</p>
<p>Return value: an array with the result of the query.</p>

= 12) getLiked =

<p>Return the post liked from the target user.</p>
<p>$foo-&gt;getLiked( args );</p>
<p>args ( mixed values )</p>
<p>You can insert the values as an associative array or as a query string, just like the function get_results in the wpdb class.</p>
<p>blogID ( integer )</p>
<p>The ID of a target blog in the network. if the value is false the function will return all the likes of the target user.</p>
<p>Default value: false</p>
<p>page ( integer )</p>
<p>The current page defined for pagination.</p>
<p>Default value: 1</p>
<p>limit ( integer )</p>
<p>The number of results that will be displayed per page.</p>
<p>Default value: false</p>
<p>table ( string )</p>
<p>It refers to the table located in the WP database. It targets a specified table that contain the target post.</p>
<p>Default value: ‘posts'</p>
<p>output_type ( string )</p>
<p>Sets the type of the resulting array. To set a different type of array, check the wpdb class on the WordPress Codex Page.</p>
<p>Default value: ‘OBJECT'</p>
<p>Return value: an array with the result of the query.</p>

= 13) getFollowingPosts =

<p>Return the posts from the users followed from the target user.</p>
<p>This function is compatible with the WPMUDEV Post Indexer plugin. If this plugin is installed, the function will use its class to get the posts from the index generated.</p>
<p>$foo-&gt;getFollowingPosts( page , limit );</p>
<p>page ( integer )</p>
<p>The current page defined for pagination.</p>
<p>Default value: 1</p>
<p>limit ( integer )</p>
<p>The number of results that will be displayed per page.</p>
<p>Default value: 10</p>
<p>Return value: an array filled with stdClass object arrays. The stdClass object contains the following data:</p>
<p>BLOG_ID</p>
<p>ID</p>
<p>post_author</p>
<p>post_author_name</p>
<p>post_date</p>
<p>post_date_gmt</p>
<p>post_content</p>
<p>post_title</p>
<p>post_excerpt</p>
<p>post_status</p>
<p>comment_status</p>
<p>ping_status</p>
<p>post_password</p>
<p>post_name</p>
<p>to_ping</p>
<p>pinged</p>
<p>post_modified</p>
<p>post_modified_gmt</p>
<p>post_content_filtered</p>
<p>post_parent</p>
<p>guid</p>
<p>permalink</p>
<p>menu_order</p>
<p>post_type</p>
<p>post_mime_type</p>
<p>comment_count</p>

= 14) getActivity =

<p>Returns the follows and the likes that the target user receive. The results are sorted in chronological order.</p>
<p>This function uses getLikes and getFollowers to gather the data.</p>
<p>$foo-&gt;getActivity( page , limit );</p>
<p>page ( integer )</p>
<p>The current page defined for pagination.</p>
<p>Default value: 1</p>
<p>limit ( integer )</p>
<p>The number of results that will be displayed per page.</p>
<p>Default value: false</p>
<p>Return value: an object array.</p>

= 15) saveUserOptions =

<p>Saves wplf-related usermeta values.</p>
<p>$foo-&gt;saveUserOptions( optionName , optionValue );</p>
<p>optionName ( string )</p>
<p>The name of the target option. NOTE: this function only accepts option's names that are previously declared in the $strictVals variable, inside the WPLF class.</p>
<p>The options accepted are:</p>
<p>email-follow-notification</p>
<p>email-like-notification</p>
<p>No default value. this variable is mandatory.</p>
<p>optionValue ( mixed values )</p>
<p>The value that will be saved.</p>
<p>No default value.</p>
<p>Return value: ( boolean ) if the saving is processed, the function will return true</p>

= 16) getUserOptions =

<p>Gets wplf-related usermeta values.</p>
<p>$foo-&gt;getUserOptions( optionName );</p>
<p>optionName ( string )</p>
<p>The name of the target option. NOTE: this function only accepts option's names that are previously declared in the $strictVals variable, inside the WPLF class.</p>
<p>The options accepted are:</p>
<p>email-follow-notification</p>
<p>email-like-notification</p>
<p>No default value. this variable is mandatory.</p>
<p>Return value: the value from the usermeta selected.</p>

= Private functions =

<p>There are also 3 private functions that you can use only inside the wplf class.</p>
<p>They are developed to ease the work inside the other functions.</p>

= 1) backToObj =

<p>Transform nested arrays into objects. This is developed to merge the results used by getLikes and getFollowers in the getActivity function NOTE: works only with first level of array</p>
<p>Unlike other functions in the class, backToObj doesn't need to be declared inside a variable.</p>
<p>$array = array();</p>
<p>$foo-&gt;backToObj( $array );</p>
<p>//now $array is an object stdClass</p>
<p>Return value: No return value. This function modifies the target array.</p>

= 2) notify =

<p>Sends an email to the target user and to the user that's followed or have his post liked. This function is used by insertFollower and insertLike. For now, it's developed to work only with these 2 public functions.</p>
<p>$foo-&gt;notify( userID , type );</p>
<p>userID ( integer )</p>
<p>The ID of the user that is followed or have his post liked by the target user. From this ID, the Display name and the email will be used to send the email to him.</p>
<p>No default value. This variable is mandatory.</p>
<p>type ( string )</p>
<p>Define the genre of the email that will be sent. For now, it has only 2 types:</p>
<p>follow</p>
<p>like</p>
<p>By selecting one of these type, the content of the email will change.</p>
<p>No default value. This variable is mandatory.</p>
<p>Return value: Because notify uses wp_mail to send the email, the function will return true if the mail is sent.</p>

= 3) wplfPage =

<p>Used to ease the pagination. It's used by getFollowers, getLikes, getFollowing, getLiked and getFollowingPosts.</p>
<p>$foo-&gt;wplfPage( page , limit );</p>
<p>page ( integer )</p>
<p>The current page defined for pagination.</p>
<p>Default value: 1</p>
<p>limit ( integer )</p>
<p>The number of results that will be displayed per page.</p>
<p>Default value: 10</p>
<p>Return value: The function will return the right point to gather the data from the query.</p>


== Screenshots ==

= Coming soon =

== Changelog ==

= 1.0 =

initial release

== Upgrade Notice ==

= none =