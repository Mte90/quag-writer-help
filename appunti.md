##Appunti di codice
Il plugin adesso si basa sul boilerplate in modo che il suo codice venga eseguito solo nelle pagine richieste.  
Il codice è dentro una classe quindi ecco degli snippet da non dimenticare.  

    array($this, 'funzione_della_classe')

Questo codice serve per gli hook di wordpress per passargli la funzione della classe

     self::PLUGIN_BASENAME

Questa variable serve per avere la basename del plugin

Il file della classe contiene il codice mentre views/admin.php contiene il codice che visualizza la pagina.

##Da valutare
Le opzioni auttenticate e authorized io le rimuoverei perchè non possiamo sapere se l'utente sta usando un altro browser o si deve riautenticare. Inoltre il codice in automatico eseguirà il login se non è connesso. La verifica dell'autorizzazione c'è ogni volta che si aprirà la pagina.

##Problemi e Dubbi
- ho tentato a usare la libreria per le meta box ma non c'e' stato niente da fare. Sono stato attento anche all'inserimento ma proprio non mi trovava il filtro che avviene durante l'inclusione della stessa. (ho caricato il file init.php all'interno del metodo load_library() trovi la riga commentata).

- ho attivato i CSS per admin al rigo 99 ma non me le attiva di default perche' all'interno della funzione enqueue_admin_styles() i vari if non funzionano o non o ben capito io come si utilizzano, piu' probabile la seconda. Comunque avevo provato ad impostare la proprieta' $plugin_screen_hook_suffix ma anche cosi' non sono stato piu' fortunato. Per il momento ho commentato il tutto ed ho messo semplicemente il wp_enqueue che me lo carica correttamente.

- sto' modificando la struttura HTML di quag_search_callback() perche' tanto ci servira' piu' all'interno degli articoli che nella pagina admin