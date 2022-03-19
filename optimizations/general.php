<?php

class WP_DbOptimization_general extends WP_DbOptimization {

	private $table_found = [];

	public function optimize() {
		global $wpdb;
		if (isset($this->table_found['cleantalk_sfw']) && $this->table_found['cleantalk_sfw']) {
			 $this->query('DELETE FROM '.$table_prefix.'cleantalk_sfw');
		}
		if (isset($this->table_found['wfknownfilelist'])  && $this->table_found['cleantalk_sfw']) {
			 $this->query('DELETE FROM '.$table_prefix.'wfknownfilelist');
		}
		if (isset($this->table_found['wfpendingissues']) && $this->table_found['cleantalk_sfw']) {
			 $this->query('DELETE FROM '.$table_prefix.'wfpendingissues');
		}
		if (isset($this->table_found['wffilemods'])  && $this->table_found['cleantalk_sfw']) {
			 $this->query('DELETE FROM '.$table_prefix.'wffilemods');
		}
		if (isset($this->table_found['email_stats']) && $this->table_found['cleantalk_sfw']) {
			 $this->query('DELETE FROM '.$table_prefix.'email_stats');
		}
		if (isset($this->table_found['cerber_lab']) && $this->table_found['cleantalk_sfw']) {
			 $this->query('DELETE FROM '.$table_prefix.'cerber_lab');
		}
		if (isset($this->table_found['cerber_lab_ip']) && $this->table_found['cleantalk_sfw']) {
			 $this->query('DELETE FROM '.$table_prefix.'cerber_lab_ip');
		}
		if (isset($this->table_found['bv_ip_store']) && $this->table_found['cleantalk_sfw']) {
			 $this->query('DELETE FROM '.$table_prefix.'bv_ip_store');
		}
		if (isset($this->table_found['rank_math_404_logs']) && $this->table_found['cleantalk_sfw']) {
			 $this->query('DELETE FROM '.$table_prefix.'rank_math_404_logs');
		}

	}

	public function after_get_info() {
		if (count($this->table_found)>0) {
			$message = sprintf('No tables for general cleanup found', 'wp-dboptimize');
			$this->logger->info($message);
			$this->register_output($message);
		}
	}

	public function get_info() {
		global $wpdb;

		if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "cleantalk_sfw" . "'") == $wpdb->prefix . "cleantalk_sfw") {
			$this->table_found['cleantalk_sfw'] = true;
		}
		if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "wfknownfilelist" . "'") == $wpdb->prefix . "wfknownfilelist") {
			$this->table_found['wfknownfilelist'] = true;
		}
		if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "wfpendingissues" . "'") == $wpdb->prefix . "wfpendingissues") {
			$this->table_found['wfpendingissues'] = true;
		}
		if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "wffilemods" . "'") == $wpdb->prefix . "wffilemods") {
			$this->table_found['wffilemods'] = true;
		}
		if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "email_stats" . "'") == $wpdb->prefix . "email_stats") {
			$this->table_found['email_stats'] = true;
		}
		if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "cerber_lab" . "'") == $wpdb->prefix . "cerber_lab") {
			$this->table_found['cerber_lab'] = true;
		}
		if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "cerber_lab_ip" . "'") == $wpdb->prefix . "cerber_lab_ip") {
			$this->table_found['cerber_lab_ip'] = true;
		}
		if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "bv_ip_store" . "'") == $wpdb->prefix . "bv_ip_store") {
			$this->table_found['bv_ip_store'] = true;
		}
		if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "rank_math_404_logs" . "'") == $wpdb->prefix . "rank_math_404_logs") {
			$this->table_found['rank_math_404_logs'] = true;
		}
	}

	public function settings_label() {
		return __('General tables cleanup', 'wp-dboptimize');
	}

	public function after_optimize() {
		global $wpdb;
		$message = sprintf('General cleanup executed');
		$this->logger->info($message);
		$this->register_output($message);

	}
}