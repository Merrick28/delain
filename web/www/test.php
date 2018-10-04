<?php


 $texte = "[3][5]Vous [5] abordez [1.1], après quelques échanges de courtoisie, il vous propose: [1.2] , blala[4]"; 
 echo "$texte <br>";
 preg_match_all ('#\[(.+)\]#isU', 
		$texte,
		$matches);
echo "<pre>"; print_r($matches); echo "</pre>"; 

print_r( count(explode(".", "11")) );
die();
?>


<html>
<head>

        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Les souterrains de Delain - Page de test définition écran</title>

</head>
<body>

<script type="text/javascript">
if (document.body)
{
    var larg = (document.body.clientWidth);
    var haut = (document.body.clientHeight);
}
else
{
    var larg = (window.innerWidth);
    var haut = (window.innerHeight);
}
document.write("Taille de l'écran: " + larg + "x "+haut+" pixels");

</script>

</body>
</html>