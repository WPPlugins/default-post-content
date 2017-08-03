=== Default Post Content ===
Contributors: peterebutler
Tags: new post, default content, post meta
Requires at least: 2.7
Tested up to: 2.9.1
Stable tag: 1.2

Tired of typing in the same default content at the end of your post?  Use the same custom fields for each new post?  Make your WordPress install do it for you.

== Description ==

Tired of typing in the same default content at the end of your post?  Use the same custom fields for each new post?  Make your WordPress install do it for you.

**Post Content**

Quickly and easily put together a block of default post text to be pulled up every time you go to write a new post.  The default content box uses the wordpress visual editor - so you can add images, embed video, and style your text just as you would for a normal post.  Save it, and voila - each new post gets the text.

**Post Meta**

You're covered on default post meta as well - use this if your theme uses a thumbnail for each post, and you need a default if you'e got nothing to show,  or if you store a little extra information in the postmeta for each post.

== Installation ==

1. Upload `default_post_content.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the *Plugins* menu in WordPress
3. Visit the *Default Post Content* page under *Settings* to add default post content and default post meta.

== Screenshots ==

1. Default Post Content administration Panel

== History ==
2009-04-14 v1
Initial release 

2009-05-24 v1.1
1. Fixed problems with visual editor and Firefox which prevented posts from being saved properly, and broke kitchen sink button
2. Plugin now removes default post meta when deactivated, so users don't have to do it manually
3. Changing a post meta value on the post-new.php page and hitting update no longer changes the default post meta values - only the values for that post.  New posts will continue to get the values set on the admin page.

2010-01-09 v1.2
1. Fixed Error that shows up when no default post content is set