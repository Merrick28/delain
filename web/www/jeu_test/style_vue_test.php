
<?php 
// penser à virer le include
include "verif_connexion.php";
$etage = $type;
?>

<?php 

for ($cpt=1;$cpt<10;$cpt++)
{
	echo "td.v$cpt{\r\n";
	echo "background:url('http://www.jdr-delain.net/test_img/f_" , $etage, "_" , $cpt, ".png');\r\n";
	echo "width:28px;";
	echo "height:28px;";
	echo "border : 0px;";
	echo "}\r\n";	
}
for ($cpt=1;$cpt<18;$cpt++)
{
	echo ".lieu$cpt{\r\n";
	echo "background:url('http://www.jdr-delain.net/test_img/t_" , $cpt, "_lie.png');\r\n";
	echo "width:28px;";
	echo "height:28px;";
	echo "border : 0px;";
	echo "}\r\n";	
}
echo ".joueur{\r\n";
echo "background-image:url('http://www.jdr-delain.net/test_img/t_" , $etage, "_per.png');\r\n";
echo "}\r\n";	
echo ".monstre{\r\n";
echo "background-image:url('http://www.jdr-delain.net/test_img/t_" , $etage, "_enn.png');\r\n";
echo "}\r\n";	
echo ".objet{\r\n";
echo "background-image:url('http://www.jdr-delain.net/test_img/t_" , $etage, "_obj.png');\r\n";
echo "}\r\n";	
for ($cpt=990;$cpt<1000;$cpt++)
{
	echo ".mur_$cpt{\r\n";
	echo "background:url('http://www.jdr-delain.net/test_img/t_" , $etage, "_mur_" , $cpt, ".png');\r\n";
	echo "width:28px;";
	echo "height:28px;";
	echo "border : 0px;";
	echo "visibility : visible;";
	echo "}\r\n";	
}
?>
.oncase{
	background:url('http://www.jdr-delain.net/test_img/sur_case.gif');
}
.vu{
	visibility:visible;
}
.pasvu{
	visibility:hidden;
}

