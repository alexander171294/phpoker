$(document).ready(function(){

        $('#openreg').click(function(){
            open_registro();
        });
        $('#closereg').click(function(){
            close_registro();
        });   
        
        $('#login_ingresar').click(function(){
            sck_enviar('login@'+$('#login_usr').val()+'@'+$('#login_pass').val());
        });         
});