<?php
/*
+----------------------------------------------------------------------
| Copyright (c) 2018,2019,2020 Genome Research Ltd.
| This is part of the Wellcome Sanger Institute extensions to
| wordpress.
+----------------------------------------------------------------------
| This extension to Worpdress is free software: you can redistribute
| it and/or modify it under the terms of the GNU Lesser General Public
| License as published by the Free Software Foundation; either version
| 3 of the License, or (at your option) any later version.
|
| This program is distributed in the hope that it will be useful, but
| WITHOUT ANY WARRANTY; without even the implied warranty of
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
| Lesser General Public License for more details.
|
| You should have received a copy of the GNU Lesser General Public
| License along with this program. If not, see:
|     <http://www.gnu.org/licenses/>.
+----------------------------------------------------------------------

# Author         : js5
# Maintainer     : js5
# Created        : 2018-02-09
# Last modified  : 2018-02-12

 * @package   BaseThemeClass/CoAuthorPlus
 * @author    JamesSmith james@jamessmith.me.uk
 * @license   GLPL-3.0+
 * @link      https://jamessmith.me.uk/base-theme-class/
 * @copyright 2018 James Smith
 *
 * @wordpress-plugin
 * Plugin Name: Website Base Theme Class - Remove post admin
 * Plugin URI:  https://jamessmith.me.uk/base-theme-class-disable-posts/
 * Description: Support functions to: clean up interface if not using posts
 * Version:     0.1.0
 * Author:      James Smith
 * Author URI:  https://jamessmith.me.uk
 * Text Domain: base-theme-class-locale
 * License:     GNU Lesser General Public v3
 * License URI: https://www.gnu.org/licenses/lgpl.txt
 * Domain Path: /lang
*/

//----------------------------------------------------------------------
// As we are removing "blog" functionality we don't need posts and
// comments fields... this requires remove a number of different bits
// of code hooked in a number of different places...
// 1) We need to modify the "new" link in the admin bar so it defaults
//    to a type other than post (default this to page - but possibly
//    could recall if you want it to default to something else!!)
// 2) Remove the new post and comments link from this menu bar!
//----------------------------------------------------------------------

namespace BaseThemeClass;

class DisablePosts {

  function __construct( $self ) {
    $this->self = $self;
    add_action( 'admin_menu',     [ $this, 'remove_posts_sidebar'    ] );
    add_action( 'admin_bar_menu', [ $this, 'change_default_new_link' ], PHP_INT_MAX-1 );
    return $this;
  }
  // Remove posts sidebar entries...
  function remove_posts_sidebar() {
    $this->self->remove_sidebar_entry( 'edit.php' );
  }

  //   and change the default "New" link to "page" of if type is passed type..
  function change_default_new_link( $wp_admin_bar, $type = '', $title = '' ) {
    if( $type === '' ) {
      $type = $this->self->defn( 'DEFAULT_TYPE' );
      if( !$type ) {
        $type = DEFAULT_DEFN[ 'DEFAULT_TYPE' ];
      }
    }
    if( $title === '' ) {
      $title = ucfirst( $type );
    }
    // We can't have the node directly (shame) so we have to copy the node...
    $new_content_node = $wp_admin_bar->get_node('new-content');
    // Change the link... and set the
    $new_content_node->href .= '?post_type='.$type;
    // Change the title (to include default add action!)
    $new_content_node->title = preg_replace(
       '/(label">).*?</', '$1'.__('New').' ('.__($title).')<', $new_content_node->title );
    $wp_admin_bar->remove_node('new-content');
    $wp_admin_bar->add_node( $new_content_node);
 //   $wp_admin_bar->remove_node('new-post');
    $wp_admin_bar->remove_menu('wp-logo');   // Not to do with posts - but good to get rid of in admin interface!
  }
}
