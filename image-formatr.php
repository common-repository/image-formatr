<?php
 /*
  * Plugin Name: Image Formatr
  * Plugin URI: http://warriorself.com/blog/about/image-formatr/
  * Description: Formats all content images on a page / post giving them captions and popups.
  * Version: 1.2.6
  * License: GPLv3
  * Author: Steven Almeroth
  * Author URI: http://warriorself.com/sma/
  * Text Domain: image-formatr
  * Domain Path: /languages/
  */

 /*  Copyright 2010-2014  Steven Almeroth  (sroth77@gmail.com)
  *
  *   This program is free software; you can redistribute it and/or modify
  *   it under the terms of the GNU General Public License as published by
  *   the Free Software Foundation; either version 2 of the License, or
  *   (at your option) any later version.
  *
  *   This program is distributed in the hope that it will be useful,
  *   but WITHOUT ANY WARRANTY; without even the implied warranty of
  *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  *   GNU General Public License for more details.
  *
  *   You should have received a copy of the GNU General Public License
  *   along with this program; if not, write to the Free Software
  *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
  */
define ('IMAGEFORMATR_TEXTDOMAIN', 'image-formatr');

include_once(dirname(__FILE__) . '/src/class.formatr.php');

$action_links = 'plugin_action_links_' . plugin_basename(__FILE__);

if (class_exists("ImageFormatr")) {
    $ifr_instance = new ImageFormatr();

    // hooks
    register_activation_hook  (__FILE__, array($ifr_instance, 'activate'  ));
    register_deactivation_hook(__FILE__, array($ifr_instance, 'deactivate'));

    // actions
    add_action('admin_menu'           , array($ifr_instance, 'admin_menu'   ));
    add_action('admin_init'           , array($ifr_instance, 'admin_init'   ));
    add_action('admin_head'           , array($ifr_instance, 'admin_head'   ));
    add_action('admin_enqueue_scripts', array($ifr_instance, 'enqueue'      ));
    add_action('plugins_loaded'       , array($ifr_instance, 'load_locale'  ));
    add_action('template_redirect'    , array($ifr_instance, 'enqueue'      ));
    add_action('wp_footer'            , array($ifr_instance, 'print_scripts'));

    // filters
    add_filter($action_links          , array($ifr_instance, 'set_link'), 10);
    add_filter('post_thumbnail_html'  , array($ifr_instance, 'featured_image'), 10, 3);
    add_filter('the_content'          , array($ifr_instance, 'filter'), $ifr_instance->priority);
}
