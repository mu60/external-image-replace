=== External image replace ===
Contributors: muromuro
Tags: post, update content, update post,
Requires at least: 4.9.2
Tested up to: 4.9.2
Stable tag: 1.0.10
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Replace the external image in the posted article with the media library at once.

== Description ==

Download the image of the external URL present in the article, register it in the media library, and replace the URL with the media library.
It is useful when importing from another blog.

For example, if the URL of the current blog is https://wiredpunch.com, the result is as follows.

＜img src="http://example.com/xxxx.jpg" ＞
↓
＜img src="https://wiredpunch.com/wp-content/uploads/2018/01/xxxx.jpg" /＞

== Installation ==

You can install this plugin directly from your WordPress dashboard:

 1. Go to the *Plugins* menu and click *Add New*.
 2. Search for *External image replace*.
 3. Click *Install Now* next to the *External image replace* plugin.
 4. Activate the plugin.

== Screenshots ==

== Frequently Asked Questions ==

== Changelog ==

= 1.0.10 =
* 2018-06-15
* An error in an environment already using smarty has been improved.

= 1.0.9 =
* 2018-06-14
* An error in an environment already using smarty has been improved.

= 1.0.8 =
* 2018-06-14
* An error in an environment already using smarty has been improved.

= 1.0.7 =
* 2018-01-26
* Fixed an error where translation was not applied correctly.

= 1.0.6 =
* 2018-01-26
* Fixed an error where translation was not applied correctly.

= 1.0.5 =
* 2018-01-26
* When a parameter is attached to the image URL, since the error which failed to download was found, the processing was changed so as to download it after removing the parameter.

= 1.0.4 =
* 2018-01-25
* Changed to read translations from GlotPress.

= 1.0.3 =
* 2018-01-25
* Changed to read translations from GlotPress.

= 1.0.2 =
* 2018-01-24
* We have organized the source code. There is no change in function.

= 1.0.1 =
* 2018-01-22
* Fixed an error that excluded domain settings would be hidden when the corresponding image could not be found.

= 1.0.0 =
* 2018-01-22
* First release
