/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this file,
 * You can obtain one at http://mozilla.org/MPL/2.0/. */
 
 function addDB() {
    var fscommand = player_DoFSCommand;

    window.player_DoFSCommand = function(command, args) 
    {
        args = String(args);
        command = String(command);

        var arrArgs = args.split(g_strDelim);
        switch(command)
        {
            case "CC_PrintResults":
            		if (args!=='') {
                    	g_oQuizResults.oOptions.bShowUserScore = (arrArgs[0] === "true");
                    	g_oQuizResults.oOptions.bShowPassingScore = (arrArgs[1] === "true");
                    	g_oQuizResults.oOptions.bShowShowPassFail = (arrArgs[2] === "true");
                    	g_oQuizResults.oOptions.bShowQuizReview = (arrArgs[3] === "true");
                    	g_oQuizResults.oOptions.strResult = arrArgs[4];
                    	g_oQuizResults.oOptions.strName = arrArgs[5];
                    	if (!g_oQuizResults.strTitle) {
                            	g_oQuizResults.strTitle = "";
                    	}
                    }
                    $.ajax(
                    	{
                    		url:"/DB/db.php", 
                    		data:{quiz: g_oQuizResults, responses: g_arrResults}, 
                    		datatype:"xml", 
                    		type: 'POST',
                    		async:false
    			})
    			.done(function() { 
    				if (typeof g_oQuizResults.sent === "undefined")
    					alert("results have been sent"); 
    				g_oQuizResults.sent = true;
    				})
			.fail(function() { alert("an error occurred"); });    			
                    break;  
            default:
                fscommand(command, args);
        }
    };
    window.DoOnClose = function() {
    	window.player_DoFSCommand("CC_PrintResults", [true,true,true,false,g_oQuizResults.strResult,''].join(g_strDelim));
    	return "sending data";    
    };
}
addDB();