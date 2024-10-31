<?php

use IDXRealtyPro\Helper\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	die( "You are not allowed to call this page directly." );
}

$plugin_name = Plugin::getPluginData()->Name;
$plugin_url  = Plugin::getPluginData()->PluginURI;
$image_url   = plugins_url( 'images/help/', IDX_REALTY_PRO_PLUGIN_FILE );

$help = [
	'setup'                    => [
		'label' => __( 'Setup' ),
		'nodes' => [
			sprintf(
				__(
					'Go to `%1$s > Admin > Overview` tab, and make sure that your server meets the requirements.  
                    ![Overview image](%2$s)'
				),
				$plugin_name,
				$image_url . 'setup1.png'
			),
			sprintf(
				__(
					'Click the `License` tab, enter your license key in the text input and click `Activate` button. Your license should display as ***valid and active***. (If you don\'t have a license yet, you can get one in [%1$s website](%2$s))  
                    ![License image](%3$s)',
					'realty-idx-pro'
				),
				$plugin_name,
				trailingslashit( $plugin_url ) . 'pricing',
				$image_url . 'setup2.png'
			),
			sprintf(
				__(
					'Next, click the `Settings` tab, then in `General` sub-tab, if you want to use Google Maps on your site enter your `Google Maps API Key` in the text input and click `Save` button.  
                    ![General Settings image](%s)',
					'realty-idx-pro'
				),
				$image_url . 'setup3.png'
			),
			sprintf(
				__(
					'Then click `Post Type` sub-tab, and enter the post type name that you want your properties to be created as. The most important in these input fields is the `Slug Name`, it must be unique and not in use in your Wordpress install (by plugins/themes).  
                    ![Add post type image](%s)',
					'realty-idx-pro'
				),
				$image_url . 'setup4.png'
			)
		]
	],
	'sc_settings'              => [
		'label' => __( 'Shortcode Settings', 'realty-idx-pro' ),
		'nodes' => [
			sprintf(
				__(
					'From WP dashboard, go to `%1$s > SC Settings`, click `Add New` button.  
                    ![Shortcode settings list table - Add New](%2$s)',
					'realty-idx-pro'
				),
				$plugin_name,
				$image_url . 'sc-settings1.png'
			),
			sprintf(
				__(
					'In the shortcode settings editor page, enter a descriptive title for your shortcode.  
                    ![Shortcode settings editor page](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings2.png'
			),
		]
	],
	'sc_settings_controls'     => [
		'label' => sprintf( __( '%s Controls', 'realty-idx-pro' ), '&mdash;' ),
		'nodes' => [
			sprintf(
				__(
					'In the `Select Server` dropdown, select the server from the options.  
                    ![Select server](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-select-server.png'
			),
			sprintf(
				__(
					'Then select from the `Select Resource:Class` dropdown. This will be the default `Resource:Class` for this shortcode.  
                    ![Select resource:class](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-select-resource-class.png'
			),
			sprintf(
				__(
					'The `Search Form Only` switch will only display the search form for this shortcode and will not display the search results section. This will also hide other controls in this editor page that will have no effect on the search form.  
                    ![Search form only switch off](%s)  
                    ![Search form only switch on](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-search-form-only1.png',
				$image_url . 'sc-settings-search-form-only2.png'
			),
			sprintf(
				__(
					'If you enabled `Search Form Only`, the `Search Page` control should be visible and you ***must*** select a page where your site can display your users\' search results. You can create a page and add a shortcode settings that displays results on that page.  
                    ![Search page select](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-search-page.png'
			),
			sprintf(
				__(
					'The `Hide Search Form` switch will only display the properties fetched that matches the entered filters for this shortcode and will not display the search form.  
                    ![Hide search form off](%s)  
                    ![Hide search form on](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-hide-search-form1.png',
				$image_url . 'sc-settings-hide-search-form2.png'
			),
			sprintf(
				__(
					'`Class Select` control ***depends on your MLS***, if this displays and no selection is made, this will default to `Resource:Class` selected (see `#2`). Selecting classes will display a dropdown select in the frontend search form.  
                    ![Class select multiselect](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-class-select.png'
			),
			sprintf(
				__(
					'`List/Photo/Marker Template` displays only if you have created template posts.  
                    ![Select template controls](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-templates.png'
			),
		]
	],
	'sc_settings_query_fields' => [
		'label' => sprintf( __( '%s Query Fields', 'realty-idx-pro' ), '&mdash;' ),
		'nodes' => [
			sprintf(
				__(
					'Filters selected here will ***always*** apply to queries for this shortcode.',
					'realty-idx-pro'
				)
			),
			sprintf(
				__(
					'In the `Select Field` dropdown menu, select the field name to add as filter.  
                    ![Select query field](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-query-fields-select-field.png'
			),
			[
				'desc'  => sprintf(
					__(
						'Then an `Operator` dropdown select should be visible, select the operator to use. The options in this dropdown depends on the type of field selected.  
                        ![String operators](%s)  
                        ![Numeric and date operators](%s)',
						'realty-idx-pro'
					),
					$image_url . 'sc-settings-operator1.png',
					$image_url . 'sc-settings-operator2.png'
				),
				'nodes' => [
					sprintf(
						__(
							'`Equals to` (*available for string/numeric/datetime fields*) - fetch listings using an ***exact match*** with the given value (case sensitive).',
							'realty-idx-pro'
						)
					),
					sprintf(
						__(
							'`Contains` (*available for string fields*) - fetch listings that ***contains*** the given value(s).',
							'realty-idx-pro'
						)
					),
					sprintf(
						__(
							'`Any (IN)` (*available for string fields*) - fetch listings that ***have any*** of the given value(s).',
							'realty-idx-pro'
						)
					),
					sprintf(
						__(
							'`Not Any (NOT IN)` (*available for string fields*) - fetch listings that ***does not have any*** of the given value(s).',
							'realty-idx-pro'
						)
					),
					sprintf(
						__(
							'`Greater than or equals to` (*available for numeric/datetime fields*) - fetch listings ***greater than or equal to*** the given value.',
							'realty-idx-pro'
						)
					),
					sprintf(
						__(
							'`Less than or equals to` (*available for numeric/datetime fields*) - fetch listings ***less than or equal to*** the given value.',
							'realty-idx-pro'
						)
					),
					sprintf(
						__(
							'`Not equal to` (*available for numeric/datetime fields*) - fetch listings ***not equal to*** the given value.',
							'realty-idx-pro'
						)
					),
					sprintf(
						__(
							'`Between` (*available for numeric/datetime fields*) - fetch listings between the two given values.',
							'realty-idx-pro'
						)
					),
				]
			],
			sprintf(
				__(
					'After selecting an operator, a **Values** link will be visible (usually for string fields). Clicking this should fetch available options **if there are any**.  
                    ![Values link](%s)  
                    ![Values popup](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-operator-values.png',
				$image_url . 'sc-settings-operator-values-popup.png'
			),
			sprintf(
				__(
					'Click `Add` button to add this to the `Query Filter` section.  
                    ![Query field add button](%s)  
                    ![Query field section](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-add-query-field.png',
				$image_url . 'sc-settings-query-field-section.png'
			),
		]
	],
	'sc_settings_order_by'     => [
		'label' => sprintf( __( '%s Order By', 'realty-idx-pro' ), '&mdash;' ),
		'nodes' => [
			sprintf(
				__(
					'Select the field that you want to add as an option to the `Order By` dropdown.  ![Order by image](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-order-by1.png'
			),
			sprintf(
				__(
					'Clicking `Add` button will add the selected field to the `Order By Fields` section. You can change the field name in the given textbox if you want.  ![Order by image](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-order-by2.png'
			),
		]
	],
	'sc_settings_order'        => [
		'label' => sprintf( __( '%s Order', 'realty-idx-pro' ), '&mdash;' ),
		'nodes' => [
			sprintf(
				__(
					'Select the default sort order of the listings that are displayed.  
                    ![Select default sort order](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-default-sort-order.png'
			),
		]
	],
	'sc_settings_view'         => [
		'label' => sprintf( __( '%s Default View', 'realty-idx-pro' ), '&mdash;' ),
		'nodes' => [
			sprintf(
				__(
					'Select the default view that your users will see the first time they load up your search page. The `Map` view will only be visible/available if you have enter your `Google Maps API Key` in the plugin settings page.
                    ![Select default view](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-default-view.png'
			)
		]
	],
	'primary_search_fields'    => [
		'label' => sprintf( __( '%s Primary Search Fields', 'realty-idx-pro' ), '&mdash;' ),
		'nodes' => [
			sprintf(
				__(
					'The primary search fields are those filters that are visible along with the search text box. To add filters, start by selecting a field in the `Select Field` dropdown.  
                ![Select field dropdown](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-primary-field-select.png'
			),
			sprintf(
				__(
					'Then select the input type. It would look best to select `Select` input type (for now) for primary fields as it renders better.  
                    ![Select input type](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-primary-field-select-input.png'
			),
			sprintf(
				__(
					'Next is to enter the options for the selected input type. The format for the options (as also stated in the text box placeholder) should be `Label:Value`.
                     (*e.g.* `Naples:NAPLES` - in this case `Naples` is the option displayed and can be seen by your users and `NAPLES` is the actual value that is sent to the server when search is submitted)
                     To easily populate the options, click the `Load Options` dropdown and select the type of options to load.  
                     `Min/Max Price` - are for fields that have price values (*e.g.* price of property)  
                     `Min Number` - are for fields that have numeric values (*e.g.* number of bedrooms)  
                     `Exact Number` - are for fields that have numeric values (like `Min Number`) though the values loaded and sent to the server when search is submitted is the exact number value.  
                     `Lookup Values` - are for fields that have actual lookup values (*e.g.* City, ). Although, this highly depends if they are available in your MLS.  
                     Once you select an option, the field options for this filter will be loaded if available.  
                     ![Load options](%s)  
                     ![Loading lookup values](%s)  
                     ![Lookup values loaded](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-primary-search-fields-load-options.png',
				$image_url . 'sc-settings-loading-lookup-values.png',
				$image_url . 'sc-settings-values-loaded.png'
			),
			sprintf(
				__(
					'Click `Add` button to add this filter to the shortcode and it should be visible in the `Search Fields` section.  
                    ![Filter added to search fields](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-primary-search-fields-section.png'
			),
			sprintf(
				__(
					'You can edit the added field by expanding the control and make your changes.
                     You can add a control label for the filter here but the output is better if it is left blank for primary fields.
                     In advanced fields, it is better to enter a label here.  
                    ![Edit field](%s)',
					'realty-idx-pro'
				),
				$image_url . 'sc-settings-primary-search-fields-edit-field.png'
			),
		]
	],
	'advanced_search_fields'   => [
		'label' => sprintf( __( '%s Advanced Search Fields', 'realty-idx-pro' ), '&mdash;' ),
		'nodes' => [
			sprintf(
				__(
					'The functions here are the same in `Primary Search Fields`. 
                    The only thing to note, is that the fields here renders better when their controls have `Label`.
                    See `Primary Search Fields`.',
					'realty-idx-pro'
				)
			),
		]
	],
	'publish_sc'               => [
		'label' => sprintf( __( '%s Publish', 'realty-idx-pro' ), '&mdash;' ),
		'nodes' => [
			sprintf(
				__(
					'Lastly, do not forget to click `Publish` or `Update` button to save your shortcode settings.',
					'realty-idx-pro'
				)
			),
			sprintf(
				__(
					'Then copy the shortcode tag (or click the `Copy to clipboard` link) and paste it wherever you want to use the shortcode.',
					'realty-idx-pro'
				)
			),
		]
	],
	'widgets'                  => [
		'label' => __( 'Widgets' ),
		'nodes' => [],
	],
	'featured_widget'          => [
		'label' => sprintf( __( '%s Featured Widget', 'realty-idx-pro' ), '&mdash;' ),
		'nodes' => [
			sprintf(
				__(
					'Select server and click save.
					![Select server](%s)',
					'realty-idx-pro'
				),
				$image_url . 'fw-select-server.png'
			),
			sprintf(
				__(
					'Select Resource:Class. Press and hold down %1$sctrl%2$s or %1$scmd%2$s and click option to select multiple values and click save.
					![Select Resource:Class](%3$s)',
					'realty-idx-pro'
				),
				'<code>',
				'</code>',
				$image_url . 'fw-select-resource-class.png'
			),
			sprintf(
				__(
					'Select the field name to search values for and click save.
					![Select field name](%s)',
					'realty-idx-pro'
				),
				$image_url . 'fw-select-field-name.png'
			),
			sprintf(
				__(
					'Enter the values to search and click save.
					![Enter values](%s)',
					'realty-idx-pro'
				),
				$image_url . 'fw-enter-values.png'
			),
		]
	]
];

return compact( 'help' );
