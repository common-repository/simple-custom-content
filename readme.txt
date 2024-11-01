=== Simple Custom Content ===

Plugin Name: Simple Custom Content
Plugin URI: https://perishablepress.com/simple-custom-content/
Description: Easily add custom content to your WP Posts and RSS Feeds.
Tags: content, custom content, feeds, posts, rss
Author: Jeff Starr
Author URI: https://plugin-planet.com/
Donate link: https://monzillamedia.com/donate.html
Contributors: specialk
Requires at least: 4.6
Tested up to: 6.7
Stable tag: 20241010
Version:    20241010
Requires PHP: 5.6.20
Text Domain: simple-custom-content
Domain Path: /languages
License: GPL v2 or later

Easily add custom content to your WP Posts and RSS Feeds.



== Description ==

[Simple Custom Content](https://perishablepress.com/simple-custom-content/) (SCC) enables you to add custom content to all of your WP Posts and RSS Feeds. Additionally, SCC provides several shortcodes for adding custom content to individual Posts, Pages, or any location in your theme template. This plugin is ideal for adding copyright information, official policies, disclaimers, credits, thank-you messages, custom links, special offers, and anything you can imagine.

**Features**

* Custom content can be text and/or markup
* Display custom content automatically on all WP Posts
* Display custom content automatically on all RSS Feeds
* Optionally display custom content only in Post Excerpts
* Optionally display custom content only in Feed Excerpts
* Provides setting to reset all plugin options to default values
* Provides Shortcodes to add custom content to Posts and Pages
* Specify location of custom content (before or after content)
* Works perfectly with or without Gutenberg Block Editor
* NEW! Option to limit custom content to WP Posts
* NEW! Option to allow custom content on WP Pages

**Automatic Custom Content**

For each of the automatic inclusion methods (WP Posts and RSS Feeds), you can specify where you would like to display the custom content:

* Before content
* After content
* Both before and after
* Do not display (disable)

**Post-Specific Custom Content**

Here is a summary of the SCC Shortcodes, which may be used to display your custom content based on where it is viewed:

* `[scs_post]` - display custom content for single posts
* `[scs_feed]` - display custom content for RSS feeds
* `[scs_both]` - display custom content for single posts &amp; feeds
* `[scs_alt]`  - displays content wherever shortcode is included

**Dynamic Post Shortcodes**

Customize your content with any of the following post variables:

	%%id%%        = Post ID
	%%date%%      = Post Date
	%%title%%     = Post Title
	%%author%%    = Post Author
	%%permalink%% = Post URL
	%%year%%      = Current year

You can use any of these shortcut variables in any of your custom content. More info provided in the plugin settings.

[Check out the screenshot](https://wordpress.org/plugins/simple-custom-content/screenshots/) to get a better idea of how it works.

**Privacy**

This plugin does not collect or store any user data. It does not set any cookies, and it does not connect to any third-party locations. Thus, this plugin does not affect user privacy in any way.

Simple Custom Content is developed and maintained by [Jeff Starr](https://twitter.com/perishable), 15-year [WordPress developer](https://plugin-planet.com/) and [book author](https://books.perishablepress.com/).

**Support development**

I develop and maintain this free plugin with love for the WordPress community. To show support, you can [make a donation](https://monzillamedia.com/donate.html) or purchase one of my books: 

* [The Tao of WordPress](https://wp-tao.com/)
* [Digging into WordPress](https://digwp.com/)
* [.htaccess made easy](https://htaccessbook.com/)
* [WordPress Themes In Depth](https://wp-tao.com/wordpress-themes-book/)
* [Wizard's SQL Recipes for WordPress](https://books.perishablepress.com/downloads/wizards-collection-sql-recipes-wordpress/)

And/or purchase one of my premium WordPress plugins:

* [BBQ Pro](https://plugin-planet.com/bbq-pro/) - Super fast WordPress firewall
* [Blackhole Pro](https://plugin-planet.com/blackhole-pro/) - Automatically block bad bots
* [Banhammer Pro](https://plugin-planet.com/banhammer-pro/) - Monitor traffic and ban the bad guys
* [GA Google Analytics Pro](https://plugin-planet.com/ga-google-analytics-pro/) - Connect WordPress to Google Analytics
* [Simple Ajax Chat Pro](https://plugin-planet.com/simple-ajax-chat-pro/) - Unlimited chat rooms
* [USP Pro](https://plugin-planet.com/usp-pro/) - Unlimited front-end forms

Links, tweets and likes also appreciated. Thank you! :)



== Installation ==

**Installation**

1. Upload the plugin to your blog and activate
2. Visit the settings to configure your options

[More info on installing WP plugins](https://wordpress.org/support/article/managing-plugins/#installing-plugins)


**Example**

For example, to display a copyright statement, you can add something like this:

`<p>Copyright %%year%% My Company dot com</p>` 

You can add that snippet to any of the "Custom Content" settings. So you can include it on posts, pages, feeds, and in the header and/or footer, or exactly wherever is required.

Simple Custom Content enables automatic and post-specific custom content. Both of these are discussed below.


**Automatic Custom Content**

To display your custom content automatically, visit the "Automatic Custom Content" settings. There you can enter custom content for WP Posts and RSS Feeds. And for each, you have the following options:

* Before content
* After content
* Both before and after
* Do not display (disable)

Check out the plugin settings for more infos.


**Post-Specific Custom Content**

To display custom content only on specific posts, visit the "Post-Specific Custom Content" settings. There you can define custom content for any of the following shortcodes:

* `[scs_post]` - custom content for single posts
* `[scs_feed]` - custom content for RSS feeds
* `[scs_both]` - custom content for single posts and feeds
* `[scs_alt]`  - content wherever shortcode is included

You can add any of these shortcodes to your posts, pages, or any custom post type. Visit the plugin settings for more infos.


**Dynamic Post Shortcodes**

Customize your content with any of the following post variables:

	%%id%%        = Post ID
	%%date%%      = Post Date
	%%title%%     = Post Title
	%%author%%    = Post Author
	%%permalink%% = Post URL
	%%year%%      = Current Year (e.g., for copyright)

You can use any of these shortcut variables in any of your custom content. More info provided in the plugin settings.


**Like the plugin?**

If you like Simple Custom Content, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/simple-custom-content/reviews/?rate=5#new-post). It helps to keep development and support going strong. Thank you!


**Upgrades**

To upgrade SCC, remove the old version and replace with the new version. Or just click "Update" from the Plugins screen and let WordPress do it for you automatically.

__Note:__ uninstalling the plugin from the WP Plugins screen results in the removal of all settings from the WP database. 


**Restore Default Options**

To restore default plugin options, either uninstall/reinstall the plugin, or visit the plugin settings &gt; Restore Default Options.


**Uninstalling**

Simple Custom Content cleans up after itself. All plugin settings will be removed from your database when the plugin is uninstalled via the Plugins screen.



== Upgrade Notice ==

To upgrade SCC, remove the old version and replace with the new version. Or just click "Update" from the Plugins screen and let WordPress do it for you automatically.

__Note:__ uninstalling the plugin from the WP Plugins screen results in the removal of all settings from the WP database. 



== Screenshots ==

1. Simple Custom Content: Plugin Settings (panels toggle open/closed)

More screenshots and info available at the [SCC Homepage](https://perishablepress.com/simple-custom-content/#screenshots)



== Frequently Asked Questions ==

**How do I change the priority of the custom content filter?**

You can use the `scs_content_priority` filter hook, for example you can add the following code via your theme functions.php, or add via [custom plugin](https://digwp.com/2022/02/custom-code-wordpress/):

`function scs_custom_content_priority() { return 999; }
add_filter('scs_content_priority', 'scs_custom_content_priority');`

This can help if you want to display your custom content at the very end of the post, after any content that may be added via other plugins, etc.


**Questions? Feedback?**

Send any questions or feedback via my [contact form](https://plugin-planet.com/support/#contact). Thanks! :)



== Changelog ==

If you like Simple Custom Content, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/simple-custom-content/reviews/?rate=5#new-post). It helps to keep development and support going strong. Thank you!


**20241010**

* Tests on WordPress 6.7


Full changelog @ [https://plugin-planet.com/wp/changelog/simple-custom-content.txt](https://plugin-planet.com/wp/changelog/simple-custom-content.txt)
