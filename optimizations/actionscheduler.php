<?php
class WP_DbOptimization_actionscheduler extends WP_DbOptimization {

	
	public function optimize() {
		global $wpdb;

		$sql = "DELETE FROM `wp_actionscheduler_actions` WHERE `status` = 'failed'";
		$sql1 = "DELETE FROM `wp_actionscheduler_actions` WHERE `status` = 'complete'";
		$sql2 = "DELETE FROM `wp_actionscheduler_actions` WHERE `status` = 'canceled'";
		$sql3 = "DELETE FROM `wp_options` WHERE `option_name` LIKE 'fio_%'";	

		$wpdb->query($sql);
		$wpdb->query($sql1);
		$wpdb->query($sql2);
		$wpdb->query($sql3);
	}

	public function get_info() {

	}

	public function settings_label() {
		return __('Delete Action schedulers', 'wp-dboptimize');
	}

	public function after_optimize() {
		$message = sprintf('Action schedulers cleaned');

		
		$this->logger->info($message);
		$this->register_output($message);
	}

	

}	