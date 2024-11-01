=== Adtoniq ===
Contributors: davidadtoniq, jboho
Tags: adblock, adblocker, ad blocker, ad block, ad blocking, blocker, advertisement, ads, trublock
Requires at least: 4.6
Tested up to: 4.9
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Maximize the value of your ad blocked audience.

== Description ==
Adtoniq gives you free tools to monetize your ad blocked audience. Use our messaging, content protection, shortcodes and JavaScript API to create a strategy to address your ad blocked audience.

Sign up for Adtoniq Cloud for access to  adblock analytics.

Highlights:

= See an example =
See a live example at https://adtoniq.io/examples/.

= Messaging =
Use the standard WordPress rich text editor to create a customized message that appears on the bottom of your site to your ad blocked users, asking them to make a choice.

Buttons are automatically added to your message to allow users to opt in or opt out of your choice. The message is displayed until the user makes a choice, which is then recorded in a cookie after which the user no longer sees the message.

= Protection =
When used in conjunction with messaging, this prevents ad blocked users who have not opted in to your messaging from seeing all or parts of your site. Use of protection is optional. Without any protection, you would be relying on the so-called "nice guy" appeal.

= User-Friendly Short Codes =
WordPress shortcodes provide the building blocks so you can offer custom communications to your ad blocked audience. Target your blocked users only, or those blocking ads and analytics, using whatever WordPress content you place within the shortcodes. For example, [adtoniq_message_adblocked]Won't you consider white-listing us?[/adtoniq_message_adblocked] would be displayed only to users with an ad blocker.

= JavaScript API =
Implement advanced adblock strategies in JavaScript by using our JavaScript API. You can hook into various events during ad block detection in order to do things like highly customized messaging, advanced content protection, and more. For example this code displays a message to your blocked users
using JavaScript: adtoniq.onBlocked(function(){alert("Won't you white list us?");});

= Accurate AdBlock Analytics =
Accurately measure your adblock rate without interference from adblockers and then explore different strategies to regain your revenue.  Once you install Adtoniq on your website, it records ad block analytics on every page view, except for those requests that come from robots that identify themselves as such, such as search engines like Google. 

= Automated Updating =
It's a war out there, and ad blockers are evolving every day. Sign up for Adtoniq Cloud to have your adblock detection code updated in realtime as adblock threats evolve.

= On-Call Customer Support and Consultation Services =
Contact Adtoniq at: support@adtoniq.com or 415.340.1949.

== Installation ==
1. Install the plugin through the WordPress plugins screen directly, or upload the plugin files to the `/wp-content/plugins/plugin-name` directory.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Adtoniq screen to access all features
== Screenshots ==
1. Messaging
2. Protection
3. Shortcodes
4. JavaScript API

== Changelog ==

= 4.0.8.4 =
* Switch over to new, non-quota based Adtoniq analytics

= 4.0.8.5 =
* Drop the "Trublock" name and brand
* Fix bug that would clear the Adtoniq Cloud API key when saving protection settings

= 4.0.8.6 =
* Allow for the protection URL to be a full url rather than only a relative path

= 4.0.8.7 =
* Minor bug fixes

= 4.0.8.8 =
* Bug fix for turning protection on, setting a url, then turning it off, and changing messaging to ads only. Message only displays when it should.

= 4.0.9.0 =
* Add new shortcode: adtoniq_clear_choice, to make it easy to clear a user's previous opt in/out and choose again
* Do not delete option variables when upgrading Adtoniq (preserves API key)
* New feature for Adtoniq Cloud customers: Update your ad block defintions on demand (via AJAX call)
* Add new documentation tab
* Switch Dashboard feed to not use CDN to get news faster
* Add AdSense feature (beta level)
* Make Opt Out button optional (and add documentation to that effect)
* Persist last tab opened in a cookie

= 4.0.9.1 =
* Temporarily disable AdSense feature until Beta period is over
* Minor text changes

= 4.0.9.3 =
* Fix minor bug in disabling AdSense

= 4.0.9.6 =
* Allow turning AdSense feature on for customers selected for closed beta

= 4.0.9.7 =
* Show notice with link to get Adtoniq Cloud, for users that have not yet registered.

= 4.0.9.10 =
* Add padding to right of default message.
* Changes to support AdSense closed beta

= 4.0.9.12 =
* Bug fix: Move ad block detection iFrame from head to body

= 4.0.9.13 =
* New feature: By leaving the Accept/Confirm button text empty, you can create a message with no buttons asking users to disable their ad blocker.

= 4.0.9.14 =
* Revert bug fix from 4.0.9.12: Turns out moving the ad block detection iFrame to the body causes issues on larger sites, because the ad block detection iFrame takes too long to load, so in this release we are moving it back to the head section. Since WordPress has no mechanism to insert content at the top of the head section, we are instead relying on a corner of the HTML specification which says that "invalid" content should be moved from the head section to the top of the body section. We now generate our iFrame in the head section, which will generate a W3C validation error, however it's also the best way to resolve all these various competing issues. This works on all browsers because it is relying on part of the W3C standard, though it is technically invalid HTML.

= 4.0.9.15 =
Bug fix for Safari users using the AdSense feature: Turns out Safari does not support the "let" keyword in JavaScript, which is supported by all other browsers. This release changes "let" to "var" to resolve this problem.

= 4.0.9.16 =
Bug fix - send correct plugin version number to server for backwards compatibility with older plugins.
Bug fix - JavaScript for AdSense no longer uses the 'let' keyword, for Safari compatibility

= 4.0.9.17 =
Bug fix - send correct AdSense publisher Id to proxy server.
Support filtering for proxy server.

= 4.0.9.18 =
AdSense ads should display most of the time now.

= 4.0.9.19 =
AdSense bug fix: Fixed case where there was no adtoniq cookie.

= 4.0.9.20 =
New feature: AdSense customers can enter a CSS Selector to identify the ad units on their pages. The default CSS Selector if nothing is entered is 'ins', which targets AdSense elements on your page. You can replace other types of ads with AdSense ads by specifying the ad units in the CSS Selector.

= 4.0.9.21 =
Bug fix for AdSense feature - In some circumstances, JavaScript() result was undefined.

= 4.0.9.23 =
Minor bug fix: Accurately record Adtoniq plugin version number for compatibility reasons

= 4.0.9.24 =
AdSense new feature: Ability to paste your AdSense code snippet to support replacing non-AdSense ad units with Adsense. See latest AdSense documentation for details.
Tweak messaging explanation on messaging tab
Minor bug fix: Trim whitespace around CSS Selector on AdSense tab

= 4.0.9.25 =
On AdSense tab, better validation for CSS Selector

= 4.0.9.26 =
On Google Analytics tab, support removing tracking Id by clearing it out and clicking save

= 4.0.9.28 =
For Google AdSense feature, if no CSS Selector is entered (field left blank), use the following default CSS Selector which should pick up most AdSense and DoubleClick ad units automatically: ins,[id^='div-gpt-ad']. Previously it would only pick up AdSense ad units (ins).

= 4.1.0.10 =
Split Adtoniq into two separate products. This product is now named Adtoniq Pro, and will continue for free, for all WordPress users. Our new product, Adtoniq Express, can be downloaded from our website at www.adtoniq.com and is not only free, but can pay you money!