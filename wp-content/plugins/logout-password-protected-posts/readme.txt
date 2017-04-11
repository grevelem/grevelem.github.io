=== Logout Password Protected Posts ===
Contributors: johnbillion
Tags: password, logout
Requires at least: 3.4
Tested up to: 3.5
Stable tag: trunk

Provides a template tag for a link for visitors to log out of password protected posts.

== Description ==

There is no built-in way for your visitors to "log out" of password protected posts once they've entered the password. Even logged in users cannot log out of password protected posts by logging out of their account. This plugin solves that problem by providing a link for your visitors to log out of password protected posts.

After installing the plugin, add <code><?php do_action('posts_logout_link'); ?></code> somewhere in your theme to display a link for logging out of password protected posts.

== Installation ==

You can install this plugin directly from your WordPress dashboard:

 1. Go to the *Plugins* menu and click *Add New*.
 2. Search for *Logout Password Protected Posts*.
 3. Click *Install Now* next to the Logout Password Protected Posts plugin.
 4. Activate the plugin.
 5. Add <code><?php do_action('posts_logout_link'); ?></code> somewhere in your theme.

Those people who are logged in to password protected posts will now see a link to log out.

For manual installation, see the guide to [Manually Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

= I can't see a link to log out. What's up? =

Have you added the template tag somewhere in your theme? You need to add <code><?php do_action('posts_logout_link'); ?></code> somewhere in your theme for the link to show up.

= I've added the template tag to my theme but I can't see the log out link. What's up? =

Ensure that you have entered a password for a password protected post. The link will not show up if you're not logged into a password protected post.

= Can I change the default text in the link? =

Sure. Add a second parameter to the template tag with the text you'd like instead. For example: <code><?php do_action('posts_logout_link','Log out!'); ?></code>

For those who want even more control, you can also add a third paramter which will be used as the class name on the link element.

== Upgrade Notice ==

= 0.2 =
* Better compatibility with WordPress 3.4. Only show the log out link on posts which are password protected.

== Changelog ==
== Changelog ==

= 0.2 =
* Better compatibility with the post password system WordPress 3.4.
* Only show the log out link on posts which are password protected.

= 0.1 =
* Initial release.
