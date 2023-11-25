<?php

require("response.php");

header('Content-Type: application/json; charset=utf-8;');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PATCH, POST, DELETE, OPTIONS, PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Auth-Token');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    $res = new Response("200", ["message" => "ok"]);
    $res ->respond();
}

$host = 'host.docker.internal';
$user = 'root';
$pass = '';
$db = 'todo';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$route = explode("/", strtok($_SERVER['REQUEST_URI'], '?'));
array_shift($route);
$option_count = sizeof($route);


switch ($route[0]) {
    case 'todos': {
        if ($_SERVER['REQUEST_METHOD'] == 'GET')  {
        $result = $conn ->query('SELECT * FROM todos ORDER BY updated_at DESC');
        $todoarr = [];
        while ($row = $result -> fetch_assoc()) {
            $temp = ["id" => $row["id"],"title" => $row["title"]];
            $temp ["completed"] = $row["id"] == 1? true:false;
            $todoarr[] = $temp;
        }
        $res = new Response('200', $todoarr);
        $res -> respond();
        $conn -> close();
        exit();
    } else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $id = file_get_contents('php://input');
        $result = $conn ->query('DELETE FROM todos WHERE id = '. $id);
        $res = new Response('204', ['message'=> 'Löschen erfolgreich']);
        $res -> respond();
        $conn -> close();
        exit();
        } else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $todo = json_decode(file_get_contents('php://input'));
            $result = $conn -> query('UPDATE todos SET title = "'. $todo -> title .'", `updated_at` = "'. date("Y-m-d H:i:s").'" WHERE id = ' . $todo -> id);
            $res = new Response('200', ['message'=> var_dump($result)]);
            $res -> respond();
            $conn -> close();
            exit();
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $todo = json_decode(file_get_contents('php://input'));
            $result = $conn -> query('INSERT INTO todos (title) VALUES ("'. $todo -> title .'")');
            $res = new Response('201', ['message'=> 'Anlegen erfolgreich']);
            $res -> respond();
            $conn -> close();
            exit();
        }
    }
    
}
$conn ->close();