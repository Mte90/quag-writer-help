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

//Funzione per creare la pagina di opzioni
function qwh_main_page(){
    add_options_page( 'Quag Writer Help', 'Quag Writer Help', 'manage_options', 'qwh_main', 'qwh_draw_options_page' );
}

//Funzione per disegnare la pagina opzioni
function qwh_draw_options_page(){
    ?>
<div class="wrap">
    <?php screen_icon(); ?>
    <h2>Quag Writer Help Settings Page</h2>
    <form action="options.php" method="post">
        <?php settings_fields( 'autenticazione_quag' ); ?>
        <?php do_settings_sections( PLUGIN_BASENAME ); ?>
        <input id="submit" class="button button-primary" type="submit" value="Save Changes" name="submit">
    </form>
</div>
    <?php
}


if(qwh_check_key()==false){
//Appoggio tutto alla creazione del pannello di admin
add_action( 'admin_init', 'qwh_creo_form' );
}else{
	echo 1;
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