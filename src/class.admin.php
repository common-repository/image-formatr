<?php
require_once(dirname(__FILE__) . '/class.base.php');

if (!class_exists("ImageFormatrAdmin")) {
    class ImageFormatrAdmin extends ImageFormatrBase {

        // default admin options for activation
        var $def_options = array(
                'capatt'    => "title",
                'newtitle'  => "Click here to enlarge.",
                'yankit'    => "",
                'imglong'   => "180",
                'imgshort'  => "",
                'img2long'  => "",
                'img2short' => "",
                'img2page'  => "3",
                'dofx'      => "on",
                'force'     => "",
                'stdthumb'  => "on",
                'attthumb'  => "thumbnail",
                'killanc'   => "on",
                'inspect'   => "",
                'addclass'  => "img",
                'remclass'  => "",
                'xcludclass'=> "wp-smiley",
                'capclass'  => "",
                'group'     => "main",
                'imgdefs'   => "",
                'imgaddl'   => "",
                'flenable'  => "",
                'flusername'=> "",
                'flnsid'    => "",
                'flfrob'    => "",
                'fltoken'   => "",
                'flapikey'  => "",
                'flsecret'  => "",
                'uninstal'  => "",
                'prettyuse' => "on",
                'priority'  => "10",
                'ppspeed'   => "normal",
                'pptheme'   => "dark_rounded",
                'ppsocial'  => "",
                # legacy options used for deactivation removal
                'highuse'   => null,  # old Highslide library
                'homelong'  => null,  # replaced by img2long
                'homeshort' => null,  # replaced by img2short
                'yanktit'   => null,  # typo for yankit
        );

        function admin_init ( )
        {
            $this-> option_descriptions = array(

                // Main settings

                'dofx'      => array(
                    'title' => __('Popup effects', 'image-formatr'),
                    'desc'  => __('Do you want images to popup?', 'image-formatr'),
                    'html'  => __('If you check this option all processed images will be wrapped in an anchor tag with the '.
                                  'special "rel" attribute that will cause the image to popup when clicked for better viewing.', 'image-formatr'),
                    'code'  => '<a rel="prettyPhoto"><img/></a>',
                               ),
                'killanc'   => array(
                    'title' => __('Ignore anchors', 'image-formatr'),
                    'desc'  => __('Ignore any image anchors in the content we process.', 'image-formatr'),
                    'code'  => '<a href="ignore-me.html"><img></a> · <a href="dont-ignore-me.html"><img usemya="true"></a>',
                    'html'  => __('NOTE: This option will be overridden with an image`s <code>usemya</code> attribute.', 'image-formatr'),
                               ),
                'capatt'    => array(
                    'title' => __('Caption attribute', 'image-formatr'),
                    'desc'  => __('The image attribute to be used as the caption.', 'image-formatr'),
                    'code'  => '<img title="Gone With the Wind" alt="book">',
                               ),
                'newtitle'  => array(
                    'title' => __('Title replacement', 'image-formatr'),
                    'desc'  => __('The new image title (used for the mouse-over hint in most browsers).', 'image-formatr'),
                    'code'  => '<img title="'. $this->get_option('newtitle') .'">',
                    'html'  => __('NOTE: this will be overridden by the <em>Strip title</em> option.', 'image-formatr'),
                               ),
                'yankit'    => array(
                    'title' => __('Strip title', 'image-formatr'),
                    'desc'  => __('Blank out the image title.', 'image-formatr'),
                    'code'  => '<img title=""/>',
                    'html'  => __('NOTE: this will override the <em>Title replacement</em> option.', 'image-formatr'),
                               ),
                'force'     => array(
                    'title' => __('Force root', 'image-formatr'),
                    'desc'  => __('Force relative parent location of images to the root.', 'image-formatr'),
                    'html'  => "Interpret <code>&lt;img src=&quot;../images/1.jpg&quot;/&gt;</code> as ".
                               "<code>&lt;img src=&quot;/images/1.jpg&quot;/&gt;</code> which helped when I changed my permalinks.",
                               ),

                // Thumbnail settings

                'stdthumb'  => array(
                    'title' => __('Standardize thumbnails', 'image-formatr'),
                    'desc'  => __('Try to size all images to the thumbnail dimensions.', 'image-formatr'),
                    'code'  => '<img usemysize="true" width="200" height="132"/>',
                    'html'  => array(__('Try to size all the thumbnails to the dimensions below even if you have '.
                                        'width &amp; height set in your image tags.', 'image-formatr'),
                                     __('So, enable this if you want to ignore any width &amp; height settings in '.
                                        'your image tags.  This option will be overridden with an image`s '.
                                        '<code>usemysize</code> attribute.', 'image-formatr')),
                               ),
                'inspect'   => array(
                    'title' => __('Auto determine orientation', 'image-formatr'),
                    'desc'  => __('Try to determine the dimensions of the image to see if it is portrait (standing up) or '.
                                  'layout (laying down) before deciding if the width/height is the long or short edges.', 'image-formatr'),
                    'link'  => 'http://php.net/manual/en/function.getimagesize.php',
                    'html'  => array(__('<em>Uses PHP <a href="http://php.net/manual/en/function.getimagesize.php" '.
                                        'target="_blank">GetImageSize</a> function.</em>', 'image-formatr'),
                                     __('<strong>WARNING</strong>: may cause pages with lots of images to load slowly.', 'image-formatr')),
                               ),
                'imgdefs'   => array(
                    'name'  => 'imglong, imgshort',
                    'title' => __('Default dimensions', 'image-formatr'),
                    'desc'  => __('These values will be used as the width & height.', 'image-formatr'),
                    'html'  => array(__('These values will be used as the width &amp; height in pixels by the <em>Auto determine orientation</em> '.
                                        'setting which, if disabled will default to the <code>width</code> in the first box and the <code>height</code> '.
                                        'in second box.', 'image-formatr'),
                                     __('NOTE: leave one of the boxes blank (or zero) and it will be calculated using the aspect '.
                                        'ratio to the other box.', 'image-formatr')),
                               ),
                'imgaddl'   => array(
                    'name'  => 'img2long, img2short, img2page',
                    'title' => __('Additional dimensions', 'image-formatr'),
                    'desc'  => __('These values will be used as the width & height.', 'image-formatr'),
                    'html'  => array(__('These dimensions are used if you want to specify different settings for the front page '.
                                        'or the single display page or everything else.', 'image-formatr'),
                                     __('These values will be used as the width &amp; height in pixels by the <em>Auto determine '.
                                        'orientation</em> setting which, if disabled will default to the <code>width</code> in the '.
                                        'first box and the <code>height</code> in second box.', 'image-formatr'),
                                     __('NOTE: leave one of the boxes blank (or zero) and it will be calculated using the aspect '.
                                        'ratio to the other box.', 'image-formatr')),
                               ),
                'attthumb'   => array(
                    'title' => __('Use attached image as thumbnail', 'image-formatr'),
                    'desc'  => __('Select the size to use for the thumbnail with Wordpress attachment images, if available.', 'image-formatr'),
                    'html'  => array(__('If you attached an image to a post/page using the Wordpress <em>Add Media</em> button or '.
                                        'through the <em>Media Library</em>, then you can use one of the auto-generated smaller '.
                                        'sizes as the thumbnail.', 'image-formatr'),
                                     __('Non-attached images will just use the full-size image as the thumbnail and if the attached '.
                                        'image size you specify is not available, the full-size image will also be used.', 'image-formatr')),
                               ),

                // Styling settings

                'addclass'  => array(
                    'title' => __('Additional classes', 'image-formatr'),
                    'desc'  => __('Enter a space-separated list of classes to add to the image container div.', 'image-formatr'),
                               ),
                'remclass'  => array(
                    'title' => __('Remove classes', 'image-formatr'),
                    'desc'  => __('Enter a space-separated list of classes to remove from the image container div.', 'image-formatr'),
                               ),
                'xcludclass'=> array(
                    'title' => __('Exclude classes', 'image-formatr'),
                    'desc'  => __('Enter a space-separated list of classes to exclude images from processing, i.e. images with these classes will not be touched, just displayed "as-is".', 'image-formatr'),
                    'code'  => 'wp-smiley exclude-me-class excludemetoo',
                    'html'  => __('Note: <tt>wp-smiley</tt> is the class used by Wordpress <em>emoticons</em>.', 'image-formatr'),
                               ),
                'capclass'  => array(
                    'title' => __('Caption classes', 'image-formatr'),
                    'desc'  => __('Enter a space-separated list of classes to add to the image caption div.', 'image-formatr'),
                    'code'  => '<div class="'. $this->get_option('capclass') .'"> Caption. </div>',
                               ),

                // Flickr settings

                'flenable'  => array(
                    'title' => __('Enable Flickr', 'image-formatr'),
                    'desc'  => __('Process images with the <code>flickr</code> attribute?', 'image-formatr'),
                    'code'  => '<img flickr="123456789">',
                               ),
                'flusername'=> array(
                    'title' => __('Username', 'image-formatr'),
                    'desc'  => __('Your screen name', 'image-formatr'),
                    'link'  => 'http://www.flickr.com/account',
                               ),
                'flnsid'    => array(
                    'title' => __('Flickr Id', 'image-formatr'),
                    'desc'  => __('Also known as: Name Server ID (NSID)', 'image-formatr'),
                    'code'  => '12345678@N00',
                               ),
                'flfrob'    => array(
                    'title' => __('Frob', 'image-formatr'),
                    'desc'  => '',
                    'code'  => '12345678901234567-1a23456bcdefghij-123456',
                               ),
                'fltoken'   => array(
                    'title' => __('Token', 'image-formatr'),
                    'desc'  => '',
                    'code'  => '12345678901234567-12345678ab123cd4',
                               ),
                'flapikey'  => array(
                    'title' => __('API key', 'image-formatr'),
                    'desc'  => '',
                    'code'  => '0a1234567890123bcd45678e90123456',
                               ),
                'flsecret'  => array(
                    'title' => __('Secret', 'image-formatr'),
                    'desc'  => '',
                    'code'  => 'a1b23c4de5f6gh78',
                               ),

                // PrettyPhoto settings

                'prettyuse' => array(
                    'title' => __('PrettyPhoto library enabled', 'image-formatr'),
                    'desc'  => __('Use the prettyPhoto library included with the Image Formatr plugin to handle the popup effect?', 'image-formatr'),
                    'html'  => array(__('Uncheck this option to disable the pre-bundled prettyPhoto JavaScript Image library from loading.  If you '.
                                        'uncheck this option and have the Popup Effects setting checked above then you need to include your own image '.
                                        'viewer library in your theme or in an integration plugin that uses the "rel" atttribute.', 'image-formatr'),
                                     __('See the <a href="http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/documentation">'.
                                        'documentation</a>.', 'image-formatr')),
                                ),
                'ppspeed'   => array(
                    'title' => __('Popup animation speed', 'image-formatr'),
                    'desc'  => __('How fast should the popup move?', 'image-formatr'),
                                ),
                'pptheme'   => array(
                    'title' => __('Popup theme', 'image-formatr'),
                    'desc'  => __('What theme should the popup use?', 'image-formatr'),
                                ),
                'ppsocial'  => array(
                    'title' => __('Include the social networking buttons?', 'image-formatr'),
                    'desc'  => __('Should the popup have links to Twitter Tweets and Facebook Likes?', 'image-formatr'),
                                ),
                'group'     => array(
                    'title' => __('Slideshow group', 'image-formatr'),
                    'desc'  => __('You can organize images into groups by giving them a <code>group</code> attribute.', 'image-formatr'),
                    'code'  => '<img group="'. $this->get_option('group') .'">',
                    'html'  => __('NOTE: this setting is the default group for all images <b>without</b> the <em>group</em> attribute.', 'image-formatr'),
                                ),

                // Advanced settings

                'priority'  => array(
                    'title' => __('Plugin load priority', 'image-formatr'),
                    'desc'  => __('What priority should this plugin be loaded with? (default 10)', 'image-formatr'),
                    'html'  => array(__('An integer argument used to specify the order in which the functions associated with a particular '.
                                        'action are executed. Lower numbers correspond with earlier execution.', 'image-formatr'),
                                     __('Note: change this priority to <b>12</b> to execute the plugin later, after all <em>short-codes</em> '.
                                        'have executed which will add the popup effect to galleries, as well as the regular images.', 'image-formatr'),
                                     __('See the <a href="http://codex.wordpress.org/Plugin_API#Hook_to_WordPress">Wordpress Codex Plugin API</a>.',
                                        'image-formatr')),
                              ),
                'uninstal'  => array(
                    'title' => __('Uninstall', 'image-formatr'),
                    'desc'  => __('Remove all Image Formatr settings from the database upon plugin deactivation?', 'image-formatr'),
                    'html'  => array(__('Check this box if you want to automatically uninstall this plugin when you deactivate it. '.
                                        'This will clean up the database but you will loose all your settings and you will have the '.
                                        'default settings if you re-activate it.', 'image-formatr'),
                                     __('If you`re not sure, don`t check it. If you do want to uninstall this plugin, don`t forget to '.
                                        'click <em>Save Changes</em>.', 'image-formatr'),
                                     __('<em>Remember: the database is cleaned up when you "Deactivate"</em>.', 'image-formatr')),
                               ),
            );

            register_setting(
                IMAGEFORMATR_TEXTDOMAIN,         // group
                $this->settings_name,            // option name in settings table
                array($this, 'admin_validate')); // sanitize callback function

            add_settings_section('main_section', __('Main settings', 'image-formatr'), array($this, 'admin_overview'), __FILE__);
            $this-> add_settings(array('dofx', 'killanc', 'yankit'), 'print_checkbox', 'main_section');
            $this-> add_settings(array('capatt'  ), 'print_caption_dd', 'main_section');
            $this-> add_settings(array('newtitle'), 'print_textbox'   , 'main_section');

            add_settings_section('thumb_section', __('Thumbnail settings', 'image-formatr'), array($this, 'admin_overview'), __FILE__);
            $this-> add_settings(array('stdthumb'), 'print_checkbox'  , 'thumb_section');
            $this-> add_settings(array('inspect' ), 'print_checkbox'  , 'thumb_section');
            $this-> add_settings(array('imgdefs' ), 'print_img_defs'  , 'thumb_section');
            $this-> add_settings(array('imgaddl' ), 'print_img_addl'  , 'thumb_section');
            $this-> add_settings(array('attthumb'), 'print_attach_dd' , 'thumb_section');

            add_settings_section('style_section', __('Styling settings', 'image-formatr'), array($this, 'admin_overview'), __FILE__);
            $this-> add_settings(array('addclass', 'remclass', 'xcludclass', 'capclass'), 'print_textbox', 'style_section');

            add_settings_section('flickr_section', __('Flickr settings', 'image-formatr'), array($this, 'admin_overview'), __FILE__);
            $this-> add_settings(array('flenable'), 'print_checkbox', 'flickr_section');
            $this-> add_settings(array('flusername', 'flnsid', 'flfrob', 'fltoken', 'flapikey', 'flsecret'), 'print_textbox', 'flickr_section');

            add_settings_section('pretty_section', __('PrettyPhoto popup settings', 'image-formatr'), array($this, 'admin_overview'), __FILE__);
            $this-> add_settings(array('prettyuse', 'ppsocial'), 'print_checkbox', 'pretty_section');
            $this-> add_settings(array('pptheme'), 'print_theme_dd', 'pretty_section');
            $this-> add_settings(array('ppspeed'), 'print_speed_dd', 'pretty_section');
            $this-> add_settings(array('group'  ), 'print_textbox' , 'pretty_section');

            add_settings_section('adv_section', __('Advanced settings', 'image-formatr'), array($this, 'admin_overview'), __FILE__);
            $this-> add_settings(array('priority'), 'print_priority', 'adv_section');
            $this-> add_settings(array('force'   ), 'print_checkbox', 'adv_section');
            $this-> add_settings(array('uninstal'), 'print_checkbox', 'adv_section');
        }

        function add_settings ( $fields, $callback, $section )
        {
            foreach ($fields as $f)
                add_settings_field( $f, $this-> option_descriptions[$f]['title'], array($this, $callback), __FILE__, $section, $f );
        }

        function print_caption_dd ( $f )
        {
            $sel_tit = ($this->get_option($f) == 'title') ? 'selected="selected"' : '';
            $sel_alt = ($this->get_option($f) == 'alt'  ) ? 'selected="selected"' : '';
            $sel_non = ($this->get_option($f) == 'x'    ) ? 'selected="selected"' : '';
            $e = <<< ELEMENT
                <select
                    id="$f"
                    name="$this->settings_name[$f]"
                  ><option value="title" $sel_tit>title</option>
                   <option value="alt"   $sel_alt>alt</option>
                   <option value="x"     $sel_non>(x) no caption</option>
                </select>
ELEMENT;
            $this-> print_element($e, $f);
        }

        function print_attach_dd ( $f )
        {
            $sel_thu = ($this->get_option($f) == 'thumbnail') ? 'selected="selected"' : '';
            $sel_med = ($this->get_option($f) == 'medium'   ) ? 'selected="selected"' : '';
            $sel_lrg = ($this->get_option($f) == 'large'    ) ? 'selected="selected"' : '';
            $sel_ful = ($this->get_option($f) == 'fullsize' ) ? 'selected="selected"' : '';
            $e = <<< ELEMENT
                <select
                    id="$f"
                    name="$this->settings_name[$f]"
                  ><option value="full"      $sel_ful>full-size</option>
                   <option value="large"     $sel_lrg>large</option>
                   <option value="medium"    $sel_med>medium</option>
                   <option value="thumbnail" $sel_thu>thumbnail</option>
                </select>
ELEMENT;
            $this-> print_element($e, $f);
        }

        function print_theme_dd ( $f )
        {
            #theme: 'pp_default', /* light_rounded / dark_rounded / light_square / dark_square / facebook */

            $sel_de = ($this->get_option($f) == 'pp_default'   ) ? 'selected="selected"' : '';
            $sel_fb = ($this->get_option($f) == 'facebook'     ) ? 'selected="selected"' : '';
            $sel_lr = ($this->get_option($f) == 'light_rounded') ? 'selected="selected"' : '';
            $sel_ls = ($this->get_option($f) == 'light_square' ) ? 'selected="selected"' : '';
            $sel_dr = ($this->get_option($f) == 'dark_rounded' ) ? 'selected="selected"' : '';
            $sel_ds = ($this->get_option($f) == 'dark_square'  ) ? 'selected="selected"' : '';
            $e = <<< ELEMENT
                <select
                    id="$f"
                    name="$this->settings_name[$f]"
                  ><option value="pp_default"    $sel_de>default</option>
                   <option value="facebook"      $sel_fb>facebook</option>
                   <option value="light_rounded" $sel_lr>light rounded</option>
                   <option value="light_square"  $sel_ls>light square</option>
                   <option value="dark_rounded"  $sel_dr>dark rounded</option>
                   <option value="dark_square"   $sel_ds>dark square</option>
                </select>
ELEMENT;
            $this-> print_element($e, $f);
        }

        function print_speed_dd ( $f )
        {
            $sel_slow = ($this->get_option($f) == 'slow'  ) ? 'selected="selected"' : '';
            $sel_norm = ($this->get_option($f) == 'normal') ? 'selected="selected"' : '';
            $sel_fast = ($this->get_option($f) == 'fast'  ) ? 'selected="selected"' : '';
            $e = <<< ELEMENT
                <select
                    id="$f"
                    name="$this->settings_name[$f]"
                  ><option value="slow"   $sel_slow>slow</option>
                   <option value="normal" $sel_norm>normal</option>
                   <option value="fast"   $sel_fast>fast</option>
                </select>
ELEMENT;
            $this-> print_element($e, $f);
        }

        function print_checkbox ( $f )
        {
            $checked = $this->get_option($f) ? 'checked="checked" ' : '';
            $e = <<< ELEMENT
                <input
                    id="$f"
                    type="checkbox"
                    name="$this->settings_name[$f]"
                    $checked
                    />
ELEMENT;
            $this-> print_element($e, $f);
        }

        function print_textbox ( $f )
        {
            $e = <<< ELEMENT
                <input
                    id="$f"
                    type="text"
                    name="$this->settings_name[$f]"
                    value="{$this->get_option($f)}"
                    style="width: 300px"
                    />
ELEMENT;
            $this-> print_element($e, $f);
        }

        function print_priority ( $f )
        {
            $e = <<< ELEMENT
                <input
                    id="$f"
                    type="text"
                    name="$this->settings_name[$f]"
                    value="$this->priority"
                    style="width: 300px"
                    />
ELEMENT;
            $this-> print_element($e, $f);
        }

        function print_img_defs ( $f )
        {
            $e = <<< ELEMENTS
                <input type="text" name="$this->settings_name[imglong]"  id="imglong"  value="{$this->get_option('imglong')}" size="5" />
                x
                <input type="text" name="$this->settings_name[imgshort]" id="imgshort" value="{$this->get_option('imgshort')}" size="5" />
ELEMENTS;
            $this-> print_element($e, $f);
        }

        function print_img_addl ( $f )
        {
            $checked = array('', '', '', '');
            $checked[$this->get_option('img2page')] = "checked";
            $e = <<< ELEMENTS
              <input type="text" name="$this->settings_name[img2long]"  id="img2long"  value="{$this->get_option('img2long')}" size="5" />
              x
              <input type="text" name="$this->settings_name[img2short]" id="img2short" value="{$this->get_option('img2short')}" size="5" />
              =
              <input type="radio" name="$this->settings_name[img2page]" id="img2page0" value="0" {$checked[0]} /> <label for="img2page0">front</label> |
              <input type="radio" name="$this->settings_name[img2page]" id="img2page1" value="1" {$checked[1]} /> <label for="img2page1">not front</label> |
              <input type="radio" name="$this->settings_name[img2page]" id="img2page2" value="2" {$checked[2]} /> <label for="img2page2">single</label> |
              <input type="radio" name="$this->settings_name[img2page]" id="img2page3" value="3" {$checked[3]} /> <label for="img2page3">not single</label>
ELEMENTS;
            $this-> print_element($e, $f);
        }

        function print_element ( $e, $f )
        {
            $name = $f;
            $code = $html = $link = $title = '';
            $desc = array_key_exists($f, $this->option_descriptions) ? $this->option_descriptions[$f] : '';
            if (is_array($desc)) extract($desc);
            $desc = __($desc, 'image-formatr');
            $_titl = esc_attr($title);
            $_desc = esc_attr(wp_strip_all_tags($desc));
            $_code = esc_html($code);
            $desc = preg_replace( "/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/", "&amp;", strtr($desc, array(chr(38) => '&')) );  # convert & to &amp; (unless already converted)
            $code = $code ? "<p><code>$_code</code></p>" : '';
            $link = $link ? "<p>See: <a href='$link' target='_blank'>$link</a></p>" : '';
            $html = is_array($html) ? '<p>'. implode('</p><p>', $html) .'</p>' : $html;

            echo <<< INPUT
                <span title="$_desc">
                    $e
                </span>
                <input
                    value="?"
                    class="thickbox"
                    alt="#TB_inline?height=300&amp;width=400&amp;inlineId=TB-$f"
                    title="$_titl"
                    type="button"
                    />
                <div id="TB-$f" style="display: none">
                    <div>
                        <p>$desc</p>
                           $code
                           $html
                           $link
                        <p><em>Option name(s): <tt>$name</tt></em></p>
                    </div>
                </div>
INPUT;
        }

        // Add settings link on plugin page
        function set_link($links) {
            $settings_link = '<a href="options-general.php?page=class.admin.php">Settings</a>';
            array_push($links, $settings_link);
            return $links;
        }

        function admin_overview ( )
        {
            #echo 'Administration settings';
        }

        function admin_menu ( )
        {
            if( !function_exists('current_user_can')
             || !current_user_can('manage_options') )
                return;

            add_options_page(
                __('Image Formatr', 'image-formatr'),
                __('Image Formatr', 'image-formatr'),
                'manage_options',
                basename(__FILE__),
                array($this, 'options_page'));
        }

        function admin_head ( )
        {
            if ($this->get_option('uninstal'))
                add_action('admin_notices', array($this, 'admin_uninstall_message'));
        }

        function admin_uninstall_message ( )
        {
        ?>
            <div class="update-nag">
                <p><?php _e('Image-formatr plugin uninstall requested in settings: '.
                            'Deactivate to clean database then Delete to complete '.
                            'uninstall.', 'image-formatr'); ?></p>
            </div>
        <?php
        }

        function admin_validate ( $input )
        {
            // only validate the integers
            $integers = array('imglong', 'imgshort', 'img2long', 'img2short', 'priority');
            foreach ($input as $key => $val) {
                $this->options[$key] = $val;
                if (in_array($key, $integers))
                    $this->admin_validate_positive_integer($input, $key);
            }

            // the checkbox fields will not be present in the $input
            // so they need to be manually set to false if absent
            $checkboxes = array('yankit', 'dofx', 'killanc', 'force', 'stdthumb', 'uninstal', 'inspect', 'flenable', 'prettyuse', 'ppsocial');
            foreach ($checkboxes as $checkbox)
                if (!array_key_exists($checkbox, $input))
                    $this->options[$checkbox] = '';

            return $this->options;
        }

        function admin_validate_positive_integer ( $input, $fieldname )
        {
            if ($input[$fieldname])
                if ( !is_numeric($input[$fieldname])
                 or $input[$fieldname] < 0
                 or sprintf("%.0f", $input[$fieldname]) != $input[$fieldname] )
                    add_settings_error( $this->settings_name, 'settings_updated',
                        sprintf(__('Only positive integers should be used as %s.',
                            'image-formatr'), $fieldname) );
        }

        function options_page()
        {
        ?>
            <div class="wrap">
                <h2>Image Formatr</h2>
                <form action="options.php" method="POST">
                    <?php settings_fields(IMAGEFORMATR_TEXTDOMAIN); ?>
                    <?php do_settings_sections(__FILE__); ?>
                    <?php submit_button(); ?>
                </form>
            </div>
        <?php
        }

    } //End Class ImageFormatrAdmin

} //End class_exists check
