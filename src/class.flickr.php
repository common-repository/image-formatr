<?php
require_once(dirname(__FILE__) . '/class.admin.php');

if (!class_exists("ImageFormatrFlickr")) {
    class ImageFormatrFlickr extends ImageFormatrAdmin {

        /**
         * Request and load the data from Flickr which we only want to do
         * once.  Unfortunately I could not find an API call to search for
         * a list of Ids, therefore we're searching for all damn photos for
         * our user and then indexing those for the ones we want.
         */
        function load_flickr_data ( )
        {
            if ( $this-> flickr-> loaded) return true;
            if (!$this-> flickr-> enable) return false;
            $this-> flickr-> loaded = true;
            $this-> flickr_photos = array();
            $page = 0;

            $params = array(
                'user_id'        => $this-> flickr-> nsid,
                'auth_token'     => $this-> flickr-> token,
                'extras'         => 'last_update,tags,url_m,url_l',
                'privacy_filter' =>  1, // 1 == public photos
                'content_type'   =>  1, // 1 == photos only
                'per_page'       =>  500, // we can only return 500 at a time max
                );
            $flickr_photos = array();
            $finished = false;
            while (!$finished) {
                $params['page'] = ++$page;
                $response = $this-> call_flickr_api('flickr.photos.search', $params, true );
                if ($response['stat'] == 'ok' and
                    $response['photos']['photo']) {
                        $flickr_photos = array_merge ($flickr_photos, $response['photos']['photo']);
                }
                if (count($response['photos']['photo']) < 500)
                    $finished = true;
            }

            foreach ($flickr_photos as $photo) {
                $this-> flickr_photos[$photo['id']] = $photo;
            }

            return count($this->flickr_photos);
        }

        /**
         * Process the [flickr] shortcodes
         *
         * [0] => [flickr pid="5496015411"]
         * [1] => 5496015411
         */
        function do_shortcode_flickr ( $matches )
        {
            if( count($matches) < 2 ) return '';
            if( !$this->load_flickr_data() ) return '';

            #return do_shortcode($matches[0]);
            if( array_key_exists( $matches[1], $this->flickr_photos ) )
                $html = <<< IMAGE
                    <img src   ="{$this->flickr_photos[$matches[1]]['url_l']}"
                         thumb ="{$this->flickr_photos[$matches[1]]['url_m']}"
                         alt   ="{$photo['tags']}"
                    />
IMAGE;
            return $html;
        }

        /**
         * Process the [flickrset] shortcodes
         *
         * [0] => [flickrset id="72157626094257379"]
         * [1] => 72157626094257379
         */
        function do_shortcode_flickrset ( $matches )
        {
            if( count($matches) < 2 ) return '';

            $params = array(
                'photoset_id'    => $matches[1],
                'auth_token'     => $this->flickr->token,
                'extras'         => 'last_update,tags,url_sq,url_l',
                'privacy_filter' =>  1, // 1 == public photos
                );
            $photoset = $this-> call_flickr_api( 'flickr.photosets.getPhotos', $params, true );

            $html = '';
            foreach ($photoset['photoset']['photo'] as $photo) {
               #$src = $this->flickr_core->getPhotoUrl($photo);
                $html .= <<< IMAGE
                    <img src    ="{$photo['url_l']}"
                         thumb  ="{$photo['url_sq']}"
                         alt    ="{$photo['tags']}"
                         width  ="{$photo['width_sq']}"
                         height ="{$photo['height_sq']}"
                         usemysize="true"
                    />
IMAGE;
            }
            return $html;
        }

        /**
         * Flickr
         * borrowed from Trent Gardner's Flickr Manager
         */
        function getRequest ( $url )
        {
            $rsp_obj = false;

            // try curl if we have it
            if (function_exists('curl_init')) {
                $session = curl_init($url);
                curl_setopt($session, CURLOPT_HEADER, false);
                curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($session);
                if (curl_errno($session) == 0)
                    $rsp_obj = unserialize($response);
                curl_close($session);
            }

            // fallback to php fopen
            else {
                $handle = fopen($url, "rb");
                if ($handle) {
                    $contents = '';
                    while (!feof($handle)) {
                        $contents .= fread($handle, 8192);
                    }
                    fclose($handle);
                    $rsp_obj = unserialize($contents);
                }
            }
            return $rsp_obj;
        }
        /**
         * Flickr
         * borrowed from Trent Gardner's Flickr Manager
         */
        function getSignature ( $params )
        {
            ksort($params);

            $api_sig = $this->flickr->secret;

            foreach ($params as $k => $v){
                $api_sig .= $k . $v;
            }
            return md5($api_sig);
        }
        /**
         * Flickr
         * borrowed from Trent Gardner's Flickr Manager
         */
        function call_flickr_api ( $method, $params, $sign = false, $rsp_format = "php_serial" )
        {
            if (!is_array($params)) $params = array();

            $call_includes = array('api_key' => $this->flickr->apikey,
                                   'method'  => $method,
                                   'format'  => $rsp_format);

            $params = array_merge($call_includes, $params);

            if ($sign) $params = array_merge($params, array('api_sig' => $this->getSignature($params)));

            $url = "https://api.flickr.com/services/rest/?" . http_build_query($params);

            return $this->getRequest($url);
        }

    } //End Class ImageFormatrFlickr

} //End class_exists check
