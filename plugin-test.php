<?php
/**
 * 
 *
 * @wordpress-plugin
 * Plugin Name: My First Plugin
 * Plugin URI:  http://wpandmore.info/quag-writer-help
 * Description: Quag Writer Help is a...
 * Version:     0.0.1
 * Author:      Andrea Barghigiani, Daniele Scasciafratte
 * Author URI:  TODO
 * Text Domain: plugin-name-locale
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

/*
 * Creo variabile $plugin_dir per contenere l'indirizzo locale della cartella
 * 
 * Da questo punto posso riferire ai miei file interni in questo modo:
 *
 * $plugin_dir . 'inc/quag/quagexp.php
 */
$plugin_dir = plugin_dir_path( __FILE__ );

//Definisco il plugin basename
define( 'PLUGIN_BASENAME', 'quag_writer_help' );

//Definisco redirect url
define( 'RED_URL', plugins_url( 'inc/quag/quagexp.php', __FILE__ ) );
//var_dump( RED_URL );
/*
 * Creo l'attivazione del mio plugin
 */
register_activation_hook( __FILE__, 'qwh_install' );

function qwh_install(){
    //Faccio le cose necessarie all'attivazione
    
    /*
     * Creo delle opzioni ed imposto il loro valore nell'attivazione
     *
     * - app_created = mi serve per capire se l'utente ha creato l'app
     * - autenticate = mi serve per capire se l'utente e' autenticato
     * - authorized = mi serve per capire se l'utente e' autorizzato
     */
    $qwh_options = array(
        'app_id' => false,
        'app_secret' => false,
        'redirect_url' => RED_URL,
        'autenticate' => false,
        'authorized' => false,
        'app_created' => false
    );
    
    update_option( 'qwh_options', $qwh_options );
    
    /*
     * Creo la pagina di opzioni per il mio menu
     */
    
}
add_action( 'admin_menu', 'qwh_main_page' );

function qwh_load_library(){
	require ('inc/quag/http.php');
	require ('inc/quag/oauth_client.php');
}

//Funzione per creare la pagina di opzioni
function qwh_main_page(){
    add_options_page( 'Quag Writer Help', 'Quag Writer Help', 'manage_options', 'qwh_main', 'qwh_draw_options_page' );
}

//ecco la funzione\
function qwh_creo_form(){
    register_setting( 'autenticazione_quag', 'qwh_options', 'qwh_valido_input' );
    //creo la sezione per le impostazioni
    add_settings_section( 'sez_autenticazione_quag', 'Sezione Principale', 'qwh_campi_input', PLUGIN_BASENAME );
    
    //creo i vari campi
    add_settings_field('qwh_app_id', 'Inserisci App ID', 'qwh_app_id_cb', PLUGIN_BASENAME, 'sez_autenticazione_quag' );
    add_settings_field('qwh_app_sec', 'Inserisci App Secret', 'qwh_app_sec_cb', PLUGIN_BASENAME, 'sez_autenticazione_quag' );
    add_settings_field('qwh_redirect_url', 'Redirect URL', 'redirect_url_cb', PLUGIN_BASENAME, 'sez_autenticazione_quag' );
}

//Avviamo il login
function qwh_login(){
	qwh_load_library();
	$client = new oauth_client_class;
	$options = get_option( 'qwh_options' );
	
// Edit those configurations!
$client -> client_id = $options['app_id'];
$client -> client_secret = $options['app_secret'];
$client -> redirect_uri = admin_url().'options-general.php?page=qwh_main';
$client -> dialog_url = 'https://www.quag.com/oauth2/authorize/?client_id={CLIENT_ID}&response_type=code&redirect_uri={REDIRECT_URI}&scope={SCOPE}&state={STATE}';
$client -> access_token_url = 'https://www.quag.com/oauth2/token/';
$client -> scope = 'user_resource thread_resource';
$api_url = "http://www.quag.com/v1/a_threads_by_interest/";
$api_params = array('q' => 'seo');

// Dont't edit! Standard OAuth2 parameters.
$client -> request_token_url = '';
$client -> append_state_to_redirect_uri = '';
$client -> authorization_header = true;
$client -> url_parameters = true;
$client -> token_request_method = 'GET';
$client -> signature_method = 'HMAC-SHA1';
$client -> oauth_version = '2.0';

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
    if (is_array($results)) {
        echo '
    <div id="a_threads_by_interest_container">
        <h1>a_threads_by_interest: <b>' . $api_params['q'] . '</b> <a href="http://www.quag.com" target="_blank"><img alt="quag" src="http://www.quag.com/m/images/logo-quag.png"/></a></h1>
        <div class="overflow_container">';
        if (sizeof($results['threads']['internal'])) {
            foreach ($results['threads']['internal'] as $internalThread) {
                echo '
            <div class="thread">
                <div class="image">
                    <a href="' . $internalThread['author']['resource_uri'] . '" target="_blank">
                        <img src="' . $internalThread["author"]['avatar_url'] . '" alt="' . $internalThread["author"]["username"] . '\'s avatar" />
                        <span class="username">' . $internalThread["author"]["username"] . '</span>
                    </a>
                </div>
                <div class="data">
                    <div>';
                foreach ($internalThread['quags'] as $quag)
                    echo '    
                        <span class="quag">' . $quag['quag'] . '</span>';
                echo '    
                    </div>
                    <div>
                        <a href="' . $internalThread['resource_uri'] . '" target="_blank">' . $internalThread['title'] . '</a>
                    </div>                     
                    <div>
                        <span class="summary">' . $internalThread['summary'] . '</span>
                    </div>         
                </div>
                <div class="clearer">&nbsp;</div>
            </div>';
            }
        }
        echo '     
        </div>
    </div>';
    }
	}
}

//Spiegazioni che riguardano la sezione di autenticazione per il plugin
function qwh_campi_input(){
    echo "<p>Inserisci i valori della tua applicazione qua sotto.</p>";
}

//Aggiungo il campo input app id
function qwh_app_id_cb(){
    //prendo le opzioni salvate nel db
    $options = get_option( 'qwh_options' );
    $app_id = $options['app_id'];
    //mostro il campo
    echo "<input id='app_id' name='qwh_options[app_id]' type='text' value='{$options['app_id']}' />";
}

//Aggiungo il campo input App Secret
function qwh_app_sec_cb(){
    //prendo le opzioni salvate nel db
    $options = get_option( 'qwh_options' );
    $app_secret = $options['app_secret'];
    //mostro il campo
    echo "<input id='app_secret' name='qwh_options[app_secret]' type='text' value='{$options['app_secret']}' />";
}

//Aggiungo il campo input Redirect URL
function redirect_url_cb(){
    //prendo le opzioni salvate nel db
    $options = get_option( 'qwh_options' );
    $redirect_url = $options['redirect_url'];
    
    //mostro il campo
    echo $options['redirect_url'];
}
//Funzione che mi controlla la validita
function qwh_valido_input( $input ){
    $valid = array();
    $valid['app_id'] = preg_replace(
		'/[^A-Za-z0-9]/',
        '',
        $input['app_id'] );
    $valid['app_secret'] = preg_replace(
		'/[^A-Za-z0-9]/',
        '',
        $input['app_secret'] );
    $valid['redirect_url'] = RED_URL;
    
    return $valid;
}
//Funzione che verifica se le chiavi di quag sono inserite ed in caso positivo 
//setta app_created come true
function qwh_check_key(){
	$options = get_option( 'qwh_options' );
	$app_secret = $options['app_secret'];
	$app_id = $options['app_id'];
	if($options['app_created']) {
		return true;
	}elseif(!empty($app_secret) && !empty($app_id)) {
		update_option('app_created', true);
		return true;
	}else{
		return false;
	}
}
//Funzione per disegnare la pagina opzioni
function qwh_draw_options_page(){
    ?>
<div class="wrap">
    <?php screen_icon(); ?>
    <h2>Quag Writer Help Settings Page</h2>
    
</div>
    <?php
    if(qwh_check_key()==false){
	//Appoggio tutto alla creazione del pannello di admin
		add_action( 'admin_init', 'qwh_creo_form' );
		echo '<form action="options.php" method="post">';
        settings_fields( 'autenticazione_quag' ); 
        do_settings_sections( PLUGIN_BASENAME ); 
		echo '<input id="submit" class="button button-primary" type="submit" value="Save Changes" name="submit">
    </form>';
	}else{
		echo '<iframe src="'.admin_url().'options-general.php?page=qwh_main&service=quag" />';
	}
}
	
if($_GET['service']=='quag'){
	qwh_login();
}