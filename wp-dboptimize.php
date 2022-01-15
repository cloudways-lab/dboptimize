<?php

// Check to make sure if WP_Optimize is already call and returns.
if (!class_exists('WP_DbOptimize')) :

class WP_DbOptimize {

	
	private $template_directories;

	protected static $_instance = null;

	protected static $_optimizer_instance = null;

	protected static $_options_instance = null;

	protected static $_notices_instance = null;

	protected static $_logger_instance = null;

	protected static $_browser_cache = null;

	protected static $_db_info = null;


	protected static $_gzip_compression = null;

	/**
	 * Class constructor
	 */
	public function __construct() {
	}

	
	public static function instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function get_optimizer() {
		if (empty(self::$_optimizer_instance)) {

			if (!class_exists('WP_DbOptimizer')) include_once('includes/class-wp-optimizer.php');

			self::$_optimizer_instance = new WP_DbOptimizer();
			
		}
		return self::$_optimizer_instance;
	}



	public static function get_options() {
		if (empty(self::$_options_instance)) {
			if (!class_exists('WP_DbOptimize_Options')) include_once('includes/class-wp-optimize-options.php');
			self::$_options_instance = new WP_DbOptimize_Options();
		}
		return self::$_options_instance;
	}

	public static function get_notices() {
		if (empty(self::$_notices_instance)) {
			if (!class_exists('WP_DbOptimize_Notices')) include_once('includes/wp-optimize-notices.php');
			self::$_notices_instance = new WP_DbOptimize_Notices();
		}
		return self::$_notices_instance;
	}

	/**
	 * Returns instance if WPO_Page_Cache class.
	 *
	 * @return WPO_Page_Cache
	 */
	public function get_page_cache() {
		if (!class_exists('WPO_Page_Cache')) include_once('cache/class-wpo-page-cache.php');

		return WPO_Page_Cache::instance();
	}

	/**
	 * Create instance of WP_Optimize_Browser_Cache.
	 *
	 * @return WP_Optimize_Browser_Cache
	 */
	public static function get_browser_cache() {
		if (empty(self::$_browser_cache)) {
			if (!class_exists('WP_Optimize_Browser_Cache')) include_once('includes/class-wp-optimize-browser-cache.php');
			self::$_browser_cache = new WP_DbOptimize_Browser_Cache();
		}
		return self::$_browser_cache;
	}

	/**
	 * Returns WP_Optimize_Database_Information instance.
	 *
	 * @return WP_Optimize_Database_Information
	 */
	public function get_db_info() {
		if (empty(self::$_db_info)) {
			if (!class_exists('WP_DbOptimize_Database_Information')) include_once('includes/wp-optimize-database-information.php');
			self::$_db_info = new WP_DbOptimize_Database_Information();
		}
		return self::$_db_info;
	}

	/**
	 * Returns instance of WP_Optimize_Gzip_Compression.
	 *
	 * @return WP_Optimize_Gzip_Compression
	 */
	static public function get_gzip_compression() {
		if (empty(self::$_gzip_compression)) {
			if (!class_exists('WP_DbOptimize_Gzip_Compression')) include_once('includes/class-wp-optimize-gzip-compression.php');
			self::$_gzip_compression = new WP_DbOptimize_Gzip_Compression();
		}
		return self::$_gzip_compression;
	}

	/**
	 * Create instance of WP_Optimize_Htaccess.
	 *
	 * @param string $htaccess_file absolute path to htaccess file, by default it use .htaccess in WordPress root directory.
	 * @return WP_Optimize_Htaccess
	 */
	public static function get_htaccess($htaccess_file = '') {
		if (!class_exists('WP_DbOptimize_Cache')) {
			include_once('includes/class-wp-optimize-htaccess.php');
		}

		return new WP_Optimize_Htaccess($htaccess_file);
	}

	/**
	 * Return instance of DbOptimize_Logger
	 *
	 * @return DbOptimize_Logger
	 */
	public static function get_logger() {
		if (empty(self::$_logger_instance)) {
			include_once('includes/class-dboptimize-logger.php');
			self::$_logger_instance = new DbOptimize_Logger();
		}
		return self::$_logger_instance;
	}


	/**
	 * Indicate whether we have an associated instance of WP-Optimize Premium or not.
	 *
	 * @returns Boolean
	 */
	public static function is_premium() {
		return true;
	}

	/**
	 * Check if script running on Apache web server. $is_apache is set in wp-includes/vars.php. Also returns true if the server uses litespeed.
	 *
	 * @return bool
	 */
	public function is_apache_server() {
		global $is_apache;
		return $is_apache;
	}

	/**
	 * Check if script running on IIS web server.
	 *
	 * @return bool
	 */
	public function is_IIS_server() {
		global $is_IIS, $is_iis7;
		return $is_IIS || $is_iis7;
	}

	/**
	 * Check if Apache module or modules active.
	 *
	 * @param string|array $module - single Apache module name or list of Apache module names.
	 *
	 * @return bool|null - if null, the result was indeterminate
	 */
	public function is_apache_module_loaded($module) {
		if (!$this->is_apache_server()) return false;
		
		if (!function_exists('apache_get_modules')) return null;

		$module_loaded = true;

		if (is_array($module)) {
			foreach ($module as $single_module) {
				if (!in_array($single_module, apache_get_modules())) {
					$module_loaded = false;
					break;
				}
			}
		} else {
			$module_loaded = in_array($module, apache_get_modules());
		}

		return $module_loaded;
	}


	/**
	 * Gets an array of plugins active on either the current site, or site-wide
	 *
	 * @return Array - a list of plugin paths (relative to the plugin directory)
	 */
	private function get_active_plugins() {

		// Gets all active plugins on the current site
		$active_plugins = get_option('active_plugins');

		if (is_multisite()) {
			$network_active_plugins = get_site_option('active_sitewide_plugins');
			if (!empty($network_active_plugins)) {
				$network_active_plugins = array_keys($network_active_plugins);
				$active_plugins = array_merge($active_plugins, $network_active_plugins);
			}
		}

		return $active_plugins;
	}

	/**
	 * This function checks whether a specific plugin is installed, and returns information about it
	 *
	 * @param  string $name Specify "Plugin Name" to return details about it.
	 * @return array        Returns an array of details such as if installed, the name of the plugin and if it is active.
	 */
	public function is_installed($name) {

		// Needed to have the 'get_plugins()' function
		include_once(ABSPATH.'wp-admin/includes/plugin.php');

		// Gets all plugins available
		$get_plugins = get_plugins();

		$active_plugins = $this->get_active_plugins();

		$plugin_info = array();
		$plugin_info['installed'] = false;
		$plugin_info['active'] = false;

		// Loops around each plugin available.
		foreach ($get_plugins as $key => $value) {
			// If the plugin name matches that of the specified name, it will gather details.
			if ($value['Name'] != $name && $value['TextDomain'] != $name) continue;
			$plugin_info['installed'] = true;
			$plugin_info['name'] = $key;
			$plugin_info['version'] = $value['Version'];
			if (in_array($key, $active_plugins)) {
				$plugin_info['active'] = true;
			}
			break;
		}
		return $plugin_info;
	}


	
	/**
	 * Schedules cron event based on selected schedule type
	 *
	 * @return void
	 */
	public function cron_activate() {
		$gmt_offset = (int) (3600 * get_option('gmt_offset'));

		$options = $this->get_options();

		if ($options->get_option('schedule') === false) {
			$options->set_default_options();
		} else {
			if ('true' == $options->get_option('schedule')) {
				if (!wp_next_scheduled('wpo_cron_event2')) {
					$schedule_type = $options->get_option('schedule-type', 'wpo_weekly');

					// Backward compatibility
					if ('wpo_otherweekly' == $schedule_type) $schedule_type = 'wpo_fortnightly';

					$this_time = (86400 * 7);

					switch ($schedule_type) {
						case "wpo_daily":
							$this_time = 86400;
							break;

						case "wpo_weekly":
							$this_time = (86400 * 7);
							break;

						case "wpo_fortnightly":
							$this_time = (86400 * 14);
							break;

						case "wpo_monthly":
							$this_time = (86400 * 30);
							break;
					}

					add_action('wpo_cron_event2', array($this, 'cron_action'));
					wp_schedule_event((current_time("timestamp", 0) + $this_time - $gmt_offset), $schedule_type, 'wpo_cron_event2');
					WP_DbOptimize()->log('running wp_schedule_event()');
				}
			}
		}
	}

	/**
	 * Clears all cron events
	 *
	 * @return void
	 */
	public function wpo_cron_deactivate() {
		$cron_jobs = _get_cron_array();
		foreach ($cron_jobs as $job) {
			foreach (array_keys($job) as $hook) {
				if (preg_match('/^wpo_/', $hook)) wp_unschedule_hook($hook);
			}
		}
	}

	/**
	 * Message to debug
	 *
	 * @param string $message Message to insert into the log.
	 * @param array  $context array with variables used in $message like in template,
	 * 						  for ex.
	 *						  $message = 'Hello {message}';
	 * 						  $context = ['message' => 'world']
	 * 						  'Hello world' string will be saved in log.
	 */
	public function log($message, $context = array()) {
		$this->get_logger()->debug($message, $context);
	}

	/**
	 * Format Bytes Into KB/MB
	 *
	 * @param  mixed $bytes Number of bytes to be converted.
	 * @return integer        return the correct format size.
	 */
	public function format_size($bytes) {
		if (!is_numeric($bytes)) return __('N/A', 'wp-optimize');

		if (1073741824 <= $bytes) {
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		} elseif (1048576 <= $bytes) {
			$bytes = number_format($bytes / 1048576, 2) . ' MB';
		} elseif (1024 <= $bytes) {
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		} elseif (1 < $bytes) {
			$bytes = $bytes . ' bytes';
		} elseif (1 == $bytes) {
			$bytes = $bytes . ' byte';
		} else {
			$bytes = '0 bytes';
		}

		return $bytes;
	}

	/**
	 * Format a timestamp into a juman readable date time
	 *
	 * @param int $timestamp
	 * @return string
	 */
	public function format_date_time($timestamp) {
		return date_i18n(get_option('date_format').' @ '.get_option('time_format'), ($timestamp + get_option('gmt_offset') * 3600));
	}

	/**
	 * Executed this function on cron event.
	 *
	 * @return void
	 */
	public function cron_action() {

		$optimizer = $this->get_optimizer();
		$options = $this->get_options();

		$this->log('WPO: Starting cron_action()');

		if ('true' == $options->get_option('schedule')) {
			$this_options = $options->get_option('auto');

			// Currently the output of the optimizations is not saved/used/logged.
			$optimizer->do_optimizations($this_options, 'auto');
		}

	}


	/**
	 * Do plugin background tasks.
	 *
	 * @return void
	 */
	public function do_weekly_cron_tasks() {
		// add tasks here.
		$this->get_db_info()->update_plugin_json();
	}

	/**
	 * This will customize a URL with a correct Affiliate link
	 * This function can be update to suit any URL as longs as the URL is passed
	 *
	 * @param String  $url					  - URL to be check to see if it an dboptimize match.
	 * @param String  $text					  - Text to be entered within the href a tags.
	 * @param String  $html					  - Any specific HTML to be added.
	 * @param String  $class				  - Specify a class for the href (including the attribute label)
	 * @param Boolean $return_instead_of_echo - if set, then the result will be returned, not echo-ed.
	 *
	 * @return String|void
	 */
	public function wp_optimize_url($url, $text, $html = '', $class = '', $return_instead_of_echo = false) {
		// Check if the URL is DbOptimize.
		$url = $this->maybe_add_affiliate_params($url);		// Return URL - check if there is HTML such as images.
		if ('' != $html) {
			$result = '<a '.$class.' href="'.esc_attr($url).'">'.$html.'</a>';
		} else {
			$result = '<a '.$class.' href="'.esc_attr($url).'">'.htmlspecialchars($text).'</a>';
		}
		if ($return_instead_of_echo) return $result;
		echo $result;
	}

	/**
	 * Get an URL with an eventual affiliate ID
	 *
	 * @param string $url
	 * @return string
	 */
	public function maybe_add_affiliate_params($url) {
		// Check if the URL is DbOptimize.
		if (false !== strpos($url, '//dboptimize.com')) {
			// Set URL with Affiliate ID.
			$url = add_query_arg(array('afref' => $this->get_notices()->get_affiliate_id()), $url);

			// Apply filters.
			$url = apply_filters('wpoptimize_dboptimize_com_link', $url);
		}
		return apply_filters('wpoptimize_maybe_add_affiliate_params', $url);
	}


	/**
	 * Returns true if optimization works in multisite mode
	 *
	 * @return boolean
	 */
	public function is_multisite_mode() {
		return (is_multisite() && self::is_premium());
	}



	/**
	 * Returns list of all sites in multisite
	 *
	 * @return array
	 */
	public function get_sites() {
		$sites = array();
		// check if function get_sites exists (since 4.6.0) else use wp_get_sites.
		if (function_exists('get_sites')) {
			$sites = get_sites(array('network_id' => null, 'deleted' => 0, 'number' => 999999));
		} elseif (function_exists('wp_get_sites')) {
			$sites = wp_get_sites(array('network_id' => null, 'deleted' => 0, 'limit' => 999999));
		}
		return $sites;
	}

	/**
	 * Returns script memory limit in megabytes.
	 *
	 * @param bool $memory_limit
	 * @return int
	 */
	public function get_memory_limit($memory_limit = false) {
		// Returns in megabytes
		if (false == $memory_limit) $memory_limit = ini_get('memory_limit');
		$memory_limit = rtrim($memory_limit);

		return $this->return_bytes($memory_limit);
	}

	/**
	 * Returns free memory in bytes.
	 *
	 * @return int
	 */
	public function get_free_memory() {
		return $this->get_memory_limit() - memory_get_usage();
	}

	/**
	 * Checks PHP memory_limit and WP_MAX_MEMORY_LIMIT values and return minimal.
	 *
	 * @return int memory limit in bytes.
	 */
	public function get_script_memory_limit() {
		$memory_limit = $this->get_memory_limit();

		if (defined('WP_MAX_MEMORY_LIMIT')) {
			$wp_memory_limit = $this->get_memory_limit(WP_MAX_MEMORY_LIMIT);

			if ($wp_memory_limit > 0 && $wp_memory_limit < $memory_limit) {
				$memory_limit = $wp_memory_limit;
			}
		}

		return $memory_limit;
	}

	/**
	 * Returns max packet size for database.
	 *
	 * @return int|string
	 */
	public function get_max_packet_size() {
		global $wpdb;
		static $mp = 0;

		if ($mp > 0) return $mp;

		$mp = (int) $wpdb->get_var("SELECT @@session.max_allowed_packet");
		// Default to 1MB
		$mp = (is_numeric($mp) && $mp > 0) ? $mp : 1048576;
		// 32MB
		if ($mp < 33554432) {
			$save = $wpdb->show_errors(false);
			@$wpdb->query("SET GLOBAL max_allowed_packet=33554432");// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			$wpdb->show_errors($save);

			$mp = (int) $wpdb->get_var("SELECT @@session.max_allowed_packet");
			// Default to 1MB
			$mp = (is_numeric($mp) && $mp > 0) ? $mp : 1048576;
		}

		return $mp;
	}

	/**
	 * Converts shorthand memory notation value to bytes.
	 * From http://php.net/manual/en/function.ini-get.php
	 *
	 * @param string $val shorthand memory notation value.
	 */
	public function return_bytes($val) {
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		$val = (int) $val;
		switch ($last) {
			case 'g':
				$val *= 1024;
				// no break
			case 'm':
				$val *= 1024;
				// no break
			case 'k':
				$val *= 1024;
		}

		return $val;
	}



	/**
	 * Try to change PHP script time limit.
	 */
	public function change_time_limit() {
		$time_limit = (defined('WP_OPTIMIZE_SET_TIME_LIMIT') && WP_OPTIMIZE_SET_TIME_LIMIT > 15) ? WP_OPTIMIZE_SET_TIME_LIMIT : 1800;

		// Try to reduce the chances of PHP self-terminating via reaching max_execution_time.
		@set_time_limit($time_limit); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
	}


}


function WP_DbOptimize() {
	return WP_DbOptimize::instance();
}

endif;

$GLOBALS['wp_optimize'] = WP_DbOptimize();
