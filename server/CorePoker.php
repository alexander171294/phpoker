<?php

define('CARTA_AS', 14);
define('CARTA_JOTA', 11);
define('CARTA_DAMA', 12);
define('CARTA_REY', 13);

define('PALO_CORAZON', 1);
define('PALO_DIAMANTE', 2);
define('PALO_PICA', 3);
define('PALO_TREBOL', 4);

define('INICIAL', 62);
define('FLOP', 63);
define('TURN', 64);
define('RIVER', 65);
define('REPARTIJA', 66);

define('NL', "\r\n");

class CorePoker
{
    static private $mazo = array(
                                    array('palo' => PALO_CORAZON, 'valor' => CARTA_AS),
                                    array('palo' => PALO_CORAZON, 'valor' => 2),
                                    array('palo' => PALO_CORAZON, 'valor' => 3),
                                    array('palo' => PALO_CORAZON, 'valor' => 4),
                                    array('palo' => PALO_CORAZON, 'valor' => 5),
                                    array('palo' => PALO_CORAZON, 'valor' => 6),
                                    array('palo' => PALO_CORAZON, 'valor' => 7),
                                    array('palo' => PALO_CORAZON, 'valor' => 8),
                                    array('palo' => PALO_CORAZON, 'valor' => 9),
                                    array('palo' => PALO_CORAZON, 'valor' => 10),
                                    array('palo' => PALO_CORAZON, 'valor' => CARTA_JOTA),
                                    array('palo' => PALO_CORAZON, 'valor' => CARTA_DAMA),
                                    array('palo' => PALO_CORAZON, 'valor' => CARTA_REY),
        
                                    array('palo' => PALO_DIAMANTE, 'valor' => CARTA_AS),
                                    array('palo' => PALO_DIAMANTE, 'valor' => 2),
                                    array('palo' => PALO_DIAMANTE, 'valor' => 3),
                                    array('palo' => PALO_DIAMANTE, 'valor' => 4),
                                    array('palo' => PALO_DIAMANTE, 'valor' => 5),
                                    array('palo' => PALO_DIAMANTE, 'valor' => 6),
                                    array('palo' => PALO_DIAMANTE, 'valor' => 7),
                                    array('palo' => PALO_DIAMANTE, 'valor' => 8),
                                    array('palo' => PALO_DIAMANTE, 'valor' => 9),
                                    array('palo' => PALO_DIAMANTE, 'valor' => 10),
                                    array('palo' => PALO_DIAMANTE, 'valor' => CARTA_JOTA),
                                    array('palo' => PALO_DIAMANTE, 'valor' => CARTA_DAMA),
                                    array('palo' => PALO_DIAMANTE, 'valor' => CARTA_REY),
        
                                    array('palo' => PALO_PICA, 'valor' => CARTA_AS),
                                    array('palo' => PALO_PICA, 'valor' => 2),
                                    array('palo' => PALO_PICA, 'valor' => 3),
                                    array('palo' => PALO_PICA, 'valor' => 4),
                                    array('palo' => PALO_PICA, 'valor' => 5),
                                    array('palo' => PALO_PICA, 'valor' => 6),
                                    array('palo' => PALO_PICA, 'valor' => 7),
                                    array('palo' => PALO_PICA, 'valor' => 8),
                                    array('palo' => PALO_PICA, 'valor' => 9),
                                    array('palo' => PALO_PICA, 'valor' => 10),
                                    array('palo' => PALO_PICA, 'valor' => CARTA_JOTA),
                                    array('palo' => PALO_PICA, 'valor' => CARTA_DAMA),
                                    array('palo' => PALO_PICA, 'valor' => CARTA_REY),
        
                                    array('palo' => PALO_TREBOL, 'valor' => CARTA_AS),
                                    array('palo' => PALO_TREBOL, 'valor' => 2),
                                    array('palo' => PALO_TREBOL, 'valor' => 3),
                                    array('palo' => PALO_TREBOL, 'valor' => 4),
                                    array('palo' => PALO_TREBOL, 'valor' => 5),
                                    array('palo' => PALO_TREBOL, 'valor' => 6),
                                    array('palo' => PALO_TREBOL, 'valor' => 7),
                                    array('palo' => PALO_TREBOL, 'valor' => 8),
                                    array('palo' => PALO_TREBOL, 'valor' => 9),
                                    array('palo' => PALO_TREBOL, 'valor' => 10),
                                    array('palo' => PALO_TREBOL, 'valor' => CARTA_JOTA),
                                    array('palo' => PALO_TREBOL, 'valor' => CARTA_DAMA),
                                    array('palo' => PALO_TREBOL, 'valor' => CARTA_REY),
    
                                );
    
    static private $players = array();
    
    static private $cantPlayers = 0;
    
    static private $inGame = array();
    
    static private $initiated = false;
    
    static private $dealer = 0;
    
    static private $mano = 0;
    static private $lastCiega = 0;
    static private $cP = 0;
    static private $cG = 0;
    
    static private $cartCount = 0;
    
    static private $turn = 0;
    static private $maxApuestas = 0;
    static private $apostador = 0;
    static private $requestedAction = null;
    
    static private $step = INICIAL;
    
    static private $pozo = 0;
    
    static private $ciegaPasar = true;
    
    static private $inTable = array();
    
    static private $eom = false;
    
    static public function sit($identifier, $name, $id)
    {
        $ide = 'PvP'.$identifier;
        $name = strlen($name) > 10 ? substr($name, 0, 8).'~' : $name; // nicks de no mas de 10 caracteres
        self::$cantPlayers = self::$cantPlayers +1;
        self::$players[$ide] = new player($ide, config::getConfig()->fichasIniciales, self::$cantPlayers, $name, $id);
        return self::$cantPlayers;
    }
    
    // funcion para establecer el sentarse luego de una conexión
    static public function sitInReconnect($obj)
    {
        PHPSocketMaster\ServerManager::SendTo($obj->realid,json_encode(array('type' => 'system', 'msg' => 'meClient', 'data' => $obj->position)));
        PHPSocketMaster\ServerManager::SendTo($obj->realid,json_encode(array('type' => 'system', 'msg' => 'fichas', 'data' => $obj->fichas)));
        PHPSocketMaster\ServerManager::SendTo($obj->realid,json_encode(array('type' => 'system', 'msg' => 'clients', 'data' => json_encode(self::getClients()))));
        PHPSocketMaster\ServerManager::SendTo($obj->realid,json_encode(array('type' => 'system', 'msg' => 'newDealer', 'data' => self::$dealer)));
        PHPSocketMaster\ServerManager::SendTo($obj->realid,json_encode(array('type' => 'system', 'msg' => 'meReconnect')));
        if(self::$step == FLOP)  // enviamos cartas del flop
        {
            PHPSocketMaster\ServerManager::SendTo($obj->realid,json_encode(array('type' => 'system', 'msg' => 'flop', 'uno' => self::$inTable[0], 'dos' => self::$inTable[1], 'tres' => self::$inTable[2])));
        } elseif(self::$step == TURN)  // enviamos cartas del turn y flop
        {
            PHPSocketMaster\ServerManager::SendTo($obj->realid,json_encode(array('type' => 'system', 'msg' => 'flop', 'uno' => self::$inTable[0], 'dos' => self::$inTable[1], 'tres' => self::$inTable[2])));
            PHPSocketMaster\ServerManager::SendTo($obj->realid,json_encode(array('type' => 'system', 'msg' => 'turn', 'uno' => self::$inTable[3])));
        } elseif(self::$step == RIVER) // enviamos cartas del river, turn y flop
        {
            PHPSocketMaster\ServerManager::SendTo($obj->realid,json_encode(array('type' => 'system', 'msg' => 'flop', 'uno' => self::$inTable[0], 'dos' => self::$inTable[1], 'tres' => self::$inTable[2])));
            PHPSocketMaster\ServerManager::SendTo($obj->realid,json_encode(array('type' => 'system', 'msg' => 'turn', 'uno' => self::$inTable[3])));
            PHPSocketMaster\ServerManager::SendTo($obj->realid,(array('type' => 'system', 'msg' => 'river', 'uno' => self::$inTable[4])));
        }
        self::resend(json_encode(array('type' => 'system', 'msg' => 'reconnect', 'target' => $obj->position)));
    }
    
    static public function me($ide)
    {
        $ide = 'PvP'.$ide;
        return self::$players[$ide];
    }
    
    // funcion para obtener clientes
    static public function getClients()
    {
        $out = array();
        foreach(self::$players as $key => $value)
        {
            $out[] = $value;
        }
        return $out;
    }
    
    // funcion que resuelve si hay un minimo de jugadores suficiente para poder empezar a jugar
    static public function continuable()
    {
        return self::$cantPlayers>=config::getConfig()->minimoJugadores;
    }
    
    // funcion que comienza el juego
    static public function init()
    {
        if(self::$initiated)
        {
            self::Resend(json_encode(array('type' => 'notify', 'msg' => 'out')));
        }
        self::Resend(json_encode(array('type' => 'notify', 'msg' => 'El juego comenzar&aacute; en 3 segundos.')));
        echo 'Iniciando mesa'.NL;
        echo '...3...'.NL;
        sleep(1);
        self::Resend(json_encode(array('type' => 'notify', 'msg' => 'El juego comenzar&aacute; en 2 segundos.')));
        // mezclamos
        self::mezclar();
        echo NL.'...2...'.NL;
        sleep(1);
        self::Resend(json_encode(array('type' => 'notify', 'msg' => 'El juego comenzar&aacute; en 1 segundo.')));
        // decidimos por qué posición comenzar 
        self::seleccionar();
        echo '...1...'.NL;
        sleep(1);
        // establecemos ciegas
        self::$cP = config::getConfig()->ciegaP;
        self::$cG = config::getConfig()->ciegaG;
        echo '<-READY->'.NL;
        self::$initiated = true;
        self::nuevaMano();
    }
    
    // funcion que simula mezclar el mazo
    static public function mezclar()
    {
        $aux = null;
        echo '[--{Mezclando Cartas}-->';
        
        for($z = 0; $z<(count(self::$mazo)*2); $z++)
        {
            $rnd = mt_rand(0, count(self::$mazo)-1);
            $rnd2 = mt_rand(0, count(self::$mazo)-1);
            $aux = self::$mazo[$rnd];
            self::$mazo[$rnd] = self::$mazo[$rnd2];
            self::$mazo[$rnd2] = $aux;
            $aux = null;
        }
        /*  CARTAS FIJAS */
        
        // primer persona
        self::$mazo[1] = array('palo' => PALO_CORAZON, 'valor' => 13);
        self::$mazo[2] = array('palo' => PALO_CORAZON, 'valor' => 12);
        // segunda persona
        self::$mazo[3] = array('palo' => PALO_CORAZON, 'valor' => 14);
        self::$mazo[4] = array('palo' => PALO_CORAZON, 'valor' => 5);
        // tercera persona
        self::$mazo[5] = array('palo' => PALO_CORAZON, 'valor' => 9);
        self::$mazo[6] = array('palo' => PALO_PICA, 'valor' => 5);
        // las 3 del flop
        // descarte
        self::$mazo[7] = array('palo' => PALO_CORAZON, 'valor' => 14);
        // tres primeras
        self::$mazo[8] = array('palo' => PALO_TREBOL, 'valor' => 4);
        self::$mazo[9] = array('palo' => PALO_TREBOL, 'valor' => 7);
        self::$mazo[10] = array('palo' => PALO_PICA, 'valor' => 8);
        // descarte
        self::$mazo[11] = array('palo' => PALO_CORAZON, 'valor' => 14);
        // turn
        self::$mazo[12] = array('palo' => PALO_DIAMANTE, 'valor' => 3);
        // descarte
        self::$mazo[13] = array('palo' => PALO_CORAZON, 'valor' => 14);
        // river
        self::$mazo[14] = array('palo' => PALO_TREBOL, 'valor' => 5);
       
    }
    
    // funcion que resuelve si estamos jugando
    static public function inGame()
    {
        return self::$initiated;
    }
    
    // funcion que empieza una nueva mano
    static public function nuevaMano()
    {
        self::Resend(json_encode(array('type' => 'notify', 'msg' => 'out')));
        self::$mano++;
        // preparar jugadores
        self::toInGame();
        // elegimos el dealer
        self::next_dealer();
        self::Resend(json_encode(array('type' => 'system', 'msg' => 'newDealer', 'data' => self::$dealer)));
        // empezamos a desachar cartas

        self::$turn = 0;
        self::$maxApuestas = 0;
        self::$apostador = 0;
        self::$requestedAction = null;
        self::$step = INICIAL;
        self::$pozo = 0;
        self::$ciegaPasar = true;
        
        self::$cartCount = 0;
        
        // despachamos las cartas
        self::despachar();
        // solicitamos ciegas
        self::getCiegas();
        // le toca al de la posicion turn
        self::$requestedAction = 'igualar';
        self::Resend(json_encode(array('type' => 'system', 'msg' => 'turno', 'data' => self::$turn, 'action' => 'igualar', 'cant' => self::$maxApuestas-self::$inGame[self::$turn-1]->apuesta)));
    }
    
    // funcion que despacha las cartas
    static public function despachar()
    {
        // dar cartas a cada uno
        foreach(self::$inGame as $key => $value)
        {
            // entregar par de cartas a cada uno
            $value->setCards(array(self::getOneCard(),self::getOneCard()));
            echo '--{despachando cartas}-->';
            // enviar sus cartas
            PHPSocketMaster\ServerManager::SendTo($value->realid,json_encode(array('type' => 'system', 'msg' => 'Cartas', 'data' => $value->getCards())));
        }
    }
    
    // funcion que manda el siguiente turno
    static public function nextTurn()
    {
        $newTurn = false;
        $nuevo = self::$turn;
        $count = 0;
        // hay alguien en juego?
        for($i = 0; $i<count(self::$inGame); $i++)
        {
            if(self::$inGame[$i]->retirado == false)
            {
                $count++;
            }
        }
        if($count < 2) { return false; }
        
        while(!$newTurn)
        {
            $nuevo = $nuevo+1 > count(self::$inGame) ? 1 : $nuevo+1;
            if(self::$inGame[$nuevo-1]->retirado == false)
            {
                $newTurn = true;
                return $nuevo;
            }
        }
        // no hay nadie salvo 1 en juego
        return false;
    }
    
    // funcion que analiza los mensajes recibidos
    static public function analize($msg, $idsocket)
    {
        $msg = $msg['payload'];
        // me toca a mi?
        if($idsocket == self::$inGame[self::$turn-1]->realid)
        {
          // si la accion esperada era igualar
          if(self::$requestedAction == 'igualar')
          {
              // si el usuario quiere igualar
              if($msg == 'igualar')
              {
                  // igualo la apuesta
                  $apuesta = self::$maxApuestas-self::$inGame[self::$turn-1]->apuesta;
                  self::$inGame[self::$turn-1]->apuesta = self::$maxApuestas;
                  self::$inGame[self::$turn-1]->fichas = self::$inGame[self::$turn-1]->fichas - $apuesta;
                  // aviso de mi apuesta
                  self::Resend(json_encode(array('type' => 'system', 'msg' => 'Paid', 'data' => self::$inGame[self::$turn-1]->apuesta, 'target' => self::$inGame[self::$turn-1])));
                  // el siguiente
                  self::$turn = self::nextTurn();
                  // evaluamos la proxima acción
                  self::evalAction();
              }
              // si el usuario quiere no ir
              if($msg == 'nir')
              {
                  self::$pozo = self::$pozo + self::$inGame[self::$turn-1]->apuesta;
                  self::$inGame[self::$turn-1]->apuesta = 0;
                  self::Resend(json_encode(array('type' => 'system', 'msg' => 'playerOut', 'target' => self::$inGame[self::$turn-1])));
                  self::$inGame[self::$turn-1]->retirado = true;
                  self::$inGame = array_values(self::$inGame);
                  
                  // el siguiente
                  self::$turn = self::nextTurn();
                  // evaluamos la proxima acción
                  self::evalAction();
              }
          }
          
          // si la accion esperada era pasar
          if(self::$requestedAction == 'pasar')
          {
              // si seleccionó pasar
              if($msg == 'pasar')
              {
                  if(self::$step == INICIAL && self::$turn == self::$apostador)
                  {
                      self::$step = FLOP;
                      self::flop();
                  } elseif(self::$step == FLOP && self::$turn == self::$apostador)
                  {
                      self::$step = TURN;
                      self::turn();
                  } elseif(self::$step == TURN && self::$turn == self::$apostador)
                  {
                      self::$step = RIVER;
                      self::river();
                  } elseif(self::$step == RIVER && self::$turn == self::$apostador)
                  {
                      self::$step = INICIAL;
                      self::repartija();
                  } else {
                      //pasar natural
                      // el siguiente
                      self::$turn = self::nextTurn();
                      self::evalAction();
                  }
              }
          }
          
          $match = false;
          preg_match('/aumentar\[([0-9]*)\]/', $msg, $match);
          
          // usar regex1
          if($match !== array())
          {
              self::$apostador = self::$turn;
              $aumento = $match[1];
              if($aumento < self::$inGame[self::$turn-1]->fichas) // si tenemos suficientes fichas
              {
                  self::$inGame[self::$turn-1]->fichas = self::$inGame[self::$turn-1]->fichas - (self::$maxApuestas + $aumento - self::$inGame[self::$turn-1]->apuesta);
                  self::$inGame[self::$turn-1]->apuesta = self::$maxApuestas + $aumento;
                  self::$maxApuestas = self::$maxApuestas + $aumento;
                  self::Resend(json_encode(array('type' => 'system', 'msg' => 'Paid', 'data' => self::$maxApuestas, 'target' => self::$inGame[self::$turn-1])));
  
                  // el siguiente
                  self::$turn = self::nextTurn();
                  // evaluamos la proxima acción
                  self::$ciegaPasar = false;
                  self::evalAction();
              }
          }
          
          // nueva mano, end of mano xD
          if($msg == 'pong' && self::$eom)
          {
              self::$eom = false;
              self::Resend(json_encode(array('type' => 'system', 'msg' => 'clients', 'data' => json_encode(self::getClients()))));
              self::Resend(json_encode(array('type' => 'system', 'msg' => 'reboot')));
              self::nuevaMano();
          }
        
        } else { // acción ilegal
            PHPSocketMaster\ServerManager::SendTo($idsocket, json_encode(array('type' => 'services', 'msg' => 'ILLEGAL')));
        }
        
    }
    
    static public function flop()
    {
        // enviar flop
        echo '--{FLOP}-->';
        self::$ciegaPasar=true;
        // dar cartas a cada uno
        foreach(self::$inGame as $key => $value)
        {
            self::$pozo = self::$pozo + $value->apuesta;
            self::$inGame[$key]->apuesta = 0;
        }
        self::$maxApuestas = 0;
        self::$apostador = self::$dealer;
        self::$turn = self::$dealer;
        self::$turn = self::nextTurn();
        self::Resend(json_encode(array('type' => 'system', 'msg' => 'pozo', 'data' => self::$pozo)));
        echo '--{Juntando Pozo}-->';
        // descartamos una carta
        $basura = self::getOneCard();
        echo '--{Descartar carta}-->';
        // enviamos cartas del flop
        self::$inTable = array(self::getOneCard(), self::getOneCard(), self::getOneCard());
        self::Resend(json_encode(array('type' => 'system', 'msg' => 'flop', 'uno' => self::$inTable[0], 'dos' => self::$inTable[1], 'tres' => self::$inTable[2])));
        echo '--{Despachar cartas x3}-->';
        self::evalAction();
    }
    
    static public function turn()
    {
        // enviar turn
        echo '--{TURN}-->';
        self::$ciegaPasar=true;
        // dar cartas a cada uno
        foreach(self::$inGame as $key => $value)
        {
            self::$pozo = self::$pozo + $value->apuesta;
            self::$inGame[$key]->apuesta = 0;
        }
        self::$maxApuestas = 0;
        self::$apostador = self::$dealer;
        self::$turn = self::$dealer;
        self::$turn = self::nextTurn();
        self::Resend(json_encode(array('type' => 'system', 'msg' => 'pozo', 'data' => self::$pozo)));
        echo '--{Juntando Pozo}-->';
        // descartamos una carta
        $basura = self::getOneCard();
        echo '--{Descartar carta}-->';
        // enviamos cartas del turn
        self::$inTable[3] = self::getOneCard();
        self::Resend(json_encode(array('type' => 'system', 'msg' => 'turn', 'uno' => self::$inTable[3])));
        echo '--{Despachar carta}-->';
        self::evalAction();
    }
    
    static public function river()
    {
        // enviar turn
        echo '--{RIVER}-->';
        self::$ciegaPasar=true;
        // dar cartas a cada uno
        foreach(self::$inGame as $key => $value)
        {
            self::$pozo = self::$pozo + $value->apuesta;
            self::$inGame[$key]->apuesta = 0;
        }
        self::$maxApuestas = 0;
        self::$apostador = self::$dealer;
        self::$turn = self::$dealer;
        self::$turn = self::nextTurn();
        self::Resend(json_encode(array('type' => 'system', 'msg' => 'pozo', 'data' => self::$pozo)));
        echo '--{Juntando Pozo}-->';
        // descartamos una carta
        $basura = self::getOneCard();
        echo '--{Descartar carta}-->';
        self::$inTable[4] = self::getOneCard();
        // enviamos cartas del river
        self::Resend(json_encode(array('type' => 'system', 'msg' => 'river', 'uno' => self::$inTable[4])));
        echo '--{Despachar carta}-->';
        self::evalAction();
    }
    
    static public function repartija()
    {
        echo '--{FINAL MANO}--';
        // hay alguien en juego?
        for($i = 0; $i<count(self::$inGame); $i++)
        {
            if(self::$inGame[$i]->retirado == false)
            {
                self::Resend(json_encode(array('type' => 'system', 'msg' => 'CartasV2', 'data' => self::$inGame[$i]->getCards(), 'target' => self::$inGame[$i])));
            }
        }
        $mesa = self::$inTable;
        echo '{Calculando Puntaje}-->';
        
        // que hay en mesa?
        //...
        
        // ahora veamos que tiene cada uno
        for($r = 0; $r<count(self::$inGame); $r++)
        {
            
            if(self::$inGame[$r]->retirado == false)
            {
                $pierna = false;
                $poker = false;
                $escalera = false;
                $escaleraReal = false;
                $color = false;
                $fullHouse = false;
                $mi = self::$inGame[$r]->getCards();
                
                // ---------- EN BUSCA DE LA ESCALERA ---------- 
                // ordenamos las cartas de mayor a menor con burbujeo jijiji
                $desordenadas = $mesa;
                $desordenadas[5] = $mi[0];
                $desordenadas[6] = $mi[1];
                $aux = null;
                for($i = 0; $i<7; $i++)
                    for($z = $i; $z<7 ; $z++)
                    {
                       if($desordenadas[$i]['valor'] < $desordenadas[$z]['valor']) 
                       {
                           $aux = $desordenadas[$i];
                           $desordenadas[$i] = $desordenadas[$z];
                           $desordenadas[$z] = $aux;
                       }
                    }
                // tratamos de ubicar alguna carta de mayor a menor que tenga antecesora
                // si van manteniendo el mismo palo vamos dejando real en true, sino la establecemos en false
                // tratamos de ubicar 5 cartas consecutivas
                $initialVal = 0;
                $cantidad = 0;
                $palo = 0;
                $real = true;
                $val = 0;
                for($i = 0; $i<7; $i++)
                {
                    if($desordenadas[$i]['valor'] == $initialVal-1)
                    { // tenemos un consecutivo
                        $initialVal--; // restamos uno para el proximo consecutivo
                        $cantidad++; // sumamos uno a la cantidad
                        // es escalera real por el momento?
                        if($desordenadas[$i]['palo'] != $palo)
                        {
                            $real = false;
                        }
                    } elseif($desordenadas[$i]['valor'] == $initialVal) { // si somos iguales a la anterior no corta la escalera
                        // no debemos cortar la escalera, simplemente ignoramos
                    } elseif($cantidad < 4) // no es consecutivo pero podría ser el inicial
                    {
                        $cantidad = 0;
                        $initialVal = $desordenadas[$i]['valor'];
                        $real = true;
                        $parValAux = $initialVal;
                        $palo = $desordenadas[$i]['palo'];
                    }
                }
                // guardamos la carta más alta
                // luego revisar como establecer el valor de A en menor carta
                if($cantidad >= 4) // tenemos una escalera posiblemente real
                {
                    $parVal = $parValAux;
                    if($parVal == $mi[1]['valor'])
                        $parSecondary = $mi[0]['valor'];
                    else
                        $parSecondary = $mi[1]['valor']; 
                    if($real)
                        $escaleraReal = true;
                    else
                        $escalera = true;
                }
                
                if(!$escaleraReal)
                    for($c = 1; $c<5; $c++)
                    {
                        $cont = 0;
                        for($p = 0; $p<7; $p++)
                        {
                            if($desordenadas[$p]['palo'] == $c)
                            {
                                $cont++;
                            }
                        }
                        if($cont > 4)
                        {
                            $color = true;
                            $palo = $c;
                            $parVal = $mi[0]['valor'] > $mi[1]['valor'] ? $mi[0]['valor'] : $mi[1]['valor'];
                        }
                    }
                
                if(!$escaleraReal)
                {
                    // EN BUSCA DEL FULL
                    // recorremos buscando un par y una pierna
                    $repeats = 0;
                    $ante = 0;
                    $FPAR = false;
                    $FPIERNA = false;
                    $fpierVal = 0;
                    $fparVal = 0;
                    for($i = 0; $i<7; $i++)
                    {
                        if($desordenadas[$i]['valor'] == $ante)
                            $repeats++;
                        else
                        {
                            if($repeats == 1)
                            {
                                $FPAR = true;
                                $fparVal = $ante;
                            }
                            elseif($repeats == 2)
                            {
                                $FPIERNA = true;
                                $fpierVal = $ante;
                            }
                            $repeats = 0;
                        }
                        $ante = $desordenadas[$i]['valor'];
                    }
                    if($FPAR && $FPIERNA)
                    {
                        $fullHouse = true;
                        $parVal = $fpierVal;
                        $parSecondary = $fparVal;
                    }
                }
                
                // si no tenemos nada mejor buscamos una pierna o un par o un poker etc
                if(!$escaleraReal)
                {
                    $pares = 0;
                    $contador = 0;
                    $parVal = 0;
                    $parSecondary = 0;
                    $ante = 0;
                    for($i = 0; $i<7; $i++)
                    {
                        if($desordenadas[$i]['valor'] == $ante)
                        {
                            if($contador == 0)
                            {
                                $contador = 1;
                                $pares++;
                                if($desordenadas[$i]['valor'] > $parVal) $parVal = $desordenadas[$i]['valor'];
                                elseif($desordenadas[$i]['valor'] > $parSecondary) $parSecondary = $desordenadas[$i]['valor'];
                            } elseif($contador == 1)
                            {
                                $pares--;
                                $contador++;
                                $pierna = true;
                                $parVal = $desordenadas[$i]['valor'];
                            } elseif($contador == 2)
                            {
                                $contador == 0;
                                $poker = true;
                                $pierna = false;
                                $parVal = $desordenadas[$i]['valor'];
                            }
                        } else {
                            $contador = 0;
                            $ante = $desordenadas[$i]['valor'];
                        }
                    }
                }
                
                // ----------  vemos si es un par o un par doble ---------- 
                $par = $pares;
                
                // ---------- MUESTREO DE MENSAJES ---------- 
                $i = $r;
                if($escaleraReal)
                {
                    self::$inGame[$i]->points = 800 + $parVal;
                    self::$inGame[$i]->sPoints = $parSecondary;
                    self::Resend(json_encode(array('type' => 'system', 'msg' => 'puntaje', 'data' => 'real', 'target' => self::$inGame[$i]->nick, 'value' => $parVal, 'points' => self::$inGame[$i]->points)));
                } elseif($poker)
                {
                    self::$inGame[$i]->points = 700 + $parVal; 
                    self::$inGame[$i]->sPoints = $parSecondary;
                    self::Resend(json_encode(array('type' => 'system', 'msg' => 'puntaje', 'data' => 'poker', 'target' => self::$inGame[$i]->nick, 'value' => $parVal, 'points' => self::$inGame[$i]->points)));
                } elseif($fullHouse)
                {
                    self::$inGame[$i]->points = 600 + $parVal;
                    self::$inGame[$i]->sPoints = $parSecondary;
                    self::Resend(json_encode(array('type' => 'system', 'msg' => 'puntaje', 'data' => 'full', 'target' => self::$inGame[$i]->nick, 'value' => $parVal, 'points' => self::$inGame[$i]->points)));
                } elseif($color)
                {
                    self::$inGame[$i]->points = 500 + $parVal; 
                    self::$inGame[$i]->sPoints = $parSecondary;
                    self::Resend(json_encode(array('type' => 'system', 'msg' => 'puntaje', 'data' => 'color', 'target' => self::$inGame[$i]->nick, 'value' => $parVal, 'points' => self::$inGame[$i]->points)));
                } elseif($escalera)
                {
                    self::$inGame[$i]->points = 400 + $parVal; 
                    self::$inGame[$i]->sPoints = $parSecondary;
                    self::Resend(json_encode(array('type' => 'system', 'msg' => 'puntaje', 'data' => 'escalera', 'target' => self::$inGame[$i]->nick, 'value' => $parVal, 'points' => self::$inGame[$i]->points)));
                } elseif($pierna)
                {
                    self::$inGame[$i]->points = 300 + $parVal;
                    self::$inGame[$i]->sPoints = $parSecondary;
                    self::Resend(json_encode(array('type' => 'system', 'msg' => 'puntaje', 'data' => 'pierna', 'target' => self::$inGame[$i]->nick, 'value' => $parVal, 'points' => self::$inGame[$i]->points)));
                } elseif($par>1){

                    // tiene un par doble
                    self::$inGame[$i]->points = 200 + $parVal;
                    self::$inGame[$i]->sPoints = $parSecondary;
                    self::Resend(json_encode(array('type' => 'system', 'msg' => 'puntaje', 'data' => 'Par Doble', 'target' => self::$inGame[$i]->nick, 'value' => $parVal, 'points' => self::$inGame[$i]->points)));
                } elseif($par==1) {
                    // el puntaje de base para un par es de 100
                    self::$inGame[$i]->points = 100 + $parVal;
                    self::$inGame[$i]->sPoints = $parSecondary;
                    self::Resend(json_encode(array('type' => 'system', 'msg' => 'puntaje', 'data' => 'Par', 'target' => self::$inGame[$i]->nick, 'value' => $parVal, 'points' => self::$inGame[$i]->points)));
                }else {
                    self::$inGame[$r]->points = $mi[0]['valor'] > $mi[1]['valor'] ? $mi[0]['valor'] : $mi[1]['valor'];
                    self::$inGame[$i]->sPoints = $mi[0]['valor'] < $mi[1]['valor'] ? $mi[0]['valor'] : $mi[1]['valor'];
                    self::Resend(json_encode(array('type' => 'system', 'msg' => 'puntaje', 'data' => 'NADA', 'target' => self::$inGame[$i]->nick, 'value' => $parVal, 'points' => 0)));
                }
            }
        }
        
        // Buscamos ganador/es
        $ptg = 0;
        $secondary = 0;
        $winner = 0;
        $winner02 = false;
        for($r = 0; $r<count(self::$inGame); $r++)
        {
            if(self::$inGame[$r]->retirado == false)
            {
                if(self::$inGame[$r]->points > $ptg)
                {
                    $ptg = self::$inGame[$r]->points;
                    $secondary = self::$inGame[$r]->sPoints;
                    $winner = $r;
                    $winner02 = false;
                } elseif(self::$inGame[$r]->points == $ptg && self::$inGame[$r]->sPoints > $secondary)
                {
                    $ptg = self::$inGame[$r]->points;
                    $secondary = self::$inGame[$r]->sPoints;
                    $winner = $r;
                    $winner02 = false;
                } elseif(self::$inGame[$r]->points == $ptg && self::$inGame[$r]->sPoints == $secondary)
                {
                    $winner02[] = $r;
                }
            }
        }
        
        // avisamos del/los ganador/es y repartimos
        if($winner02 === false) // un solo ganador
        {
            self::Resend(json_encode(array('type' => 'system', 'msg' => 'winer', 'data' => self::$pozo, 'target' => self::$inGame[$winner])));
            self::$inGame[$winner]->fichas =  self::$inGame[$winner]->fichas + self::$pozo;
            self::$pozo = 0;
        } else { // más de uno
            $canti = 1 + count($winner02);
            $fpozo = self::$pozo/$canti;
            self::Resend(json_encode(array('type' => 'system', 'msg' => 'winer', 'data' => $fpozo, 'target' => self::$inGame[$winner])));
            self::$inGame[$winner]->fichas = self::$inGame[$winner]->fichas + $fpozo;
            for($z = 0; $z < count($winner02); $z++)
            {
                self::Resend(json_encode(array('type' => 'system', 'msg' => 'winer', 'data' => $fpozo, 'target' => self::$inGame[$winner02[$z]])));
                self::$inGame[$winner02[$z]]->fichas = self::$inGame[$winner02[$z]]->fichas + $fpozo;
            }
            self::$pozo = 0;   
        }
        
        echo 'EOH-]'.NL.NL;
        self::$eom = true;
        self::mezclar();
        
        PHPSocketMaster\ServerManager::SendTo(self::$inGame[$winner]->realid,json_encode(array('type' => 'system', 'msg' => 'ping')));
    }
    
    static public function evalAction()
    {
        // si no tenemos la misma cantidad de fichas que la maxima apuesta enviamos accion igualar
        if(self::$maxApuestas>self::$inGame[self::$turn-1]->apuesta)
        {
            self::Resend(json_encode(array('type' => 'system', 'msg' => 'turno', 'data' => self::$inGame[self::$turn-1]->position, 'action' => 'igualar', 'cant' => self::$maxApuestas-self::$inGame[self::$turn-1]->apuesta)));
            self::$requestedAction = 'igualar';
        }
        elseif(self::$ciegaPasar == true || self::$turn != self::$apostador)
        {
            self::Resend(json_encode(array('type' => 'system', 'msg' => 'turno', 'data' => self::$inGame[self::$turn-1]->position, 'action' => 'pasar')));
            self::$requestedAction = 'pasar';
        } else
        {
            self::$requestedAction = 'pasar';
            self::analize(array('payload' => 'pasar'), null);
        }
    }
    
    static public function toInGame()
    {
        self::$inGame = array();
        foreach(self::$players as $key => $value)
        {
            if(!$value->afk)
                self::$inGame[] = $value;
        }
    }
    
    static public function getCiegas()
    {
        // tenemos que aumentar la ciega?
        if(self::$mano >= self::$lastCiega + config::getConfig()->ciegaManos)
        {
            self::$lastCiega = self::$mano;
            self::$cG = self::$cG * config::getConfig()->ciegaCrecimiento;
            self::$cP = self::$cP * config::getConfig()->ciegaCrecimiento;
        }
        // pedimos al que le sigue que envíe la ciega pequeña al que le sigue al dealer
        $player = ( self::$dealer) > count(self::$inGame)-1 ? 0 :  self::$dealer;
        // -------------------------------------------------------------------------------- COMPROBAR SI HAY DINERO SUFICIENTE PARA CIEGA
        self::$inGame[$player]->fichas = self::$inGame[$player]->fichas - self::$cP;
        // avisamos a todos del pago de la ciega
        self::Resend(json_encode(array('type' => 'system', 'msg' => 'Paid', 'data' => self::$cP, 'target' => self::$inGame[$player])));
        self::$inGame[$player]->apuesta = self::$cP;
        
        // ahora la ciega grande
        $player = ( self::$dealer+1) > count(self::$inGame)-1 ? 0 :  self::$dealer+1;
        $player = ( self::$dealer+1) > count(self::$inGame) ? 1 :  $player;
        // --------------------------------------------------------------------------------- COMPROBAR SI HAY DINERO SUFICIENTE PARA CIEGA
        self::$inGame[$player]->fichas = self::$inGame[$player]->fichas - self::$cG;
        // avisamos a todos del pago de la ciega
        self::Resend(json_encode(array('type' => 'system', 'msg' => 'Paid', 'data' => self::$cG, 'target' => self::$inGame[$player])));
        self::$inGame[$player]->apuesta = self::$cG;
        self::$apostador = $player+1;
        // fin de ciegas
        self::$maxApuestas = self::$cG;
        $turn = (self::$dealer+3)<=count(self::$inGame) ? self::$dealer+3 : (3-(count(self::$inGame)-self::$dealer));
        self::$turn = $turn;
    }
    
    static public function getOneCard()
    {
        self::$cartCount++;
        return self::$mazo[self::$cartCount];
    }
    
    static public function seleccionar()
    {
        self::$dealer = mt_rand(1,count(self::$players));
    }
    
    static public function next_dealer()
    {
        if(self::$dealer+1>count(self::$inGame))
            self::$dealer = 0;
        self::$dealer++;
    }
    
    static public function resend($msg)
    {
        PHPSocketMaster\ServerManager::resend($msg);
    }
    
    static public function disconnected($realid)
    {
        foreach(self::$players as $key => $value)
        {
            if($value->realid == $realid)
            {
                self::$players[$key]->afk = true; //esta afk
                self::Resend(json_encode(array('type' => 'system', 'msg' => 'afk', 'target' => $value)));
            }   
        }
        // lo sacamos de ingame
        for($i = 0; $i<count(self::$inGame); $i++)
        {
            if(self::$inGame[$i]->realid == $realid)
            {
                unset(self::$inGame[$i]);
                // reordenamos el arreglo sin uno menos xD
                self::$inGame = array_values(self::$inGame);
            }
        }
    }
    
    static public function reconnect($id, $realid)
    {
        // revisamos si existe algun socket con este id y lo reconectamos
        foreach(self::$players as $key => $value)
        {
            if(self::$players[$key]->nick == $id) // comprobamos si coincide
            {
                self::$players[$key]->realid = $realid;
                self::$players[$key]->afk = false;
                // hay que sentarlo
                self::sitInReconnect(self::$players[$key]);
                return true;
            }
        }
        
        return false;
    }
    
}

class player
{
    public $identifier = null;
    public $fichas = 0;
    public $position = 0;
    private $paridad = array(null, null);
    public $nick = null;
    public $realid = 0;
    public $apuesta = 0;
    public $retirado = false;
    public $points = 0;
    public $sPoints = 0;
    public $afk = false;
    
    public function __construct($id, $fichas, $pos, $name, $realid)
    {
        $this->identifier = $id;
        $this->fichas = (int)$fichas;
        $this->position = $pos;
        $this->nick = $name;
        $this->realid = $realid;
    }
    
    public function setCards($paridad)
    {
        $this->paridad = $paridad;
    }
    
    public function getCards()
    {
        return $this->paridad;
    }
}