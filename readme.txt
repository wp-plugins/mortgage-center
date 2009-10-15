=== Mortgage Center ===
Contributors: amattie, zillow
Tags: zillow, closing.com, mortgages, mortgage rates, mortgage calculator
Requires at least: 2.8
Tested up to: 2.8
Stable tag: trunk

This plugin allows WordPress to load mortgage-related data from [Zillow](http://www.zillow.com) and [Closing.com](http://www.closing.com).

== Description ==

**REQUIRES PHP 5**

The Mortgage Center plug-in creates a mortgage resource center inside your WordPress blog. It includes:

* Real-time mortgage rates from Zillow’s GetRateSummary API
* A monthly payment calculator utilizing Zillow’s GetMonthlyPayments API
* Closing cost estimates (courtesy of [Closing.com](http://www.closing.com))
* A few educational mortgage articles (courtesy of [Zillow.com Help Center](http://www.zillow.com/mortgage/help/Main.htm))
* Links to the latest blog posts from Mortgages Unzipped, Blown Mortgage, and Mortgage Reports.

The data is presented on a single page, with the default being &lt;www.yourblog.com/mortgage&gt;. However, you have the ability to
change that URL in the event that you already have a page on your blog with that URL. You can customize which modules appear
on your page, as well as whether your blog displays national rates or rates for a specific state, in the plug-in admin
options.

== Installation ==

1. Go to your WordPress admin area, then go to the "Plugins" area, then go to "Add New".
2. Search for "mortgage center" (sans quotes) in the plugin search box.
3. Click "Install" on the right, then click "Install" at the top-right in the window that comes up.
4. Go to the "Settings" -> "Mortgage Center" area.
5. Select the state you want. By default, it's set to pull mortgage data for California.

== Changelog ==

== Frequently Asked Questions ==

= How do I use the module after I install it? =

The module is loaded / activated when the URL in your browser location bar matches the format of &lt;http://www.yourblog.com/mortgage&gt;.
In other words, to load the mortgage center, you'll want to point your browser to / link to &lt;http://www.yourblog.com/mortgage&gt;.

= Can I customize the styling and display format? =

Yes. All of the styles are controlled via an external CSS stylesheet named client.css (located in the 'css' folder of the plugin).
You can easily override any of the styles in there. Be aware, however, that the default styles were created to be compliant with
all of the branding requirements of the different APIs. It's possible that overriding any of the styles could put you out of
compliance with the API provider(s).

= National rates are displaying, but I want to display rates for the state that I live in - what steps do I need to take? =

Navigate to the mortgage center settings (&lt;http://www.yourblog.com/wp-admin/options-general.php?page=mortgage-center-options&gt;),
select the desired state in the drop-down, and then click submit. The mortgage center should now display rates for your desired state
instead of national rates.

= Do I have to show all of the modules? =

No. You can turn off any of the modules via the mortgage center admin portion of your WordPress installation.

= How are the rates calculated? Are they accurate? =

Rates on Zillow Mortgage Marketplace are compiled from real rates being given to borrowers in real-time. These are not low teaser or
marketing rates designed to pull you in and trick you. These are actual rates being given to real people right now. You can learn more
at the Zillow Mortgage Marketplace's ["About Our Rates"](http://www.zillow.com/mortgage/help/AboutOurRates.htm) page.

= The mortgage insurance column in the monthly payment estimate module is empty - why? =

The mortgage insurance column will only populate if the down payment entered is less than 20.

= How do I draw attention to the mortgage center? =

The easiest way would be to link to the plug-in from either you navigation bar or your sidebar. With version 2 of this plugin, it is
planned to build a sidebar module that displays current rates in order to drive traffic into your mortgage center.

= Can I add additional news sources? =

Not right at this time.  If you have suggestions for other news sources or features, please amattie+mortgagecenter@gmail.com.

= How do I add Zillow co-branding to the mortgage center? =

Navigate to the mortgage center admin options from your Wordpress admin interface, enter your Zillow screen name in the co-brand
field, then click submit.

== Screenshots ==

