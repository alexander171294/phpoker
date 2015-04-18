function tools_connMSG(msg)
{
    $('#con-msg').html(msg);
    $('#conexion').fadeIn();
    $('#blacky').show();
}

function tools_connMSGnew(msg)
{
    $('#con-msg').html(msg);
}

function tools_connMSGout()
{
    $('#conexion').fadeOut();
    $('#blacky').hide();
}

function tools_login(welc, nick)
{
    $('#nick').html(nick);
    $('#registro').hide();
    $('#login').hide();
    $('#menu').show();
    $('#menu').animate({
          'margin-top':'120px'
    },700);
}

function open_registro()
{
    $('#login').animate({
          'margin-top':'-470px'
    },1500);
    $('#registro').animate({
          'margin-top':'280px'
    },1500);
    $('#closereg').show();
    $('#openreg').hide();
}

function close_registro()
{
    $('#login').animate({
          'margin-top':'-260px'
    },1500);
    $('#registro').animate({
          'margin-top':'70px'
    },1500);
    $('#openreg').show();
    $('#closereg').hide();
}