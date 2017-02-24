/**
 * Created by MisterCray on 11/7/2016.
 */

function update(ops, idno, quantity)   {
    $.post('ajax/handler.php', { type: "invupdate", op: ops, id: idno, qty: quantity})
        .done( function(response)	{
            var ajaxRequest;
            var div ="#counts"+idno;
            $(div).html(response);
            var noti = "#erralpha"+idno;
            $(noti).html("Updated").show().fadeOut("slow").css('color','black');
        });
}

/*function check_op(check)    {
    if($.isNumeric(check.charAt(0)))
        return "=";
    else
        return check.charAt(0);
}*/

$(document).ready(function(){
    $('input').bind("cut copy paste",function(e) {
        e.preventDefault();
    });
});

$("input").on('keypress', function(e)    {
    if(e.which === 13)   {
        event.preventDefault();
        if(this.value != '')    {
            if($.isNumeric(this.value.charAt(0)))
                 update("=", this.id, this.value);
            else
                 update(this.value.charAt(0), this.id, this.value.substr(1,this.value.length-1));
            this.value = '';
        }
        else    {
            var div = "#erralpha"+this.id;
            $(div).html("Invalid").show().fadeOut("slow");
        }
    }
    else if (e.which != 8 && e.which != 0 && e.which != 43 && e.which != 45 && e.which != 61 && (e.which < 48 || e.which > 57)) {
        //display error message
        var div = "#erralpha"+this.id;
        $(div).html("Digits Only").show().fadeOut("slow");
        return false;
    }
});

$('.op').click(function() {
    var con = this.id.split('_');
    var field = "#"+con[1];
    if($(field).val() != '')    {
        var count = update(con[0], con[1], $(field).val());
        $(field).val("");
        var div ="#counts"+con[1];
        $(div).html(count);
    }
    else    {
        var div = "#erralpha"+con[1];
        $(div).html("Invalid").show().fadeOut("slow");
    }
});

$('.notes').click(function()    {
    var con = this.id.split("_");
    if($('#notepad').is(":hidden")) {
        $('#notepad').html("" +
            "<img src='images/notepad.png' >" +
            "<img src='images/close.png' id='close_comment' >" +
            "<textarea class='comment' rows='15' cols='40' ></textarea>" +
            "<button class='com_submit' id='comsub'>Submit</button></div>");
            $('#notepad').show().fadeIn("slow");

        //Retrieve any existing comments from DB, if any.
        $.post('ajax/handler.php', { type: "comments", id: con[1], process: "get"})
            .done(function(response)   {
                if(response) {
                    $('.comment').html(response);
                    $('.comment').attr('id','com1');
                }
                else {
                    $('.comment').html("Enter comment here...");
                    $('.comment').attr('id','com0');
                }
            })

        $('#comsub').click(function()   {
            var comtext = $('.comment').val();
            if($('.comment').attr("id") == "com1") {
                $.post('ajax/handler.php', {type: "comments", id: con[1], process: "set", comment: comtext})
                    .done(function(response) {
                        var noti = "#erralpha" + con[1];
                        $(noti).html("Comment Updated!").show().fadeOut("slow").css('color', 'black');
                    })
                $('#notepad').hide().fadeOut("slow");
            }
            else    {
                $('#notepad').append("" +
                    "<span id='com_noti'></span>");
                $('#com_noti').html("Invalid comment!").show().fadeOut("slow");
            }
        })

        $('#close_comment').click(function ()   {
            $('#notepad').empty();
            $('#notepad').hide().fadeOut("slow");
        })
    }
    else    {
        $('#notepad').empty();
        $('#notepad').hide().fadeOut("slow");
    }

$('.comment').bind('input propertychange', function()   {
    if(this.id == "com0")   {
        $('.comment').html("");
        $('.comment').attr("id","com1");
    }
})

    /*if($('#notepad').is(":hidden")) {
        $('#notepad').show().fadeIn("slow");
        var con = this.id.split("_");
        $.post('ajax/handler.php', { type: "comments", id: con[1], process: "get" })
            .done( function(response)   {
                if(!response) {
                    $('.comment').html("Enter comment here...");
                    $('.comment').attr("id","com0");
                }
                else {
                    $('.comment').html("Existing Comment: " + response);
                    $('.comment').attr("id","com1");
                }
            });
    }
    else {
        $('.comment').html("");
        $('#notepad').hide().fadeOut("slow");
    }*/
    //$('#comment').width();
})

$('#additem').click(function() {
    if($('#addbox').length)
        $('#addbox').remove();
    else    {
        $('body').append("<div id='addbox' style='display: none'>" +
                         "<img src='images/close.png' id='close_add' >" +
                         "<center><h2><u>New Item</u></h2><br/><h3>" +
                         "Product ID: <input type='text' id='newid' placeholder='Product ID' /><br/>" +
                         "Product Name: <input type='text' id='newname' placeholder='Product Name' /><br/>" +
                         "Stock Quantity: <input type='text' id='newstock' placeholder='Current Stock' /></center>" +
                         "<span id='addnoti'></span>" +
                         "<button class='com_submit' id='newitemsub'>Submit</button></div>");
        $('#addbox').show().fadeIn("slow");
        $('#close_add').click(function()    {
            $('#addbox').hide().fadeOut("slow");
            $('#addbox').remove();
        })
        $('#newitemsub').click(function() {
            var new_id = $('#newid').val();
            var new_name = $('#newname').val();
            var new_stock = $('#newstock').val();
            $.post('ajax/handler.php', {type: "newitem", id: new_id, name: new_name, stock: new_stock})
                .done(function(response) {
                    if(response == "Pass") {
                        $('#addnoti').html("Item Added").fadeOut("slow");
                        setTimeout(function()   {
                            $('#addbox').hide().fadeOut("slow");
                            $('#addbox').remove();
                        }, 1000);
                    }
                    else
                        $('#addnoti').html("Invalid Entry").show().fadeOut("slow");
                })
        })
    }
})

$('#reset').click(function()    {

})

$('#collate').click(function()  {
        $('#newitems').append("<div id='collater'><img src='images/collating.svg' id='collating' />" +
                         "<center><div id='colnoti' style='font-weight: 900;'></div></center></div>");
        $('#colnoti').html("Collating...");
        $('.wrapper').fadeTo("slow", 0.5).css('pointer-events','none');
        $('#collating').show().fadeIn("slow");
        $.post('ajax/handler.php', {type: "collate"})
            .done(function(response) {
                setTimeout(function() {
                    $('#colnoti').html("Tables Collated.<br/>Reloading...").show().fadeOut(4000);
                }, 4000)
                alert(response);
                /*setTimeout(function()   {
                    location.reload();
                },8000)*/
            })
})


