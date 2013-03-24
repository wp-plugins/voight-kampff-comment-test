=== Voight Kampff Comment Test ===
Contributors: jjmartucci
Plugin Name: Voight Kampff Comment Test
Plugin URI: http://www.scientiaest.com/projects/wordpress-plugin-vkct/
Author: Joseph Martucci
Author URI: http://scientiaest.com
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZUDJJN3YTZKE4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: comments, comment form, spam, survey, user input, quiz
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 0.1.1

Add additional fields to your comment forms to prevent spam, survey your users, or quiz them 

before they leave a comment. 

== Description ==

The Voight Kampff test is a fictional test from the book "Do Androids Dream of Electric Sheep", popularized by the movie Blade Runner which relied on a series of questions to determine if a person was really a person, or a replicant, an organic robot that appeared and acted human. The Voight Kampff Comment Test (VKCT) plugin allows you to add additional fields to the comment forms throughout your site to check if your users are human (not spam bots), and even if they're going to leave a relvent comment. These fields can be text fields or dropdowns, and can be:

* Optional - The user can enter information but isn't required to.
* Required - The user must enter information before posting a comment.
* Required and exact - The user must enter information and it must match a preset answer.

This combination of fields allows you to do three things:

* Enact a simple spam filter by adding a required question to your site.
* Gather additional information from your users when they leave a comment.
* Quiz users which a challenge question before they leave a comment to avoid off-topic and unwanted comments.

A default VKCT question can be added to every comment form on your site, or you can use the shortcode `[vkct]` on a per post basis to either override the default question, or apply the VKCT fields where needed. The `[vkct]` shortcode can be called multiple times, allowing you to create as many additional fields as you want.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

By default, the VKCT plugin adds the required question 'Do androids dream of electric sheep?' to each comment section on your site. This can be changed in Plugins > Voight Kampff Comment Test

== Settings ==

= Shortcode Defaults =

The shortcode defaults are both the settings for the default question which will appear on your site and the default settings if you use the `[vkct]` shortcode without any options. 

1. Require answer - If this is checked, a user must enter an answer before leaving a comment.
2. Display with comment - Above | Below | None - This will set where the users entered information appears in their comment. Use None if you don't want it to display.
3. Default Question - The question that will appear by default.
4. Default Answer - If you want to require a near match to the default answer in order to submit a comment, enter it here. If this field is blank, any answer will be accepted.

**Note** - Answers given are done by near match. Common punctuation is stripped and the answer is converted to all lowercase. So, if the answer you are expecting is “Philip K. Dick”, then “philipkdick” or “Philip K Dick” would be accepted, but “Phillip K Dick” would be rejected. 

= Global Defaults =
These settings affect all of the VKCT fields.

1. Use default question - If checked, this will add the default question to every comment section on your site. Uncheck if you only want questions to appear on posts in which you include the `[vkct]` shortcode.
2. Ask logged in users to answer - Never | When using shortcode | Always - This setting will determine whether or not logged in users see the question. Settings this to 'When using shortcode' will allow you to use the default question as a spam filter, but still require registered users to answer alternate questions generated through the shortcode.
3. Required text indicator - Change the required indicator on the comments form. Accepts `<span>`, `<small>`, `<b>`, and `<i>` as HTML markup. Defaults to *.
4. Question / answer display format - Change the HTML markup of the displayed question and answer. The actual question and answer will appear where 'question' and 'answer' are in this field. Accepts `<p>`, `<span>`, `<small>`, `<b>`, and `<i>` as HTML markup. 

== Using the VKCT shortcode ==
	`[vkct
	question = 'any string'
	required = 'true' or 'false'
	display = 'above' 'below' or 'none'
	answer = 'any string' or 'Option1#!Option2#!Option3' or '#!Option1#!Option2#!Option3' ]`
	
For the answer option, any string (e.g. 'Answer') will generate a text field input. A list of options separated with a hashbang (#!) will generate a dropdown menu, with Option 1 selected as the default. If you start the list with a hashbang, the first choice will be an empty field, which, if the field is required, will require the user to select an option before posting a comment.

== Example shortcode usage ==

You wrote a post on PHP frameworks, and want any one who is going to leave a comment to indicate what their favorite framework is. Adding `[vkct question="What is your favorite framework?" required = "true" answer = "#!Cake PHP#!Yii#!Zend"]` will add a dropdown menu to the comment section from which the user will have to chose from Cake PHP, Yii, or Zend, and if they do not choose an answer, their comment will be rejected.

== Frequently Asked Questions ==
= What's the best use of this plugin? =

The best use is to setup a default, required question that's relevant to your site. If you write about sci-fi books, ask, "What is the last sci-fi book you read?". It will stop automated spam, and generate some nice feedback from your anonymous users. Then, when you write a post where you want to ask a more pointed question, or where you'd like to prevent people who didn't fully read your post from commenting, ask a required / exact question using the shortcode.

= Are there any questions I should avoid asking? =

I've heard some people get touchy when you ask about their mother...

== Screenshots ==

1. Example of fields added using the VKCT plugin.

== Changelog ==

= 0.1.1 (03/23/2013)=
* Minor readme changes, added screenshot.

= 0.1 (03/12/2013) =
* Initial release.

== Upgrade Notice ==
= 0.1 (03/12/2013) =
* Initial release.
