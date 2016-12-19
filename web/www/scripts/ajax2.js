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
