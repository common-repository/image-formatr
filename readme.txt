=== Image Formatr ===
Contributors: huntermaster
Tags: images, flickr, caption, formatting, post, page
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=sroth77@gmail.com&item_name=Image+Formatr+Wordpress+plugin
Requires at least: 2.9
Tested up to: 4.4
Stable tag: 1.2.6
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Formats all content images on a page / post giving them captions and popups.

== Description ==

Image Formatr is a simple plugin that goes through all the content images on
posts & pages, and with zero user changes:

  1. gives them a standardized thumbnail format using CSS
  2. puts a caption underneath each one using the title
  3. makes them linked so they popup in full size

Thumbnails are not generated, but will be used if available in the media library.
This plugin is driven by the [prettyPhoto](http://www.no-margin-for-errors.com/projects/prettyPhoto/)
library so we could call this plugin a prettyPhoto integration plugin.

*Note: Supports images on **Flickr**.

= Usage =

*This only applies to the images you put in your content, not theme graphics.*

    <img
      src="/images/picture.jpg"
      class="alignright"
      title="A sample caption"
      link="http://example.com/"
      hint="Image borrowed from example.com"
    />

After the plugin runs, the output to the browser looks like:

    <div class="img alignright">
      <a
        href="/images/picture.jpg"
        rel="prettyPhoto[main]">
        <img
          src="/images/picture.jpg"
          title="Image borrowed from example.com" alt=""
          width="140" height="90"/>
      </a>
      <div style="width: 100%;">
        <a href="http://example.com/" target="_blank">A sample caption</a>
      </div>
    </div>


= Documentation =

You can find plugin documentation at http://warriorself.com/blog/about/image-formatr/

= Features =

  * Standardizes all thumbnails without altering posts
  * Supports displaying images from Flickr: &lt;img flickr="1234567890"
    title="The magnificent Ceiba at the Archaeological Site of Palenque."&gt;
  * Generates image captions using the image `title` or `alt`
  * Integrates Wordpress media library thumbnails
  * Gives popups for Wordpress gallery images (adjust priority setting to 12)
  * Shows all content images on the blog as small thumbnails
    (does not create new thumbnails but can use Media Library for thumbs)
  * Allows for fine-grained control of each image's format
  * Zooms image to large size when clicked using the
    [prettyPhoto](http://www.no-margin-for-errors.com/projects/prettyPhoto/)
    library
  * Outputs standard XHTML compliant markup

== Gallery Support ==

To get Wordpress Gallery images to popup, this plugin must run at a priority
greater (later) than the [gallery] shortcode which runs at priority 11.  Therefore
set the <code>priority</code> setting to 12 in the Administration Options.

== Overrides ==

The actions of the plugin are enabled and disabled with administration settings
but can be overridden on each individual image.

  * **`usemysize`** - true/false - *true = do not ignore an image width and height*
  * **`usemya`**    - true/false - *true = do not ignore a parent anchor tag*
  * **`nocap`**     - true/false - *true = do not create a caption*
  * **`nofx`**      - true/false - *true = no popup effect*
  * **`link`**      - string url - *make the caption a link to the url*
  * **`hint`**      - string txt - *this will be the new image title*
  * **`asis`**      - true/false - *true = don't change nuthin*
  * **`group`**     - string txt - *separate popup slideshows*
  * **`thumb`**     - string url - *image thumbnail* (version 0.9.7)
  * **`page`**      - single/!single/front/!front - *page filtering* (version 0.9.7)

If you want to surround an image with an anchor tag `<a>`, then you should add
a `usemya` attribute within the image tag or else your anchor will be ignored
and replaced.  If you do not want the popup effect at all, add a `nofx` attribute
to the image.  If you do not want any caption, you can specify `nocap`, or just
leave the title blank. And to have the plugin completely ignore an image and
output the content directly from the post, use the `asis` attribute.

**Example**

    <a href="http://www.example.com/">
    <img
      src="/images/picture.jpg"
      title="Click to visit website"
      nocap="true"
      usemya="true"
    /></a>

Note: concerning the *true/false* overrides, do not include "false" parameters
like `<img usemya="false">`, i.e these overrides should only include the attributes
for which you want to designate a "true" value.

== Credits ==

Image Formatr is Copyright 2014 [Steven Almeroth](mailto:sroth77@gmail.com) and
licensed under a GPL license.

Based on: [image-caption](http://wordpress.org/extend/plugins/image-caption/)
by [Yaosan Yeo](http://www.channel-ai.com/blog/).

PrettyPhoto: The JavaScript
[Image thumbnail viewer](http://www.no-margin-for-errors.com/projects/prettyPhoto/)
library by Stephane Caron is licensed under GPLv2.

== Installation ==

Installation:

  1. Download and extract the plugin to your computer
  1. Extract the files keeping the directory structure in tact
  1. Upload the extracted directory (image-formatr) to your WordPress plugin
  directory (wp-content/plugins)
  1. Activate the plugin through the *Plugins* menu in WordPress admin

== Screenshots ==

1. Image Formatr administration screen

== Website ==

More information, including how to contact me, is available at
[warriorself.com](http://warriorself.com/blog/about/image-formatr/).

== Frequently Asked Questions ==

1.) *Wordpress "smiley" emoticons like `:)` keep showing up with the other
images.  How can I prevent smileys from being effected?*

Starting with version 0.9.6 this plugin includes a *class exclusion list* which
prevents an image from being processed by the plugin if it contains a CSS class
that is in the list.  Wordpress uses "wp-smiley" for their smileys so enter
`wp-smiley` into the exclusion classes in the Wordpress administration settings
for Image-formatr then click "Update Options".

2.) *These image attributes (e.g. page, nocap, link, etc) are not XHTML
standard attributes.  Why do you use them?*

The Image Formatr *override* attributes do not get written to the browser.
They are only used by the plugin for format configuration of individual
images.  Unless you specify the `asis` attribute, all images in your content
are deconstructed then rebuilt sending only XHTML compliant markup to the
client.

== Translations ==

* If you would like to contribute a translation of the administration settings
screen please have a look at the
[.pot](http://plugins.svn.wordpress.org/image-formatr/trunk/languages/image-formatr.pot)
file and post to the [support forum](https://wordpress.org/support/plugin/image-formatr)
of [e-mail to me](mailto:sroth77@gmail.com).

== ToDo List ==

* add screenshot of output image with caption

== Wish List ==

* phone-home feature, activate/deactivate stats helper with version number
* add admin option for html/xhtml &lt;img/&gt; closing tags
* add admin option for moving title attribute to alt attribute should it
overwrite an existing alt?
* debug mode could show images not found and whatnots and profiling stats
* change [flickrset id="1234"] to [flickr set="1234"]
* change [flickr pid="123"] to [flickr img="123"]
* show "the_content" ordering vis a vie wp-hooks-filters-flow.php
    Priority 8 :
        WP_Embed-&gt;run_shortcode()
        WP_Embed-&gt;autoembed()
    Priority 10 :
        wptexturize()
        convert_smilies()
        convert_chars()
        wpautop()
        shortcode_unautop()
        prepend_attachment()
    Priority 11 :
        capital_P_dangit()
        do_shortcode()
    Priority 20 :
        ImageFormatr-&gt;filter()

== Bug List ==

* bug: add_settings_field() &lt;label for="s"&gt; not &lt;label for="stdthumb"&gt;
    work-around is to only use unique single char id fields defined as constants

== Changelog ==

= 1.2.6 =
  * 2015-12-13 Flickr https API
  * Fix Flickr API calls to use https instead of http

= 1.2.5 =
  * 2014-07-02 Standardize-thumbnails patch
  * Fix Standardize-thumbnail setting, redeux

= 1.2.4 =
  * 2014-07-01 Internationalization
  * Add Serbian translation of admin screen
  * Fix Standardize-thumbnail setting
  * Add license file & info

= 1.2.3 =
  * 2014-05-20 PrettyPhoto settings admin
  * Fix priority default when upgrading from previous version
  * Fix PHP notice when not using captions
  * Add admin options for PrettyPhoto: social buttons, theme, speed
  * Add admin option validation for priority setting
  * Changed admin styling to use retina icons
  * Changed prettyPhoto social buttons option logic, simplified
  * Changed default prettyPhoto popup theme

= 1.2.2 =
  * 2014-05-14 Plugin priority patch
  * Add admin option to adjust the plugin execution order, no longer hard-coded
  * Fix activation warning, default options-collection to array
  * Fix Media Library thumbnail display

= 1.2.1 =
  * 2014-05-11 Wordpress Gallery support
  * Add [gallery] shortcode support, looks for "attachment-thumbnail" class
  * Add settings link in Admin plugins list
  * Change plugin priority from 10 to 12
  * Remove explicit support for BBcode (popups still work)

= 1.1 =
  * 2013-04-12 Media library image thumbnail support
  * Add admin option to enable using media library auto-generated images as
    thumbnails

= 1.0.1 =
  * 2013-03-25 Dynamic filter loading removal patch
  * reverse moving *add_filter* from *image-formatr* to *class.formatr*
  * remove admin option to toggle processing of page/post content
  * remove admin option to toggle processing of WP Text Widget content
  * remove usage of *output buffering* to build the image div
  * fix *Caption attribute* drop-down to properly show the option value
  * move *activate/deactivate* callbacks from class.admin to class.base

= 1.0 =
  * 2013-03-24 Admin UI layout with info popups
  * redo Admin with a better UI using settings information popups
  * add admin option for name to use for default prettyPhoto slideshow group
  * add admin option to toggle processing of page/post content
  * add admin option to toggle processing of WP Text Widget content
  * fix Flickr loop bug if we could not open the image url

= 0.10.1 =
  * 2012-12-12 Remove asis attribute and add caption css class
  * Remove the asis attribute if provided
  * add php5 constructor, move old constructor to base
  * add admin option to add css-class to caption div
  * simplify width styling

= 0.10.0 =
  * 2012-11-25 Add Flickr support and remove Highslide library
  * The Highslide JavaScript image viewer library had a license that was not
    compatible so it was replaced with the PrettyPhoto library
  * Flickr image support with [flickr pid="123"] or <img flirckr="123">
  * Flickr set support with [flickrset id="123"]
  - Note: [flickrset id="123"] usage is deprecated and will become [flickr
    set="123"] next release

= 0.9.7.5 =
  * 2011-11-03 BBCode [img] support
  * Added: support for [img]http://mydomain.com/image.jpg[/img]
  * Added: CSS image class modification options

= 0.9.7.4 =
  * 2011-07-17 Small images not up-sized
  * Changed css style width to max-width

= 0.9.7.3 =
  * 2011-06-12 Internet Explorer patch
  * Fixed: JavaScript obect trailing comma removed from hs.addSlideshow() call
  * Added: Highslide library disable option if you already use Highslide
  * Changed: Auto determine orientation admin setting now defaults to off

= 0.9.7 =
  * 2011-03-21 Administration upgrade version
  * Fixed: style-sheet displayed correctly allowing height attribute without width
  * Fixed: trailing slash on image tag no longer required &lt;img/&gt;
  * Fixed: admin options for thumbnail dimensions UI bug
  * Fixed: image aspect ratio now correctly calculated
  * Added: 'thumb' attribute to show a thumbnail image
  * Added: 'page' attribute to allow for image to be excluded from certain pages
  * Added: uninstall plugin option which can clean Image Formatr out of the database
  * Changed: home page image dimensions expanded to include single page, etc.
  * Changed: admin options to serialize all settings into one table row
  * Changed: class getting kinda big so I split it up into three classes
  * Changed: admin screen updated to current Wordpress API standards
  * Removed: admin html include file no longer needed with new API

= 0.9.6 =
  * 2011-02-13 Smiley exclusion patch
  * Added: Exclude image style class list which prevents smileys from being included
  * Added: import Highslide Integration plugin settings upon Image Formatr activation
  * Added: forgot to include graphics directory in 0.9.5 release

= 0.9.5 =
  * 2011-01-18 Highslide integrated directly into Image-formatr plugin
  * Added: Admin option for the mouse-over hint "Click here to enlarge"
  * Added: Admin option to use different image dimensions on the home page
  * Added: Admin option for Highslide settings
  * Added: restrict image url protocols to http, https, ftp, file
  * Added: "Group" image attribute can separate Highslide popup slideshows
  * Added: Highslide JavaScript library w/gallery (highslide.js) 4.1.9 (2010-07-05)
  * Removed: Highslide Integration plugin requirement
  * Changed: "Strip title" admin option now actually strips the title
  * Changed: "Hint" image attribute gets displayed differently

= 0.9.4 =
  * 2011-01-13 Class structure used and performance increased
  * Added: Class structure encapsulation
  * Added: Admin setting to disable image inspection (speed increase)
  * Changed: Admin setting "Thumbnail dimensions" to allow zero,
  which then calculates based on aspect ratio
  * Removed: PHP-GD library call and Snoopy call (speed increase)

= 0.9.3 =
  * 2011-01-13 Bugfix patch
  * Changed: admin settings bug fixes

= 0.9.2 =
  * 2010-04-16 Smiley/emoticon displayed
  * Changed: Smileys like :) were causing errors so I added a check to make
  sure we are not effecting emoticon graphics within the post.  Now smileys
  display fine, thanks to http://blog.andrewkinabrew.com/

= 0.9.1 =
  * 2010-03-9 Standard thumbnail dimensions
  * Changed: Allow for zero length long or short thumbnail dimension in the
  administration settings

= 0.9 =
  * 2010-01-26 Initial beta release
  * Renamed the `usemyanchor` image modifier attribute to `usemya` with no
  deprecated support for the old one
  * Fixed: caption administration setting not working
  * Added: force-root image location mangler administration setting
  * Added: administration setting to standardize all thumbnail sizes
  * Added: `usemysize` attribute to allow for individual image sizing
  * Changed: no longer supports MyCSS plugin

= 0.8 =
  * 2010-01-5 Initial alpha release

== Upgrade Notice ==

= 1.2 =
Wordpress Gallery support

= 1.1 =
Media library image thumbnail support

= 1.0 =
Admin UI restructuring using settings information popups
