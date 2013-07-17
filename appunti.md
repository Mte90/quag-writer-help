##Appunti di codice
Il plugin adesso si basa sul boilerplate in modo che il suo codice venga eseguito solo nelle pagine richieste.  
Il codice è dentro una classe quindi ecco degli snippet da non dimenticare.  

    array($this, 'funzione_della_classe')

Questo codice serve per gli hook di wordpress per passargli la funzione della classe

     self::PLUGIN_BASENAME

Questa variable serve per avere la basename del plugin

Il file della classe contiene il codice mentre views/admin.php contiene il codice che visualizza la pagina.

##Da valutare
Spostare il codice js in un file js

##Problemi e Dubbi
- ho attivato i CSS per admin al rigo 99 ma non me le attiva di default perche' all'interno della funzione enqueue_admin_styles() i vari if non funzionano o non o ben capito io come si utilizzano, piu' probabile la seconda. Comunque avevo provato ad impostare la proprieta' $plugin_screen_hook_suffix ma anche cosi' non sono stato piu' fortunato. Per il momento ho commentato il tutto ed ho messo semplicemente il wp_enqueue che me lo carica correttamente.

L'imprtante è che hai risolto il problema :-)

