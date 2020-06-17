=== MZ MBO Access ===
Contributors: mikeill
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=A95ZEELLHGECE
Tags: mindbody, schedule, calendar, yoga, MBO, mindbodyonline, gym, access, restrict
Requires at least: 3.0.1
Tested up to: 5.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Restrict wordpress content based on client Mindbody account details.
Create two access levels based on MBO membership details.
Achieved via shortcode(s) on the page, with MBO login form.
Option for a form which redirects clients to one of three pages: Level 1, Level 2, No Access.

== Description ==

Install and you can limit content based on user MBO memberships:

[mbo-client-access access_levels="1, 2"]
RESTRICTED CONTENT HERE
[/mbo-client-access]

You can also redirect users based on their access level.

== Installation ==

Steps to install and configure MZ MBO Access:

1. If not already, install MZ Mindbody API plugin.
2. Upload the directory, `mz-mbo-access` to the `/wp-content/plugins/` directory
3. Set MBO credentials and access levels in Settings->MZ Mindbody Settings page.
4. Add shortcode as desired, surrounding restricted content.

== Frequently Asked Questions ==

= Coming soon, no doubt. =

== Screenshots ==

1. Admin Tab in MZ Mindbody Access Settings page
2. Mindbody Login Form
3. Welcome, Client. Access denied.
4. Welcome, Client, redirect access denied.
5. Welcome, Client, redirect access level (1 or 2).
6. Logged Out.
7. Access Granted.

== Changelog ==

= v1.0.1 =
Add cache-busting to script.
Add support for overriding buttons.
Add password reset request button.

= v1.0 =
Initial release.

== Upgrade Notice ==

= v1.0.1 =

== Notes ==

None yet. Hopefully will work well.

