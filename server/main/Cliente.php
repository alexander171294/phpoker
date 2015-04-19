<?php

require('DataCenter.php');

// creamos nuestro cliente y lo que va a realizar
class Cliente extends \PHPServerSocket\SocketClient
{

    private $identify = false;
    private $nick = false;
    
    public function onError() { }
    
    public function onReady() 
    {
        var_dump($this->isWebSocket());
        $this->send('welcomeMSG');
    }
    
    public function onReceiveMessage($message)
    {
        $msg = is_array($message) ? $message[0] : $message;
        $msg = is_array($msg) ? $msg['payload'] : $msg;
        if($this->isWebSocket()) // es un cliente web
        {
            // si aún no estamos identificados
            if(!$this->identify)
            {
                $msg = explode('@', $msg);
                if($msg[1] == 'login') // acceso
                {
                    // aquí proceso de identificacion //////////////////////////////////////
                    $this->identify = true;
                    $this->nick = $msg[2];    
                    $this->send(json_encode(array('type'=>'welcome', 'obj' => file_get_contents('settings/welcome.html'), 'obj2' => $this->nick)));
                    echo '> Cliente conectado [Puerta de enlace: #'.$this->id.'] [nick:'.$this->nick.']'.PHP_EOL;       
                } elseif($msg[1] == 'registro') // registro
                {
                    // PROCESO DE REGISTRO /////////////////////////////////////////////////
                }
            } else $this->procesar($msg); // sino procesamos    
        } else { // es un socket normal
            $msg = explode('@', $msg);   
        }
    }
    
    public function procesar($msg)
    {
        /////////////////////////////////////// CLIENTE ///////////////////////////////////
    }
    
    public function onSendRequest(&$cancel, $message){}
    public function onSendComplete($message){}
    
    public function onRefresh() {}
    
    public function _onDisconnect()
    {
        echo '> Cliente desconectado #'.$this->id.PHP_EOL;
    }
    
}