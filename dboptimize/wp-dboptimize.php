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
	 * Return instance of Updraft_Logger
	 *
	 * @return Updraft_Logger
	 */
	public static function get_logger() {
		if (empty(self::$_logger_instance)) {
			include_once('includes/class-updraft-logger.php');
			self::$_logger_instance = new Updraft_Logger();
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
	 * Check whether one of free/Premium is active (whether it is this instance or not)
	 *
	 * @param String $which - 'free' or 'premium'
	 *
	 * @return String|Boolean - plugin path (if installed) or false if not
	 */
	private function is_active($which = 'free') {
		$active_plugins = $this->get_active_plugins();
		foreach ($active_plugins as $file) {
			if ('wp-optimize.php' == basename($file)) {
				$plugin_dir = WP_PLUGIN_DIR.'/'.dirname($file);
				if (('free' == $which && !file_exists($plugin_dir.'/premium.php')) || ('free' != $which && file_exists($plugin_dir.'/premium.php'))) return $file;
			}
		}
		return false;
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
	 * Returns array of translations used in javascript code.
	 *
	 * @return array
	 */
	public function wpo_js_translations() {
		return apply_filters('wpo_js_translations', array(
			'automatic_backup_before_optimizations' => __('Automatic backup before optimizations', 'wp-optimize'),
			'error_unexpected_response' => __('An unexpected response was received.', 'wp-optimize'),
			'optimization_complete' => __('Optimization complete', 'wp-optimize'),
			'with_warnings' => __('(with warnings - open the browser console for more details)', 'wp-optimize'),
			'optimizing_table' => __('Optimizing table:', 'wp-optimize'),
			'run_optimizations' => __('Run optimizations', 'wp-optimize'),
			'table_optimization_timeout' => 120000,
			'cancel' => __('Cancel', 'wp-optimize'),
			'cancelling' => __('Cancelling...', 'wp-optimize'),
			'enable' => __('Enable', 'wp-optimize'),
			'disable' => __('Disable', 'wp-optimize'),
			'please_select_settings_file' => __('Please, select settings file.', 'wp-optimize'),
			'are_you_sure_you_want_to_remove_logging_destination' => __('Are you sure you want to remove this logging destination?', 'wp-optimize'),
			'fill_all_settings_fields' => __('Before saving, you need to complete the currently incomplete settings (or remove them).', 'wp-optimize'),
			'table_was_not_repaired' => __('%s was not repaired. For more details, please check the logs (configured in your logging destinations settings).', 'wp-optimize'),
			'table_was_not_deleted' => __('%s was not deleted. For more details, please check your logs configured in logging destinations settings.', 'wp-optimize'),
			'please_use_positive_integers' => __('Please use positive integers.', 'wp-optimize'),
			'please_use_valid_values' => __('Please use valid values.', 'wp-optimize'),
			'update' => __('Update', 'wp-optimize'),
			'run_now' => __('Run now', 'wp-optimize'),
			'starting_preload' => __('Started preload...', 'wp-optimize'),
			'loading_urls' => __('Loading URLs...', 'wp-optimize'),
			'current_cache_size' => __('Current cache size:', 'wp-optimize'),
			'number_of_files' => __('Number of files:', 'wp-optimize'),
			'toggle_info' => __('Show information', 'wp-optimize'),
			'page_refresh' => __('Refreshing the page to reflect changes...', 'wp-optimize'),
			'settings_have_been_deleted_successfully' => __('WP-Optimize settings have been deleted successfully.', 'wp-optimize'),
			'loading_data' => __('Loading data...', 'wp-optimize'),
			'spinner_src' => esc_attr(admin_url('images/spinner-2x.gif')),
			'settings_page_url' => admin_url('admin.php?page=wpo_settings'),
			'sites' => $this->get_sites(),
			'user_always_ignores_table_delete_warning' => (get_user_meta(get_current_user_id(), 'wpo-ignores-table-delete-warning', true)) ? true : false,
			'post_meta_tweak_completed' => __('The tweak has been performed.', 'wp-optimize'),
		));
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
	 * Scheduler public functions to update schedulers
	 *
	 * @param  array $schedules An array of schedules being passed.
	 * @return array            An array of schedules being returned.
	 */
	public function cron_schedules($schedules) {
		$schedules['wpo_daily'] = array('interval' => 86400, 'display' => 'Once Daily');
		$schedules['wpo_weekly'] = array('interval' => 86400 * 7, 'display' => 'Once Weekly');
		$schedules['wpo_fortnightly'] = array('interval' => 86400 * 14, 'display' => 'Once Every Fortnight');
		$schedules['wpo_monthly'] = array('interval' => 86400 * 30, 'display' => 'Once Every Month');
		return $schedules;
	}

	/**
	 * Returns count of overdue cron jobs.
	 *
	 * @return integer
	 */
	public function howmany_overdue_crons() {
		$how_many_overdue = 0;
		if (function_exists('_get_cron_array') || (is_file(ABSPATH.WPINC.'/cron.php') && include_once(ABSPATH.WPINC.'/cron.php') && function_exists('_get_cron_array'))) {
			$crons = _get_cron_array();
			if (is_array($crons)) {
				$timenow = time();
				foreach ($crons as $jt => $job) {
					if ($jt < $timenow) {
						$how_many_overdue++;
					}
				}
			}
		}
		return $how_many_overdue;
	}

	/**
	 * Run updates on plugin activation.
	 */
	public function run_updates() {
		include_once('includes/class-wp-optimize-updates.php');
		WP_Optimize_Updates::check_updates();
	}




	public function order_sort($a, $b) {
		if ($a['order'] == $b['order']) return 0;
		return ($a['order'] > $b['order']) ? 1 : -1;
	}
	
	private function wp_normalize_path($path) {
		// Wp_normalize_path is not present before WP 3.9.
		if (function_exists('wp_normalize_path')) return wp_normalize_path($path);
		// Taken from WP 4.6.
		$path = str_replace('\\', '/', $path);
		$path = preg_replace('|(?<=.)/+|', '/', $path);
		if (':' === substr($path, 1, 1)) {
			$path = ucfirst($path);
		}
		return $path;
	}

	public function get_templates_dir() {
		return apply_filters('wp_optimize_templates_dir', $this->wp_normalize_path('templates'));
	}

	public function get_templates_url() {
		return apply_filters('wp_optimize_templates_url', WPO_PLUGIN_URL.'/templates');
	}

	/**
	 * Return or output view content
	 *
	 * @param String  $path                   - path to template, usually relative to templates/ within the WP-O directory
	 * @param Boolean $return_instead_of_echo - what to do with the results
	 * @param Array	  $extract_these		  - key/value pairs for substitution into the scope of the template
	 *
	 * @return String|Void
	 */
	public function include_template($path, $return_instead_of_echo = false, $extract_these = array()) {
		if ($return_instead_of_echo) ob_start();

		if (preg_match('#^([^/]+)/(.*)$#', $path, $matches)) {
			$prefix = $matches[1];
			$suffix = $matches[2];
			if (isset($this->template_directories[$prefix])) {
				$template_file = $this->template_directories[$prefix].'/'.$suffix;
			}
		}

		if (!isset($template_file)) {
			$template_file = 'templates/'.$path;
		}

		$template_file = apply_filters('wp_optimize_template', $template_file, $path);

		do_action('wp_optimize_before_template', $path, $template_file, $return_instead_of_echo, $extract_these);

		if (!file_exists($template_file)) {
			error_log("WP Optimize: template not found: ".$template_file);
			echo __('Error:', 'wp-optimize').' '.__('template not found', 'wp-optimize')." (".$path.")";
		} else {
			extract($extract_these);
			// The following are useful variables which can be used in the template.
			// They appear as unused, but may be used in the $template_file.
			$wpdb = $GLOBALS['wpdb'];// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $wpdb might be used in the included template
			$wp_optimize = $this;// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $wp_optimize might be used in the included template
			$optimizer = $this->get_optimizer();// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $optimizer might be used in the included template
			$options = $this->get_options();// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $options might be used in the included template
			$wp_optimize_notices = $this->get_notices();// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $wp_optimize_notices might be used in the included template
			include $template_file;
		}

		do_action('wp_optimize_after_template', $path, $template_file, $return_instead_of_echo, $extract_these);

		if ($return_instead_of_echo) return ob_get_clean();
	}

	/**
	 * Build a list of template directories (stored in self::$template_directories)
	 */
	private function register_template_directories() {

		$template_directories = array();

		$templates_dir = $this->get_templates_dir();

		if ($dh = opendir($templates_dir)) {
			while (($file = readdir($dh)) !== false) {
				if ('.' == $file || '..' == $file) continue;
				if (is_dir($templates_dir.'/'.$file)) {
					$template_directories[$file] = $templates_dir.'/'.$file;
				}
			}
			closedir($dh);
		}

		// Optimal hook for most extensions to hook into.
		$this->template_directories = apply_filters('wp_optimize_template_directories', $template_directories);

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
	 * Schedule cron tasks used by plugin.
	 *
	 * @return void
	 */
	public function schedule_plugin_cron_tasks() {
		if (!wp_next_scheduled('wpo_weekly_cron_tasks')) {
			wp_schedule_event(current_time("timestamp", 0), 'weekly', 'wpo_weekly_cron_tasks');
		}

		add_action('wpo_weekly_cron_tasks', array($this, 'do_weekly_cron_tasks'));
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
	 * @param String  $url					  - URL to be check to see if it an updraftplus match.
	 * @param String  $text					  - Text to be entered within the href a tags.
	 * @param String  $html					  - Any specific HTML to be added.
	 * @param String  $class				  - Specify a class for the href (including the attribute label)
	 * @param Boolean $return_instead_of_echo - if set, then the result will be returned, not echo-ed.
	 *
	 * @return String|void
	 */
	public function wp_optimize_url($url, $text, $html = '', $class = '', $return_instead_of_echo = false) {
		// Check if the URL is UpdraftPlus.
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
		// Check if the URL is UpdraftPlus.
		if (false !== strpos($url, '//updraftplus.com')) {
			// Set URL with Affiliate ID.
			$url = add_query_arg(array('afref' => $this->get_notices()->get_affiliate_id()), $url);

			// Apply filters.
			$url = apply_filters('wpoptimize_updraftplus_com_link', $url);
		}
		return apply_filters('wpoptimize_maybe_add_affiliate_params', $url);
	}

	/**
	 * Setup WPO logger(s)
	 */
	public function setup_loggers() {

		$logger = $this->get_logger();
		$loggers = $this->wpo_loggers();

		if (!empty($loggers)) {
			foreach ($loggers as $_logger) {
				$logger->add_logger($_logger);
			}
		}

		add_action('wp_optimize_after_optimizations', array($this, 'after_optimizations_logger_action'));
	}

	/**
	 * Run logger actions after all optimizations done
	 */
	public function after_optimizations_logger_action() {
		$loggers = $this->get_logger()->get_loggers();
		if (!empty($loggers)) {
			foreach ($loggers as $logger) {
				if (is_a($logger, 'Updraft_Email_Logger')) {
					$logger->flush_log();
				}
			}
		}
	}

	/**
	 * Returns list of WPO loggers instances
	 * Apply filter wp_optimize_loggers
	 *
	 * @return array
	 */
	public function wpo_loggers() {

		$loggers = array();
		$loggers_classes_by_id = array();
		$options_keys = array();

		$loggers_classes = $this->get_loggers_classes();

		foreach ($loggers_classes as $logger_class => $source) {
			$loggers_classes_by_id[strtolower($logger_class)] = $logger_class;
		}

		$options = $this->get_options();
		
		$saved_loggers = $options->get_option('logging');
		$logger_additional_options = $options->get_option('logging-additional');

		// create loggers classes instances.
		if (!empty($saved_loggers)) {
			// check for previous version options format.
			$keys = array_keys($saved_loggers);

			// if options stored in old format then reformat it.
			if (false == is_numeric($keys[0])) {
				$_saved_loggers = array();
				foreach ($saved_loggers as $logger_id => $enabled) {
					if ($enabled) {
						$_saved_loggers[] = $logger_id;
					}
				}

				// fill email with admin.
				if (array_key_exists('updraft_email_logger', $saved_loggers) && $saved_loggers['updraft_email_logger']) {
					$logger_additional_options['updraft_email_logger'] = array(
						get_option('admin_email')
					);
				}

				$saved_loggers = $_saved_loggers;
			}

			foreach ($saved_loggers as $i => $logger_id) {

				if (!array_key_exists($logger_id, $loggers_classes_by_id)) continue;

				$logger_class = $loggers_classes_by_id[$logger_id];

				$logger = new $logger_class();

				$logger_options = $logger->get_options_list();

				if (!empty($logger_options)) {
					foreach (array_keys($logger_options) as $option_name) {
						if (array_key_exists($option_name, $options_keys)) {
							$options_keys[$option_name]++;
						} else {
							$options_keys[$option_name] = 0;
						}

						$option_value = isset($logger_additional_options[$option_name][$options_keys[$option_name]]) ? $logger_additional_options[$option_name][$options_keys[$option_name]] : '';

						// if options in old format then get correct value.
						if ('' === $option_value && array_key_exists($logger_id, $logger_additional_options)) {
							$option_value = array_shift($logger_additional_options[$logger_id]);
						}

						$logger->set_option($option_name, $option_value);
					}
				}

				// check if logger is active.
				$active = (!is_array($logger_additional_options) || (array_key_exists('active', $logger_additional_options) && empty($logger_additional_options['active'][$i]))) ? false : true;

				if ($active) {
					$logger->enable();
				} else {
					$logger->disable();
				}

				$loggers[] = $logger;
			}
		}

		$loggers = apply_filters('wp_optimize_loggers', $loggers);

		return $loggers;
	}

	/**
	 * Returns associative array with logger class name in a key and path to class file in a value.
	 *
	 * @return array
	 */
	public function get_loggers_classes() {
		$loggers_classes = array(
			'Updraft_PHP_Logger' => 'includes/class-updraft-php-logger.php',
			'Updraft_Email_Logger' => 'includes/class-updraft-email-logger.php',
			'Updraft_Ring_Logger' => 'includes/class-updraft-ring-logger.php'
		);

		$loggers_classes = apply_filters('wp_optimize_loggers_classes', $loggers_classes);

		if (!empty($loggers_classes)) {
			foreach ($loggers_classes as $logger_class => $logger_file) {
				if (!class_exists($logger_class)) {
					if (is_file($logger_file)) {
						include_once($logger_file);
					}
				}
			}
		}

		return $loggers_classes;
	}

	/**
	 * Returns information about all loggers classes.
	 *
	 * @return array
	 */
	public function get_loggers_classes_info() {
		$loggers_classes = $this->get_loggers_classes();

		$loggers_classes_info = array();

		if (!empty($loggers_classes)) {
			foreach (array_keys($loggers_classes) as $logger_class_name) {

				if (!class_exists($logger_class_name)) continue;

				$logger_id = strtolower($logger_class_name);
				$logger_class = new $logger_class_name();

				$loggers_classes_info[$logger_id] = array(
					'description' => $logger_class->get_description(),
					'available' => $logger_class->is_available(),
					'allow_multiple' => $logger_class->is_allow_multiple(),
					'options' => $logger_class->get_options_list()
				);
			}
		}

		return $loggers_classes_info;
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
	 * Returns true if current user can run optimizations.
	 *
	 * @return bool
	 */
	public function can_run_optimizations() {
		// we don't check permissions for cron jobs.
		if (defined('DOING_CRON') && DOING_CRON) return true;

		if (self::is_premium() && false == user_can(get_current_user_id(), 'wpo_run_optimizations')) return false;
		return true;
	}

	/**
	 * Returns true if current user can manage plugin options.
	 *
	 * @return bool
	 */
	public function can_manage_options() {
		if (self::is_premium() && false == user_can(get_current_user_id(), 'wpo_manage_settings')) return false;
		return true;
	}

	/**
	 * CHeck if current user can purge the cache.
	 *
	 * @return bool
	 */
	public function can_purge_the_cache() {
		if (self::is_premium()) {
			return WP_Optimize_Premium()->can_purge_the_cache();
		}

		return true;
	}

	/**
	 * Output information message for users who have no permissions to run optimizations.
	 */
	public function prevent_run_optimizations_message() {
		$this->include_template('info-message.php', false, array('message' => __('You have no permissions to run optimizations.', 'wp-optimize')));
	}

	/**
	 * Output information message for users who have no permissions to manage settings.
	 */
	public function prevent_manage_options_info() {
		$this->include_template('info-message.php', false, array('message' => __('You have no permissions to manage WP-Optimize settings.', 'wp-optimize')));
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
	 * Output success/error messages from $output array.
	 *
	 * @param array $output ['messages' => success messages, 'errors' => error messages]
	 */
	private function wpo_render_output_messages($output) {
		foreach ($output['messages'] as $item) {
			echo '<div class="updated fade below-h2"><strong>'.$item.'</strong></div>';
		}

		foreach ($output['errors'] as $item) {
			echo '<div class="error fade below-h2"><strong>'.$item.'</strong></div>';
		}
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
	 * Log fatal errors to defined log destinations.
	 */
	public function log_fatal_errors() {
		$last_error = error_get_last();

		if (isset($last_error['type']) && E_ERROR === $last_error['type']) {
			$this->get_logger()->critical($last_error['message']);
		}
	}

	/**
	 * Close browser connection and continue script work. - Taken from UpdraftPlus
	 *
	 * @param array $txt Response to browser; this must be JSON (or if not, alter the Content-Type header handling below)
	 * @return void
	 */
	public function close_browser_connection($txt = '') {
		if (!headers_sent()) {
			// Close browser connection so that it can resume AJAX polling
			header('Content-Length: '.(empty($txt) ? '0' : 4+strlen($txt)));
			header('Connection: close');
			header('Content-Encoding: none');
		}

		if (session_id()) session_write_close();
		echo "\r\n\r\n";
		echo $txt;
		// These two added - 19-Feb-15 - started being required on local dev machine, for unknown reason (probably some plugin that started an output buffer).
		$ob_level = ob_get_level();
		while ($ob_level > 0) {
			ob_end_flush();
			$ob_level--;
		}
		flush();
		if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
	}


	/**
	 * Try to change PHP script time limit.
	 */
	public function change_time_limit() {
		$time_limit = (defined('WP_OPTIMIZE_SET_TIME_LIMIT') && WP_OPTIMIZE_SET_TIME_LIMIT > 15) ? WP_OPTIMIZE_SET_TIME_LIMIT : 1800;

		// Try to reduce the chances of PHP self-terminating via reaching max_execution_time.
		@set_time_limit($time_limit); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
	}

	/**
	 * Does the request come from UDC
	 *
	 * @return boolean
	 */
	public function is_updraft_central_request() {
		return defined('UPDRAFTCENTRAL_COMMAND') && UPDRAFTCENTRAL_COMMAND;
	}

	/**
	 * Does the data need to be included in this request. Currently only true if the request is made from UpdraftCentral.
	 *
	 * @return boolean
	 */
	public function template_should_include_data() {
		/**
		 * Filters wether data should be included in certain templates or not.
		 */
		return apply_filters('wpo_template_should_include_data', $this->is_updraft_central_request());
	}

	/**
	 * Load the templates for the modal window
	 */
	public function load_modal_template() {
		$this->include_template('modal.php');
	}

	/**
	 * Delete transients and semaphores data from options table.
	 */
	public function delete_transients_and_semaphores() {
		global $wpdb;

		$masks = array(
			'updraft_locked_wpo_%',
			'updraft_unlocked_wpo_%',
			'updraft_last_lock_time_wpo_%',
			'updraft_semaphore_wpo_%',
			'wpo_locked_%',
			'wpo_unlocked_%',
			'wpo_last_lock_time_%',
			'wpo_semaphore_%',
			'_transient_timeout_wpo_%',
			'_transient_wpo_%',
		);

		$where_parts = array();
		foreach ($masks as $mask) {
			$where_parts[] = "(`option_name` LIKE '{$mask}')";
		}

		$wpdb->query("DELETE FROM {$wpdb->options} WHERE " . join(' OR ', $where_parts));
	}

	/**
	 * Prevents bots from indexing plugins list
	 */
	public function robots_txt($output) {
		$output .= "\n";
		$output .= "Disallow: /uploads/wpo-plugins-tables-list.json\n";
		return $output;
	}
}


function WP_DbOptimize() {
	return WP_DbOptimize::instance();
}

endif;

$GLOBALS['wp_optimize'] = WP_DbOptimize();
