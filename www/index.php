<?php 

require 'server.php';
require 'vendor/autoload.php';

use Bananovs\Server;
use Dotenv\Dotenv;

if (function_exists('date_default_timezone_set'))
date_default_timezone_set('Europe/Moscow');

// Регистрируем хранилище, получаем programId и secret

if (!getenv('ACCESS_TOKEN')) {
    try {
        // $dotenv = (new Dotenv('.env'))->load();
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        // $dotenv->required(['DB_HOST', 'DB_DATABASE'])->notEmpty();
    } catch (Throwable $ex) {
        echo $ex->getMessage();
        exit(1);
    }
}
$token  = $_ENV['ACCESS_TOKEN'];
$data = $_GET;

if($data['access_token'] != $token) die('Invalid token');

if($data['truncate'] == true) {

    $server = new Server($data['access_token']);
    $resp = $server->truncate($data['p']);
    echo json_encode($resp);die;
    
}

if($data['register'] == 1) {

    $data = new Server($data['access_token']);
    $resp = $data->register();
    echo json_encode($resp);die;
    
}

// Получаем данные для сохранения
$server = new Server($data['access_token']);
$resp = $server->storeData($data['p'], json_encode($data));

echo $resp;
