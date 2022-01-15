<?php

/**
 * All commands that are intended to be available for calling from any sort of control interface (e.g. wp-admin, UpdraftCentral) go in here. All public methods should either return the data to be returned, or a WP_Error with associated error code, message and error data.
 */
class WP_DbOptimize_Commands {

	private $optimizer;

	private $options;

	private $wpo_sites; // used in get_optimizations_info command.

	public function __construct() {
		$this->optimizer = WP_DbOptimize()->get_optimizer();
		$this->options = WP_DbOptimize()->get_options();
	}

	public function get_version() {
		return WPO_VERSION;
	}

	public function enable_or_disable_feature($data) {
	
		$type = (string) $data['type'];
		$enable = (boolean) $data['enable'];
	
		$options = array($type => $enable);

		return $this->optimizer->trackback_comment_actions($options);
	}
	
	public function save_manual_run_optimization_options($sent_options) {
		return $this->options->save_sent_manual_run_optimization_options($sent_options);
	}


	/**
	 * Perform the requested optimization
	 *
	 * @param  array $params Should have keys 'optimization_id' and 'data'.
	 * @return array
	 */
	public function do_optimization($params) {
		
		if (!isset($params['optimization_id'])) {
			$results = array(
				'result' => false,
				'messages' => array(),
				'errors' => array(
					__('No optimization was indicated.', 'wp-optimize')
				)
			);
		} else {
			$optimization_id = $params['optimization_id'];
			$data = isset($params['data']) ? $params['data'] : array();
			$include_ui_elements = isset($data['include_ui_elements']) ? $data['include_ui_elements'] : false;
			
			$optimization = $this->optimizer->get_optimization($optimization_id, $data);
	
			$result = is_a($optimization, 'WP_DbOptimization') ? $optimization->do_optimization() : null;

			$results = array(
				'result' => $result,
				'messages' => array(),
				'errors' => array(),
			);

			
			if (is_wp_error($optimization)) {
				$results['errors'][] = $optimization->get_error_message().' ('.$optimization->get_error_code().')';
			}
	
		}
		return $results;
	}

	/**
	 * Preview command, used to show information about data should be optimized in popup tool.
	 *
	 * @param array $params Should have keys 'optimization_id', 'offset' and 'limit'.
	 *
	 * @return array
	 */
	public function preview($params) {
		if (!isset($params['optimization_id'])) {
			$results = array(
				'result' => false,
				'messages' => array(),
				'errors' => array(
					__('No optimization was indicated.', 'wp-optimize')
				)
			);
		} else {
			$optimization_id = $params['optimization_id'];
			$data = isset($params['data']) ? $params['data'] : array();
			$params['offset'] = isset($params['offset']) ? (int) $params['offset'] : 0;
			$params['limit'] = isset($params['limit']) ? (int) $params['limit'] : 50;

			$optimization = $this->optimizer->get_optimization($optimization_id, $data);

			if (is_a($optimization, 'WP_DbOptimization')) {
				if (isset($params['site_id'])) {
					$optimization->switch_to_blog((int) $params['site_id']);
				}
				$result = $optimization->preview($params);
			} else {
				$result = null;
			}

			$results = array(
				'result' => $result,
				'messages' => array(),
				'errors' => array()
			);
		}

		return $results;
	}

	/**
	 * Get information about requested optimization.
	 *
	 * @param array $params Should have keys 'optimization_id' and 'data'.
	 * @return array
	 */
	public function get_optimization_info($params) {
		if (!isset($params['optimization_id'])) {
			$results = array(
				'result' => false,
				'messages' => array(),
				'errors' => array(
					__('No optimization was indicated.', 'wp-optimize')
				)
			);
		} else {
			$optimization_id = $params['optimization_id'];
			$data = isset($params['data']) ? $params['data'] : array();
			$include_ui_elements = isset($data['include_ui_elements']) ? $data['include_ui_elements'] : false;

			$optimization = $this->optimizer->get_optimization($optimization_id, $data);
			$result = is_a($optimization, 'WP_DbOptimization') ? $optimization->get_optimization_info() : null;

			$results = array(
				'result' => $result,
				'messages' => array(),
				'errors' => array(),
			);
		}

		return $results;
	}



}
