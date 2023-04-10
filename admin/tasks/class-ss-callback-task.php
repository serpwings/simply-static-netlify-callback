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
		
		$github_username = $this->options->get('github_username');
		$github_repo = $this->options->get('github_repo');
		$github_token = $this->options->get( 'github_token' ) ;

		if ($github_token && $github_username && $github_repo) {
			$url = 'https://api.github.com/repos/' . $github_username . '/' . $github_repo . '/dispatches';

			$headers = array(
                  'Accept' => 'application/vnd.github.v3+json',
                  'Content-Type' => 'application/json',
                  'Authorization' => 'Bearer ' . $github_token,
              );
		
			// Push needed data in $data
			$method = $this->options->get( 'callback_request_method' );
			$args = array(
				'method' => $method,
				'headers' => $headers,
				'sslverify' => $ssl_verification,
				'body' => json_encode(array(
					'event_type' => 'wordpress',
					"client_payload"=> array(
						'callback_home' => $this->options->get( 'callback_home' ),
						"callback_deploy_url"=> $this->options->get( 'callback_deploy_url' ),
						"archive_name"=> $this->options->get( 'archive_name' ),
						"page_404"=> $this->options->get( 'page_404' ),
						"page_robots"=> $this->options->get( 'page_robots' ),
						"page_redirects"=> $this->options->get( 'page_redirects' ),
						"page_search"=> $this->options->get( 'page_search' ),
						)
					)
				)
			);
	  }

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
}
