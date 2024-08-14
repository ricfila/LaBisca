# La Bi$ca
[<img src="https://labisca.altervista.org/media/img/logo%20bisca.png">](https://labisca.altervista.org)
Sito web dedicato all'archiviazione e alla consultazione dei punteggi realizzati al *Giuoco del Due*. Disponibile all'indirizzo https://labisca.altervista.org.


## Installazione
Per il corretto funzionamento, in seguito all'installazione è opportuno creare il file `conn.php` nella directory principale con il seguente contenuto:
```
<?php
$server = "";
$username = "";
$password = "";
$database = "";

$conn = new mysqli($server, $username, $password, $database);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
?>
```
e valorizzare le quattro variabili con i dati corretti per la connessione al DB. Lo script di creazione delle tabelle per l'inizializzazione del database si trova nel file `php/db.sql`.

---

[<img alt="Deployed with FTP Deploy Action" src="https://img.shields.io/badge/Deployed With-FTP DEPLOY ACTION-%3CCOLOR%3E?style=for-the-badge&color=0077b6">](https://github.com/SamKirkland/FTP-Deploy-Action)
