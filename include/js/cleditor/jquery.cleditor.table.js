/*!
 CLEditor Table Plugin v1.0.3
 http://premiumsoftware.net/cleditor
 requires CLEditor v1.2.2 or later
 
 Copyright 2010, Chris Landowski, Premium Software, LLC
 Dual licensed under the MIT or GPL Version 2 licenses.
*/
(function(n){function t(t,i){n(i.popup).children(":button").unbind("click").bind("click",function(){var r=i.editor,u=n(i.popup).find(":text"),f=parseInt(u[0].value),e=parseInt(u[1].value),t;if(f>0&&e>0){for(t="<table cellpadding=2 cellspacing=2 border=1>",y=0;y<e;y++){for(t+="<tr>",x=0;x<f;x++)t+="<td>"+x+","+y+"<\/td>";t+="<\/tr>"}t+="<\/table><br />"}t&&r.execCommand(i.command,t,null,i.button),u.val("4"),r.hidePopups(),r.focus()})}n.cleditor.buttons.table={name:"table",image:"table.gif",title:"Insert Table",command:"inserthtml",popupName:"table",popupClass:"cleditorPrompt",popupContent:"<table cellpadding=4 cellspacing=0><tr><td>Cols:<br><input type=text value=4 style='width:40px'><\/td><td>Rows:<br><input type=text value=4 style='width:40px'><\/td><\/tr><\/table><input type=button value=Submit>",buttonClick:t},n.cleditor.defaultOptions.controls=n.cleditor.defaultOptions.controls.replace("rule ","rule table ")})(jQuery);