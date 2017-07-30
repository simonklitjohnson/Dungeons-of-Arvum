setInterval(function(){
	if(chatmessages.length > 0)
	{
		lastentry = chatmessages[chatmessages.length - 1];

		if((lastentry['timestamp'] + 15) < Math.round(+new Date()/1000))
		{
			clearChat();

		}
	}
}, 1000);

var clearChat = function()
{
			chatmessages = [];
			$(".chatmessages").fadeOut("slow", function(){
				$(".chatmessages .message").remove();
				$(".chatmessages").show();
			});
}

var Game = {
    display: null,
 
    init: function() {
        this.display = new ROT.Display({width:41, height:21, fontSize: 18});
        $(".canvascontainer").prepend(this.display.getContainer());
     	Game.display.drawText(5, 2, "+-+-+-+-+-+-+-+-+\n|D|u|n|g|e|o|n|s|\n+-+-+-+-+-+-+-+-+\n\n\n+-+-+\n|o|f|\n+-+-+\n\n\n+-+-+-+-+-+\n|A|r|v|u|m|\n+-+-+-+-+-+");
     	Game.display.drawText(5, 17, "Please enter the IP address\nof the server.");
        
    }
}

Game.map = {};


Game._generateMap = function() {
    Game.display.drawText(5, 5, "hidde\ntest");

    /*var digger = new ROT.Map.Digger(mapwidth, mapheight);
 	freeCells = [];
    var digCallback = function(x, y, value) {
	    	var key = x+","+y;
	        if (value) {
	        	this.map[key] = "#";
	        } else { /* do not store walls *//*
	        	freeCells.push(key);
	        	this.map[key] = ".";
	    	}
    	}
    
    digger.create(digCallback.bind(this));
    map(this.map);*/
}


drawFromData = function(mapdata) {
	ix = 0;
	iy = 0;
	$.each(mapdata, function( index, value ) {
		iy = 0;
		$.each(value, function (indexx, valuee)
		{
			valuee = JSON.parse(valuee);
			Game.display.draw(ix, iy, valuee['rep'], valuee['color']);
			iy++;
		});
		ix++;
	});
}

drawText = function(text)
{
	Game.display.drawText(1,  4, text);

}

setPlayerInfo = function(playerinfo)
{
	$(".playername").text(playerinfo['name']);
	$(".playerlevel").text(playerinfo['level']);
	$(".playercurxp").text(playerinfo['curxp']);
	$(".playermaxxp").text(playerinfo['maxxp']);
	$(".playercurhp").text(playerinfo['curhp']);
	$(".playermaxhp").text(playerinfo['maxhp']);
	$(".playercurmana").text(playerinfo['curmana']);
	$(".playermaxmana").text(playerinfo['maxmana']);
	$(".playerx").text(playerinfo['x']);
	$(".playery").text(playerinfo['y']);
	$(".healthpotions").text(playerinfo['healthpots']);
	$(".manapotions").text(playerinfo['manapots']);
	$(".playerarmor").html("<span style='color:"+playerinfo['armor']['color']+" !important;'>" + playerinfo['armor']['name'] + "</span>");
	for (var i = 0; i <= 8; i++) {
		$(".i" + (i+1)).html("<span style='color:"+playerinfo['inventory'][i]['color']+" !important;'>" + playerinfo['inventory'][i]['text'] + "</span>");
	}

}

queueMessage = function(message)
{
	thismessage = {"message": message, "timestamp": Math.round(+new Date()/1000)};
	chatmessages.push(thismessage);
	childcount = $(".chatmessages").children().length;
	if(childcount > 5)
	{
		$(".chatmessages .message").last().fadeOut("slow", function(){
			$(".chatmessages .message").last().remove();
		});
						
	}
	$(".chatmessages").prepend(message);
}

window.addEventListener("keydown", function(e) {
    var code = e.keyCode;

    var vk = "?"; /* find the corresponding constant */
    for (var name in ROT) {
        if (ROT[name] == code && name.indexOf("VK_") == 0) { vk = name; }
    }
    if(chatmode == false)
    {
	    if(code == 13) // Enter chat mode
	    {
	    	chatmode = true;
	    	$(".chatcursor").addClass("blink");
			$('.blink').each(function() {
			    var elem = $(this);
			    chatinterval = setInterval(function() {
			        if (elem.css('visibility') == 'hidden') {
			            elem.css('visibility', 'visible');
			        } else {
			            elem.css('visibility', 'hidden');
			        }    
			    }, 400);
			});
	    } else {
	    	keypress(vk);
		}
	}else {
	if(e.keyCode == 8) //Backspace
		{
			chatstring = chatstring.substring(0, chatstring.length - 1);
			$(".mytext").text(chatstring);
				
		}
	if(e.keyCode == 27) //Escape
		{
			$(".chatcursor").removeClass("blink");
			clearInterval(chatinterval);
			$(".chatcursor").css('visibility', 'visible');
			chatstring = "";
			$(".mytext").text(chatstring);
			chatmode = false;
			chatmodeflag2 = false;
		}
	}

});

window.addEventListener("keypress", function(e) {
	var keyCode = e.which;
    if(chatmode == true && chatmodeflag2 == true)
    {
		chatstring = chatstring + String.fromCharCode(keyCode);
		$(".mytext").text(chatstring);

		if(e.keyCode == 13)
		{
			$(".chatcursor").removeClass("blink");
			clearInterval(chatinterval);
			$(".chatcursor").css('visibility', 'visible');
			chat(chatstring);
			chatstring = "";
			$(".mytext").text(chatstring);
			chatmode = false;
			chatmodeflag2 = false;
			inRequest = false;
		}
	}
	if(chatmode == true && chatmodeflag2 == false)
	{
		chatmodeflag2 = true;
	}
});