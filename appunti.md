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