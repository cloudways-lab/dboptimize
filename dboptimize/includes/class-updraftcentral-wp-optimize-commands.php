<?php


class UpdraftCentral_WP_DbOptimize_Commands extends UpdraftCentral_Commands {
	
	private $commands;

	/**
	 * Class constructor
	 */
	public function __construct() {
		if (!class_exists('WP_DbOptimize_Commands')) include_once(__DIR__.'includes/class-commands.php');
		$this->commands = new WP_DbOptimize_Commands();
		
	}

	/**
	 * Magic method to pass on the command to WP_DbOptimize_Commands
	 *
	 * @param String $name		- command name
	 * @param Array	 $arguments	- command parameters
	 *
	 * @return Array - response
	 */
	public function __call($name, $arguments) {
	
		if (!is_callable(array($this->commands, $name))) {
			return $this->_generic_error_response('wp_dboptimize_no_such_command', $name);
		}
		
		$result = call_user_func_array(array($this->commands, $name), $arguments);
		
		if (is_wp_error($result)) {
			return $this->_generic_error_response($result->get_error_code(), $result->get_error_data());
		} else {
			return $this->_response($result);
		}
	}
}
