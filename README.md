# Leafly Specials WordPress Plugin

![Leafly Specials for WordPress](http://www.wpdispensary.com/wp-content/uploads/2015/11/leafly-specials-wordpress-plugin.jpg)

Easily display your dispensaries specials on your WordPress powered website

## PLEASE READ

**Leafly is closing it's API, so as of 2.29.16, this plugin will no longer work.**

I will be keeping an eye out if an API ever opens back up and I can update this plugin.

As for the plugin itself, the originl README created for it is below.

### Adding your APP ID and KEY

Once you install this plugin, you'll notice a new options page in your WordPress dashboard under the Settings section, titled "Leafly Connect".

On this page, you'll be able to add in your APP ID and KEY, which is needed for the plugin to work.

Not sure where to get your APP ID and KEY?

You get them from the [Leafly Developer](https://developer.leafly.com) area, which lets you sign up for an account and create an app.

When you create the app, you'll be given a KEY and ID to use, which is what you'll need to copy over to this plugin's settings page.

**Caching built in**

Leafly gives their API users a limit of 25 hits per day for their **seed** account, or 60 hits per minute for their **bloom** account.

To help your dispensary utilize this plugin without needing to upgrade to bloom, and taking too many hits to your account, I've built in a cache that refreshes once per hour.

There's nothing that you need to do on your end in order to get this to work, it's baked right in to the plugin - pardon the pun :)

### Widget Options
After you install the Leafly Specials WordPress plugin, you'll be able to add a custom widget to your website's sidebar (or anywhere else that widgets are enabled in your theme).

The widget is colored red, so you'll be able to easily spot it on your widgets page. Drag it into place where ever you'd like it to show, and fill in the options, which you can see to the left.

Here, you can add in your dispensaries URL slug and the amount of specials you'd like to show (limit: 100). 

You can also select to show or hide various other elements (view the screenshots below)

### Shortcode Options

A secondary option built into the plugin to display your specials from Leafly is the shortcode. Sometimes, it might be a better option to show specials on a page of your website (for instance, the home page), so the shortcode will give you all of the flexibility you need.

Here is the basic shortcode:

`[leaflyspecials slug="denver-relief"]`

You will need to add in your slug, just like the widget options. The shortcode will default to showing 5 specials, and all of the options given in the widget (avatar, star rating, detailed rating, recommendation, shop again and comments.

If you'd like to remove some of these options from showing, you can add the option to the shortcode with the value of *no*, like this:

`[leaflyspecials slug="denver-relief" limit="5" avatar="no" stars="no" ratings="no" recommend="no" shopagain="no" comments="no" viewall="no"]`

## Screenshots

![Widget display](http://www.wpdispensary.com/wp-content/uploads/2016/01/leafly-specials-widget-display.jpg) ![Backend widget options](http://www.wpdispensary.com/wp-content/uploads/2016/01/leafly-specials-widget-settings.jpg)