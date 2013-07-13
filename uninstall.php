<?php

//Se la disinstallazione e' chiamata fuori da WordPress, esci
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

//Calcello le opzioni del mio plugin
delete_option( 'qwh_options' );

//Continua a rimuovere tt quello che hai messo  