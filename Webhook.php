<?php

function processMessage($message) {
    // processa a mensagem recebida

    $servername = "servername";
    $database = "database";
    $username = "username";
    $password = "password";
    $sql = "sql";
    $dsn_Options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

    try {
        $my_Db_Connection = new PDO($sql, $username, $password, $dsn_Options);
        echo "Connected successfully";
    } catch (PDOException $error) {
        echo 'Connection error: ' . $error->getMessage();
    }

// Create connection
    $conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $message_id = $message['message_id'];
    $chat_id = $message['chat']['id'];
    if (isset($message['text'])) {

        $text = $message['text'];//texto recebido na mensagem
        $first_name = $message['from']['first_name'];
        $last_name = $message['from']['last_name'];

        $query = "INSERT INTO Webhook (first_name, last_name, text) VALUES ('{$first_name}', '{$last_name}', '{$text}')";

        mysqli_query($conn, $query);
    }

}

function sendMessage($method, $parameters) {
    $options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => json_encode($parameters),
            'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
        )
    );

    $context  = stream_context_create( $options );
    file_get_contents(API_URL.$method, false, $context );
}

/*Com o webhook setado, não precisamos mais obter as mensagens através do método getUpdates.Em vez disso,
* como o este arquivo será chamado automaticamente quando o bot receber uma mensagem, utilizamos "php://input"
* para obter o conteúdo da última mensagem enviada ao bot.
*/
$update_response = file_get_contents("php://input");

$update = json_decode($update_response, true);

if (isset($update["message"])) {
    processMessage($update["message"]);
}

?>