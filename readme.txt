=== Leafly Specials for WordPress ===
Contributors: deviodigital
Tags: leafly, specials, widget, shortcode
Requires at least: 3.0
Tested up to: 4.4.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily display your dispensary specials from Leafly on your own site with a widget or shortcode

== Description ==

Easily display your dispensary specials from Leafly on your own site with a widget or shortcode.

This plugin utilizes the Leafly API to pull your dispensaries specials from the Leafly website and saves them in a cache file to display on your own website through the use of a shortcode, which is placed on a page or post, or a widget which is placed in your sidebar.

You can get your own Leafly API app ID and KEY from the <a href="http://developer.leafly.com" target="_blank">Leafly Developer</a> website.

**Widget Option**

You can drag and drop the Leafly Specials widget to any widgetized area on your website, through the `Appearance > Widgets` section of the admin dashboard. There are options you can fill out to choose what is actually displayed in the widget.

**Shortcode Option**

Here is the basic shortcode:

`[leaflyspecials slug="denver-relief"]`

You will need to add in your slug, just like the widget options. The shortcode will default to showing 5 specials, and all of the options given in the widget (avatar, star rating, detailed rating, recommendation, shop again and comments.

If you'd like to remove some of these options from showing, you can add the option to the shortcode with the value of <em>no</em>, like this:

`[leaflyspecials slug="denver-relief" limit="5" title="yes" details="yes" fineprint="yes" permalink="yes" viewall="no"]`


== Installation ==

1. Upload the `leafly-specials` folder to the `/wp-content/plugins/` directory or add it directly through your WordPress admin dashboard
2. Activate the plugin through the `Plugins` menu in WordPress
3. Go to `Settings > Leafly Connect` and add in your Leafly API ID and KEY (required in order for the plugin to work)
4. Add the Leafly Specials widget through the `Appearance > Widgets` area of your dashboard, or use the shortcode to display your specials on any page or post

== Screenshots ==

1. The widget options that you can use to customize the way your specials display on your website
2. Sample layout of how the Leafly Specials will show (all options are showing in this demo)
