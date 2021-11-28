<?php

require_once('wp-dboptimize.php');

if (!defined('WPODB_PLUGIN_MAIN_PATH')) define('WPODB_PLUGIN_MAIN_PATH', __DIR__);
if (!defined('WPODB_VERSION')) define('WPODB_VERSION', '1.0.0');

class WP_Optm_CLI_Command extends WP_CLI_Command {


	
	/**
	 * Command line params.
	 *
	 * @var array
	 */
	private $args;

	/**
	 *
	 * @param array $args 		command line params.
	 * @param array $assoc_args command line params in associative array.
	 */
	public function __invoke($args, $assoc_args) { // phpcs:ignore PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__invokeFound

		$this->args = $args;


		// change underscores to hypes in command.
		if (isset($args[0])) {
			$args[0] = str_replace('-', '_', $args[0]);
		}

		if (!empty($args) && is_callable(array($this, $args[0])) && $args[0] === 'optimizations') {
			call_user_func(array($this, $args[0]), $assoc_args);
			return;
		}

		if (!empty($args) && is_callable(array($this, $args[0])) && isset($args[1])) {
			call_user_func(array($this, $args[0]), $args[1], $assoc_args);
			return;
		}

		
	
		WP_CLI::log('usage: wp dboptimize <command> <optimization-id> [--site-id=<site-id>] [--param1=value1] [--param2=value2] ...');
		WP_CLI::log("\n".__('These are common WP-DBOptimize commands used in various situations:', 'wp-optimize')."\n");

		$commands = array(
			'version' => __('Display version of WP-DbOptimize', 'wp-dboptimize'),
			'sites' => __('Display list of sites in a WP multisite installation.', 'wp-dboptimize'),
			'optimizations' => __('Display available optimizations', 'wp-dboptimize'),
			'do-optimization' => __('Do selected optimization', 'wp-dboptimize'),
		);

		foreach ($commands as $command => $description) {
			WP_CLI::log(sprintf("     %-25s %s", $this->colorize($command, 'bright'), $description));
		}
	}

	/**
	 * Display WP-Optimize version.
	 */
	public function version() {
		WP_CLI::log(WPODB_VERSION);
	}

	/**
	 * Display list of optimizations.
	 */
	public function optimizations() {
		$optimizer = WP_DbOptimize()->get_optimizer();

		
		$optimizations = $optimizer->sort_optimizations($optimizer->get_optimizations());

		foreach ($optimizations as $id => $optimization) {

			if (false === $optimization->display_in_optimizations_list()) continue;

			// This is an array, with attributes dom_id, activated, settings_label, info; all values are strings.
			$html = $optimization->get_settings_html();

			WP_CLI::log(sprintf("     %-25s %s", $id, $html['settings_label']));
		}
	}

	public function all_optimizations()
	{
		$optimizer = WP_DbOptimize()->get_optimizer();

		$optimizations = $optimizer->sort_optimizations($optimizer->get_optimizations());
		$ret = [];
		foreach ($optimizations as $id => $optimization) {

			if (false === $optimization->display_in_optimizations_list()) continue;

			// This is an array, with attributes dom_id, activated, settings_label, info; all values are strings.
			$html = $optimization->get_settings_html();

			$ret[] = $id;
		}

		return $ret;
	}

	/**
	 * Display list of sites when on a multisite install
	 */
	public function sites() {
		if (!is_multisite()) {
			WP_CLI::error(__('This command is only available on a WP multisite installation.', 'wp-optimize'));
		}

		$sites = WP_DbOptimize()->get_sites();

		WP_CLI::log(sprintf("     %-15s %s", __('Site ID', 'wp-optimize'), __('Path', 'wp-optimize')));
		foreach ($sites as $site) {
			WP_CLI::log(sprintf("     %-15s %s", $site->blog_id, $site->domain.$site->path));
		}
	}

	/**
	 * Call do optimization command.
	 *
	 * @param array $assoc_args array with params for optimization, optimization_id item required.
	 */
	public function do_optimization($args, $assoc_args) {

		// if (!isset($assoc_args['optimization-id'])) {
		// 	WP_CLI::error(__('Please, select optimization.', 'wp-optimize'));
		// 	return;
		// }

		// does all optmizations at once
		if ($args == 'all') {
			$all_optimizations  = $this->all_optimizations();
			$assoc_args['optimization-id'] = implode(',', $all_optimizations);		

		} else {
			if ($args) {
				$assoc_args['optimization-id'] = $args;			
			}	
		}

		if (isset($assoc_args['site-id'])) {
			$assoc_args['site_id'] = array_values(array_map('trim', explode(',', $assoc_args['site-id'])));
		}

		if (isset($assoc_args['include-ui'])) {
			$assoc_args['include_ui_elements'] = array_values(array_map('trim', explode(',', $assoc_args['include-ui'])));
		} else {
			$assoc_args['include_ui_elements'] = false;
		}

		// save posted parameters in data item to make them available in optimization.

		$assoc_args['data'] = $assoc_args;

		

		// get array with optimization ids.
		$optimizations_ids = array_values(array_map('trim', explode(',', $assoc_args['optimization-id'])));
		

		foreach ($optimizations_ids as $optimization_id) {
			$assoc_args['optimization_id'] = $optimization_id;
			$results = $this->get_commands()->do_optimization($assoc_args);


			if (is_wp_error($results)) {
				WP_CLI::error($results);
			} elseif (!empty($results['errors'])) {
				$message = implode("\n", $results['errors']);
				WP_CLI::error($message);
			} else {
				$message = implode("\n", $results['result']->output);
				WP_CLI::success($message);
			}
		}
	}

	/**
	 * Return instance of WP_DbOptimize_Commands.
	 *
	 * @return WP_DbOptimize_Commands
	 */
	private function get_commands() {
		// Other commands, available for any remote method.
		if (!class_exists('WPCLI_DbOptimize_Commands')) include_once('class-commands.php');

		return new WPCLI_DbOptimize_Commands();
	}


	private function colorize($string, $color) {
		$tokens = array(
			'yellow' => '%y', // ['color' => 'yellow',
			'green' => '%g', // ['color' => 'green'],
			'blue' => '%b', // ['color' => 'blue'],
			'red' => '%r', // ['color' => 'red'],
			'magenta' => '%p', // ['color' => 'magenta'],
			'magenta' => '%m', // ['color' => 'magenta',
			'cyan' => '%c', // ['color' => 'cyan',
			'grey' => '%w', // ['color' => 'grey',
			'black' => '%k', // ['color' => 'black',
			'reset' => '%n', // ['color' => 'reset',
			'yellow_bright' => '%Y', // ['color' => 'yellow', 'style' => 'bright',
			'green_bright' => '%G', // ['color' => 'green', 'style' => 'bright',
			'blue_bright' => '%B', // ['color' => 'blue', 'style' => 'bright',
			'red_bright' => '%R', // ['color' => 'red', 'style' => 'bright',
			'magenta_bright' => '%P', // ['color' => 'magenta', 'style' => 'bright',
			'magenta_bright_2' => '%M', // ['color' => 'magenta', 'style' => 'bright',
			'cyan_bright' => '%C', // ['color' => 'cyan', 'style' => 'bright',
			'grey_bright' => '%W', // ['color' => 'grey', 'style' => 'bright',
			'black_bright' => '%K', // ['color' => 'black', 'style' => 'bright',
			'reset_bright' => '%N', // ['color' => 'reset', 'style' => 'bright',
			'yellow_bg' => '%3', // ['background' => 'yellow',
			'green_bg' => '%2', // ['background' => 'green',
			'blue_bg' => '%4', // ['background' => 'blue',
			'red_bg' => '%1', // ['background' => 'red',
			'magenta_bg' => '%5', // ['background' => 'magenta',
			'cyan_bg' => '%6', // ['background' => 'cyan',
			'grey_bg' => '%7', // ['background' => 'grey',
			'black_bg' => '%0', // ['background' => 'black',
			'blink' => '%F', // ['style' => 'blink',
			'underline' => '%U', // ['style' => 'underline',
			'inverse' => '%8', // ['style' => 'inverse',
			'bright' => '%9', // ['style' => 'bright',
			'bright_2' => '%_' // ['style' => 'bright']
		);

		$token = isset($tokens[$color]) ? $tokens[$color] : $tokens['bright'];
		return WP_CLI::colorize($token.$string.'%n');
	}
}

WP_CLI::add_command('dboptimize', 'WP_Optm_CLI_Command');
