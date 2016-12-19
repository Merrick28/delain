function retour()
{
var xhr; 
    try {  xhr = new ActiveXObject('Msxml2.XMLHTTP');   }
    catch (e) 
    {
        try {   xhr = new ActiveXObject('Microsoft.XMLHTTP');    }
        catch (e2) 
        {
          try {  xhr = new XMLHttpRequest();     }
          catch (e3) {  xhr = false;   }
        }
     }
 
    xhr.onreadystatechange  = function()
    { 
         if(xhr.readyState  == 4)
         {
              if(xhr.status  == 200) 
                 parent.gauche.document.getElementById("menu_dyn").innerHTML =xhr.responseText; 
              // mise en comment : on n'affiche rien si erreur sur le retour, pas d'actualisation
              //else 
              //   parent.gauche.document.getElementById("menu_dyn").innerHTML ="Error code " + xhr.status;
         }
    }; 
	
   xhr.open( "GET", "menu_dynamique.php",  true); 
   xhr.send(null); 
   //
   // on change maintenant le div ramasser
   //
   //xhr.onreadystatechange  = function()
   // { 
   //      if(xhr.readyState  == 4)
   //      {
   //           if(xhr.status  == 200) 
   //              parent.gauche.document.getElementById("ramasser").innerHTML =xhr.responseText; 
              // mise en comment : on n'affiche rien si erreur sur le retour, pas d'actualisation
              //else 
              //   parent.gauche.document.getElementById("menu_dyn").innerHTML ="Error code " + xhr.status;
    //     }
   //}; 
   //xhr.open( "GET", "ramasser_dyn.php",  true); 
   //xhr.send(null); 
}
