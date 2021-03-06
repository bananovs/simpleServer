<?php 

namespace Bananovs;

require 'vendor/autoload.php';

use \RedBeanPHP\R as R;

class Server extends \RedBeanPHP\SimpleModel
{
    private $token;
    private $secret;

    public function __construct($token)
    {
        $this->token = $token;
        R::setup('sqlite:./db/database.db');
        if ( !R::testConnection() )
        {
                exit ('Нет соединения с базой данных');
        }
    }

    public function register()
    {
        $register = [
            'programId' => $this->intRandom(),
            'secret' => $this->strRandom()
        ];

        $dbID = $this->createDatabase($register);

        // $register = array_push($register, $dbID);

        return $register;
    }

    public function storeData(?string $programId, ?string $data)
    {
        $db = R::dispense( $programId );
        $data = json_decode($data, true);

        foreach ($data as $key => $value) {
            if($key === 'access_token') continue;
            if($key === 's') continue;
            $db->$key =  $value;
        }
        
        $db->created_at =  time();
        
        $id = R::store( $db );

        return json_encode(['success' => 'ok', 'id' => $id]);
    }

    public function truncate($programId)
    {
        R::exec('SET FOREIGN_KEY_CHECKS = 0;');
        R::wipe($programId);

        return json_encode(['success' => 'ok']);
    }


    public function createDatabase($register)
    {
        // Сохранение в БД
        $db = R::dispense( $register['secret'] );

        $db->value = $this->strRandom(40);
        $db->created_at =  time();
        
        $id = R::store( $db );

        return $id;

    }

    private function strRandom($length = 16)
    {
        // $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pool = 'abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    private function intRandom()
    {
        return rand(1, 1000000);
    }




}