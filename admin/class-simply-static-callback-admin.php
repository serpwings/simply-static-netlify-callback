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

    public function simply_static_settings_view_tab_callback() {
        ?> <a class='nav-tab' id='callback-tab' href='#tab-callback'><?php echo _e( 'Callback', 'simply-static-callback' ); ?></a> <?php
    }

    public function simply_static_class_name_callback($class_name, $task_name) {
        return 'callback' === $task_name ? 'Simply_Static_Callback\\' . ucwords( $task_name ) . '_Task' : $class_name;
    }

    public function simply_static_options_callback($options) {
        $plugin = \Simply_Static\Plugin::instance();

        // Set callback request headers name and values
        $callback_request_headers = array();
        $callback_request_headers_name = array_values($plugin->fetch_post_array_value( 'callback_request_headers_name' ));
        $callback_request_headers_value = array_values($plugin->fetch_post_array_value( 'callback_request_headers_value' ));

        foreach($callback_request_headers_name as $index => $name) {
            if(!empty($name) && !empty($callback_request_headers_value[$index])){
                $callback_request_headers[$name] = $callback_request_headers_value[$index];
            }
        }

        return array_merge($options, [
            'callback_enabled' => $plugin->fetch_post_value( 'callback_enabled' ),
            'callback_url' => $plugin->fetch_post_value('callback_url'),
            'callback_home' => $plugin->fetch_post_value('callback_home'),
            'callback_deploy_url' => $plugin->fetch_post_value('callback_deploy_url'),
            'callback_ssl_disabled' => $plugin->fetch_post_value('callback_ssl_disabled'),
            'callback_request_method' => $plugin->fetch_post_value('callback_request_method'),
            'callback_request_headers' => $callback_request_headers,
            'callback_data_delivery_method' => $plugin->fetch_post_value('callback_data_delivery_method'),
            'callback_data_local_dir' => $plugin->fetch_post_value('callback_data_local_dir'),
            'callback_data_temp_files_dir' => $plugin->fetch_post_value('callback_data_temp_files_dir'),
            'callback_data_archive_name' => $plugin->fetch_post_value('callback_data_archive_name'),
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
                        <label for='callbackUrl'><?php _e( "Callback URL", 'simply-static-callback' ); ?></label>
                    </th>
                    <td>
                        <input type='text' id='callbackUrl' size="100" name='callback_url' value='<?php echo esc_attr( $options->get('callback_url') ) ?>' placeholder="https://example.com/callback.php" />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for='callbackSSLDisabled'><?php _e( "Disable SSL Verification", 'simply-static-callback' ); ?></label>
                    </th>
                    <td>
                        <input type='checkbox' id='callbackSSLDisabled' name='callback_ssl_disabled' value='1' <?php echo esc_attr( $options->get('callback_ssl_disabled') === '1' ? 'checked="checked"' : '' ) ?> />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for='callbackRequestMethod'><?php _e( "Callback request method", 'simply-static-callback' ); ?></label>
                    </th>
                    <td>
                        <select id="callbackRequestMethod" name="callback_request_method">
                            <option value="POST" <?php \Simply_Static\Util::selected_if( $options->get('callback_request_method') === 'POST' ) ?>>POST</option>
                            <option value="GET" <?php \Simply_Static\Util::selected_if( $options->get('callback_request_method') === 'GET' ) ?>>GET</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for='callbackRequestHeaders'><?php _e( "Callback request headers", 'simply-static-callback' ); ?></label>
                    </th>
                    <td style="padding:0;">
                        <table id="callback_request_headers">
                            <?php if(is_countable($options->get('callback_request_headers')) && count($options->get('callback_request_headers')) > 0): ?>
                                <?php $index = 0; ?>
                                <?php foreach($options->get('callback_request_headers') as $name => $value): ?>
                                    <tr index="<?php echo esc_attr($index); ?>">
                                        <td>
                                            <input type="text" name="callback_request_headers_name[<?php echo esc_attr($index); ?>]" value="<?php echo esc_attr($name); ?>" placeholder="Authorization" />
                                            <input type="text" name="callback_request_headers_value[<?php echo esc_attr($index); ?>]" value="<?php echo esc_attr($value); ?>" placeholder="Bearer xxxxxxxx" />
                                        </td>
                                        <td style="min-width:80px;">
                                            <button class="remove button-primary">-</button>
                                            <button class="add button-primary">+</button>
                                        </td>
                                    </tr>
                                    <?php $index++; ?>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr index="0">
                                    <td>
                                        <input type="text" name="callback_request_headers_name[0]" placeholder="Authorization" />
                                        <input type="text" name="callback_request_headers_value[0]" placeholder="Bearer xxxxxxxx" />
                                    </td>
                                    <td style="min-width:80px;">
                                        <button class="remove button-primary">-</button>
                                        <button class="add button-primary">+</button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <!-- empty hidden one for jQuery-->
                            <tr index="" class="cloneable">
                                <td>
                                    <input type="text" name="callback_request_headers_name[{{index}}]" disabled="disabled" placeholder="Authorization" />
                                    <input type="text" name="callback_request_headers_value[{{index}}]" disabled="disabled" placeholder="Bearer xxxxxxxx" />
                                </td>
                                <td style="min-width:80px;">
                                    <button class="remove button-primary">-</button>
                                    <button class="add button-primary">+</button>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for='callbackData'><?php _e( "Callback data", 'simply-static-callback' ); ?></label>
                    </th>
                    <td>
                        <input type='checkbox' id='callbackDataDeliveryMethod' name='callback_data_delivery_method' value='1' <?php echo esc_attr($options->get('callback_data_delivery_method') === '1' ? 'checked="checked"' : '') ?> />
                        <label for="callbackDataDeliveryMethod"><?php _e( "Delivery Method", 'simply-static-callback' ); ?></label>
                        <p class="description"><?php _e( "Local Directory or ZIP Archive", 'simply-static-callback' ); ?></p>
                        <br />
                        <input type='checkbox' id='callbackDataLocalDir' name='callback_data_local_dir' value='1' <?php echo esc_attr($options->get('callback_data_local_dir') === '1' ? 'checked="checked"' : '') ?> />
                        <label for="callbackDataLocalDir"><?php _e( "Local Directory", 'simply-static-callback' ); ?></label>
                        <p class="description"><?php _e( "Only used if delivery method is Local Directory", 'simply-static-callback' ); ?></p>
                        <br />
                        <input type='checkbox' id='callbackDataTempFilesDir' name='callback_data_temp_files_dir' value='1' <?php echo esc_attr($options->get('callback_data_temp_files_dir') === '1' ? 'checked="checked"' : '') ?> />
                        <label for="callbackDataTempFilesDir"><?php _e( "Temporary Files Directory", 'simply-static-callback' ); ?></label>
                        <p class="description"><?php _e( "Only used if delivery method is ZIP Archive", 'simply-static-callback' ); ?></p>
                        <br />
                        <input type='checkbox' id='callbackDataArchiveName' name='callback_data_archive_name' value='1' <?php echo esc_attr($options->get('callback_data_archive_name') === '1' ? 'checked="checked"' : '') ?> />
                        <label for="callbackDataArchiveName"><?php _e( "Generated archive name", 'simply-static-callback' ); ?></label>
                        <p class="description"><?php _e( "Only used if delivery method is ZIP Archive", 'simply-static-callback' ); ?></p>
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
