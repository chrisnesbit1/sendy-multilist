=== Multilist Subscribe for Sendy ===
Contributors: cnesbit, freemius
Tags: Sendy, subscribe, widget, email list, multilist, email marketing, SES, SMTP, post notification
Donate link: https://chrisanesbit.com/multilist-subscribe-for-sendy/
Requires at least: 5.5
Tested up to: 5.7
Requires PHP: 5.4
Stable tag: 1.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Multilist Subscribe for Sendy adds a subscribe widget to your WordPress website so you can send post notifications to all your Sendy.co mailing lists.

== Description ==
Multilist Subscribe for Sendy integrates WordPress post notifications into one or more of your Sendy email marketing campaigns. Setup is easy and Sendy can leverage the power of Amazon's Simple Email Service (SES), or any SMTP relay service, for dependable email marketing.

= How to Use =
1. Navigate to "Sendy Email Templates", then "Add New", to create a Sendy campaign template associated with a post type - Subscribers to this campaign will receive the campaign emails when a new post of that post type is published.
1. Navigate to "Appearance", then "Widgets", and add a "Sendy Multilist" widget to your WordPress website - this is the subscribe form for the Sendy campaigns you created in the previous step.

= Campaign Body Shortcodes =
"Multilist Subscribe, for Sendy" offers a variety of useful shortcode for the body of the campaign template's email message.

**Shortcodes supported only by this plugin, not available to other areas of WordPress**
[post_title] The title of the post you're publishing (this will be included as a hyperlink to your post)
[post_content] The body of your post (WordPress shortcodes, within the post's content, are supported)
[post_excerpt] The excerpt of your post (the entire post_content will be used if no excerpt can be determined. Additionally, WordPress shortcodes, within the post's excerpt, are supported)
[read_more] The words "read more" will be included as a link to your post
[post_url] Just the actual link to your post. Great for use with your own buttons!
[featured_image] the post's featured image will be displayed in the template
[featured_image_url] Just the actual link to the featured image. This can be handy if you need special formatting in the email campaign template.

**Sendy.co Shortcodes (Provided by [Sendy.co](https://sendy.co), and only available to Sendy)**
[Name,fallback=] *This shortcode injects the subscribers name, or a fallback phrase that you specify if no name exists
[Email] *The subscriber's email will be included as a link to open a new email to the reader
[webversion] *A link to the web version of the email, to be viewed in a browser
[unsubscribe] *A link the subscriber can use to unsubscribe from your email list

WordPress shortcodes are also supported in the Email Message Body.

== Installation ==
1. Upload "sendy-multilist.zip" to the "/wp-content/plugins/" directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Navigate to "Sendy Email Templates", then "Add New", to create a Sendy campaign template associated with a post type - Subscribers to this campaign will receive the campaign emails when a new post of that post type is published.
1. Navigate to "Appearance", then "Widgets", and add a "Sendy Multilist" widget to your WordPress website - this is the subscribe form for the Sendy campaigns you created in the previous step.


== Frequently Asked Questions ==
= Does this plugin include Sendy? =
No. Sendy is a separate product created by Sendy.co. You can purchase your own license for the self-hosted installation from Sendy.co and set it up yourself.

= Why aren't my post notifications being sent =
1. Make sure you copy/paste'd your List ID, API Key, etc.. exactly correct.
1. Make sure your Sendy Campaign Template is "Published" and the status is "Active".
1. Check your Permalink Settings (Go to "Settings" > "Permalink") and make sure that "index.php" is not part of your "Common Setting".
1. If everything looks right, but the post notifications are still not being sent, open a support ticket in the plugin's WordPress directory page.

= Where do I find my API Key? =
Login to your Sendy account and click your account name (in the top right corner), then click "Settings".
Your API Key will be on the right-hand side of your Settings page. Copy that key and paste it into the "Sendy API Key" field, in your "Sendy Multilist" Widget.

= Where do I find my List ID? =
1. Login to your Sendy account and click "View All Lists" in the gray box located on the left. (If you have multiple "Brands" in your Sendy account, you must select a brand first).
1. When viewing the "Subscriber Lists" page, in Sendy, the "ID" Column contains the list IDs. Hover over the ID for the list you want to add to WordPress to select it, then use your keyboard to copy that value (Control + C on Windows, Command + C on Mac). Paste that value into the "List ID" field in your Sendy Campaign Template, in WordPress


== Screenshots ==
1. Subscriber widget for multiple Sendy lists
2. List view, of Sendy Campaign Templates
3. Add/Edit a Sendy Campaign Template

== Changelog ==
# 1.6.1
* fix bug caused when editting a page/post in WordPress admin
* fix reference vendor library

# 1.6.0
* add vendor code that went missing

# 1.5.9
* add now-required option to route registration

# 1.5.8
* update WordPress version tested up to

# 1.5.7
* update Freemius library

# 1.5.5
* fix versioning issue

# 1.5.4
* bugfix

# 1.5.3
* replaced PHP curl with standard WordPress code
* updated Subscribe call to include the API call

# 1.5.2
* minor bugfix

# 1.5.1
* added the [post_url], [featured_image], and [featured_image_url] shortcodes, for use in the body of the campaign template

# 1.5.0
* More informative list page for  Sendy Campaign Templates
* Ability to set a Sendy Campaign Template as Active or Inactive
* Only send a campaign the first time a post is published
* Ability to use shortcodes in the body of the campaign email

# 1.0.1 
* Minor changes to simplify code and update readme.

# 0.1 
* Initial release.

== Upgrade Notice ==
* add now-required option to route registration
