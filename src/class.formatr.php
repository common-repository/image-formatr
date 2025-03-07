<?php
require_once(dirname(__FILE__) . '/class.flickr.php');

if (!class_exists("ImageFormatr")) {
    class ImageFormatr extends ImageFormatrFlickr {

        // html image tag attributes
        // <img src="pic.jpg" title="This picture is great" width="800"
        //  group="sub" hint="borrowed from pics.org"/>
        var $image_atts = array(
                'alt',
                'asis',
                'class',
                'flickr',
                'group',
                'height',
                'hint',
                'id',
                'link',
                'nocap',
                'nofx',
                'page',
                'src',
                'thumb',
                'title',
                'usemya',
                'usemysize',
                'width',
                );

        // administration settings are stored in a single table row
        var $options = array();

        // load plugin settings
        function init()
        {
            // main settings
            $this->caption_att     =            $this->get_option('capatt') ? $this->get_option('capatt') : 'title'; // attribute to be used for image caption
            $this->strip_title     =            $this->get_option('yankit') ? true : false; // should "title" attribute be stripped?
            $this->add_class       =            $this->get_option('addclass' ); // list of css classes to add to the container div
            $this->cap_class       =            $this->get_option('capclass' ); // list of css classes to add to the caption div
            $this->def_group       =            $this->get_option('group'    ); // the default PrettyPhoto grouping designator
            $this->attach_thumb    =            $this->get_option('attthumb' ); // the size of the attachment image to use as the thumbnail
            $this->new_title       =            $this->get_option('newtitle' ); // the new title replacement
            $this->def_img_width   = abs(intval($this->get_option('imglong'  )));
            $this->def_img_height  = abs(intval($this->get_option('imgshort' )));
            $this->addl_img_width  = abs(intval($this->get_option('img2long' )));
            $this->addl_img_height = abs(intval($this->get_option('img2short')));
            $this->addl_page       =     intval($this->get_option('img2page' ));
            $this->priority        =     intval($this->get_option('priority' ) ? $this->get_option('priority' ) : 10);

            // flickr settings
            $this->flickr           = new stdClass();
            $this->flickr->loaded   =          false;
            $this->flickr->enable   = (boolean)$this->get_option('flenable'  );
            $this->flickr->username =          $this->get_option('flusername');
            $this->flickr->nsid     =          $this->get_option('flnsid'    );
            $this->flickr->frob     =          $this->get_option('flfrob'    );
            $this->flickr->token    =          $this->get_option('fltoken'   );
            $this->flickr->apikey   =          $this->get_option('flapikey'  );
            $this->flickr->secret   =          $this->get_option('flsecret'  );

            // remove class list
            foreach (explode(' ', $this->get_option('remclass')) as $class)
                if (trim($class))
                    $this->remove_classes[] = trim($class);

            // exclude class list
            foreach (explode(' ', $this->get_option('xcludclass')) as $class)
                if (trim($class))
                    $this->exclude_classes[] = trim($class);

            // default image dimensions
            if ($this->def_img_height > $this->def_img_width) {
                $this->def_img_long   = $this->def_img_height;
                $this->def_img_short  = $this->def_img_width;
            } else {
                $this->def_img_long   = $this->def_img_width;
                $this->def_img_short  = $this->def_img_height;
            }

            // additional pages image dimensions
            if ($this->addl_img_height > $this->addl_img_width) {
                $this->addl_img_long   = $this->addl_img_height;
                $this->addl_img_short  = $this->addl_img_width;
            } else {
                $this->addl_img_long   = $this->addl_img_width;
                $this->addl_img_short  = $this->addl_img_height;
            }

            // load additional pages image dimensions, if blank, with defaults
            if (!$this->addl_img_width and !$this->addl_img_height) {
                $this->addl_img_width  = $this->def_img_width;
                $this->addl_img_height = $this->def_img_height;
            }
            if (!$this->addl_img_long and !$this->addl_img_short) {
                $this->addl_img_long  = $this->def_img_long;
                $this->addl_img_short = $this->def_img_short;
            }
        }

  //////////////////////////////////////////////// parse content methods

        function filter ( $markup )
        {
            // if we are displaying a page that meets the additional-page
            // criteria (e.g. single), then we use the additional dimensions
            if ( ($this->addl_page == self::FRONT      and  is_front_page())
              or ($this->addl_page == self::NOT_FRONT  and !is_front_page())
              or ($this->addl_page == self::SINGLE     and  is_single()    )
              or ($this->addl_page == self::NOT_SINGLE and !is_single()    )
              ) {
                $this-> def_img_width  = $this-> addl_img_width;
                $this-> def_img_height = $this-> addl_img_height;
                $this-> def_img_long   = $this-> addl_img_long;
                $this-> def_img_short  = $this-> addl_img_short;
            }

            ////////////////////////////////        [flickr                ]
            if ($this->flickr->enable) {
                $markup = preg_replace_callback('/\[flickrset\s+id="(\d+)"\]/', array($this, 'do_shortcode_flickrset'), $markup);
                $markup = preg_replace_callback('/\[flickr\s+pid="(\d+)"\]/'  , array($this, 'do_shortcode_flickr'   ), $markup);
               #$markup = preg_replace_callback("/\[flickr[^\]]+\]/"          , array($this, 'do_shortcode_flickr'   ), $markup);
            }

            // regular img tags ////////////     <p>   <a     >      <img      / >     <    /a>     < /p>   insensitive-case
            $markup = preg_replace_callback("/(?:<p>)?(<a[^>]*>)?\s*(<img[^>]*\/?>)\s?(<\s*\/a>)?(?:<\/p>)?/i", array($this, 'parse'), $markup);

            return $markup;
        }

        /**
         * Parse the image markup tags
         *
         * matches[0]: <a><img></a>
         * matches[1]: <a href>
         * matches[2]: <img src>
         * matches[3]: </a>
         */
        function parse ( $matches )
        {
            $orig_markup = $matches[0];

            if (count($matches) < 3)
                return $orig_markup;

            $anchor_tag  = $matches[1];
            $image_tag   = $matches[2];

             // add the xhtml closing slash, if it's not present,
             // and make sure it has a space before it so wp_kses_hair()
             // will be happy
            $image_tag = preg_replace("%\s*/?\s*>\s*$%", ' />', $image_tag);

            $image_atts  = $this->get_attributes($image_tag);

            // return the untouched markup if we can't find any attributes: no src?
            if (!count($image_atts)) return $orig_markup;

            // merge the image atts with the full class default att list
            // so we don't have to check if key exists
            // [id] =>
            // [src] => http://warriorself.com/images/asia/bangkok_1517.jpg
            // [width] =>
            // [height] =>
            // [alt] =>
            // [title] => Licensed to soak
            // [class] => alignright
            // [usemya] =>
            // [nofx] =>
            // [group] =>
            // [nocap] =>
            // [link] =>
            // [hint] =>
            // [asis] =>
            // [usemysize] =>
            // [page] =>
            $image_atts = array_merge(array('group' => $this->def_group), $image_atts);

            // TODO: still need to get data-image-formatr & others (data-image-formatr-hint etc)

            $image_atts = array_merge(array_fill_keys($this->image_atts, ''), $image_atts);

            // return the original markup (sans asis) if the asis attribute is set
            if ($image_atts['asis'])
                return $this->get_rid_of_attr($orig_markup, 'asis');

            // return the untouched markup if the image style contains
            // an excluded class
            if ($this->exclude_classes)
                foreach (explode(' ', $image_atts['class']) as $class)
                    if (in_array(trim($class), $this->exclude_classes))
                        return $orig_markup;

            // return nothing if we're not on the right page
            // in effect this image gets deleted, why are we doing this?
            if ( ($image_atts['page'] == 'front'   and !is_front_page())
              or ($image_atts['page'] == 'single'  and !is_single()    )
              or ($image_atts['page'] == '!front'  and  is_front_page())
              or ($image_atts['page'] == '!single' and  is_single()    )
              ) return '';

            // add in the href from the surrounding anchor, if any
            $image_atts['anchor'] = $anchor_tag;

            // add the none attribute which comes from the form if they
            // dont want a caption
            $image_atts['none'] = "";

            // personal fix for my website, force all parent-relative urls
            // to be root-relative instead, i.e. change ../ to /
            if ( substr($image_atts['src'], 0, 3) == "../" and $this->get_option('force') )
                $image_atts['src'] = substr($image_atts['src'], 2);

            // Flickr hack
            if ($image_atts['flickr']) {
                if ( $this->load_flickr_data() and array_key_exists($image_atts['flickr'], $this->flickr_photos) ) {
                                               $image_atts['src'  ] = $this->flickr_photos[$image_atts['flickr']]['url_l'];
                   #if (!$image_atts['thumb']) $image_atts['thumb'] = $this->flickr_photos[$image_atts['flickr']]['url_m'];
                   #if (!$image_atts['title']) $image_atts['title'] = $this->flickr_photos[$image_atts['flickr']]['tags'];
                    if (!$image_atts['alt'  ]) $image_atts['alt'  ] = $this->flickr_photos[$image_atts['flickr']]['tags'];
                }
            }

            // remove any css classes we don't want
            if ($image_atts['class'])
                foreach ($this->remove_classes as $class)
                    $image_atts['class'] = str_replace($class, "", $image_atts['class']);

            return $this->format($image_atts, $image_tag);
        }

        /**
         * Format the html output
         *
         * @param array $param The image attributes/parameters as an associative array
         * [alt] => IMG_4308
         * [asis] =>
         * [class] => attachment-thumbnail
         * [flickr] =>
         * [group] => main
         * [height] => 150
         * [hint] =>
         * [id] =>
         * [link] =>
         * [nocap] =>
         * [nofx] =>
         * [page] =>
         * [src] => /wp-content/uploads/2014/05/IMG_4308-150x150.jpg
         * [thumb] =>
         * [title] =>
         * [usemya] =>
         * [usemysize] =>
         * [width] => 150
         * [anchor] => <a href='/wp-content/uploads/2014/05/IMG_4308.jpg'>
         * [none] =>
         *
         * @param string $image The image tag markup, with closing slash
         * <img src="..." .../>
         *
         * @return string The screen markup
         */
        function format ( $param, $image )
        {
            // setup dimensions width & height /////////////////////////////////

            // default dimensions
            if ($param['usemysize']) {
                $img_width  = $param['width'];
                $img_height = $param['height'];
            } else {
                $img_width  = $this->def_img_width;
                $img_height = $this->def_img_height;
            }

            // re-link gallery images
            if ($param['class'] == 'attachment-thumbnail' and !$param['thumb']) {
                preg_match("%href='([^']+)'%", $param["anchor"], $matches);
                if ($matches)
                    $param['thumb'] = $param['src'];
                    $param['src'] = $matches[1];
            }

            // collect the actual image dimensions
            if ($this->get_option('inspect')) {
                // first load the image dimensions
                #list($img_width, $img_height, $img_type, $img_attr) = getimagesize($param['src']);
                // [0] => 1000
                // [1] => 668
                // [2] => 2
                // [3] => width="1000" height="668"
                // [bits] => 8
                // [channels] => 3
                // [mime] => image/jpeg
                $img_size = $this->get_image_size($param['src']);
                if ( (is_array($img_size)) and (count($img_size) > 1) ) {
                    if (!$param['usemysize'])
                        if ($img_size[0] > $img_size[1]) {
                            $img_width  = $this->def_img_long;
                            $img_height = $this->def_img_short;
                        } else {
                            $img_width  = $this->def_img_short;
                            $img_height = $this->def_img_long;
                        }
                    if (!$img_width  and $img_size[1]) $img_width  = intval($img_height * $img_size[0] / $img_size[1]);
                    if (!$img_height and $img_size[0]) $img_height = intval($img_width  * $img_size[1] / $img_size[0]);
                }
            }

            // setup image dimension print variables ///////////////////
            if ($param['usemysize']) {
                $width  = $img_width   ? "width: {$img_width}px;"   : "";
                $height = $img_height  ? "height: {$img_height}px;" : "";
            } else {
                $width  = $img_width   ? "max-width: {$img_width}px;"   : "";
                $height = $img_height  ? "max-height: {$img_height}px;" : "";
            }

            // setup source & id print variables ///////////////////////
            $thumb = $param['thumb'] ? $param['thumb'] : $this-> get_attachment_url($param);
            $src = $thumb ? $thumb : $param['src'];
            $id  = $param['id'] ? "id=\"{$param['id']}\""  : "";

            // edit title print variable ///////////////////////////////
            if (!$param['usemya'])
                $title = "title=\"{$param['title']}\"";
            if ($param['hint'])
                $title = "title=\"{$param['hint']}\"";
            elseif (!$param['nofx'])
                $title = "title=\"{$this->new_title}\"";
            if ($this->strip_title)
                $title = "";

            // setup caption print variable ////////////////////////////
            $caption = "";
            if (array_key_exists($this->caption_att, $param) and $this->caption_att != 'x')
              $caption = $param[$this->caption_att];
            if ($param['nocap'])
              $caption = "";
            if ($caption and $param['link'])
              $caption = "<a href=\"{$param['link']}\" target=\"_blank\">$caption</a>";
            if ($caption)
              $caption = "<div class=\"$this->cap_class\" style=\"width:100%\">$caption</div>";

            // setup effect print variable /////////////////////////////
            $effect = "";
            if ($this->get_option('dofx'))
              $effect = <<< EFFECT
                rel="prettyPhoto[{$param['group']}]"
EFFECT;
            if ($param['nofx'])
              $effect = "";

            // setup anchor print variable /////////////////////////////
            $anchor = $anchor_close = "";
            if (!empty($param['anchor']))
              if ( ($param['usemya']==true) or (!$this->get_option('killanc')) )
                $anchor = $param['anchor'];
            if (!$anchor)
              if (!$param['nofx'])
              $anchor = <<< ANCHOR
                <a
                  href="{$param['src']}"
                  title="{$param['title']}"
                  $effect
                >
ANCHOR;
            if ($anchor)
              $anchor_close = "</a>";

            $class = trim("{$this->add_class} {$param['class']}");
            $container_style = '';

            if ($this->get_option('stdthumb')) {
                $container_style = 'style="width:{$img_width}px"';
                $image = <<< IMAGE
                  <img src="$src" alt="{$param['alt']}" style="$width $height" $title/>
IMAGE;
            }

            return <<< DIV_A_IMG
              <div $id class="$class" $container_style>
                $anchor$image$anchor_close
                $caption
              </div>
DIV_A_IMG;
        }

    } //End Class ImageFormatr

} //End class_exists check
