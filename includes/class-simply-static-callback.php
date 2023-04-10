<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across admin area.
 *
 * @link       https://www.yeswehack.com
 * @since      1.0.0
 *
 * @package    Simply_Static_Callback
 * @subpackage Simply_Static_Callback/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Simply_Static_Callback
 * @subpackage Simply_Static_Callback/includes
 * @author     Arthur Bouchard <a.bouchard@yeswehack.com>
 */
class Simply_Static_Callback {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Simply_Static_Callback_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SIMPLY_STATIC_CALLBACK_VERSION' ) ) {
			$this->version = SIMPLY_STATIC_CALLBACK_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'simply-static-callback';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();

    }

    static function simplyStaticIsActivated() {
        $active_plugins_basenames = get_option( 'active_plugins' );
        foreach ( $active_plugins_basenames as $plugin_basename ) {
            if ( false !== strpos( $plugin_basename, '/simply-static.php' ) ) {
				
				if (!function_exists('is_plugin_active')) {
					include_once(ABSPATH . 'wp-admin/includes/plugin.php');
				}
				
                return is_plugin_active($plugin_basename) ? $plugin_basename : false;
            }
        }

        return false;
    }

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Simply_Static_Callback_Loader. Orchestrates the hooks of the plugin.
	 * - Simply_Static_Callback_i18n. Defines internationalization functionality.
	 * - Simply_Static_Callback_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simply-static-callback-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simply-static-callback-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-simply-static-callback-admin.php';

		$this->loader = new Simply_Static_Callback_Loader();


        $this->load_simply_static_dependencies();

        /**
         * The callback task for simply static plugin
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/tasks/class-ss-callback-task.php';
	}

    private function load_simply_static_dependencies() {

        $SSBasename = self::simplyStaticIsActivated();
        $SSFullPath = WP_PLUGIN_DIR . '/' . plugin_dir_path($SSBasename);

        if (!$SSBasename ) {
            wp_die('Sorry, but this plugin requires the Simply Static Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
        }

        // Require SSTask to extend our callback task
        require_once $SSFullPath . 'src/tasks/class-ss-task.php';
    }

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Simply_Static_Callback_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Simply_Static_Callback_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Simply_Static_Callback_Admin( $this->get_plugin_name(), $this->get_version() );

        // actions
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'simply_static_settings_view_tab', $plugin_admin, 'simply_static_settings_view_tab_callback' );
        $this->loader->add_action( 'simply_static_settings_view_form', $plugin_admin, 'simply_static_settings_view_form_callback' );

        // filters
        $this->loader->add_filter( 'simply_static_class_name', $plugin_admin, 'simply_static_class_name_callback', 10, 2 );
        $this->loader->add_filter( 'simply_static_options', $plugin_admin, 'simply_static_options_callback', 10, 1 );
        $this->loader->add_filter( 'simplystatic.archive_creation_job.task_list', $plugin_admin, 'simplystatic_archive_creation_job_task_list_callback', 20, 2 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Simply_Static_Callback_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
