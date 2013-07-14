<?php
/**
 * Plugin Name.
 *
 * @package   Quag_Writer_Help
 * @author    Andrea Barghigani e Daniele Scasciafratte
 * @license   GPL-2.0+
 * @link      http://wpandmore.info/quag-writer-help
 * @copyright 2013 WpAndMore
 */

/**
 * Plugin class.
 *
 *
 * @package Quag_Writer_Help
 * @author    Andrea Barghigani e Daniele Scasciafratte
 */
class Quag_Writer_Help {
	
	const PLUGIN_BASENAME = 'quag_writer_help';
	
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = '0.0.1';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'qwh';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	
	/**
	* Return the Const PLUGIN_BASENAME
	*
	* @since    1.0.0
	*
	* @var      string
	*/
	protected $plugin_basename = self::PLUGIN_BASENAME;
	
	/**
	* Return the Redirect Url
	*
	* @since    1.0.0
	*
	* @var      string
	*/
	protected $red_url = 'options-general.php?page=qwh_main';

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the options page and menu item.
		 add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Load admin style sheet and JavaScript.
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Define custom functionality. Read more about actions and filters: http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		add_action( 'admin_init', array( $this, 'load_setting' ) );
		add_filter( 'TODO', array( $this, 'filter_method_name' ) );
		
		//TODO: Verificare se la url del redirectè giusta
		$this->red_url = admin_url().'options-general.php?page=qwh_main';
		$options = get_option( 'qwh_options' );
		
		/*if($options['redirect_url'] != $this->red_url){
			$options['redirect_url'] == $this->red_url;
			update_option( 'qwh_options', $options );
		}*/
		
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		/*
		* Creo delle opzioni ed imposto il loro valore nell'attivazione
		*
		* - app_created = mi serve per capire se l'utente ha creato l'app
		* - autenticate = mi serve per capire se l'utente e' autenticato
		* - authorized = mi serve per capire se l'utente e' autorizzato
		*/
		
		// Verifico se la configurazione c'è altrimenti la creo
		if( false == get_option( 'qwh_options' ) ) {    
			 
			$qwh_options = array(
				'app_id' => false,
				'app_secret' => false,
				'redirect_url' => $this->red_url,
				'autenticate' => false,
				'authorized' => false,
				'app_created' => false
			);
			
			add_option( 'qwh_options', $qwh_options );
		} 	
		
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Quag Writer Help', $this->plugin_slug ),
			__( 'Quag Writer Help', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug."_main",
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Set the function for settings
	 *
	 * @since    1.0.0
	 */
	public function load_setting() {
		register_setting( 'autenticazione_quag', 'qwh_options', array($this, 'valido_input') );
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        WordPress Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Filter Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}
	
	/**
	 * Include library
	 *
	 * @since    1.0.0
	 */
	public function load_library() {
		require ('inc/quag/http.php');
		require ('inc/quag/oauth_client.php');
	}
	
	public function creo_form(){

		//creo la sezione per le impostazioni
		add_settings_section( 'sez_autenticazione_quag', 'Sezione Principale', array($this, 'campi_input'), self::PLUGIN_BASENAME );
		
		//creo i vari campi
		add_settings_field('qwh_app_id', 'Inserisci App ID', array($this, 'app_id_cb'), self::PLUGIN_BASENAME, 'sez_autenticazione_quag' );
		add_settings_field('qwh_app_sec', 'Inserisci App Secret', array($this, 'app_sec_cb'), self::PLUGIN_BASENAME, 'sez_autenticazione_quag' );
		add_settings_field('qwh_redirect_url', 'Redirect URL', array($this, 'redirect_url_cb'), self::PLUGIN_BASENAME, 'sez_autenticazione_quag' );
		//Show the form
		settings_errors();
		echo '<form action="options.php" method="post">';
		settings_fields( 'autenticazione_quag' ); 
		do_settings_sections( self::PLUGIN_BASENAME ); 
		submit_button();
		echo '</form>';
	}
	
	//Spiegazioni che riguardano la sezione di autenticazione per il plugin
	function campi_input(){
		echo "<p>Inserisci le chiavi dell'applicazione Quag.</p>";
	}
	//Aggiungo il campo input app id
	public function app_id_cb(){
		//prendo le opzioni salvate nel db
		$options = get_option( 'qwh_options' );
		$app_id = $options['app_id'];
		//mostro il campo
		echo "<input id='app_id' name='qwh_options[app_id]' type='text' value='{$options['app_id']}' />";
	}

	//Aggiungo il campo input App Secret
	public function app_sec_cb(){
		//prendo le opzioni salvate nel db
		$options = get_option( 'qwh_options' );
		$app_secret = $options['app_secret'];
		//mostro il campo
		echo "<input id='app_secret' name='qwh_options[app_secret]' type='text' value='{$options['app_secret']}' />";
	}

	//Aggiungo il campo input Redirect URL
	public function redirect_url_cb(){
		//prendo le opzioni salvate nel db
		$options = get_option( 'qwh_options' );
		$redirect_url = $options['redirect_url'];
		
		//mostro il campo
		echo $options['redirect_url'];
	}
	//Funzione che mi controlla la validita
	public function valido_input( $input ){
		$valid = array();
		$valid['app_id'] = preg_replace(
			'/[^A-Za-z0-9]/',
			'',
			$input['app_id'] );
		$valid['app_secret'] = preg_replace(
			'/[^A-Za-z0-9]/',
			'',
			$input['app_secret'] );
		$valid['redirect_url'] = $this->red_url;
		
		return $valid;
	}
}