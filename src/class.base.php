<?php
if (!class_exists("ImageFormatrBase"))
{
    class ImageFormatrBase
    {
        // additional pages image dimension administration settings
        const FRONT      = 0;
        const NOT_FRONT  = 1;
        const SINGLE     = 2;
        const NOT_SINGLE = 3;

        // the image class list to remove
        var $remove_classes = array();

        // the image class exclusion list
        var $exclude_classes = array();

        ////////////////////////////////////////////////////////// constructor

        /**
         * PHP4 constructor compatibility function
         */
        function ImageFormatr ( )
        {
            return $this->__construct();
        }

        /**
         * Constructor
         *
         * Get settings from database and call init()
         */
        function __construct ( )
        {
            $this->settings_name = 'plugin_' . IMAGEFORMATR_TEXTDOMAIN;  // Wordpress settings table entry name
            $this->options = get_option($this->settings_name, array());
            $this->init();
        }

        /**
         * Activation
         *
         * If we are upgrading from an old version, try to copy over the old
         * settings and remove the old keys from the database.
         */
        function activate()
        {
            // remove old keys if present
            // loop thru the default options and see if we have any keys in the
            // database with the old names from previous versions of the plugin.
            foreach ($this->def_options as $option => $default_value) {
                $old_key1 = "if_$option"; // legacy option key
                $old_key2 = "image-formatr_$option"; // legacy option key

                // try to pull out the value for the old key and use it
                if (!array_key_exists($option, $this->options) and !is_null($default_value)) {
                    $old_value = get_option($old_key2) ? get_option($old_key2) : get_option($old_key1);
                    $this->options[$option] = $old_value ? $old_value : $default_value;
                }
                // remove legacy options
                delete_option($old_key1);
                delete_option($old_key2);
            }
            // update database
            update_option($this->settings_name, $this->options);
            // init our object
            $this->init();
        }

        /**
         * Dectivation
         *
         * Uninstall the option from the database if the setting says to do so.
         */
        function deactivate()
        {
            // uninstall all options from the database
            if ($this->get_option('uninstal')) {
                delete_option($this->settings_name);
                // delete any leftover legacy option straggelers
                // from older versions of the plugin
                foreach ($this->def_options as $option => $value)
                    delete_option(IMAGEFORMATR_TEXTDOMAIN."_$option");
            }
        }

        /**
         * Load translation resources.
         */
        function load_locale ( )
        {
            load_plugin_textdomain(IMAGEFORMATR_TEXTDOMAIN, false, dirname(dirname(plugin_basename(__FILE__))).'/languages/');
        }

        /**
         * Add client resources.
         */
        function enqueue ( )
        {
            if (is_admin()) {
                wp_enqueue_style (IMAGEFORMATR_TEXTDOMAIN, plugins_url('image-formatr-admin.css', __FILE__), array(), false, 'all');
                wp_enqueue_style ('thickbox');
                wp_enqueue_script('thickbox');
            } else {
                wp_enqueue_style ('prettyPhoto', plugins_url('prettyPhoto.css', __FILE__), array(), false, 'all');
                wp_enqueue_script('prettyPhoto', plugins_url('prettyPhoto.js' , __FILE__), array('jquery'), '3.1.4', true );
            }
        }

        /**
         * Print the on-load JavaScript at the bottom of the page which
         * is actually preferred to loading in the head for a faster
         * perceived load time.
         */
        function print_scripts ( )
        {
            if ( $this->get_option('prettyuse') and !is_admin() ) {
                $social_tools = '';
                if (!$this->get_option('ppsocial'))
                    $social_tools = "social_tools: false,";

                echo <<< FOOTER
<script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function(){
    jQuery("a[rel^='prettyPhoto']").prettyPhoto({
        $social_tools
        theme: '{$this->get_option('pptheme')}',
        animation_speed: '{$this->get_option('ppspeed')}'
    });
  });
</script>

FOOTER;
            }
        }

        /**
         * This is the callback for the "post_thumbnail_html" filter which will
         * do the output for the featured image (i.e. post thumbnail).
         */
        function featured_image ( $html, $post_id, $post_image_id )
        {
            return $this->filter($html);
        }

        /**
         * Pull out the given element's attributes into an array.
         *
         * Returns an array of the image attributes/parameters
         * [src]   => http://warriorself.com/images/asia/bangkok_1517.jpg
         * [class] => alignright
         * [title] => Licensed to soak
         */
        function get_attributes ( $element )
        {
            return $this->get_attributes_using_wordpress($element);
        }

        /**
         * Pull out the given element's attributes into an array using regular
         * expresions.
         */
        function get_attributes_using_wordpress ( $element )
        {
            $attrs  = array();

            foreach (wp_kses_hair($element, array('http','https','ftp','file')) as $att => $info)
                $attrs[$att] = $info['value'];

            return $attrs;
        }

        /**
         * Pull out the given element's attributes into an array using the PHP
         * DOM extension.
         */
        function get_attributes_using_PHPDOM_extension ( $element )
        {
            if (!extension_loaded("DOM"))
                return $this->get_attributes_with_regular_expressions($element);

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $element_encoded = mb_convert_encoding($element, 'HTML-ENTITIES', get_bloginfo('charset'));
            $doc->loadHTML($element_encoded);

            $xpath = new DOMXPath($doc);

            $attrs  = array();
            foreach($xpath->query('//img/@*') as $attr)
                $attrs[$attr->name] = $attr->value;

            return $attrs;
        }

        /**
         * Use the native PHP getimagesize() function to get the image
         * width & height.
         */
        function get_image_size ( $src )
        {
            $url  = parse_url(get_option('siteurl'));
            $site = "http://" . $url["host"]; // no trailing slash
            $size = array();

            // site relative?
            if (substr($src,0,1) == '/')
                $url = $site . $src;
            else
                $url = $src;

            try {
                $size = getimagesize($url);
            }
            catch (Exception $e) {
                error_log("Cannot getimagesize(): {$e->getMessage()}");
            }

            return $size;
        }

        /**
         * Return the option for the given key
         */
        function get_option ( $key )
        {
            if (array_key_exists($key, $this->options))
                return $this->options[$key];

            return '';
        }

        /**
         * Get the inner html from a node.
         *
         * @param DOMElement $node The node we want to print
         * @return string The inner html markup of the given node
         */
        function get_inner_html( $node ) {
            $innerHTML= '';
            $children = $node->childNodes;
            foreach ($children as $child) {
                $innerHTML .= $child->ownerDocument->saveHTML( $child );
            }
            return $innerHTML;
        }

        /**
         * Remove an attribute from a given markup string.
         *
         * What we do is use the PHP Document Object Model class DOMDocument
         * to find and remove the given attribute.
         *
         * @param string $markup The html markup that we want to alter
         * @param string $attr   The name of the attribute we want to remove
         * @return string The markup without the attribute or its parameter
         */
        function get_rid_of_attr ( $markup, $attr )
        {
            if (strpos($markup, $attr) === false)
                return $markup;

            $dom = new DOMDocument;
            $dom->loadHTML($markup);
            $imgs = $dom->getElementsByTagName('img');

            foreach ($imgs as $img)
                if ($img->hasAttribute($attr))
                    $img->removeAttribute($attr);

            $body = $dom->documentElement->firstChild;

            return $this->get_inner_html($body);
        }

        /**
         * Get the attached image url in the specified size
         *
         * First we look in the class parameter of the image for the attachment
         * id and failing this we look in the database. Then we use this id to
         * try and grab the attached image of that size.
         */
        function get_attachment_url( $param )
        {
            global $wpdb;

            # we need to go to the database to find the id
            $query = "SELECT ID FROM `$wpdb->posts` WHERE guid='{$param['src']}'";

            $attachment_id = $wpdb->get_var($query);

            # if the image has been attached, we should have the id now
            if ($attachment_id) {
                $attachment_img_src = wp_get_attachment_image_src($attachment_id, $this->attach_thumb);

                if ($attachment_img_src and isset($attachment_img_src[0]))
                    return $attachment_img_src[0];
            }
        }

    } //End Class ImageFormatrBase

} //End class_exists check
