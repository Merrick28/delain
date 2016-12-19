// here we define global variable
var ajaxdestination="";

function getdata(what,where) { // get data from source (what)
 try {
   xmlhttp = window.XMLHttpRequest?new XMLHttpRequest():
  		new ActiveXObject("Microsoft.XMLHTTP");
 }
 catch (e) { /* do nothing */ }

 document.getElementById(where).innerHTML ="<div style=\"text-align:center;\">-- en cours de chargement --</div>";
// we are defining the destination DIV id, must be stored in global variable (ajaxdestination)
 ajaxdestination=where;
 xmlhttp.onreadystatechange = triggered; // when request finished, call the function to put result to destination DIV
 xmlhttp.open("GET", what);
 xmlhttp.send(null);
  return false;
}

function triggered() { // put data returned by requested URL to selected DIV
  if (xmlhttp.readyState == 4) if (xmlhttp.status == 200) 
    document.getElementById(ajaxdestination).innerHTML =xmlhttp.responseText;
// début ajout SD pour javascript 
    var e = document.getElementById(ajaxdestination);
    var scripts = e.getElementsByTagName('script');
		for(var i=0; i < scripts.length;i++)
		{
			if (window.execScript)
			{
				window.execScript(scripts[i].text.replace('<!--',''));
			}
			else
			{
				window.eval(scripts[i].text);
			}
		}
// fin ajout SD pour Javascript
}

function voirList(el,what,where) {  
var str = $("form").serialize();
   //alert(str);
var xhr;
try { xhr = new XMLHttpRequest(); }
catch (e) {
xhr = new ActiveXObject(Microsoft.XMLHTTP);
             }  
 xhr.onreadystatechange = function () {
if (xhr.readyState == 4)
  if (xhr.status == 200)
    document.getElementById(where).innerHTML=xhr.responseText;
        else
    alert('Statut xhr ' + xhr.status);
}

xhr.open('POST', what, true);
xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
 xhr.send('ajax=1&' + str);
      }

