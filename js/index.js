//Function to set cursor focus auto
/*$(document).ready(function() {
	setTimeout(function() {
		$('#user').focus();
		$('#user').attr("placeholder", "Here");
	},2000);
})*/

$("#login-button").click(function(event){
    event.preventDefault();
    $.post('ajax/handler.php', { type: "login", uname: $('#user').val(), upass: $('#pass').val() })
        .done( function(response)	{
            if(response != "fail")	{

                $('form').fadeOut(500);
                $('.wrapper').addClass('form-success');
                $('.title').html("Hello, "+response+".<br/>Redirecting you to stock page...");
                setTimeout(function(){
                        window.location.href = "view.php";
                },3000);


            }

            else	{

                $('.title').html("Invalid login! Try again.");
                $('form').fadeOut(80);
                $('form').fadeIn(80);

            }
            var ajaxRequest;
        });
});