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
        {
            if (xhr.status == 200)
            {
                document.getElementById(where).innerHTML=xhr.responseText;
                if (where=='le_tout')
                {
                    // si toute la page a été rechargée, il faut réarmer les boutons
                    $("div#dropdown-box").unbind('click');
                    $("div#dropdown-box").click( function (event){ switch_menu(event); });
                    $("button[class^='button-switch']").unbind('click');
                    $("button[class^='button-switch']").click( function (){ switch_perso(this.id); });
                }
            }
            else
            {
                alert('Statut xhr ' + xhr.status);
            }
        }
    }

    xhr.open('POST', what, true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send('ajax=1&' + str);
}
