=== IDX Realty Pro ===
Contributors: JAkzam, pcgrejaldo
Donate link: https://idxrealtypro.com/features/pricing
Tags: IDX, MLS, RETS, Real Estate, IDX Plugin
Requires at least: WP 4.8, PHP 5.5.0
Tested up to: 4.9.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easily add IDX listings and property searches from your MLS provider's RETS data feed to your real estate website for better SEO.

== Description ==

[IDX Realty Pro]: https://www.idxrealtypro.com "IDX Realty Pro, plugin author"

100% SSL(HTTPS) Compliant! NO Subdomain! NO Iframes! NO Script Embeds!

We know that real estate agents, brokers and web develoeprs for those in the field need: Quick! Accurate! Simple! And Secure!
IDX Realty Pro does just that for the best website content management system on Earth, WordPress. No more iframes, site 
performance killing scripts or expensive customization fees. With [IDX Realty Pro](https://www.idxrealtypro.com), you 
get all the benefits of Real-Time MLS data synced on your site, as part of your site.

NOTE: Google recently updated their code again to penalize sites without SSL Certificates, showing a secure HTTPS URL.
We are the ONLY WordPress IDX plugin that complies with those requirements.

Simple and Beautiful
To make your site grab visitors' eyes, with eye catching property displays, IDXRP has incorporated easy to use templates that can be 100% customized. Our team has a combined total of nearly 20 years in the Real Estate industry as licensed brokers and agents, so they know what you really need. 

Quickly Add Widgets & Shortcodes
Regardless of your theme or editing preferences, there is an option for creating property pages for communities, featured properties and other specifics that help coral visitors into your Leads* list.

Ask about our Drag n' Drop page build and theme with built in IDX Property widget and advanced layout capabilities.

Responsive and Mobile Ready
IDX Realty Pro has been designed to be mobile compatible on smartphones and tablets when used with any responsive WordPress theme.

Reliable Updates and Support
Not only do we use the latest RETS data dictionary and feed interpretation technology, we stay on top of real estate industry trends to help our users stay ahead of the large firms and online search portals. 

[Features](http://idxrealtypro.com/features/)

* 100% SSL (HTTPS) Compliant
* On-Site Exclusive Listing Control
* Responsive Mobile Design
* Fully Customizable
* Easy User Interface
* Modern Property Search
* Modern Design
* Self Hosted Data (Speed)
* Optional Hosting
* Easy to Install
* True WordPress Integration
* Google Analytics Friendly
* SEO/Google Indexed Property Pages
* List and gallery result views
* Reciprocity MLS data feeds
* Large photos and thumbnails
* Search widgets
* Saved searches
* Search Engine Optimized
* Fully MLS compliant
* Dynamic community pages
* Virtual tours
* Streamlined Lead Capture
* User Insight with IDX Lead Pro
* Premium Support Included
* A much more... Check out our site for more info on our features

Capture and Convert Your Leads*
With the IDX Leads Pro addon, you have a powerful lead capture tool to generate, manage, maintain and update new leads. Send automated property update emails for user's saved searches and manual searches an agent creates. Get notifications instantly, with your lead capture settings in IDX Leads Pro.

== Installation ==

1. **Important:** Backup your files and database.

2. Upload `realty-idx-pro.zip` via "wp-admin/plugin-install.php?tab=upload" or if using FTP, unzip `realty-idx-pro.zip` and upload `/realty-idx-pro/` directory to the `/wp-content/plugins/` directory.

3. Activate the plugin through the 'Plugins' menu in WordPress (under the plugin name: **"IDX Realty Pro"**).

== Frequently Asked Questions ==

= Where can I learn more about this plugin? =

Please see the [IDX Realty Pro FAQ](https://idxrealtypro.com/docs/idx-realty-pro-v3-x-faq-frequently-asked-questions/)

= I am having issues with the plugin, how can I reach you? =

* You may post your questions and issues on [WP plugin support page](https://wordpress.org/support/plugin/realty-idx-pro) and we'll try our best to assist you as soon as we can.

== Screenshots ==

1. Shows the plugin admin page with the "Overview" tab.

2. Shows the properties list search page.

3. Shows the single property page.

4. Show the shortcode settings editor.

== Changelog ==

= 3.2.0 =
* updated: bootstrap to v4.0.0
* added: Featured widget

= 3.1.13 =
* fixed: googlebot unable to to render search app - re-added `babel-polyfill` on a separate vendor file (`vendor-front`) on front-end search app only

= 3.1.12 =
* fixed: `Map` view throws a script error when it is set as the default search view
* fixed: primary fields label displays in frontend even if the value is empty in its settings
* changed: adding controls to `Primary Search Fields` should now have an empty label by default

= 3.1.11 =
* fixed: `.map()` is not a function script error when passed value is not an array in *Tax Archive* tab

= 3.1.10 =
* fixed: *only one instance of babel-polyfill is allowed* script error - dropped dependency on `babel-polyfill` library, using `babel-runtime` instead
* fixed: `foreach` invalid argument (non-array) when a single taxonomy option is selected
* changed: check for and deregister `yoast-seo-polyfill` - no longer needed

= 3.1.9 =
* added: map info window link to property details page

= 3.1.8 =
* added: plugin help page
* added: `title` attribute to generated shortcode (for admin purposes only)
* added: `marked` library - for markdown parsing used in help page

= 3.1.7 =
* updated: bootstrap to v4
* fixed: `Map` view for mobile - elements doesn't break nicely on mobile

= 3.1.6 =
* added: `IDXRP` admin bar tool with `Clear Cached Data` sub-item to clear cached plugin data

= 3.1.5 =
* fixed: listing status value `active` does not matches in substring

= 3.1.4 =
* changed: listing status output value is now an image generated through canvas script instead of just plain text

= 3.1.3 =
* fixed: search app styles
* changed: search tooltip trigger to `focus`

= 3.1.2 =
* fixed: property details page styles and google maps marker template not getting printed
* added: re-download default templates on plugin activation/upgrade

= 3.1.1 =
* fixed: Uncaught ReferenceError: `error_code` is not defined

= 3.1.0 =
* changed: code rewrite - listings data are now served from an API server
* updated: bootstrap to v4.0.0.beta2

= 3.0.2 =
* fixed: `taxonomies` and `status_publish_values` aren't saved when `Run Task/Sync` button is clicked
* fixed: sync process doesn't reload when process is done
* fixed: undefined index notice for `meta_description`
* fixed: incorrect label for marker template setting

= 3.0.1 =
* fixed: `default_view` settings is overridden by default hard-coded value
* added: `hide_search` control switch in shortcode settings editor and function in search app
* added: `set_next_replication_timestamp` and `get_next_replication_timestamp` cli command

= 3.0.0 =
* Code overhaul - code and data structure change.

= 2.2.13 =
* added: uninstall function
* added: upgrade to v3.x warning in plugins list

= 2.2.12 =
tested with WP 4.8

= 2.2.11 =
* added: `set_rets_metadata` cli command
* added: `show_loading` icon option

= 2.2.10 =
* changed: price formatting - no decimal
* changed: `Decimal` data type precision

= 2.2.9 =
* fixed: replication process throws `unauthorized` error for some MLS - removed call to `PHRets::Disconnect` method after db creation

= 2.2.8 =
* fixed: missing parameter 2 on `RetsCredentials::retsDbTableDelta()` method call for reset replication
* fixed: `post_date` is now auto set by WP instead of custom value as it creates scheduled property posts if timezone setting is set to less the UTC
* added: `idxrp_replicate_rets_data_post_status` filter - to enable modification of which properties gets published

= 2.2.7 =
* fixed: pass current listing search form params on initial load

= 2.2.6 =
* added: `street_name_field` into suggestions
* fixed: `RIGHT JOIN` instead of `LEFT JOIN` in listings search app

= 2.2.5 =
* added: `he` library to decode html entities
* fixed: db select error for suggestions due to the *list of field names* being enclosed in backticks

= 2.2.4 =
* fixed: load default lookup values have duplicates and are not sorted alphabetically by `label`
* fixed: `[idxrp_group]` shortcode settings options not sorted alphabetically
* fixed: when `default_class` is set, `resource_class` param doesn't get set to the default selected class
* fixed: script error on `orderby` undefined `label` property

= 2.2.3 =
* changed: `rets` class property as it's causing `401 Unauthorized` issues

= 2.2.2 =
* added: `selected_class` attribute for `[idxrp_search_app]` shortcode
* changed: `default_class` attribute for `[idxrp_search_app]` shortcode is now a checkbox - to hide class select box on search apps
* fixed: `favorite` functionality - stored as array key field id value from MLS; converted to post ID on fetch
* fixed: `lat` and `lng` fields not properly set
* fixed: default template file content doesn't load

= 2.2.1 =
* fixed: `photos_url` returns single value instead of `array`

= 2.2.0 =
* rewritten listing data storage and query - they are now stored in a separate table per class

= 2.1.7 =
* added: `Back` button - initialized on search app constructor (e.g. when visiting the search/group app)
* fixed: `orderby` and `order` params are not passed when changing pages

= 2.1.6 =
* fixed: photo URL data insertion fails for some MLS where their object id does not start with `0`

= 2.1.5 =
* added `IDXRealtyPro/Controller/Admin::upgraderProcessComplete` method hook handler
* added: `add_address_fields_meta` cli command
* changed: search for address components are now queried against postmeta
* changed: search query changes - address fields now also added as post meta
* fixed: `search_only` app doesn't execute search string on redirect to search app page
* fixed: multiselect script error - dependency was missing
* fixed: search form reverts fields to the last value selected on reset
* fixed: search query doesn't filter properly for numeric values

= 2.1.4 =
* added: specific method for an mls that doesn't support `GetObject` in downloading photos
* changed: passwordless login form for non-admin users
* fixed: redirect to the current search url after login from favorite action

= 2.1.3 =
* fixed: `longName` (or other elements) may not have been defined by some MLS - force parse `field` elements with expected keys

= 2.1.2 =
* changed: street number/name/suffix and neighborhood fields are no longer required fields
* fixed: `Control` key throwing script errors on drag-n-drop
* fixed: no `try-catch` blocks for `PHRets` method calls
* fixed: support for MLS with numeric field names

= 2.1.1 =
* added: `reset_post_title` cli command
* changed: *back to search* button uri with `back` query string to disable auto-redirect for single search result
* changed: object ids to `0,1` from `*` to download for `thumbnail` photo type
* fixed: `download_thumbnails` cli command db query args
* fixed: mls number search param not getting merged into `meta_query` params

= 2.1.0 =
* added: `map` and `grid` in global template setting and shortcode attribute
* added: config file for MLS servers that supports photo location
* added: list of server keys that doesn't support `Offset`
* added: template editor template loader quicktags
* changed: RETS metadata field checkboxes to 2-sides-multiselect
* changed: template tag regex pattern - it's now possible for numeric field names in post title format and template field name
* changed: RETS metadata fields checkboxes to 2-sides-multiselect boxes
* fixed: `Admin::getShortcodeMatches()` infinite recursion

= 2.0.18 =
* added: `Auto Update` option in `Admin > General` under `Plugin`
* added: `Back to search` link on `single-property.php`
* added: `Meta Description` textbox in `Admin > Settings > Single` tab
* added: `orderby` and `order` input in search app shortcode generator
* added: `set_replication_timestamp` CLI command
* added: use of initial form values for initial app load
* added: user favorites list
* changed: `reset` process of `download_thumbnails` cli command
* fixed: markup for favorite button on `Photo` and `Map` views
* removed: saved searches list

= 2.0.17 =
* added: `Update default templates` button in `Admin > Overview` tab
* changed: loading default template in template editor should now replace existing content

= 2.0.16 =
* added: favorite button on search results
* fixed: Fatal error: Call to undefined method `IDXRealtyPro\Model\Front::getUserSearchData()`
* fixed: no login/registration form popup when clicking favorite for non-logged-in users

= 2.0.15 =
* added: `Intl` polyfill
* added: favorite button on single property post
* added: save search function
* added: `[idxrp_user_acct]` shortcode generator
* fixed: single word search freezes db query on multiple taxonomy term match

= 2.0.14 =
* added: `Load Default Templates` on templates post editor
* added: `rest` context to `Shortcodes::getPropertyFieldValue()` param
* fixed: IDXRP Field shortcode/tag generator may break if non-unique keys are encountered for `select` options
* fixed: options gets replaced when clicking `Add` button on Adv Filter due to incorrect array `index` used

= 2.0.13 =
* added: last RETS activity log message
* fixed: unable to change advanced filter control label

= 2.0.12 =
* added: taxonomy archive template
* fixed: `[idxrp_group]` shortcode admin error doesn't print for required attribute `taxonomy=>terms` pair

= 2.0.11 =
* fixed: `WP_Filesystem()` call returns fatal as it is not defined - added conditional check to include `wp-admin/includes/file.php` before the call

= 2.0.10 =
* added: MultiSelect control
* added: anchor tag wrapper to grid view item
* changed: default `orderby` to "price" field
* changed: "Grid" button label to "Photo"
* fixed: grid view layout may break if images have different sizes (height)
* fixed: MultiCheckbox doesn't trigger instant search when option is enabled

= 2.0.9 =
* fixed: tinymce stylesheet not loaded in post editor

= 2.0.8 =
* added: code adjustments to support `rets_mlsli_com` server
* added: `idxrp_field` tag and shortcode generator for `idxrp_template` post editor
* fixed: `edit_post_link` template tag has no handler

= 2.0.7 =
* added: `[idxrp_field]` shortcode
* added: instant search option and functionality
* added: photo modification timestamp field
* changed: default resource settings for RETS servers are now downloaded from remote server
* fixed: date formatting outputs raw data in `display` context

= 2.0.6 =
* added: `idxrp_group` shortcode generator
* added: `Tools` tab - with `Download Thumbnails` tool
* added: default templates installer in admin page > Overview tab
* added: Open Graph meta tags for singular posts
* changed: Grid view markup and style rules changed to bootstrap
* fixed: `Search App Settings` dialog body is not scrollable when content goes beyond its bounds
* fixed: `wptexturize` breaks template output - removed filter on `idxrp_template` output and added back after
* fixed: element with `longName` key being removed when sorting fields

= 2.0.5 =
* fixed: http_authentication field does not change when selecting other options
* fixed: initial search submit *may* not return results due to incorrect page value passed along with the request
* changed: front-end default templates moved to `WP_CONTENT_DIR` - downloaded on plugin activation (if RETS credentials already exists) or upon successful verification of RETS credentials

= 2.0.4 =
* fixed: some RETS server doesn't provide proper or correct data which makes the replication process go into an infinite loop when replicating more than 1 class of a resource
* implemented: single property marker info window template option in admin settings > Single tab
* fixed: form and query filter params are not passed along in pagination function
* implemented: map marker info window templates

= 2.0.3 =
* fixed: on reset replication, existing property post is not retrieved and updated for mls/listing-key that matches currently inserted data
* fixed: grid view is not showing the price field - incorrect object property used due to refactoring

= 2.0.2 =
* fixed: search params not being sent along with the request due to change in params key between `post` and `get` method

= 2.0.1 =
* fixed: undefined index error

= 2.0.0 =
* Code overhaul - rewritten to utilize WP 4.7 REST API

= 1.4.16 =
* fixed: sync process doesn't put properties to `draft` status when they are no longer "active"

= 1.4.15 =
* changed: no longer creates base64 encoded images but actual image file instead as conversion and parsing seems slow
* fixed: loading spinner positioned at the bottom of the screen instead of center screen

= 1.4.14 =
* added: Open Graph meta tags for base64 encoded images
* added: `property-photos.php` which provides base64 images an image URL
* fixed: `ListPrice` min/max suffix not stripped in adjacent post where clause query resulting to db query errors

= 1.4.13 =
* fixed: `IDXAddressDisplayYN` values to check now includes ['Yes', 'Y', 1]

= 1.4.12 =
* fixed: `wpdb` query error for `ListPrice_min` and `ListPrice_max` as the suffix (_min/_max) are not stripped before usage in db query
* added: filters `idxrp_replicate_rets_data_rets_query`, `idxrp_get_filtered_properties_where_clause` and `idxrp_get_filtered_properties_filter_keys`

= 1.4.11 =
* added: check for `photos_save_location` at the start of `downloadRetsPhotos()` method, so method body can be skipped if its value is empty (`default`)
* added: `IDXRP_PHRETS_POST_METHOD` constant for MLS that returns 404 error due to limitations with `GET` method when executing queries
* changed: thumbnail is now also pulled from RETS server
* fixed: `photos_location` var_map may be set incorrectly if photo query reply code doesn't match any of the hardcoded values - is now removed

= 1.4.10 =
* removed : `strpos` call

= 1.4.9 =
* fixed: `foreach` invalid argument warning notice when `$property_fields` is empty

= 1.4.8 =
* fixed: `AdminModel::deleteAllCredentialRelatedData()` method to only run if post being deleted is of `rets_credential` post type
* fixed: `foreach()` warning notice for invalid argument when params is generated by Beaver widget
* removed: `single.php` from array of `single-<post_type>.php` templates

= 1.4.7 =
* changed: `Front::fullWidthSinglePropertyTemplate()` single-property template file names to dynamic
* fixed: `setCookies()` method `array_key_exists()` param 1 warning notice

= 1.4.6 =
* added: constant `IDXRP_SKIP_OFFICE_AGENT_DATA` to skip agent/office data
* changed: `IDXListing` to `IDXAddressDisplayYN`

= 1.4.5 =
* changed: force 5-digits postal code
* changed: `IDXListing` field check when creating property post title
* fixed: *More Filters* displays all values instead of a limited set of values based on `idxrp_properties_list` attributes

= 1.4.4 =
* added: `fix_photos_url` cli subcommand
* added: exclude property photos in `Media Library` *list* mode
* fixed: `photos_url` field being emptied on sync process

= 1.4.3 =
* added: `filters_only` attribute to `[idxrp_properties_list]` shortcode
* changed: search function additional parsing
* updated: bootstrap to v3.3.7

= 1.4.2 =
* added: `fix_property_title` cli subcommand for fixing `post_title` (address) and its slug
* added: `IDX_REALTY_PRO_REQUIRED_PHP` for minimum required PHP version
* added: `IDX_REALTY_PRO_REQUIRED_WP` for minimum required Wordpress version
* added: `IDXRP_CLI_UNLI_MEMORY` if set to `true`, will use maximum memory when running *CLI* commands
* added: `IDXRP_PROPERTY_POST_TYPE` set to string to change the post type name of properties. *Must be set **before** the plugin is activated.*
* added: `Photos Download Limit` in credential post editor (under `Replicate RETS Data` metabox) which limits the number of listings per batch or request when downloading photos
* changed: creation and linking of listing thumbnails are now done after photos of a listing are downloaded
* changed: found properties label on *reset*
* changed: minimum WP required is now 4.6
* fixed: `#view-mapping-btn` button throws a script error due to stripped `form` tag
* fixed: sort select dropdown doesn't set *selected* option

= 1.4.1 =
* `[idxrp_properties_list]` shortcode breaks on multiple MLS setup due to incorrect SQL query and field name not converted to their mapped field name
* add param in `[idxrp_properties_list]` shortcode *should* list specifically selected values of param only (*e.g.* `City`) as well as search within that form *should* limit results within those selected params as well
* added `BuildingDesign` and `SubdivisionName`
* changed: `sort` or `orderby` displays key as option label to a specific *string* label
* fixed: `idxrp_list_template_params` cookie key/value set manually so it can be used immediately
* fixed: `reset` doesn't *reset* list price label
* fixed: property type and sub type available options in search form
* fixed: typo in max list price input variable which doesn't set the label properly
* fixed: single/list template WP editor not switching between `visual` and `text` due to editor id param are the same for the different listing classes
* fixed: more filters are not filtering results properly
* refactor: search form input names

= 1.4.0 =
* added: Featured and Exclusive Properties widget and shortcode generator
* added: IDXRP_CLI_PHAR_PATH constant check in CliHelper::getWpCliPharPath() method for custom `wp-cli.phar` file path
* added: WP cron schedule for installing/updating `wp-cli.phar` file
* changed: mapping files are now located in `<uploads-dir>/idxrp-mapping/` directory
* fixed: multiple param values returns empty result
* fixed: setGetUrl method always returns an error due to incorrect element reference - `$(this)` changed to `$(e.target)`
* fixed: while in beaver builder edit mode, adding properties list widget returns empty result due to additional `page_id` wp_query argument
* fixed: WpPhRets Connect() error due to rets_credential_id not being set properly
* tested: WP 4.6

= 1.3.1 =
* fixed: property_fields query skipped due to incorrect expression

= 1.3.0 =
* added: "Property Category" custom taxonomy with default "Featured" and "Exclude" categories - used for additional query filters
* added: [idxrp_featured_properties] shortcode
* added: search suggest on input
* changed: [idxrp_properties_list] orderby available parameters to only include commonly queried fields
* changed: bootstrap styles are now namespaced by "idxrpbs"
* changed: jquery-cookie to js-cookie
* changed: most js scripts now depends on backbone and underscore scripts
* changed: replication - property photos downloads only 1 photo for featured image use and other photos are downloaded on single property requests and displayed using base64 encoding and are saved via transients for 1 day
* changed: rets replication long-polling to server sent events
* changed: rets-credential-script.js now depends on backbone/underscore scripts
* removed: "Property" class - property fields are now JOINed into post in queries and accessed through WP_Post post_meta __get magic method

= 1.2.4 =
* added: WP suggest script for property search input
* fixed: properties list/search query breaks when ordered by "date"

= 1.2.3 =
* fixed: replication errors due to incorrect field count returned because of custom <ListPrice>_(min/max) fields - added $context parameter to BaseModel::getLocalFields method.
* fixed: replication reset routine halts due to AdminModel::deleteAllCredentialRelatedData hooked to "before_delete_post" hook - hooked method is now removed when doing replication reset

= 1.2.2 =
* added: minimum and maximum select option variant of ListPrice shorcode/widget property field param
* fixed: beaver builder widget module adds field param select on all table instead of just the general settings tab due to incorrect selector
* fixed: beaver builder widget settings does not persist on save
* fixed: property-list.js script being printed multiple times when beaver builder previews changes

= 1.2.1 =
* fixed: sync replication errors immediately due to duplicate AdminModel::setReplicationProgress() call

= 1.2.0 =
* added: 'delete_rets_credential' wp-cli subcommand
* added: 'idxrp_photos_dirname_input_section' filter for images save location input section
* added: 'idxrp_replicate_rets_data_address_array' filter
* added: grid column option 1
* added: reset button on list/details template editor
* added: slider settings tab in plugin admin page
* changed: `[idxrp_properties_list]` shortcode mce UI
* changed: bootstrap files now bundled
* changed: font-awesome files now bundled
* changed: get_terms() deprecated argument
* changed: PropertiesListSc widget form output with script
* changed: single property settings moved to its own tab
* changed: wp-cli command from idx_realty_pro to idxrp
* fixed: incorrect local photos path on replication reset
* updated: bxslider to version 4.2.5

= 1.1.5 =
* fixed: Plugin::getInputFieldHtml array to string conversion notice
* added: HGMLS and GLVAR support

= 1.1.4 =
* added: Helper::getFileMapKeys method
* changed: hardcoded field map keys for fields-mapping-dialog.php

= 1.1.3 =
* fixed: incorrect param provided to do_action_ref_array
* fixed: broken featured image due to incorrect file path

= 1.1.2 =
* refactor: Settings to Plugin
* fixed: undefined class due to incorrect class name casing
* fixed: field mapping references between agents/office and property tables

= 1.1.1 =
* tested: WP 4.5
* fixed: `class` and `resource` error accessing tags editor page

= 1.1.0 =
* added: support for `tnv.rets.mlxinnovia.com` RETS server
* added: office and agent data table included in replication process
* changed: works with WP 4.4.2

= 1.0.1 =
* added: pre_get_posts hook handler check for attachment post_type in query_vars when querying for media library objects

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 3.0.0 =
**WARNING:** Upgrading to this version will make your current setup to stop working. Please see [v3 Migration/Upgrade FAQ](https://idxrealtypro.com/docs/idx-realty-pro-v3-x-faq-frequently-asked-questions/im-using-v2-x-x-of-the-plugin-how-do-i-migrate-to-v3-x/).

= 2.2.0 =
* Backup database
* Update plugin
* Reset replication
* Re-generate shortcodes
* Update default templates

= 1.0.0 =
* Initial release: If you encounter any bugs please file a support ticket.
