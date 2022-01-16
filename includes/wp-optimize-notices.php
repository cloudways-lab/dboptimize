<?php

if (!class_exists('DbOptimize_Notices_1_0')) require_once(WPODB_PLUGIN_MAIN_PATH.'includes/dbobtimize-notices.php');

class WP_DbOptimize_Notices extends DbOptimize_Notices_1_0 {

	protected static $_instance = null;

	private $initialized = false;

	protected $self_affiliate_id = 216;

	protected $notices_content = array();

	/**
	 * Creates and returns the only notice instance
	 *
	 * @return object WP_DbOptimize_Notices instance
	 */
	public static function instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * This method gets any parent notices and adds its own notices to the notice array
	 *
	 * @return Array returns an array of notices
	 */
	protected function populate_notices_content() {
		
		$parent_notice_content = parent::populate_notices_content();

		$child_notice_content = array(
			
		);

		return array_merge($parent_notice_content, $child_notice_content);
	}
	
	/**
	 * Call this method to setup the notices
	 */
	public function notices_init() {
		if ($this->initialized) return;
		$this->initialized = true;
		$this->notices_content = (defined('WP_DBOPTIMIZE_NOADS_B') && WP_DBOPTIMIZE_NOADS_B) ? array() : $this->populate_notices_content();
	}


	/**
	 * This method will call the is premium function in the WPO object to check if this install is premium and if it is we won't display the notice
	 *
	 * @return boolean a bool to indicate if we should display the notice or not
	 */
	protected function is_wpo_premium_installed() {
		if (WP_DbOptimize::is_premium()) {
			return false;
		}

		return true;
	}

	/**
	 * This function will check if we should display the rate notice or not
	 *
	 * @return boolean - to indicate if we should show the notice or not
	 */
	protected function show_rate_notice() {
		
		$options = WP_DbOptimize()->get_options();
		$installed = $options->get_option('installed-for', 0);
		$installed_for = time() - $installed;
		
		if ($installed && $installed_for > 28*86400) {
			return true;
		}

		return false;
	}

	

	/**
	 * This method checks to see if the notices dismiss_time parameter has been dismissed
	 *
	 * @param  String $dismiss_time a string containing the dimiss time ID
	 * @return Boolean returns true if the notice has been dismissed and shouldn't be shown otherwise display it
	 */
	protected function check_notice_dismissed($dismiss_time) {

		$time_now = (defined('WP_DBOPTIMIZE_NOTICES_FORCE_TIME') ? WP_DBOPTIMIZE_NOTICES_FORCE_TIME : time());
	
		$options = WP_DbOptimize()->get_options();

		$notice_dismiss = ($time_now < $options->get_option($dismiss_time, 0));

		return $notice_dismiss;
	}

	/**
	 * Check notice data for seasonal info and return true if we should display this notice.
	 *
	 * @param array $notice_data
	 * @return bool
	 */
	protected function skip_seasonal_notices($notice_data) {
		$time_now = defined('WPO_NOTICES_FORCE_TIME') ? WPO_NOTICES_FORCE_TIME : time();
		// Do not show seasonal notices in Premium version.
		if (false === WP_DbOptimize::is_premium()) {
			$valid_from = strtotime($notice_data['valid_from']);
			$valid_to = strtotime($notice_data['valid_to']);
			$dismiss = $this->check_notice_dismissed($notice_data['dismiss_time']);
			if (($time_now >= $valid_from && $time_now <= $valid_to) && !$dismiss) {
				// return true so that we return this notice to be displayed
				return true;
			}
		}

		return false;
	}

	
}

$GLOBALS['wp_dboptimize_notices'] = WP_DbOptimize_Notices::instance();
