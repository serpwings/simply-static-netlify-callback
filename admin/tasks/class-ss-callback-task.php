<?php

namespace Simply_Static_Callback;

use Simply_Static\Options;

class Callback_Task extends \Simply_Static\Task {

	/**
	 * @var string
	 */
	protected static $task_name = 'callback';

	/**
	 * @var int
	 */
	protected $try;

	/**
	 * @var \WP_Http
	 */
	protected $http;

	/**
	 * @var Callback_Task
	 */
	protected static $_instance;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->trials = 3;
		$this->http = new \WP_Http();
	}

	public static function getInstance() {

		if (!isset(self::$_instance)) {
			self::$_instance = new Callback_Task();
		}

		return self::$_instance;
	}

	/**
	 * Call callback at the end
	 * @return boolean|\WP_Error true if done, false if not done, WP_Error if error
	 */
	public function perform() {
		$url = $this->options->get( 'callback_url' );
		$ssl_verification = $this->options->get( 'callback_ssl_disabled' ) == '1' ? false : true;

		// Push needed data in $data
		$data = array();
		self::add_callback_data($data);

		if ($this->options->get('callback_request_method' === 'GET')) {
			$url .= '?'.http_build_query($data);
		}

		$headers = $this->options->get( 'callback_request_headers' );

		$method = $this->options->get( 'callback_request_method' );

		$args = array(
			'method' => $method,
			'headers' => $headers,
			'sslverify' => $ssl_verification,
			'body' => $data,
		);

        $this->save_status_message(sprintf('Making callback on: %s', $url), 'simply-static');

        // Loop to try multiple times
        for ($t = 1; $t <= $this->trials; $t++) {
            $response = $this->http->request($url, $args);

            if ($response instanceof \WP_Error) {
                $this->save_status_message( __( sprintf('[Try #%s] An error occured on callback url [%s] : %s', $t, $response->get_error_code(), $response->get_error_message()), 'simply-static'.$t ) );

                if ($this->trials === $t) {
                    return $response;
                }

                sleep(2);
            } else {
                break;
            }
        }

        $this->save_status_message( __( sprintf('Callback responded with [%s] : %s', $response['response']['code'], $response['body']), 'simply-static' ) );

        return true;
	}

    public static function add_callback_data(&$data) {
        $options = Options::instance();

        if ($options->get( 'callback_data_delivery_method' ) === '1'){
            $data['delivery_method'] = $options->get( 'delivery_method' );
			$data['callback_home'] = $options->get( 'callback_home' );
			$data['callback_deploy_url'] = $options->get( 'callback_deploy_url' );
			
            switch($options->get( 'delivery_method' )) {
                case 'zip':
                    $options->get( 'callback_data_temp_files_dir' ) === '1' ? $data['temp_files_dir'] = $options->get( 'temp_files_dir' ) : null;
                    $options->get( 'callback_data_archive_name' ) === '1' ? $data['archive_name'] = sprintf('%s.%s', $options->get( 'archive_name' ), 'zip') : null;
                    break;
                case 'local':
                    $options->get( 'callback_data_local_dir' ) === '1' ? $data['local_dir'] = $options->get( 'local_dir' ) : null;
                    break;
            }
        }
    }
}
