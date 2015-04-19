<?php

// cargamos phpSocketMaster
require('../SocketMaster/iSocketMaster.php');

/**
 * @wiki https://github.com/alexander171294/PHPSocketMaster/wiki/onEvent-Funciones
 */

class Socket extends \PHPSocketMaster\SocketMaster
{

	public function onConnect()
	{
		echo '> Conectado correctamente';
	}

	public function onDisconnect()
	{
		echo '> desconectado :(';
	}

	public function onReceiveMessage($message)
	{
		echo '< '.$message;
    // supongamos que ahora le queremos responder el mensaje
    // usamos la funcion para enviar un mensaje
    
    // la otra forma es desde fuera de los eventos, 
    // puede observar un ejemplo más abajo
	}

	public function onError($errorMessage)
	{
		echo 'Oops error ocurred: '.$errorMessage;
		die();
	}

	public function onNewConnection(\PHPSocketMaster\SocketBridge $socket) { }
    
  public function onSendRequest(&$cancel, $message) 
  {

  }
  
  public function onSendComplete($message) 
  {
   
  }
  
  // esta funcion la veremos más adelante
  public function onRefresh(){}
  
}

$sock = new Socket('localhost', '2026');
$sock->connect();
$sock->send('HOLA');