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
 * @author  Andrea Barghigani e Daniele Scasciafratte
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
	protected $plugin_screen_hook_suffix = 'qwh_main';
	
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
		add_action( 'init', array( $this, 'load_library' ) );

		// Add the options page and menu item.
		 add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
        
        //Add Meta Boxes
        add_action( 'add_meta_boxes', array( $this, 'qwh_metaboxes' ) );
        
        //Add DashBoard Widget
        add_action('wp_dashboard_setup', array( $this,'qwh_dashboard_widget'));
    
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );


		// Load public-facing style sheet and JavaScript.
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Hook
		add_action( 'admin_init', array( $this, 'load_setting' ) );
		
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
		
		//Commentato perchè adesso non servono
		/*if( false == get_option( 'qwh_options' ) ) {    
			 
			$qwh_options = array(
				'app_id' => false,
				'app_secret' => false,
				'redirect_url' => admin_url().'options-general.php?page=qwh_main'
			);
			
			add_option( 'qwh_options', $qwh_options );
		}*/
		
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
        
        if ($this->qwh_check_if_quag()) {
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
		
		if ($this->qwh_check_if_quag()) {
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
		//Imposto il salvataggio delle opzioni
		//Commentato perchè al momento perchè richiesto
		//register_setting( 'autenticazione_quag', 'qwh_options', array($this, 'valido_input') );
		//verifico se le chiavi oauth sono inserite
		if($this->check_key()){
			//abilito quindi la callback ajax per la ricerca
			add_action('wp_ajax_quag_search', array($this,'quag_search_callback'));
			//Commentato perchè non è necessario al momento fare un autenticazione per ottenere queste informazioni
			/*add_action('wp_ajax_quag_search_dashboard', array($this,'quag_search_dashboard_callback'));*/
		}
	}
	
	/**
	 * Include library
	 *
	 * @since    1.0.0
	 */
	public function load_library() {
		//resetto la sessione altrimenti wordpress genera parecchi errori e non funziona niente
		ob_start();
		//Includo le librerie
		//Commentato perchè non è necessario al momento fare un autenticazione per ottenere queste informazioni
		//require ('inc/quag/http.php');
		//require ('inc/quag/oauth_client.php');
	}
	
	/**
	 * Create the form for settings
	 *
	 * @since    1.0.0
	 */
	public function creo_form(){
        
		//creo la sezione per le impostazioni
		add_settings_section( 'sez_autenticazione_quag', 'Sezione Principale', array($this, 'campi_input'), self::PLUGIN_BASENAME );
		
		//creo i vari campi
		add_settings_field('qwh_app_id', 'Inserisci App ID', array($this, 'app_id_cb'), self::PLUGIN_BASENAME, 'sez_autenticazione_quag' );
		add_settings_field('qwh_app_sec', 'Inserisci App Secret', array($this, 'app_sec_cb'), self::PLUGIN_BASENAME, 'sez_autenticazione_quag' );
		add_settings_field('qwh_redirect_url', 'Redirect URL', array($this, 'redirect_url_cb'), self::PLUGIN_BASENAME, 'sez_autenticazione_quag' );
		//Show the form
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
	
	/**
	 * Check key on the DB
	 *
	 * @since    1.0.0
	 */
	//Funzione che verifica se le chiavi di quag sono inserite ed in caso positivo 
	//setta app_created come true
	public function check_key(){
		//Disattivato perchè al momento non serve
		/*$options = get_option( 'qwh_options' );
		$app_secret = $options['app_secret'];
		$app_id = $options['app_id'];
		//Verifico se è presente nel db
		if(!empty($app_secret) or !empty($app_id)) {
			return true;
		}else{
			return false;
		}*/
		return true
	}
	
	/**
	 * Set the config for oauth
	 *
	 * @since    1.0.0
	 */
	 //Commentato perchè non è necessario al momento fare un autenticazione per ottenere queste informazioni
	/*public function quag_oauth_data(){
		$client = new oauth_client_class;
		$options = get_option( 'qwh_options' );
			
		// Edit those configurations!
		$client -> client_id = $options['app_id'];
		$client -> client_secret = $options['app_secret'];
		$client -> redirect_uri = admin_url().'options-general.php?page=qwh_main';
		$client -> dialog_url = 'https://www.quag.com/oauth2/authorize/?client_id={CLIENT_ID}&response_type=code&redirect_uri={REDIRECT_URI}&scope={SCOPE}&state={STATE}';
		$client -> access_token_url = 'https://www.quag.com/oauth2/token/';
		$client -> scope = 'user_resource thread_resource';
		
		// Dont't edit! Standard OAuth2 parameters.
		$client -> request_token_url = '';
		$client -> append_state_to_redirect_uri = '';
		$client -> authorization_header = true;
		$client -> url_parameters = true;
		$client -> token_request_method = 'GET';
		$client -> signature_method = 'HMAC-SHA1';
		$client -> oauth_version = '2.0';
		
		return $client;
		
	}*/
	
	/**
	 * Do it a false search for authorized the app
	 *
	 * @since    1.0.0
	 */
	//Avviamo il login
	public function login(){
		//Commentato perchè non è necessario al momento fare un autenticazione per ottenere queste informazioni
		/*$client = $this->quag_oauth_data();

		$api_url = "http://www.quag.com/v1/a_threads_by_interest/";
		$api_params = array('q' => '');

		if (strlen($client -> client_id) == 0 || strlen($client -> client_secret) == 0)
			die('Please go to Quag API Apps page http://www.quag.com/account/clients , ' . 'create an application, set the client_id to App ID/API Key and client_secret with App Secret');

		if (($success = $client -> Process())) {
			
			if (strlen($client -> access_token))
				$success = $client -> CallAPI($api_url . '?' . http_build_query($api_params), 'GET', array(), array('FailOnAccessError' => false, 'AsArray' => true), $results);
		}
		$success = $client -> Finalize($success);


		if ($client -> exit)
			exit ;
		if ($success) {
			echo '<br>Autorizzato da Quag :-)<br/><br/>';
		}*/
	}
	
	/**
	 * Callback that show the search
	 *
	 * @since    1.0.0
	 */
	//Callback tramite ajax per la ricerca
	//Commentato perchè non è necessario al momento fare un autenticazione per ottenere queste informazioni
	/*public function quag_search_callback() {
		
		$client = $this->quag_oauth_data();
		$api_url = "http://www.quag.com/v1/a_threads_by_interest/";
		//Prendiamo il post del campo di ricerca
		$api_params = array('q' => $_POST['search'] );

		if (($success = $client -> Process())) {
			if (strlen($client -> access_token))
				$success = $client -> CallAPI($api_url . '?' . http_build_query($api_params), 'GET', array(), array('FailOnAccessError' => false, 'AsArray' => true), $results);
        }
		$success = $client -> Finalize($success);

		if ($client -> exit)
			exit ;
		if ($success) {
			if (is_array($results)) {
                ?>
                    <div id="a_threads_by_interest_container">
                        <div id="int_users"><?php echo $results["meta"]["users"]; ?></div>
                        <div class="overflow_container">
                            
                            
                <?php
				    if (sizeof($results['threads']['internal'])) {
					   foreach ($results['threads']['internal'] as $internalThread) {
                ?>
				
					<div class="thread">
						
						<div class="data">
                            <h4><a href="<?php echo $internalThread['resource_uri']; ?>" target="_blank">
                                <?php echo $internalThread['title']; ?>
                            </a></h4>
                            <div>
                                <span class="summary"><?php echo $internalThread['summary']; ?></span>
                            </div>         
						</div>
					</div>
                <?php
					}
				}
				echo '     
				</div>
			</div>';
			}
		}
		die();
	}*/
    
    public function quag_search_callback() {
		
		$api_url = "http://www.quag.com/v1/o_threads_by_interest/";
		//Prendiamo il post del campo di ricerca
		$api_params = array('q' => $_POST['search'] );

		$json_contents = file_get_contents($api_url . '?' . http_build_query($api_params));
        $array_contents = json_decode($json_contents, true);
		if (is_array($array_contents)) {
            ?>
            <div id="o_threads_by_interest_container">
                    <div class="overflow_container">
            <?php
		    if (sizeof($array_contents['threads']['internal'])) {
			   foreach ($array_contents['threads']['internal'] as $internalThread) {
            ?>
							<div class="thread">
							<div class="image">
                <a href="<?php echo $internalThread['author']['resource_uri'] ?>" target="_blank">
                <img src="<?php echo  $internalThread["author"]['avatar_url']?>" alt="<?php echo  $internalThread["author"]["username"] ?>\'s avatar" />
                <span class="username"><?php echo  $internalThread["author"]["username"]?></span>
                </a>
                  </div>
                <div class="data">
                <? foreach ($internalThread['quags'] as $quag) { ?>
                <span class="quag"><?php echo $quag['quag'] ?></span>
                    <? } ?>
                <div>
                <a href="<?php echo $internalThread['resource_uri'] ?>" target="_blank"><?php echo $internalThread['title'] ?></a>
                </div>  
                         <div>
                             <span class="summary"><?php echo $internalThread['summary']; ?></span>
                         </div>         
				</div>
			</div>
            <?php
			}
		}
		echo '</div>
		</div>';
		}
		die();
	}
    
    /**
	 * Callback per la creazione delle metabox
	 *
	 * @since    1.0.0
	 */
    public function qwh_metaboxes(){
        $screens = array( 'post', 'page' );
        
        foreach( $screens as $s ){
            add_meta_box(
                'qwh-search-field',
                __("Ricerca un Argomento su Quag", $this->plugin_slug ),
                array( $this, 'qwh_mostra_search_field'),
                $s,
                'side',
                'core'
            );
        }
        
    }
    
    public function qwh_mostra_search_field(){
        $this->login();
        
        echo '<div id="search-form"><input type="text" id="quag_search"/>
        <input id="quag_ok" class="button button-primary" type="button" value="Cerca"/></div>
        <div id="quag"></div>';
    }
	
	function qwh_dashboard_widget() {
		global $wp_meta_boxes;
		
		wp_add_dashboard_widget('qwh_widget_dashboard', 'Cerca l\'argomento del tuo prossimo articolo! ',array($this,'qwh_dashboard'));
	}
	
	function qwh_dashboard() {
		global $wpdb;
		$terms = array();
		//Faccio una query per avere gli id di 5 categorie con il maggior numero di post
		$query = 'SELECT term_id FROM '.$wpdb->term_taxonomy.' WHERE taxonomy = "category" ORDER BY count ASC LIMIT 5';
		$num = $wpdb->get_col($query);
		//Ottengo il nome e lo metto in un array
		foreach ( $num as $term ) {
			$name = get_term_by('id',$term,'category');
			$terms[] = $name->name;
		}
		
		//Faccio una query per avere gli id di 5 tag con il maggior numero di post
		$query = 'SELECT term_id FROM '.$wpdb->term_taxonomy.' WHERE taxonomy = "post_tag" ORDER BY count ASC LIMIT 5';
		$num = $wpdb->get_col($query);
		//Ottengo il nome e lo metto in un array
		foreach ( $num as $term ) {
			$name = get_term_by('id',$term,'post_tag');
			$terms[] = $name->name;
		}
		
		echo 'I termini più utilizzati nel sito sono: ';
		foreach ( $terms as $term ) {
			echo "<span class='tag_dashboard_top' data-name='{$term}'>{$term}</span>, ";
		}
		echo '<input type="text" id="quag_search_dashboard"/>
		<input id="quag_ok_dashboard" class="button button-primary" type="button" value="Cerca"/>
		<div id="quag"></div>';
	}
    
    public function qwh_stampa_top_tags(){
        $tags = get_tags("number=10");
			if (empty($tags))
                return;
			$counts = $tag_links = array();
			foreach ( (array) $tags as $tag ) {
					$counts[$tag->name] = $tag->count;
			}
			asort($counts);
			$counts = array_reverse( $counts, true );
			$html = '<div class="post_tags">Tag del sito: ';
			foreach ( $counts as $tag  => $count ) {
				$html .= "<span class='tag_top' data-name='{$tag}'>";
				$html .= "{$tag}</span> ";
			}
			$html .= '</div>';
			echo $html;
    }
    
    /**
	 * Callback per salvare i valori contenuti in una metabox
	 *
	 * @since    1.0.0
	 */
    public function qwh_save_postdata( $post_id ){
    }

    /**
     * Funzione che mi permette di sapere con che account sono collegato
     *
     * mi sembra di aver capito che con queste API non e' possibile
     * ricevere delle informazioni riguardanti gli utenti a p
     */
    public function qwh_who_are_me(){
    	
    }
	
	//Carica i file css e js nell'amministrazione solo se in pagine con roba del plugin
	function qwh_check_if_quag(){
		global $pagenow;
	
		$screen = get_current_screen();
		
		//verifico se sono nella modifica/scrittura di un post/pagina/cp/dashboard
		if(in_array( $pagenow, array( 'post.php', 'post-new.php', 'index.php' ) )) {
			return true;
		//verifico se nella pagina del plugin
		} elseif ( $screen->id == $this->plugin_screen_hook_suffix ) {
			return true;
		} else {
			return false;
		}
	}
}