xmlhttp=null;

if (window.XMLHttpRequest)
{
	// code for Firefox, Opera, IE7, etc.
	xmlhttp=new XMLHttpRequest();
}
else if (window.ActiveXObject)
{
	// code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}

function ajaxresult(url)
{
  if (xmlhttp!=null)
    {
      xmlhttp.onreadystatechange=state_change;
      xmlhttp.open("GET",url,true);
      xmlhttp.send(null);
    }
  else
    {
      alert("Your browser does not support XMLHTTP.");
    }
}

function state_change()
{
  if (xmlhttp.readyState==4 || xmlhttp.readyState == "complete")
  {
    // 4 = "loaded"
    if (xmlhttp.status==200)
    {
	// 200 = "OK"
	setit(new String(xmlhttp.responseText));
    }
    else
    {
	xmlhttp.abort();
    }
  }
}

function setit(stringer)
{
	ajaxres = stringer;
	ajaxaction();
}