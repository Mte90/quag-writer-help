<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 Your Name or Company Name
 */
?>
<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?
		//Se le chiavi non ci ci sono mostriamo il form
		if($this->check_key()==false){
			$this->creo_form();
		}else{
			//Effetto il login per l'autorizzazione
			$this->login();
			//Se già autorizzato non farà niente, mosrà solo Autorizzato da quag
			echo '<script type="text/javascript" >
				jQuery(document).ready(function($) {
				//Al click sul pulsante avvia la chiamata ajax
					$(\'#quag_ok\').click(function() {
						var data = {
							action: \'quag_search\',
							search: $(\'#quag_search\').val()
						};

						$.post(ajaxurl, data, function(response) {
							$(\'#quag\').html(response)
						}).fail(function(){
							alert("error");
						});
					});
				});
				</script>';
			echo '<input type="text" id="quag_search"/>
			<input id="quag_ok" class="button button-primary" type="button" value="Cerca"/>
			<div id="quag"></div>';
		}
	?>

</div>
