<?php

class WP_DbOptimization_woocommerce extends WP_DbOptimization {

	
	public function after_optimize() {
        
		$message = sprintf(__('%s orphaned relationship data deleted', 'wp-dboptimize'),  $this->processed_count);
		$this->logger->info($message);
		$this->register_output($message);
	}

	
	public function optimize() {
        $clean = "DELETE FROM ".$this->wpdb->options."
        WHERE option_name LIKE '_wc_session_%' OR option_name LIKE '_wc_session_expires_%'";
		$sessiondata = $this->query($clean);
		$this->processed_count += $sessiondata;
	}

	public function after_get_info() {
		if ($this->found_count > 0) {
			$message = sprintf(_n('%s woocommerce session cleared', $this->found_count, 'wp-dboptimize'));
		} else {
			$message = __('No woocommerce session in your database', 'wp-dboptimize');
		}
		$this->register_output($message);
	}

	public function get_info() {
        $sql = "SELECT COUNT(*) FROM `" . $this->wpdb->options . "options` WHERE option_name like='_wc_session_%' OR option_name LIKE '_wc_session_expires_%' `);";
		$sessionData = $this->wpdb->get_var($sql);
		$this->found_count += $sessionData;
	}
	
	public function settings_label() {
		return __('Clean woocommerce sessions', 'wp-dboptimize');
	}
}

