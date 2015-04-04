<?php namespace PHPSocketMaster;


class newClient extends SocketEventReceptor
{
    use timeOut;
    
	private $name = 'Noname';
	private $requested = false;
	public $id;
    private $ready = false;
    private $requestName = false;
    private $position = 0;
    private $started = false;
    private $pinged = true;

	public function onError()
	{
		echo '> oOps error in client: '.$this->name;
		// borramos el cliente con el error
		ServerManager::DeleteClient($this->id); 
	}

	public function onConnect()
	{
        if($this->ready == false)
        {
		    echo '> New client...';
            $this->ready = true;
        } else {
            $this->requestName = true;
            $this->getBridge()->send(json_encode(array('type' => 'system', 'msg' => 'getNick')));
            //$this->setTimeOut(array($this, 'clientverify'), 3.5, true);
        }
	}
  
  public function clientverify()
  {
      // establecemos que no respondió el ping
      $this->pinged = false;
      // enviamos solicitud de ping
      $this->getBridge()->send(json_encode(array('type' => 'services', 'msg' => 'V-ping')));
      // damos un segundo y medio para responder
      $this->setTimeOut(array($this, 'verifyStep2'), 1.5, false);
      // mostramos en consola el envio de ping
      echo '<-('.$this->id.')-> ';
  }
  
  public function verifyStep2()
  {
      if(!$this->pinged) //si no respondio en este segundo y medio
      {
          \CorePoker::disconnected($this->id);    
      }
  }

	public function onDisconnect()
	{
		echo '> disconnect client: '.$this->name;
		ServerManager::DeleteClient($this->id);
        \CorePoker::disconnected($this->id);
	}

	public function onReceiveMessage($message)
	{                                          
		
		// fix for windows sockets message
		$message = is_array($message) ? $message[0] : $message;
		// que es lo que nos mandan?
        if($message['payload'] == 'V-pong')
        {
            $this->pinged = true;
            echo '->('.$this->id.')<-';
        } else if($this->requestName)
        {
            if($this->started)
            {
                \CorePoker::analize($message, $this->id);
            } else {
                if(!\CorePoker::reconnect($message['payload'], $this->id))
                {
                    $this->name = $message['payload'];
                    $this->sitdown();
                    $this->started = true;
                }
            }
        }
	}
    
    public function sitdown()
    {
        // sentarme
        $this->position = \CorePoker::sit(base64_encode($this->id.$this->name), $this->name, $this->id);
        // enviarme a mi la posición
        $this->getBridge()->send(json_encode(array('type' => 'system', 'msg' => 'meClient', 'data' => $this->position)));
        // enviarme a mi las fichas que tengo
        $this->getBridge()->send(json_encode(array('type' => 'system', 'msg' => 'fichas', 'data' => \CorePoker::me(base64_encode($this->id.$this->name))->fichas)));
        // enviarme a mi la posición
        $this->getBridge()->send(json_encode(array('type' => 'system', 'msg' => 'clients', 'data' => json_encode(\CorePoker::getClients()))));
        // enviar a todos mi posición
        ServerManager::Resend(json_encode(array('type' => 'system', 'msg' => 'newClient', 'data' => $this->position, 'nick' => $this->name, 'fichas' => \CorePoker::me(base64_encode($this->id.$this->name))->fichas)));
        
        // verificamos si se puede comenzar, y en caso de que se pueda comenzar, mezclamos, repartimos, pedimos ciegas, etc.
        if(\CorePoker::continuable())
        {
            if(!\CorePoker::inGame())
                \CorePoker::init();
        }
        else
             ServerManager::Resend(json_encode(array('type' => 'notify', 'msg' => 'Se est&aacute;n esperando m&aacute;s jugadores')));
    }
    
    public function onSendRequest(&$cancel, $message) 
    {
        //...
    }
    
    public function onSendComplete($message) 
    {
        //... 
    }
    
    public function onRefresh() // revisamos si es nuestro turno
    {
        $this->timeOut_refresh();
    }
}
