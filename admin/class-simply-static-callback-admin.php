<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.yeswehack.com
 * @since      1.0.0
 *
 * @package    Simply_Static_Callback
 * @subpackage Simply_Static_Callback/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * 
 * @package    Simply_Static_Callback
 * @subpackage Simply_Static_Callback/admin
 * @author     Arthur Bouchard <a.bouchard@yeswehack.com>
 */
class Simply_Static_Callback_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Simply_Static_Callback_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Simply_Static_Callback_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/simply-static-callback-admin.css', array( Simply_Static\Plugin::SLUG . '-admin-styles' ), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Simply_Static_Callback_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Simply_Static_Callback_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/simply-static-callback-admin.js', array( 'jquery', Simply_Static\Plugin::SLUG . '-settings-styles' ), $this->version, false );

	}

    public function netlify_status_admin_bar_callback(){
        global $wp_admin_bar;
        $wp_admin_bar->add_node(
            array(
                'id' => 'netlify-status-topbar',
                'title' => '<div id="netlify-badge" class="netlify-status-badge"><img src="https://api.netlify.com/api/v1/badges/f48f2c36-41b6-4a08-a36d-624d68fbe87d/deploy-status"></div>',
                'parent' => 'top-secondary'
            )
        );
    }


    public function simply_static_settings_view_tab_callback() {
        ?> <a class='nav-tab' id='callback-tab' href='#tab-callback'><?php echo _e( 'Callback', 'simply-static-callback' ); ?></a> <?php
    }

    public function simply_static_class_name_callback($class_name, $task_name) {
        return 'callback' === $task_name ? 'Simply_Static_Callback\\' . ucwords( $task_name ) . '_Task' : $class_name;
    }

    public function simply_static_options_callback($options) {
        $plugin = \Simply_Static\Plugin::instance();

        return array_merge($options, [
            'callback_enabled' => $plugin->fetch_post_value( 'callback_enabled' ),
            'github_token' => $plugin->fetch_post_value('github_token'),
            'github_username' => $plugin->fetch_post_value('github_username'),
            'github_repo' => $plugin->fetch_post_value('github_repo'),
            'callback_home' => $plugin->fetch_post_value('callback_home'),
            'callback_deploy_url' => $plugin->fetch_post_value('callback_deploy_url'),
            'callback_request_method' => $plugin->fetch_post_value('callback_request_method'),
        ]);
    }

    public function simplystatic_archive_creation_job_task_list_callback($task_list, $delivery_method) {
        $options = \Simply_Static\Options::instance();

        if ($options->get('callback_enabled') === '1') {
            $task_list[] = 'callback';
//          $task_list = ['callback']; // Debug only
        }

        return $task_list;
    }

    public function simply_static_settings_view_form_callback() {
        $options = \Simply_Static\Options::instance();
        ?>

        <div id='callback' class='tab-pane'>
            <h2 class="title"><?php _e( "Callback on complete", 'simply-static-callback' ); ?></h2>
            <p><?php _e( "If you want to create an automated callback on generation complete you can specify the fields below.", 'simply-static' ); ?></p>
            <table class='form-table' id='callback'>
                <tbody>
                <tr>
                    <th>
                        <label for='callbackEnabled'><?php _e( "Enable callback", 'simply-static-callback' ); ?></label>
                    </th>
                    <td>
                        <input type='checkbox' id='callbackEnabled' name='callback_enabled' value='1' <?php echo esc_attr( $options->get('callback_enabled') === '1' ? 'checked="checked"' : '' ) ?> />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for='githubUserName'><?php _e( "Github UserName", 'simply-static-callback' ); ?></label>
                    </th>
                    <td>
                        <input type='text' id='githubUserName' size="100" name='github_username' value='<?php echo esc_attr( $options->get('github_username') ) ?>' placeholder="username" />
                    </td>
                </tr>
				<tr>
                    <th>
                        <label for='githubRepo'><?php _e( "Github Repo", 'simply-static-callback' ); ?></label>
                    </th>
                    <td>
                        <input type='text' id='githubRepo' size="100" name='github_repo' value='<?php echo esc_attr( $options->get('github_repo') ) ?>' placeholder="repo-name" />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for='githubToken'><?php _e( "Github Token", 'simply-static-callback' ); ?></label>
                    </th>
                    <td>
                        <input type='text' id='githubToken' size="100" name='github_token' value='<?php echo esc_attr( $options->get('github_token') ) ?>' placeholder="token" />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for='callbackRequestMethod'><?php _e( "Callback request method", 'simply-static-callback' ); ?></label>
                    </th>
                    <td>
                        <select id="callbackRequestMethod" name="callback_request_method">
                            <option value="POST" <?php \Simply_Static\Util::selected_if( $options->get('callback_request_method') === 'POST' ) ?>>POST</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for='callbackHome'><?php _e( "Home URL", 'simply-static-callback' ); ?></label>
                    </th>
                    <td>
                        <input type='text' id='callbackHome' size="100" name='callback_home' value='<?php echo home_url() ?>' placeholder='<?php echo home_url() ?>' />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for='callbackDeployUrl'><?php _e( "Deploy URL", 'simply-static-callback' ); ?></label>
                    </th>
                    <td>
                    <input type='text' id='callbackDeployUrl' size="100" name='callback_deploy_url' value='<?php echo esc_attr( $options->get('callback_deploy_url') ) ?>' placeholder="https://example.com/callback.php" />
                    </td>
                </tr>
                </tbody>
            </table>

            <p class='submit'>
                <input class='button button-primary' type='submit' name='save' value='<?php _e( "Save Changes", 'simply-static-callback' );?>' />
            </p>
        </div>
        <?php
    }
}
