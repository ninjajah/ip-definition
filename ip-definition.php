<?php
/**
 * IP definition plugin
 *
 * Plugin Name: IP definition plugin
 * Plugin URI:
 * Description: The plugin defines the IP address and shows information depending on the country
 * Version:     0.1
 * Author:      P.B.
 * Author URI:
 * License:
 * License URI:
 * Text Domain: ip-definition
 */

if (!defined('ABSPATH')) {
	die('Invalid request.');
}

require_once('vendor/autoload.php');

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use GeoIp2\Database\Reader;

add_action( 'plugins_loaded', array( 'Carbon_Fields\\Carbon_Fields', 'boot' ) );

if (!class_exists('IpDefinition')) {
	class IpDefinition
	{
		public function __construct()
		{

		}

		public static function plugin_init()
		{
			add_action('carbon_fields_register_fields', [__CLASS__, 'plugin_options']);
			add_action('init', [__CLASS__, 'plugin_post_type']);
			add_shortcode('ip_definition', [__CLASS__, 'plugin_shortcode']);
			add_action('wp', [__CLASS__, 'plugin_output_handler']);

		}

		public static function enqueue_assets()
		{

		}

		public static function plugin_options()
		{

			$countryList = [
				'AF' => 'Afghanistan',
				'AX' => 'Aland Islands',
				'AL' => 'Albania',
				'DZ' => 'Algeria',
				'AS' => 'American Samoa',
				'AD' => 'Andorra',
				'AO' => 'Angola',
				'AI' => 'Anguilla',
				'AQ' => 'Antarctica',
				'AG' => 'Antigua and Barbuda',
				'AR' => 'Argentina',
				'AM' => 'Armenia',
				'AW' => 'Aruba',
				'AU' => 'Australia',
				'AT' => 'Austria',
				'AZ' => 'Azerbaijan',
				'BS' => 'Bahamas the',
				'BH' => 'Bahrain',
				'BD' => 'Bangladesh',
				'BB' => 'Barbados',
				'BY' => 'Belarus',
				'BE' => 'Belgium',
				'BZ' => 'Belize',
				'BJ' => 'Benin',
				'BM' => 'Bermuda',
				'BT' => 'Bhutan',
				'BO' => 'Bolivia',
				'BA' => 'Bosnia and Herzegovina',
				'BW' => 'Botswana',
				'BV' => 'Bouvet Island (Bouvetoya)',
				'BR' => 'Brazil',
				'IO' => 'British Indian Ocean Territory (Chagos Archipelago)',
				'VG' => 'British Virgin Islands',
				'BN' => 'Brunei Darussalam',
				'BG' => 'Bulgaria',
				'BF' => 'Burkina Faso',
				'BI' => 'Burundi',
				'KH' => 'Cambodia',
				'CM' => 'Cameroon',
				'CA' => 'Canada',
				'CV' => 'Cape Verde',
				'KY' => 'Cayman Islands',
				'CF' => 'Central African Republic',
				'TD' => 'Chad',
				'CL' => 'Chile',
				'CN' => 'China',
				'CX' => 'Christmas Island',
				'CC' => 'Cocos (Keeling) Islands',
				'CO' => 'Colombia',
				'KM' => 'Comoros the',
				'CD' => 'Congo',
				'CG' => 'Congo the',
				'CK' => 'Cook Islands',
				'CR' => 'Costa Rica',
				'CI' => 'Cote d\'Ivoire',
				'HR' => 'Croatia',
				'CU' => 'Cuba',
				'CY' => 'Cyprus',
				'CZ' => 'Czech Republic',
				'DK' => 'Denmark',
				'DJ' => 'Djibouti',
				'DM' => 'Dominica',
				'DO' => 'Dominican Republic',
				'EC' => 'Ecuador',
				'EG' => 'Egypt',
				'SV' => 'El Salvador',
				'GQ' => 'Equatorial Guinea',
				'ER' => 'Eritrea',
				'EE' => 'Estonia',
				'ET' => 'Ethiopia',
				'FO' => 'Faroe Islands',
				'FK' => 'Falkland Islands (Malvinas)',
				'FJ' => 'Fiji the Fiji Islands',
				'FI' => 'Finland',
				'FR' => 'France, French Republic',
				'GF' => 'French Guiana',
				'PF' => 'French Polynesia',
				'TF' => 'French Southern Territories',
				'GA' => 'Gabon',
				'GM' => 'Gambia the',
				'GE' => 'Georgia',
				'DE' => 'Germany',
				'GH' => 'Ghana',
				'GI' => 'Gibraltar',
				'GR' => 'Greece',
				'GL' => 'Greenland',
				'GD' => 'Grenada',
				'GP' => 'Guadeloupe',
				'GU' => 'Guam',
				'GT' => 'Guatemala',
				'GG' => 'Guernsey',
				'GN' => 'Guinea',
				'GW' => 'Guinea-Bissau',
				'GY' => 'Guyana',
				'HT' => 'Haiti',
				'HM' => 'Heard Island and McDonald Islands',
				'VA' => 'Holy See (Vatican City State)',
				'HN' => 'Honduras',
				'HK' => 'Hong Kong',
				'HU' => 'Hungary',
				'IS' => 'Iceland',
				'IN' => 'India',
				'ID' => 'Indonesia',
				'IR' => 'Iran',
				'IQ' => 'Iraq',
				'IE' => 'Ireland',
				'IM' => 'Isle of Man',
				'IL' => 'Israel',
				'IT' => 'Italy',
				'JM' => 'Jamaica',
				'JP' => 'Japan',
				'JE' => 'Jersey',
				'JO' => 'Jordan',
				'KZ' => 'Kazakhstan',
				'KE' => 'Kenya',
				'KI' => 'Kiribati',
				'KP' => 'Korea',
				'KR' => 'Korea',
				'KW' => 'Kuwait',
				'KG' => 'Kyrgyz Republic',
				'LA' => 'Lao',
				'LV' => 'Latvia',
				'LB' => 'Lebanon',
				'LS' => 'Lesotho',
				'LR' => 'Liberia',
				'LY' => 'Libyan Arab Jamahiriya',
				'LI' => 'Liechtenstein',
				'LT' => 'Lithuania',
				'LU' => 'Luxembourg',
				'MO' => 'Macao',
				'MK' => 'Macedonia',
				'MG' => 'Madagascar',
				'MW' => 'Malawi',
				'MY' => 'Malaysia',
				'MV' => 'Maldives',
				'ML' => 'Mali',
				'MT' => 'Malta',
				'MH' => 'Marshall Islands',
				'MQ' => 'Martinique',
				'MR' => 'Mauritania',
				'MU' => 'Mauritius',
				'YT' => 'Mayotte',
				'MX' => 'Mexico',
				'FM' => 'Micronesia',
				'MD' => 'Moldova',
				'MC' => 'Monaco',
				'MN' => 'Mongolia',
				'ME' => 'Montenegro',
				'MS' => 'Montserrat',
				'MA' => 'Morocco',
				'MZ' => 'Mozambique',
				'MM' => 'Myanmar',
				'NA' => 'Namibia',
				'NR' => 'Nauru',
				'NP' => 'Nepal',
				'AN' => 'Netherlands Antilles',
				'NL' => 'Netherlands the',
				'NC' => 'New Caledonia',
				'NZ' => 'New Zealand',
				'NI' => 'Nicaragua',
				'NE' => 'Niger',
				'NG' => 'Nigeria',
				'NU' => 'Niue',
				'NF' => 'Norfolk Island',
				'MP' => 'Northern Mariana Islands',
				'NO' => 'Norway',
				'OM' => 'Oman',
				'PK' => 'Pakistan',
				'PW' => 'Palau',
				'PS' => 'Palestinian Territory',
				'PA' => 'Panama',
				'PG' => 'Papua New Guinea',
				'PY' => 'Paraguay',
				'PE' => 'Peru',
				'PH' => 'Philippines',
				'PN' => 'Pitcairn Islands',
				'PL' => 'Poland',
				'PT' => 'Portugal, Portuguese Republic',
				'PR' => 'Puerto Rico',
				'QA' => 'Qatar',
				'RE' => 'Reunion',
				'RO' => 'Romania',
				'RU' => 'Russian Federation',
				'RW' => 'Rwanda',
				'BL' => 'Saint Barthelemy',
				'SH' => 'Saint Helena',
				'KN' => 'Saint Kitts and Nevis',
				'LC' => 'Saint Lucia',
				'MF' => 'Saint Martin',
				'PM' => 'Saint Pierre and Miquelon',
				'VC' => 'Saint Vincent and the Grenadines',
				'WS' => 'Samoa',
				'SM' => 'San Marino',
				'ST' => 'Sao Tome and Principe',
				'SA' => 'Saudi Arabia',
				'SN' => 'Senegal',
				'RS' => 'Serbia',
				'SC' => 'Seychelles',
				'SL' => 'Sierra Leone',
				'SG' => 'Singapore',
				'SK' => 'Slovakia (Slovak Republic)',
				'SI' => 'Slovenia',
				'SB' => 'Solomon Islands',
				'SO' => 'Somalia, Somali Republic',
				'ZA' => 'South Africa',
				'GS' => 'South Georgia and the South Sandwich Islands',
				'ES' => 'Spain',
				'LK' => 'Sri Lanka',
				'SD' => 'Sudan',
				'SR' => 'Suriname',
				'SJ' => 'Svalbard & Jan Mayen Islands',
				'SZ' => 'Swaziland',
				'SE' => 'Sweden',
				'CH' => 'Switzerland, Swiss Confederation',
				'SY' => 'Syrian Arab Republic',
				'TW' => 'Taiwan',
				'TJ' => 'Tajikistan',
				'TZ' => 'Tanzania',
				'TH' => 'Thailand',
				'TL' => 'Timor-Leste',
				'TG' => 'Togo',
				'TK' => 'Tokelau',
				'TO' => 'Tonga',
				'TT' => 'Trinidad and Tobago',
				'TN' => 'Tunisia',
				'TR' => 'Turkey',
				'TM' => 'Turkmenistan',
				'TC' => 'Turks and Caicos Islands',
				'TV' => 'Tuvalu',
				'UG' => 'Uganda',
				'UA' => 'Ukraine',
				'AE' => 'United Arab Emirates',
				'GB' => 'United Kingdom',
				'US' => 'United States of America',
				'UM' => 'United States Minor Outlying Islands',
				'VI' => 'United States Virgin Islands',
				'UY' => 'Uruguay, Eastern Republic of',
				'UZ' => 'Uzbekistan',
				'VU' => 'Vanuatu',
				'VE' => 'Venezuela',
				'VN' => 'Vietnam',
				'WF' => 'Wallis and Futuna',
				'EH' => 'Western Sahara',
				'YE' => 'Yemen',
				'ZM' => 'Zambia',
				'ZW' => 'Zimbabwe'
			];
			$pages = get_pages();
			$pagesList = [];

			foreach ($pages as $page) {
				$pagesList[$page->ID] = $page->post_title;
			}

			Container::make('post_meta', __('Rules'))
				->where('post_type', '=', 'ipd_rule')
				->add_fields([
					Field::make('multiselect', 'ipd_countries', __('White Listed Countries'))
						->add_options($countryList),
					Field::make('multiselect', 'ipd_effected_pages', __('Effected Pages'))
						->add_options($pagesList),
					Field::make('rich_text', 'ipd_white_list_text', __('White List Text')),
					Field::make('rich_text', 'ipd_black_list_text', __('Black List Text'))
				]);

			Container::make('theme_options', __('Options'))
				->set_page_parent('edit.php?post_type=ipd_rule')
				->add_fields(array(
					Field::make('radio', 'ipd_place', __('Place on page'))
						->set_options(array(
							'before_content' => __('Before content'),
							'after_content' => __('After content'),
						)),
					Field::make('checkbox', 'ipd_behind_cloudflare', __('Is the site behind CloudFlare?'))
						->set_option_value('yes'),
					Field::make('checkbox', 'ipd_debug_mode', __('Debug mode'))
						->set_option_value('yes'),
					Field::make('textarea', 'ipd_css', __('Css customize'))
						->set_rows(8)
				));

		}

		public static function plugin_post_type()
		{

			$labels = array(
				'name' => __('Rules', 'post type general name'),
				'singular_name' => __('Ip Definition rule', 'post type singular name'),
				'menu_name' => __('Ip Definition', 'admin menu'),
				'name_admin_bar' => __('Ip Definition rule', 'add new on admin bar'),
				'add_new' => __('Add rule', 'book'),
				'add_new_item' => __('Add new'),
				'new_item' => __('New'),
				'edit_item' => __('Edit'),
				'view_item' => __('View'),
				'all_items' => __('All rules'),
				'search_items' => __('Search'),
				'parent_item_colon' => __('Parent element:'),
				'not_found' => __('Not found'),
				'not_found_in_trash' => __('Not found in trash')
			);

			$args = array(
				'labels' => $labels,
				'description' => '',
				'taxonomies' => [],
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				'rewrite' => ['slug' => 'ipd-rules'],
				'capability_type' => 'post',
				'has_archive' => false,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => ['title'],
				'menu_icon' => 'dashicons-update-alt'
			);

			register_post_type('ipd_rule', $args);

			add_filter('manage_ipd_rule_posts_columns', 'ipd_rule_posts_columns');
			function ipd_rule_posts_columns()
			{
				return array(
					'cb' => '<input type="checkbox" />',
					'title' => __('Title'),
					'ipb_shortcode' => __('Shortcode'),
					'date' => __('Date')
				);
			}

			add_action('manage_ipd_rule_posts_custom_column', 'ipd_rule_posts_custom_column', 10, 2);
			function ipd_rule_posts_custom_column($column, $post_id)
			{
				switch ($column) {
					case 'ipb_shortcode' :
						echo '[ip_definition id=' . $post_id . ']';
						break;
				}
			}

		}

		public static function plugin_shortcode($attrs)
		{
			if (!empty($attrs)) {

				extract($attrs);

				$ipd_countries = carbon_get_post_meta($id, 'ipd_countries');

				$text = IpDefinition::get_text($id, $ipd_countries);
				return IpDefinition::plugin_output_block($text);

			} else {
				return false;
			}

		}

		public static function plugin_output_handler()
		{
			if (is_page()) {

				add_filter('the_content', 'content_handler');

				function content_handler($content)
				{
					$query_args_meta = array(
						'posts_per_page' => -1,
						'post_type' => 'ipd_rule',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key' => 'ipd_effected_pages',
								'value' => get_the_ID(),
								'compare' => 'IN'
							),
						)
					);

					$ipb_rules = get_posts($query_args_meta);
					$out = '';

					foreach ($ipb_rules as $ipb_rule) {

						$ipd_countries = carbon_get_post_meta($ipb_rule->ID, 'ipd_countries');
						$text = IpDefinition::get_text($ipb_rule->ID, $ipd_countries);
						$out .= IpDefinition::plugin_output_block($text);

					}

					switch (IpDefinition::ipd_place()) {
						case 'before_content':
							return $out . $content;
						case 'after_content':
							return $content . $out;
						default:
							return $content;
					}


				}
			}

		}

		public static function plugin_output_block($text)
		{

			$css_styles = IpDefinition::get_css_styles();
			$debug_mode = IpDefinition::is_debug_mode();
			$ip = IpDefinition::get_ip();
			ob_start();

			?>
			<style>
				.ipb_block {
				<?php echo $css_styles;?>
				}
			</style>
			<div class="ipb_block">

				<?php if ($debug_mode) {
					echo '<strong>' . $ip . '</strong><br/>';
				} ?>

				<?php echo $text; ?>
			</div>
			<?php

			$content = ob_get_contents();
			ob_end_clean();

			return $content;


		}

		public static function get_ip()
		{
			if (IpDefinition::is_behind_cloudflare()) {
				if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$http_x_headers = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
					$_SERVER['REMOTE_ADDR'] = $http_x_headers[0];
				}
			}

			return $_SERVER['REMOTE_ADDR'];
		}

		public static function get_country_code()
		{
			$reader = new Reader(plugin_dir_path(__FILE__) . 'db/GeoLite2-Country.mmdb');
			$country_code = $reader->country(IpDefinition::get_ip())->country->isoCode;
			return strtoupper($country_code);
		}

		public static function is_behind_cloudflare()
		{
			return carbon_get_theme_option('ipd_behind_cloudflare');
		}

		public static function ipd_place()
		{
			return carbon_get_theme_option('ipd_place');
		}

		public static function is_debug_mode()
		{
			return carbon_get_theme_option('ipd_debug_mode');
		}

		public static function get_css_styles()
		{
			return carbon_get_theme_option('ipd_css');
		}


		public static function get_text($ipb_rule_id, $ipd_countries)
		{
			if (in_array(IpDefinition::get_country_code(), $ipd_countries)) {
				$text = carbon_get_post_meta($ipb_rule_id, 'ipd_white_list_text');
			} else {
				$text = carbon_get_post_meta($ipb_rule_id, 'ipd_black_list_text');
			}

			return $text;
		}

	}

	add_action('plugins_loaded', ['IpDefinition', 'plugin_init']);

}
