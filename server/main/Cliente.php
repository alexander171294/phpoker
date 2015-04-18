<?php

// creamos nuestro cliente y lo que va a realizar
class Cliente extends \PHPServerSocket\SocketClient
{

    private $identify = false;
    private $nick = false;
    
    public function onError() { }
    
    public function onReady() 
    {
        $this->send('welcomeMSG');
    }
    
    public function onReceiveMessage($message)
    {
        $msg = is_array($message) ? $message[0] : $message;
        if(!$this->identify)
        {
            $msg = explode('@', $msg['payload']);;
            // aquí proceso de identificacion //////////////////////////////////////
            $this->identify = true;
            $this->nick = $msg[0];    
            $this->send(json_encode(array('type'=>'welcome', 'obj' => file_get_contents('settings/welcome.html'), 'obj2' => $this->nick)));
            echo '> Cliente conectado #'.$this->id.' '.$this->nick.PHP_EOL;
        } else $this->procesar($msg);
    }
    
    public function procesar($msg)
    {
    
    }
    
    public function onSendRequest(&$cancel, $message){}
    public function onSendComplete($message){}
    
    public function onRefresh() {}
    
    public function _onDisconnect()
    {
        echo '> Cliente desconectado #'.$this->id.PHP_EOL;
    }
    
}