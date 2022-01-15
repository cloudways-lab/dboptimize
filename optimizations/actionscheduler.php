<?php
class WP_DbOptimization_actionscheduler extends WP_DbOptimization {

	
	public function optimize() {
		global $wpdb;
		$table = $wpdb->prefix . "actionscheduler_actions";
		$prefix = $wpdb->prefix;
		if ($wpdb->get_var("SHOW TABLES LIKE '" . $table . "'") == $table) {
			$sql = "DELETE FROM ".$prefix."actionscheduler_actions WHERE `status` = 'failed'";
			$sql1 = "DELETE FROM ".$prefix."actionscheduler_actions WHERE `status` = 'complete'";
			$sql2 = "DELETE FROM ".$prefix."actionscheduler_actions WHERE `status` = 'canceled'";
			$sql3 = "DELETE FROM ".$prefix."options WHERE `option_name` LIKE 'fio_%'";
			$wpdb->query($sql);
			$wpdb->query($sql1);
			$wpdb->query($sql2);
			$wpdb->query($sql3);

			// deleting log for scheduled webhook action
			$sql_scheduled_hook = "DELETE lg FROM ".$prefix."actionscheduler_logs lg
									LEFT JOIN ".$prefix."actionscheduler_actions aa
									ON aa.action_id = lg.action_id
									WHERE aa.status IS 'pending'
									AND aa.hook IS 'woocommerce_deliver_webhook_async'";
			$wpdb->query($sql_scheduled_hook);

			// delete trashed scheduled webhook action 
			$sql_trashed_schedule = "DELETE FROM ".$prefix."actionscheduler_actions
			WHERE status = 'pending'
			AND hook = 'woocommerce_deliver_webhook_async'";
			$wpdb->query($sql_trashed_schedule);


		} else {
			$table_found = false;
		}

	}

	public function after_get_info() {
		if ($this->table_found == false) {
			$message = sprintf('No action scheduler tables found', 'wp-dboptimize');
			$this->logger->info($message);
			$this->register_output($message);
		}
	}

	public function get_info() {
		global $wpdb;

		$table = $wpdb->prefix . "actionscheduler_actions";
		if ($wpdb->get_var("SHOW TABLES LIKE '" . $table . "'") == $table) {
			$this->table_found = false;
		}
	}

	public function settings_label() {
		return __('Delete Action schedulers', 'wp-dboptimize');
	}

	public function after_optimize() {
		global $wpdb;
		$table = $wpdb->prefix . "actionscheduler_actions";
		if ($wpdb->get_var("SHOW TABLES LIKE '" . $table . "'") == $table) {
			$message = sprintf('Action schedulers cleaned');
			$this->logger->info($message);
			$this->register_output($message);
		} else {
			$message = sprintf('0 action scheduler tables cleared', 'wp-dboptimize');
			$this->logger->info($message);
			$this->register_output($message);
		}

	}

}