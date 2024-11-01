<?php
/*
 * @package WPLF
 * @version 1.4 */
/*
Plugin Name: TT Like & Follow WPMU Plugin
Plugin URI: https://wordpress.org/plugins/tt-like-follow-wpmu-plugin
Description: Socialize your Network! Enable "follow" button on Multisite Wordpress and add a like button on posts.
Author: Daniele Biggiogero, Paoltaia, Stiofan
Version: 1.1
Author URI: http://themetailors.com
Network: True
 
License: GPL2

	Copyright 2013  Daniele Biggiogero  (email : daniele.biggiogero@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

if ( preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF']) ) { die('You are not allowed to call this page directly.'); }

global $wplf_id;

$wplf_id = "1.4";

define( 'FOLLOW_ROOT' , dirname( __FILE__ ) );

require_once( FOLLOW_ROOT . "/tt-wplf_class.php" );
require_once( FOLLOW_ROOT . "/tt-wplf_install.php" );
require_once( FOLLOW_ROOT . "/tt-wplf_page.php" );
require_once( FOLLOW_ROOT . "/tt-wplf_dashboard.php" );
require_once( FOLLOW_ROOT . "/tt-wplf_widget.php" );

?>
