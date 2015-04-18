<?php

// nombre de la clase que gestionará los sockets 
define('SRV_MGR', 'Administrador');

// requerimos PHPSocketMaster
require('SocketMaster/iSocketMaster.php');

// activamos websocket
define('SRV_WSK', true);

// requerimos ServerManager
require('SocketServer/ServerManager.php');

require('main/Cliente.php');

// creamos mi administrador de sockets
class Administrador extends \PHPServerSocket\ServerManager
{
    // creamos nuestra funcion que agrega clientes al servidor (para cuando nos llege una conexion)
   static public function AddNewClient()
   {
      // agregamos el cliente usando una instancia de nuestra clase de cliente que creamos arriba
      self::_AddNewClient(new Cliente());
   }
   
   // reescribimos la funcion reporter
   // esta función se ejecutará cuando ocurran eventos en el administrador
   static public function socketReporter($report)
   {
   
      // si el reporte es que el server ejecutó la función listen
      if($report == R_LISTEN)
      {
          // mostramos el reporte en la consola
          echo '==> Iniciando servidor... <=='.PHP_EOL;
          echo '> Servidor iniciado a las '.date('H:i',time());
      }
      if($report == R_NCLIENT)
      {
          // mostramos el reporte en la consola
          //echo 'nuevo cliente';
      }
      if($report == R_DCLIENT)
      {
          // mostramos el reporte en la consola
          //echo 'cliente eliminado';
      }
   }
}

// iniciamos el servidor indicando ip local o ip local de la red, y el puerto en el cual vamos a esperar conexiones
Administrador::start('127.0.0.1', '2026');