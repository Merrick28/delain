var currentCaseId = 0;
function show_case(position)
{
if( position == currentCaseId){
	var xhr_object = null;

	if(window.XMLHttpRequest) // Firefox
	   xhr_object = new XMLHttpRequest();
	else if(window.ActiveXObject) // Internet Explorer
	   xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
	else { // XMLHttpRequest non support� par le navigateur
	   alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
	   return;
	}
	var method   = "GET";
	var filename = "det_vue.php";
	filename += "?position="+position;
	var data     = null;
	xhr_object.open(method, filename, true);
	
	xhr_object.onreadystatechange = function() {
	   if(xhr_object.readyState == 4) {
      	var tmp = xhr_object.responseText;
      	AffBulle(tmp);
   	}
	}
	
	if(method == "POST")
	   xhr_object.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	
	xhr_object.send(data);
	}
}