<?php namespace PHPSocketMaster;

/**
 * @wiki https://github.com/alexander171294/PHPSocketMaster/wiki/PHPSocketMaster-como-WebSocket
 */
 
 define('LOCAL_IP', '192.168.0.101');
 define('LOCAL_PORT', '6768');
 define('LOCAL_VPING', true); // permitimos que el server envíe pings para saber si el cliente está online
 
 // DATABASE SERVER
 define('REMOTE_HOST', 'localhost');
 define('REMOTE_USER', 'root');
 define('REMOTE_PASS', '');
 define('REMOTE_DB', '');

// example of websocket using directly SocketMaster

require('config.php');
\config::loadConfig();
require('CorePoker.php');
require('sockets/iSocketMaster.php');
require('db/littledb.php');


// import implementation of SocketMaster as Socket
include('Listen.php');
// import implementation of SocketEventReceptor as newClient
include('newClient.php');

class ServerManager
{
	static private $sock = null;
	static private $clients = array(); // array of clients online
	static private $NewClient = null;
  static private $db = null;

	static public function start()
	{
		// create a new socket
		self::$sock = new Socket(LOCAL_IP, LOCAL_PORT);

		self::$sock->listen();
    
    self::$db = \LittleDB::get_instance(array('host' => REMOTE_HOST, 'user' => REMOTE_USER, 'password' => REMOTE_PASS, 'database' => REMOTE_DB)); 

		echo '** listen **';

		self::AddNewClient();

		while(true)
		{
			self::$sock->refreshListen(self::$NewClient, SCKM_WEB); // add SCKM_WEB for the WebSocket
			// refresh messages
			for($i=0; $i<count(self::$clients); $i++)
			{
				self::$clients[$i]->refresh();
			}
		}
	}

	static public function AddNewClient()
	{
		self::$NewClient = new newClient();
	}

	static public function AddClient($sock)
	{
		$sock->SocketEventReceptor->id = count(self::$clients); // add te id
		self::$clients[] = $sock;
	}

	static public function Resend($message)
	{
		for($i=0; $i<count(self::$clients); $i++)
			{
				self::$clients[$i]->send($message);
			}	
	}
    
    static public function sendTo($id, $message)
    {
        self::$clients[$id]->send($message);
    }

	static public function DeleteClient($id)
	{
		unset(self::$clients[$id]);
		// reordenamos indices del arreglo
		self::$clients = array_values(self::$clients);
        foreach(self::$clients as $key => $obj)
        {
            $obj->SocketEventReceptor->id = $key;
        }
	}

}

ServerManager::Start();