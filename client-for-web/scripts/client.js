var msgClosed = true;
var SysNick = false;
var SysFichas = 0;
var SysPos = 0;
var websocket = '';

/* EVENTS **/
$(document).ready(function(){
    
        if(autoConnect)
            mainExec(autoHost, autoPort);
    
        //create a new WebSocket object.
        $("#connect").click(function(){
            mainExec($('#host').val(), $('#port').val());        
        });
    
        // boton de igualar
        $('#iguala').click(function(){
            
            websocket.send('igualar');
            SysFichas = SysFichas - $('#iguala').data('cant');
            $('#igualar').fadeOut();
            //$('#shadow').fadeOut();
        });
    
        // boton de igualar
        $('#nir').click(function(){
            websocket.send('nir');
            $('#igualar').fadeOut();
            //$('#shadow').fadeOut();
        });
    
        // boton de aumentar
        $('#aumentar').click(function(){
            if(SysFichas > $('#cantAument').val())
            {
                websocket.send('aumentar['+$('#cantAument').val()+']');
                SysFichas = SysFichas - $('#cantAument').val();
                $('#cantAument').val(10);
                $('#igualar').fadeOut();
            } else alert('No tienes suficientes fichas');
            //$('#shadow').fadeOut();
        });
    
        // boton de aumentar
        $('#aumentar2').click(function(){
            if(SysFichas > $('#cantAument2').val())
            {
                websocket.send('aumentar['+$('#cantAument2').val()+']');
                SysFichas = SysFichas - $('#cantAument2').val();
                $('#cantAument2').val(10);
                $('#pasar').fadeOut();
            } else alert('No tienes suficientes fichas');
            //$('#shadow').fadeOut();
        });
    
        $('#pasarBTN').click(function(){
            websocket.send('pasar');
            $('#pasar').fadeOut();
            //$('#shadow').fadeOut();
        });
});

/** MAIN FUNCTION **/

function mainExec(host, port)
{
            var wsUri = "ws://"+host+":"+port;
            websocket = new WebSocket(wsUri);
            
            websocket.onmessage = function(ev) 
            {
                var msg = JSON.parse(ev.data); //PHP sends Json data
                if(msg.type == 'system')
                {
                    if(msg.msg == 'getNick')
                    { // get nick
                        getNick();
                    }
                    
                    if(msg.msg == 'meClient')
                    {
                        SysPos = msg.data;
                        meClient();
                    }
                    
                    if(msg.msg == 'fichas')
                    {
                        SysFichas = msg.data;
                        $('#vcard'+SysPos+' .fichas').html(SysFichas);
                    }
                    
                    if(msg.msg == 'clients')
                    {
                        clients(msg);
                    }
                    
                    if(msg.msg == 'newClient')
                    {
                        // sentamos nuevo jugador
                        if(msg.data != SysPos)
                        {
                            $('#vcard'+msg.data+' .name').html(msg.nick);
                            $('#vcard'+msg.data+' .fichas').html(msg.fichas);
                            $('#cards'+msg.data).addClass('cards');
                        } else 
                        { // ignoramos nuevo jugador en nuestra posición
                            console.log(msg.data);
                        }
                    }
                    
                    if(msg.msg == 'reboot')
                    {
                        reboot();
                    }
                    
                    if(msg.msg == 'newDealer')
                    {
                        // mostramos el dealer correspondiente
                        showDealer(msg.data);
                    }
                    
                    // mis cartas
                    if(msg.msg == 'Cartas')
                    {
                        cartas(msg);
                    }
                    
                    // cartas de los demas final de mano
                    if(msg.msg == 'CartasV2')
                    {
                        cartasv2(msg);
                    }
                    
                    // pago de apuesta
                    if(msg.msg == 'Paid')
                    {
                        paid(msg);
                    }
                    
                    // pago de apuesta
                    if(msg.msg == 'turno')
                    {
                        turn(msg);
                    }
                    
                    if(msg.msg == 'playerOut')
                    {
                        outear(msg.target);
                    }
                    
                    // actualización del pozo
                    if(msg.msg == 'pozo')
                    {
                        pozo(msg.data);
                    }
                    
                    // lanzamos flop
                    if(msg.msg == 'flop')
                    {
                        lanzarFlop(msg.uno, msg.dos, msg.tres);
                    }
                    
                    // lanzamos turn
                    if(msg.msg == 'turn')
                    {
                        lanzarTurn(msg.uno);
                    }
                    
                    // lanzamos turn
                    if(msg.msg == 'river')
                    {
                        lanzarRiver(msg.uno);
                    }
                    
                    if(msg.msg == 'puntaje')
                    {
                        console.log('puntajeA');
                        puntaje(msg);
                    }
                    
                    if(msg.msg == 'afk')
                    {
                        afk(msg.target);
                    }
                    
                    if(msg.msg == 'reconnect')
                    {
                        recon(msg.target);
                    }
                    
                    if(msg.msg == 'meReconnect')
                    {
                        $('#CartasMesa').fadeIn();
                    }
                    
                    if(msg.msg == 'winer')
                    {
                        console.log('GANADOR');
                        winner(msg);
                    }
                    
                    if(msg.msg == 'ping')
                    {
                        console.log('PING');
                        setTimeout(function(){
                            websocket.send('pong');
                        }, 3000);
                    }
                    
                }
                if(msg.type=='notify')
                {
                    if(msg.msg != 'out')
                        notificar(msg.msg);
                    else
                        desnotificar();
                }
                if(msg.type=='services')
                {
                    if(msg.msg == 'V-ping')
                    {
                        websocket.send('V-pong');
                    }
                    
                    if(msg.msg == 'ILLEGAL')
                    {
                        alert('ACCIÓN ILEGAL!');
                    }
                }
            }
            websocket.onopen = function(ev) { // connection is open
                $('#panel').fadeOut();
                $('#shadow').fadeOut();
                $('#notificator').html("Por favor espere...");
                $('#notificator').fadeIn();
            }
            // log de errores
            websocket.onerror = function(ev)
            {
                notificar('El cliente sufri&oacute; una falla.');
                console.log(ev.data);
                clear();
            };
            // desconexión
            websocket.onclose = function(ev)
            {
                if(!autoConnect)
                {
                  notificar('Error de conexi&oacute;n.');
                  $('#shadow').fadeIn();
                  $('#panel').fadeIn();
                  clear();
                } else {
                  notificar('Error de conexi&oacute;n. Reconectando');
                  clear();
                  setTimeout(function(){
                            mainExec(host, port);
                        }, 3000);
                }
            };
}

function getNick()
{
    SysNick = autoID;
    if(SysNick!=='')
        websocket.send(SysNick);
}

function meClient()
{
    $('#vcard'+SysPos+' .name').html(SysNick);
    $('#vcard'+SysPos).addClass('mevcard');
    $('#cards'+SysPos).addClass('cards');
}

function pozo(cantidad)
{
    console.log('Actualizacion de pozo '+cantidad);
    $('#pozo').html('$'+cantidad);
    var i = 0;
    for(i = 0; i<9; i++)
    {
        $('#vcard'+i+' .apuesta').fadeOut();
    }
}

function lanzarTurn(uno)
{
    console.log('La mano entra al Turn');
    var palo = '';
    var valor = 0;
    // primera carta del flop
    if(uno.palo == 1)
        palo = '♥';
    if(uno.palo == 2)
        palo = '♦';
    if(uno.palo == 3)
        palo = '♠';
    if(uno.palo == 4)
        palo = '♣';
    
    if(uno.valor>1 && uno.valor<11)
            valor = uno.valor;
    else
    {
        if(uno.valor==14)
            valor = 'A';
        if(uno.valor==11)
            valor = 'J';
        if(uno.valor==12)
            valor = 'Q';
        if(uno.valor==13)
            valor = 'K';
    }
    
    $('#turn #card').addClass('Palo-'+uno.palo);
    $('#turn #card').html(valor+'<br />'+palo); 
}

function lanzarRiver(uno)
{
    console.log('La mano entra al River');
    var palo = '';
    var valor = 0;
    // primera carta del flop
    if(uno.palo == 1)
        palo = '♥';
    if(uno.palo == 2)
        palo = '♦';
    if(uno.palo == 3)
        palo = '♠';
    if(uno.palo == 4)
        palo = '♣';
    
    if(uno.valor>1 && uno.valor<11)
            valor = uno.valor;
    else
    {
        if(uno.valor==14)
            valor = 'A';
        if(uno.valor==11)
            valor = 'J';
        if(uno.valor==12)
            valor = 'Q';
        if(uno.valor==13)
            valor = 'K';
    }
    
    $('#river #card').addClass('Palo-'+uno.palo);
    $('#river #card').html(valor+'<br />'+palo); 
}

function lanzarFlop(uno, dos, tres)
{
    console.log('La mano entra al Turn');
    var palo = '';
    var valor = 0;
    // primera carta del flop
    if(uno.palo == 1)
        palo = '♥';
    if(uno.palo == 2)
        palo = '♦';
    if(uno.palo == 3)
        palo = '♠';
    if(uno.palo == 4)
        palo = '♣';
    
    if(uno.valor>1 && uno.valor<11)
            valor = uno.valor;
    else
    {
        if(uno.valor==14)
            valor = 'A';
        if(uno.valor==11)
            valor = 'J';
        if(uno.valor==12)
            valor = 'Q';
        if(uno.valor==13)
            valor = 'K';
    }
    
    $('#flop #card1').addClass('Palo-'+uno.palo);
    $('#flop #card1').html(valor+'<br />'+palo); 
    // segunda del flop
    if(dos.palo == 1)
        palo = '♥';
    if(dos.palo == 2)
        palo = '♦';
    if(dos.palo == 3)
        palo = '♠';
    if(dos.palo == 4)
        palo = '♣';
    
    if(dos.valor>1 && dos.valor<11)
            valor = dos.valor;
    else
    {
        if(dos.valor==14)
            valor = 'A';
        if(dos.valor==11)
            valor = 'J';
        if(dos.valor==12)
            valor = 'Q';
        if(dos.valor==13)
            valor = 'K';
    }
    
    $('#flop #card2').addClass('Palo-'+dos.palo);
    $('#flop #card2').html(valor+'<br />'+palo); 
    // tercera del flop
    if(tres.palo == 1)
        palo = '♥';
    if(tres.palo == 2)
        palo = '♦';
    if(tres.palo == 3)
        palo = '♠';
    if(tres.palo == 4)
        palo = '♣';
    
    if(tres.valor>1 && tres.valor<11)
            valor = tres.valor;
    else
    {
        if(tres.valor==14)
            valor = 'A';
        if(tres.valor==11)
            valor = 'J';
        if(tres.valor==12)
            valor = 'Q';
        if(tres.valor==13)
            valor = 'K';
    }
    
    $('#flop #card3').addClass('Palo-'+tres.palo);
    $('#flop #card3').html(valor+'<br />'+palo); 
}

function clients(msg)
{
    var i = 0;
    msg.data = JSON.parse(msg.data);
    for(i = 0; i<msg.data.length; i++)
    {
        if(msg.data[i].position != SysPos) // no nos posicionamos a nosotros :P
        {  
            $('#cards'+msg.data[i].position).addClass('cards');
            $('#vcard'+msg.data[i].position+' .name').html(msg.data[i].nick);
            $('#vcard'+msg.data[i].position+' .fichas').html(msg.data[i].fichas);
        }
    }
}

function cartas(msg)
{
    console.log('--CARTAS--');
    console.log(msg.data[0]);
    // mostramos primera carta
    $('#vcard'+SysPos+' .cards .card1').addClass('Palo-'+msg.data[0].palo);
    $('#CartasMesa').fadeIn();
    var valor = 0;
    var palo = '';
    if(msg.data[0].valor>1 && msg.data[0].valor<11)
            valor = msg.data[0].valor;
    else
    {
        if(msg.data[0].valor==14)
            valor = 'A';
        if(msg.data[0].valor==11)
            valor = 'J';
        if(msg.data[0].valor==12)
            valor = 'Q';
        if(msg.data[0].valor==13)
            valor = 'K';
    }
    if(msg.data[0].palo == 1)
        palo = '♥';
    if(msg.data[0].palo == 2)
        palo = '♦';
    if(msg.data[0].palo == 3)
        palo = '♠';
    if(msg.data[0].palo == 4)
        palo = '♣';
    $('#vcard'+SysPos+' .cards .card1').html(valor+'<br />'+palo);                                          
                        
    // mostramos segunda carta
    $('#vcard'+SysPos+' .cards .card2').addClass('Palo-'+msg.data[1].palo);
    if(msg.data[1].valor>1 && msg.data[1].valor<11)
        valor = msg.data[1].valor;
    else
    {
        if(msg.data[1].valor==14)
            valor = 'A';
        if(msg.data[1].valor==11)
            valor = 'J';
        if(msg.data[1].valor==12)
            valor = 'Q';
        if(msg.data[1].valor==13)
            valor = 'K';
    }
    if(msg.data[1].palo == 1)
        palo = '♥';
    if(msg.data[1].palo == 2)
        palo = '♦';
    if(msg.data[1].palo == 3)
        palo = '♠';
    if(msg.data[1].palo == 4)
        palo = '♣';
    $('#vcard'+SysPos+' .cards .card2').html(valor+'<br />'+palo); 
    $('.cards').fadeIn();
}

function cartasv2(msg)
{
    if(msg.target.position != SysPos)
    {
        console.log('-- Cartas de '+msg.target.nick+' --');
        // mostramos primera carta
        $('#vcard'+msg.target.position+' .cards .card1').addClass('Palo-'+msg.data[0].palo);
        $('#CartasMesa').fadeIn();
        var valor = 0;
        var palo = '';
        if(msg.data[0].valor>1 && msg.data[0].valor<11)
                valor = msg.data[0].valor;
        else
        {
            if(msg.data[0].valor==14)
                valor = 'A';
            if(msg.data[0].valor==11)
                valor = 'J';
            if(msg.data[0].valor==12)
                valor = 'Q';
            if(msg.data[0].valor==13)
                valor = 'K';
        }
        if(msg.data[0].palo == 1)
            palo = '♥';
        if(msg.data[0].palo == 2)
            palo = '♦';
        if(msg.data[0].palo == 3)
            palo = '♠';
        if(msg.data[0].palo == 4)
            palo = '♣';
        $('#vcard'+msg.target.position+' .cards .card1').html(valor+'<br />'+palo);                                          

        // mostramos segunda carta
        $('#vcard'+msg.target.position+' .cards .card2').addClass('Palo-'+msg.data[1].palo);
        if(msg.data[1].valor>1 && msg.data[1].valor<11)
            valor = msg.data[1].valor;
        else
        {
            if(msg.data[1].valor==14)
                valor = 'A';
            if(msg.data[1].valor==11)
                valor = 'J';
            if(msg.data[1].valor==12)
                valor = 'Q';
            if(msg.data[1].valor==13)
                valor = 'K';
        }
        if(msg.data[1].palo == 1)
            palo = '♥';
        if(msg.data[1].palo == 2)
            palo = '♦';
        if(msg.data[1].palo == 3)
            palo = '♠';
        if(msg.data[1].palo == 4)
            palo = '♣';
        $('#vcard'+msg.target.position+' .cards .card2').html(valor+'<br />'+palo); 
        $('.cards').fadeIn();
    }
}

function notificar(msg)
{
    $('#notificator').html(msg);
    if(msgClosed)
    {
        msgClosed = false;
        $('#notificator').fadeIn();
    }
}
function desnotificar()
{
    msgClosed = true;
    $('#notificator').fadeOut();
}
function showDealer(id)
{
    var i = 0;
    for(i=1; i<9; i++)
    {
        $('#dealer'+i).hide();
    }
    console.log('Dealer en posici&oacute;n '+id);
    $('#dealer'+id).fadeIn();
}
function paid(msg)
{
    console.log(msg.target);
    $('#vcard'+msg.target.position+' .fichas').html(msg.target.fichas);
    console.log(msg.target.nick+' paga '+msg.data);
    $('#vcard'+msg.target.position+' .apuesta').fadeIn();
    $('#vcard'+msg.target.position+' .apuesta').attr('title', '$'+msg.data);
    $('#vcard'+msg.target.position+' .apuestaPrice').html('$'+msg.data);
}
function turn(msg)
{
    var i = 0;
    for(i=1; i<9; i++)
    {
        $('#vcard'+i).removeClass('turn');
    }
    $('#vcard'+msg.data).addClass('turn');
    if(msg.data == SysPos)
    {
        //$('#shadow').fadeIn();
        if(msg.action == 'igualar')
        {
            $('#igualar').fadeIn();
            $('#iguala').val('Igualar $'+msg.cant);
            $('#iguala').data('cant',msg.cant);
        }
        if(msg.action == 'pasar')
            $('#pasar').fadeIn();
    }
}

function clear()
{
    var i = 0;
    for(i=1; i<9; i++)
    {
        // chau me, chau staff, nick y etc.
        $('#vcard'+i).removeClass('mevcard');
        $('#vcard'+i).removeClass('staff');
        $('#vcard'+i).removeClass('turn');
        $('#vcard'+i+' .name').html('Vac&iacute;o');
        $('#vcard'+i+' .fichas').html('00000');
        $('#vcard'+i+' .apuestaPrice').html('');
        $('#cards'+i).removeClass('retirado');
        $('#vcard'+i).removeClass('afk');
    }
    reboot();
    
}

function reboot()
{
    var i = 0;
    for(i=1; i<9; i++)
    {
        // chau dealer
        $('#dealer'+i).hide();
        $('#vcard'+i).removeClass('winner');
        $('#vcard'+i+' .card1').removeClass('winner');
        $('#vcard'+i+' .card2').removeClass('winner');
        $('#vcard'+i+' .apuestaPrice').html('');
    }
    for(i=1; i<5; i++)
    {
        $('.card1').removeClass('Palo-'+i);
        $('.card2').removeClass('Palo-'+i);
    }
    // borramos el flop
    for(i=1; i<4; i++)
    {
        $('#flop #card'+i).removeClass('Palo-1');
        $('#flop #card'+i).removeClass('Palo-2');
        $('#flop #card'+i).removeClass('Palo-3');
        $('#flop #card'+i).removeClass('Palo-4');
        $('#flop #card'+i).html(''); 
    }
    // borramos el turn
    for(i=1; i<4; i++)
    {
        $('#turn #card').removeClass('Palo-'+i);
    }
    $('#turn #card').html(''); 
    // borramos el river
    for(i=1; i<4; i++)
    {
        $('#river #card').removeClass('Palo-'+i);
    }
    $('#river #card').html(''); 
    $('#CartasMesa').fadeOut();
    $('.card1').html('');
    $('.card2').html('');
    $('.apuesta').hide();
    $('#igualar').hide();
    $('#pasar').hide();
    $('.cards').hide();
}

function outear(target)
{
    if(SysPos == target.position)
    {
        $('#cards'+target.position).addClass('retirado');
        console.log('Te retiraste.'); 
    } 
    else
    {
        $('#cards'+target.position).hide();
        console.log('El jugador '+target.nick+' se retira');
    }
    
}

function winner(msg)
{
    console.log(msg.target.nick + ' ganó '+msg.data);
    var i = 0;
    for(i=1; i<9; i++)
    {
        $('#vcard'+i).removeClass('turn');
    }
    $('#vcard'+msg.target.position).addClass('winner');
    $('#vcard'+msg.target.position+' .card1').addClass('winner');
    $('#vcard'+msg.target.position+' .card2').addClass('winner');
}

function puntaje(msg)
{
    console.log('puntaje');
    console.log('El jugador '+msg.target+' obtuvo '+msg.data+' de '+msg.value+' ganando '+msg.points+' puntos');
}

function afk(target)
{
    $('#vcard'+target.position).addClass('afk');
}

function recon(target)
{
    $('#vcard'+target).removeClass('afk');
}