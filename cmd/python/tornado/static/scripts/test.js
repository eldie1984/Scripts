/* test.js */

function reset(interfaz,error)
{
/*	alert("Vas a resetear la interfaz " + interfaz + "con el error" + error);*/
	var r=confirm("Vas a resetear la interfaz " + interfaz + "con el error" + error);
  alert(r);
  if (r)
	{
	 
    //  doPost("reset", new Array(new Array("interfaz", interfaz), new Array("error", error)));
    method = "post"; // Set method to post by default if not specified.
    path="reset";
    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);
    params={interfaz:interfaz , error:error}
    for(var key in params) {
      if(params.hasOwnProperty(key)) {
              var hiddenField = document.createElement("input");
              hiddenField.setAttribute("type", "hidden");
              hiddenField.setAttribute("name", key);
              hiddenField.setAttribute("value", params[key]);
              form.appendChild(hiddenField);
                                        }
                             }
     document.body.appendChild(form);
     form.submit();
       }
	else
	{
	    x="You pressed Cancel!";
	}
}

/*
function post_to_url(path, params, method) {
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);
    
    for(var key in params) {
    	if(params.hasOwnProperty(key)) {
              var hiddenField = document.createElement("input");
              hiddenField.setAttribute("type", "hidden");
              hiddenField.setAttribute("name", key);
              hiddenField.setAttribute("value", params[key]);
              form.appendChild(hiddenField);
                                        }
                             }
     document.body.appendChild(form);
     form.submit();
       }

$(document).ready(function() {
    $('#reload-button').click(function(event) {
        jQuery.ajax({
            url: '//localhost:8888/',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'reload',
            },
            success: function() {
                window.location.reload(true);
            }
        });
    });
});
/*
function doPost(url, parameters) { 
  // create the AJAX object 
  var xmlHttp = undefined; 
  if (window.ActiveXObject){ 
    try { 
      xmlHttp = new ActiveXObject("MSXML2.XMLHTTP"); 
    } catch (othermicrosoft){ 
      try { 
        xmlHttp = new ActiveXObject(
            "Microsoft.XMLHTTP"); 
      } catch (failed) {} 
    } 
  }    
 
  if (xmlHttp == undefined &amp;&amp; window.XMLHttpRequest) { 
    // If IE7, Mozilla, Safari, etc: Use native object 
    xmlHttp = new XMLHttpRequest(); 
  }  
 
  if (xmlHttp != undefined) { 
    // open the connections 
    xmlHttp.open("POST", url, true); 
    // callback handler 
    xmlHttp.onreadystatechange = function() { 
      // test if the response was totally sent 
      if (xmlHttp.readyState == 4 
            &amp;&amp; xmlHttp.status == 200) { 
        // so far so good 
        // do something useful with the response 
      } 
    }  
 
    // create the parameter string 
    // iterate the parameters array 
    var parameterString;  
 
    for (var i = 0; i &lt; n; i++) { 
      parameterString += (i &gt; 0 ? "&amp;" : "") 
          + parameters[i][0] + "="
          + encodeURI(parameters[i][1]); 
    }  
 
    // set the necessary request headers 
    xmlHttp.setRequestHeader("Content-type", 
        "application/x-www-form-urlencoded"); 
    xmlHttp.setRequestHeader("Content-length", 
        parameterString.length); 
    xmlHttp.setRequestHeader("Connection", "close");  
 
    // send request 
    xmlHttp.send(parameterString); 
  } 
}*/