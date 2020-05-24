Come avviare e consultare le API:

1) entrare da shell nella cartella principale del progetto e runnare 'npm i' (solo la prima volta o se si aggiungono/rimuovono pacchetti npm);
2) runnare il comando 'npm start dev' (la prima volta prependere 'sudo' per inserire la voce DNS nel file host locale; prependere sempre 'sudo' se lo si prepende anche allo stop visto lo stop da sudo rimuove anche la entry nel file host)
3) nel browser navigare 'http://api.randa2randa.test/api/doc' per visualizzare la documentazione delle API

Per chiudere il progetto entrare da shell nella cartella principale del progetto e runnare 'npm stop dev' (prepender 'sudo' per rimuovere la voce DNS dal file host locale);