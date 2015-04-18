var websocket = '';
var host = '127.0.0.1';
var port = '2026';

/* EVENTS **/
$(document).ready(function(){
        connect();      
});

function connect()
{
        tools_connMSG('Estableciendo conexi&oacute;n...');
        var wsUri = "ws://"+host+":"+port;
        websocket = new WebSocket(wsUri);
        
        websocket.onmessage = function(ev) 
        {
            procesar(ev.data);    
        }
        
        websocket.onopen = function(ev) { // connection is open
            tools_connMSGout();        
        }
        // log de errores
        websocket.onerror = function(ev)
        {
            alert('Internal Client Error');
            reconnect();
            console.log(ev.data);
        }
        
        // desconexión
        websocket.onclose = function(ev)
        {
            reconnect();
        }
}

function reconnect()
{
    tools_connMSG('Reconectando...');
}

function procesar(ev)
{
    var msg = JSON.parse(ev);

    if(msg.type == 'welcome')
    {
        tools_login(msg.obj, msg.obj2);
    }
}

function sck_enviar(msg)
{
    alert(msg);
    websocket.send(msg);
}