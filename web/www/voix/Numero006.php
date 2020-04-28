<?php 
include "counter.php";
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>La Voix des Souterrains - n&deg;6 - Novembre 2005</title>
    <link href="CSS/voix006.css" rel="stylesheet" type="text/css">
<?php 
  if ($_GET['skin'] == 'journal')
  {
     print "<link href=\"CSS/journal.css\" rel=\"stylesheet\" type=\"text/css\">";
  }
  else
  {
     print "<link href=\"CSS/delain.css\" rel=\"stylesheet\" type=\"text/css\">";
  }
?>
<script type="text/javascript">
<!--
//=======================================================
     //detect browser settings for showing and hiding DIVs
     isNS4 = (document.layers) ? true : false;
     isIE4 = (document.all && !document.getElementById) ? true : false;
     isIE5 = (document.all && document.getElementById) ? true : false;
     isNS6 = (!document.all && document.getElementById) ? true : false;
//=======================================================

function switchDiv(strDivName,bolVisible){
 //identify the element based on browser type
 if (isNS4) {
   objElement = document.layers[strDivName];
 } else if (isIE4) {
   objElement = document.all[strDivName].style;
 } else if (isIE5 || isNS6) {
   objElement = document.getElementById(strDivName).style;
 }
 
 if(isNS4){
     if(!bolVisible) {
       objElement.visibility ="hidden"
     } else {
       objElement.visibility ="visible"
     }    
 }else if(isIE4){
     if(!bolVisible) {
       objElement.visibility = "hidden";
     } else {
       objElement.visibility = "visible";
     }
 } else if (isIE5 || isNS6) {
      if(!bolVisible){
         objElement.display = "none";
      } else {
        objElement.display = "";
        }
      }
}
//-->
</SCRIPT>

</head>


<body class="fond1" onLoad="switchDiv('korgrim',false);
switchDiv('Titre',true);
switchDiv('Nyouzes',true);
switchDiv('PA_1',true);
switchDiv('PD_1',false);
switchDiv('Pubs',true);
switchDiv('Contact',true);
switchDiv('Pub_TR',true);
switchDiv('Interview1',true);
switchDiv('Interview2',true);
switchDiv('Edito',true);
switchDiv('Albert',true);
switchDiv('Courrier',true);">



<div class="fond">

<div class="titre" id="Titre">
<center>
 <table class="fond2" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td class="coin_hg"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"   ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche">&nbsp;</td>
  <td>
   <center>
    <a class="blanc" href="archives.htm">
   <font face="Cardinal" size="7" color="#FF0000">L</font><font face="Cardinal" size="6" color="#000000">a </font>
   <font face="Cardinal" size="7" color="#FF0000">V</font><font face="Cardinal" size="6" color="#000000">oix des </font>
   <font face="Cardinal" size="7" color="#FF0000">S</font><font face="Cardinal" size="6" color="#000000">outerrains</font>
   <!--   <img src="Titres/Titre3.jpg" class="titre_img">-->
	</a>
	</center>
   </td>
   <td class="ligne_droite">&nbsp;</td>
  </tr> 
  <tr>
   <td class="coin_bg"  ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"  ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
 </center>
</div>



<div class="pub_TR" id="Pub_TR">
 <table class="fond2" width="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">Partenaires</th>
 </tr>
 <tr>
  <td class="coin_hg"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"   ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
   <center>
		  <a href="pubs/Trolls_Royce-Silver_Goule.jpg" target="_blank" class="blanc">
		    <img src="pubs/Trolls_Royce-Silver_Goule.jpg" height="250px" width="250px">
		  </a>
	</center>
	</p><hr><p>
	- A la recherche d'un amour durable ? L'agence matrimoniale &quot;Elle, &eacute;moi&quot; ouvre ses portes. <br>
    &quot;Avant, les filles ne faisaient pas attention &agrave; moi. Trop petit disaient-elles. Je suis pass&eacute; &agrave; 
	l'agence &quot;Elle, &eacute;moi&quot; et depuis, j'ai rencontrer des tas de filles qui me courent apr&egrave;s. 
	Avec un hachoir, d'accord, mais elle me courent apr&egrave;s comme m&ecirc;me&quot;(Monsieur D. de Delain).
   </p></td>
   <td class="ligne_droite"><img src="img_archives/del.gif" height="8" width="10"></td>
  </tr> 
  <tr>
   <td class="coin_bg"  ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"  ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
</div>

<div class="InterviewBarde" id="InterviewBarde">
 <table class="fond2" width="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="17">Interview : LE BARDE.</th>
 </tr>
 <tr>
  <td class="coin_hg"                ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="15"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"                ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="trialcolonne" colspan="13"><p><em>
    Il &eacute;tait une fois un &eacute;talon noir dont les hennissement sauvages annon&ccedil;aient l'arriv&eacute;e du 
	fou hiss&eacute; sur son dos L'aventurier passionn&eacute;, amateur de vers et de femmes, &eacute;tait avant tout un 
	allum&eacute; assoiff&eacute; d'action. Le danger &eacute;tait sa bouff&eacute;e d'oxyg&egrave;ne. Il s'y ruait t&ecirc;te 
	baiss&eacute;e, l'&oelig;il p&eacute;tillant, le sourire carnassier et &eacute;panoui, provoquant la torpeur de ses ami, 
	affol&eacute;s par tant d'enthousiasme.<br>
    Bardamu l'ignorait encore, mais de leur rencontre na&icirc;trait une solide amiti&eacute;. 
  </em></p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="10" width="10"></td>
 </tr>
  <tr>
   <td class="coin_bg"               ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="15"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"               ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 <tr>
  <th colspan="11"><img src="img_archives/del.gif" height="8" width="10"></th>
  <th colspan="6">Interrog&eacute;s, voici ce que r&eacute;v&egrave;lent les amis du Barde :</th>
 </tr>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hdi"              ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes_haut"    ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hgi"              ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hdi"              ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes_haut"    ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hgi"              ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
    <strong>Bardamu</strong> : Blackrider ?<br>
    <strong>Blackrider</strong> : Bien le bonjour, ami chroniqueur <br>
    <strong>Bardamu</strong> : Enchant&eacute;... Descends de l&agrave;-dessus, et raconte-moi tout!<br>
    <strong>Blackrider</strong> : Je me nomme Tristan de Kent mais l'on me nomme Blackrider.<br>
    Pourquoi? Car je chevauche un cheval noir et suis &eacute;quip&eacute; d'une armure noire. 
    Mais &eacute;galement car en des temps recul&eacute;s, on me chassait dans toute ma contr&eacute;e.
    Je d&eacute;fiais en effet chaque seigneur et gr&acirc;ce a cette armure et &agrave; ma hache je les terrassais. 
    Je suis &eacute;galement de la race des hommes des t&eacute;n&egrave;bres, j'ai la peau sombre.<br>
    Cette armure &eacute;tait chaotique, vous l'aurez compris, et m'avait poss&eacute;d&eacute;.
    J'en ai tu&eacute; mes meilleurs amis de la fi&egrave;re escouade et la femme que j'aimais.
    Mon histoire est bien triste, mais un jour une n&eacute;cromancienne, Elaine Von Sheffren me lib&eacute;ra du d&eacute;mon 
	qui me poss&eacute;dait par l'armure.<br>
    <strong>Bardamu</strong> : Waow... Quelle histoire!! Comment as-tu atterri dans les profondeurs?<br>
    <strong>Blackrider</strong> : Apres avoir &eacute;t&eacute; secouru par Elaine, j'ai err&eacute; dans Anastasia avec mes 
	compagnons de route quand un jour nous avons franchi une porte, et nous sommes retrouv&eacute;s dans le monde de Delain, 
    v&ecirc;tus de simples pagnes et de nos seuls poings pour nous d&eacute;fendre.<br>
    Il y avait l&agrave; Cynthia mandragore, la guerri&egrave;re mage Farouche, Onde Fra&icirc;che la soigneuse, Gloin le 
	nain de roc, Narcam, Balin, Gilean le mage elfe clair, sans oublier Alaim ex-roi des Nordiques et bien d'autres. Ce que 
	nous ne savions pas c'est que Tristelune notre barde qui contait nos aventures avait eu un probl&egrave;me au passage du 
	portail et son &acirc;me s'&eacute;tait fondue dans chacun de nous. A peine le temps de retrouver mes amis que je 
	m'aper&ccedil;ois que nous parlions en rimes comme le faisait Tristelune.<br>
    <strong>Bardamu</strong> : Tu es plut&ocirc;t discret... Pourquoi?<br>
    <strong>Blackrider</strong> : J'ai &eacute;t&eacute; roi de Sheffren , le pays des humains des t&eacute;n&egrave;bres, dans ma 
	contr&eacute;e, et cela ne s'est pas bien pass&eacute;, je ne suis pas fait pour &ecirc;tre au devant de la sc&egrave;ne. 
	De plus les bardes essayent de rester neutres, tels des mercenaires, nous vendons nos chants de louanges ou de moquerie &agrave; 
	qui le veut bien, m&ecirc;me a des gens de la Main du Mal pourquoi pas. Si ces chants ne nous emp&ecirc;chent pas de respecter 
	nos alliances.<br>
    <strong>Bardamu</strong> : Les seuls chants que la Main risque de te commander, ce sont tes r&acirc;les au moment de ton tr&eacute;pas 
	qu'elle aura elle m&ecirc;me assur&eacute;!!<br>
    <strong>Blackrider</strong> : Je n'en attends pas moins d'eux, d'ailleurs Viviane peut vous dire que Puck et Sunfire chantent 
	tr&egrave;s mal avec une bille de fronde &agrave; la place des dents. (C'&eacute;tait il y a bien longtemps)<br>
    <strong>Bardamu</strong> : H&eacute;h&eacute;h&eacute;!! Bien... Qu'es-tu venu chercher dans les souterrains?<br>
    <strong>Blackrider</strong> : Avant tout les bardes cherchent &agrave; tuer Malkiar pour une seule et simple raison, pouvoir rentrer 
	chez eux. Car nous pensons que le portail a &eacute;t&eacute; g&eacute;n&eacute;r&eacute; par Malkiar lui m&ecirc;me. Nous 
	cherchons donc un portail semblable aux portails d&eacute;moniaques mais qui renverraient vers Anastasia.<br>
    En attendant nous visons les plus hautes marches du podium des tueurs de monstres, attention nous ne tuons pas des rats, 
	comme certains le font, nous nous attaquons aux monstres du Pays des Gel&eacute;es et du Masque de Mort, en attendant qu'un 
	jour nous puissions enfin descendre tous dans l'Antichambre.<br>
    <strong>Bardamu</strong> : mmm... Mais, avec qui voyages-tu?<br>
  </p></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes"><img src="img_archives/del.gif" class="del"></td>
  <td class="ligne_gauche_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
    <strong>Blackrider</strong> : Je voyage essentiellement avec mes fr&egrave;res bardes, toutefois j'ai eu le bonheur de combattre aux 
	c&ocirc;t&eacute;s des De Profundis plusieurs fois. J'ai &eacute;galement suivi pendant un moment les Aventuriers Aventureux, 
	et les Chevaliers de Justice ainsi que les Renards Mercenaires, et les Transfuges de Nordock. Nous avons particip&eacute; 
	&agrave; plusieurs guerres contre les forces du Mal.<br>
    J'ai aussi c&ocirc;toy&eacute; Kaali pendant un moment qui est une personne particuli&egrave;rement respectable.<br>
    <strong>Bardamu</strong> : Hein ?? <em>* regard ahuri *</em><br>
    Euh... Sinon... Tu es l'alli&eacute; du Bien... N'as-tu jamais &eacute;prouv&eacute; l'envie de basculer de l'autre 
	c&ocirc;t&eacute;?<br>
    <strong>Blackrider</strong> : Je suis l'alli&eacute; des gens qui servent le bien, mais les bardes sont plut&ocirc;t neutres. 
	Et j'ai des tendances assez noires du fait de mon pass&eacute;. Pour l'instant nos aventures dans les souterrains nous ont 
	oblig&eacute;s &agrave; nous allier contre des oppresseurs qui en voulaient &agrave; nos vies. Notre but est de tuer Malkiar 
	quiconque nous y aidera sera le bienvenu. Toutefois nous ne supportons pas les gens qui tuent d'autres aventuriers sans raison, 
	dans ce cas nous punissons le sang par le sang. Certains d'entre nous ont d&eacute;j&agrave; sombr&eacute; du c&ocirc;t&eacute; 
	obscur, je pense &agrave; la belle S&eacute;r&eacute;na, qui depuis a disparu dans les limbes.<br>
    <strong>Bardamu</strong> : Arffffffff... S&eacute;r&eacute;na... Quelle femelle!!!
    <em>* silence *</em><br>
    Reprenons : Sers-tu un Dieu ?<br>
    <strong>Blackrider</strong> : La plupart des bardes sont ath&eacute;es, pourquoi, me direz vous, et bien parce que nous avons 
	d&eacute;j&agrave; nos dieux dans notre monde, inutile d'essayer de nous convertir aux cultes de Delain. <br>
    <strong>Bardamu</strong> : Qui ne souhaites-tu pas rencontrer dans les profondeurs ? <br>
    <strong>Blackrider</strong> : Ingeloztamillizan, qui m'a d&eacute;j&agrave; mis KO, en fait ce sont des gel&eacute;es qui m'ont 
	termin&eacute;, car &agrave; l'&eacute;poque Ingeo n'&eacute;tait pas si puissant et je l'avais m&ecirc;me bless&eacute;. <br>
    <strong>Bardamu</strong> : mmm... Ingelo... <em>* air nostalgique *</em><br>
    Bien... As-tu un message &agrave; d&eacute;livrer?<br>
    <strong>Blackrider</strong> : Oui un message aux Nihilistes, Naine Blanche et son &eacute;quipe, qui ont tu&eacute; quatre bardes de 
	mon &eacute;quipe en se cachant dans un temple et en lan&ccedil;ant une bonne dizaine de boule de feu sur le groupe. <br>
    Si je vous recroise vous n'aurez pas le temps de vous mettre &agrave; l'abri cette fois <br>
    <strong>Bardamu</strong> : Dis Blackrider ? Tu veux pas nous composer un petit chant pour La Voix? <br>
    <strong>Blackrider</strong> : Oui, je vais vous improviser &ccedil;a a chaud, une pub pour La Voix<br>
	<br>
    <em>Aventuriers, aventuri&egrave;res, <br>
    La gazette va vous conter <br>
    L'histoire extraordinaire <br>
    Qui dans les souterrains s'est pass&eacute;e <br>
    N'h&eacute;sitez pas un instant <br>
    &Agrave; arr&ecirc;ter de briser des dents <br>
    Rangez vos runes sans retard <br>
    Car la gazette parle de Malkiar <br>
    De ses l&eacute;gions de Morbelins <br>
    De brouzoufs de runes et d'armes magiques, <br>
    De qu&ecirc;tes, de renomm&eacute;e et de destin, <br>
    Bref de fesses de pouvoir et de fric.</em><br>
	<br>
    <strong>Bardamu</strong> : <em>* large sourire *</em><br>
    Merci chevalier. Peut-&ecirc;tre nous croiserons nous bient&ocirc;t...<br>
    <strong>Blackrider</strong> : Ce sera avec un plaisir non dissimul&eacute;, noble chroniqueur.<br>
    <br>
    Depuis, le nain et l'agit&eacute; voyagent c&ocirc;te &agrave; c&ocirc;te.<br>
	Depuis, le nain ne conna&icirc;t plus la qui&eacute;tude...
   </p></td>
   <td class="ecartement"   ><img src="img_archives/del.gif" class="del"></td>
   <td class="ligne_droite_i" ><img src="img_archives/del.gif" class="del"></td>
   <td class="intercolonnes"><img src="img_archives/del.gif" class="del"></td>
   <td class="ligne_gauche_i" ><img src="img_archives/del.gif" class="del"></td>
   <td class="ecartement"   ><img src="img_archives/del.gif" class="del"></td>
   <td class="colonne"><p>
    <strong>Paladhe</strong>, humain d&eacute;muni d'intelligence et dont les &quot;paroles&quot; sont ici traduites par Celebfin :<br>
    &quot;En un mot, Paladhe a crach&eacute; par terre et ca a fait :<br>
    &quot;Splachhh !&quot;<br>
    &quot;C'est le signe d'un profond respect venant de Paladhe je crois... &quot;<br>
    </p><hr><p>
    <strong>Cynthia Mandragore</strong>, submerg&eacute;e par un &eacute;clat de rire :<br>
    &quot;C'est un coureur de jupons &agrave; toute &eacute;preuve ... <br>
    J'en ai fait les frais...<br>
    Il les lui faut toutes, Marina, Viviane, Cynthia, Iannah, j'en passe et des meilleures...&quot;<br>
    </p><hr><p>
    <strong>Kaali</strong>, le tueur rageur :<br>
    &quot; Ce que j'ai &agrave; dire du Barde?<br>
    Il vit aussi bien qu'il meurt et ce n'est pas une petite qualit&eacute;.<br>
    J'ai eu l'occasion de le tuer par deux fois au cours des guerres avec mon employeur de l'&eacute;poque (la MdM) et cela ne l'a pas emp&ecirc;cher de m'embaucher pour une exp&eacute;dition sans tra&icirc;trise et en restant honn&ecirc;te jusqu'au bout.<br>
    C'est tout ce que j'aurais &agrave; dire sur la place publique. &quot;<br>
    </p><hr><p>
    <strong>Grubas</strong>, dont l'app&eacute;tit runique ne conna&icirc;t pas de fin :<br>
    &quot; Un valeureux combattant....<br>
    Peut-&ecirc;tre trop...... &quot;<br>
    </p><hr><p>
    <strong>Balin saigne monstres</strong>, une authentique pipelette :<br>
    &quot; Heu... A part la destruction d'une tr&egrave;s belle &eacute;p&eacute;e (et tr&egrave;s ch&egrave;re) au combat, son avidit&eacute; de Runes et sa belle voix de Po&egrave;te... &quot;<br>
    </p><hr><p>
    <strong>Madmarsu...</strong> Non, en fait, c'est lui la pipelette!<br>
    &quot; H&eacute; bien... je dirais... non, je peux pas. On dit pas de mal de ses pairs ni de ses p&egrave;res, n'est-ce pas?<br>
    Enfin, du mal, ce serait beaucoup dire! Je sais qu'il court vite (VERS l'adversaire, hein, pas dans l'autre sens comme... non, je dirai pas de mal des assassins non plus, pas ce soir en tout cas ), qu'il a une connaissance approfondie de moult niveaux de ces souterrains (je pense qu'il n'y a que les &eacute;gouts o&ugrave; il ne soit pas all&eacute; en fait... Et encore, il y a peut-&ecirc;tre poursuivi de malfrats?), que son ex-femme, bien que r&ocirc;dant encore aupr&egrave;s de lui, le bat froid pour des histoires de donzelle dont l'&eacute;cho m&ecirc;me s'est &eacute;teint...<br>
    &quot;Enfin bref, c'est un bon chef : il ne nous emp&ecirc;che pas de travailler&quot;, comme dit l'autre <br>
    Ah! si: il a fait son autoportrait de sa banni&egrave;re tout seul (quoi de plus normal pour un autoportrait, me direz-vous!?)&quot;<br>
    </p><hr><p>
    <strong>Cynthia</strong>, qui revient &agrave; la charge :<br>
    &quot; ca il a pas peur d'aller au temple, c'est truff&eacute; de pr&ecirc;tresses aux petits soins pour lui... &quot;<br>
  </p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" class="del"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" class="del"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bdi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="intercolonnes_bas"    ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bgi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bdi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="intercolonnes_bas"    ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bgi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
</div>

<div class="Edito" id="Edito">
 <table class="fond2" width="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">EDITO</th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
    <strong>Les ateliers de &quot;La Voix&quot; ne sont plus. </strong>
	<br><br>
    Les fac&eacute;ties de divers monstres chahuteurs ont eu raison de 
	l'imprimerie o&ugrave; s&eacute;vissait Daysleeper. Le metteur en page qui souffre de d&eacute;pression depuis cet 
	&eacute;v&eacute;nement chaotique a du, avec l'aide de toute la r&eacute;daction, recopier &agrave; la main cette 
	sixi&egrave;me &eacute;dition, et ceci plus de 500 fois (nombre approximatif d'exemplaires &eacute;coul&eacute;s par 
	num&eacute;ro).
    <br><br>
    Cependant, Daysleeper ne peut s'en prendre qu'&agrave; lui. Ce sont les restes de son d&icirc;ner qui ont suscit&eacute; 
	l'affolement chez les bestioles. Attir&eacute;es en masse par l'odeur all&eacute;chante d'un repas vieux de plusieurs 
	jours, en total &eacute;tat de putr&eacute;faction, elles ont p&eacute;n&eacute;tr&eacute; les locaux et y ont tout 
	saccag&eacute;. Un troll sniffeur se serait m&ecirc;me d&eacute;couvert un fort penchant pour l'encre d'imprimerie. 
	Impossible d'ailleurs de d&eacute;loger l'importun qui, masse au poing s'agite dangereusement sous mes yeux au moment 
	m&ecirc;me o&ugrave; je r&eacute;dige ces quelques lignes.
    <br><br>
	Bref &hellip; Voici donc une gazette relook&eacute;e, et tr&egrave;s, hum, artisanale, qui vous donne des nouvelles du 
	Ch&acirc;teau, de la Moria, vous invite &agrave; d&eacute;couvrir Blackrider, Seigneur des Bardes d'Anastasia, sans 
	oublier les niouzes, et diverses autres surprises.
	<br><br>
    La r&eacute;daction, toute retourn&eacute;e.
   </p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="8" width="10"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
</div>

<div class="PA_1" id="PA_1">
 <table class="fond2" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="11">Petites Annonces</th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hdi"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes_haut"         ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hgi"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
    - Echange recette &quot;Omelette aux moignons&quot; contre recette &quot;Cui(ra)sses de grenouille&quot; ou &quot;Baba g&eacute;ant au rhum&quot;.
	<br><br>
    </p><hr><p>
	<br>
    - EMPLOI: pr&ecirc;tre cherche aventurier volontaire pour d&eacute;barrasser dispensaire de parasites.<br>
    </p></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_gauche_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
    - EMPLOI: nihiliste cherche pr&ecirc;tre volontaire pour d&eacute;barrasser dispensaire de parasites.<br>
    </p><hr><p>
    - Mages pyromanes exp&eacute;riment&eacute;s cherche centaines de volontaires pour exp&eacute;rience. Pri&egrave;re se r&eacute;unir dalle -2/-5/0. Bonne r&eacute;mun&eacute;ration pour survivants. Nains s'abstenir.<br>
    </p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="8" width="10"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bdi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="intercolonnes_bas"        ><img src="img_archives/del.gif" height="8" width="10"></td>
   <td class="coin_bgi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
</div>

<div class="ReportageChateauMorbelin" id="ReportageChateauMorbelin">
 <table class="fond2" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="11">Reportage : Le CH&Acirc;TEAU et les MORBELINS</th>
 </tr>

 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="9"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td width="500px" colspan="7"><p><em>
  Il existe dans les souterrains quantit&eacute; de monstres lib&eacute;r&eacute;s de l'autorit&eacute; de Malkiar. Parmi eux, maints Morbelins qui se r&eacute;partissent en deux tribus : les Fils d'Huruk et les Marcheurs, tous soumis &agrave; Notrak, Seigneur de la Nation Morbeline Libre.<br>
  D&eacute;sireux de trouver un havre de paix, les Fils d'Huruk, se sont empar&eacute;s du Ch&acirc;teau, situ&eacute; au sixi&egrave;me sous sol.
  Puis, les Marcheurs ont envoy&eacute; Nerlash n&eacute;gocier une alliance avec les Fils d'Huruk.
  Un pacte fut ainsi scell&eacute; entre les deux tribus.<br>
  Le Ch&acirc;teau, d&eacute;cid&eacute;ment tr&egrave;s convoit&eacute; par les Morbelins, avait d&eacute;j&agrave; connu quelques remous avec Arach. Le d&eacute;ment autoproclam&eacute; prince Morbelin avait en son temps pris le Ch&acirc;teau en otage, et seule la t&eacute;m&eacute;rit&eacute; de nombreux guerriers en &eacute;tait venue &agrave; bout.<br>
  Cependant, l'acc&egrave;s du Ch&acirc;teau reste interdit aux aventuriers, &agrave; moins que ceux ci ne se soumettent &agrave; une taxe s'&eacute;levant &agrave; 3 000 bfzs, qui a pour objectif de financer les travaux de restauration fort on&eacute;reux . <br>
  Cette situation provoque la r&eacute;volte des aventuriers, qui apr&egrave;s avoir entra&icirc;n&eacute; la chute d'Arach n'admettent pas que le Ch&acirc;teau leur reste clos.<br>
  A pr&eacute;sent, quelques questions &agrave; Mishra et Nerlash, qui viennent &agrave; peine de quitter le Ch&acirc;teau 
  </em></p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="9"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 <tr>
  <th colspan="11"><img src="img_archives/del.gif" class="del"></th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hdi"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes_haut"         ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="coin_hgi"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
  <strong>Bardamu</strong> : Mishra, pourquoi t'&ecirc;tre engag&eacute;e aux c&ocirc;t&eacute;s de Nerlash? A l'origine, il a quand 
  m&ecirc;me tu&eacute; ta peluche!<br>
  <strong>Mishra</strong> : Oui. Il est vrai que notre rencontre fut peu banale... Pour une fois j'ai pr&eacute;f&eacute;r&eacute; le 
  dialogue aux armes. Au d&eacute;part je lui ai simplement demand&eacute; de &quot;remplacer&quot; ma d&eacute;funte peluche. 
  Mais il s'est av&eacute;r&eacute; qu'au fur et &agrave; mesure de nos discutions nous avons sympathis&eacute;, et il a 
  chang&eacute; ma vision de la Nation Morbeline! C'est peu apr&egrave;s qu'il ma parl&eacute; de sa mission. Je ne pouvais 
  &agrave; ce moment me r&eacute;soudre &agrave; le laisser partir seul dans ces Souterrains hostiles. Alors c'est tout 
  naturellement que je lui ai propos&eacute; mon aide et que je l'ai pris sous mon aile!<br>
  <strong>Bardamu</strong> : Qui vous a accompagn&eacute;s jusqu'aux portes du Ch&acirc;teau?<br>
  <strong>Mishra</strong> : Mon groupe pour commencer: Chimar, Soryane, Ribellu, Magui-Pusta, HamtarO &amp; Cie... 
  Je tiens &agrave; les remercier tout particuli&egrave;rement, sans eux rien n'aurait &eacute;t&eacute; possible... blablabla... 
  Mais sur la route nous avons &eacute;galement rencontr&eacute; de nombreux aventuriers pr&ecirc;ts &agrave; nous aider! 
  Tu en as d'ailleurs fais parti Barda'! Belle promenade...<br>
  <strong>Bardamu</strong> : Elfette, c'est toujours un plaisir de te croiser... Nerlash, quel message as-tu d&eacute;livr&eacute; 
  &agrave; Notrak?? Hum... Pas de langue de bois!<br>
  <strong>Nerlash</strong> : Che croich que che peuch en dire pluch maintenanch ! Che crois aucchi qu'il est bonch que les aventuriech 
  en chassent pluch au chujet des morbelinch. Che chuis un membre de la tribuch des Marcheurs de Vofur et ch'apportais cha 
  promecche d'allianche &agrave; Notrak, chef de la tribuch des Fichs d'Uruk, qui tiench le Ch&acirc;teau et diriche la 
  Nachion morbeline libre. Nous chommes r&eacute;cholus &agrave; combattre Malkiarch et &agrave; d&eacute;fendre notre droit 
  d'exchister en tant que Nachion chouveraine et libre. <br>
  <strong>Bardamu</strong> : La rumeur dit que Notrak serait dispos&eacute; &agrave; vous 
  pr&ecirc;ter une escorte si l'envie vous titille de descendre en Moria... Vrai?<br>
  <strong>Nerlash</strong> : Ouich ch'est exact! Notrak ne demande pas mieux que de voirch la Moria acchainie des horreurs qui la 
  h&acirc;nte. D'autant qu'il affirme que des choses bien pires che terrent par decchous!
</p></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes"><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ligne_gauche_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
  <strong>Mishra</strong> : En effet. Il est vrai qu'il serait pr&ecirc;t &agrave; nous fournir une escorte de ses meilleurs guerriers, 
  si nous d&eacute;cidions d'aller faire un tour en Moria.<br>
  <strong>Bardamu</strong> : Pas mal d'aventuriers s'irritent de se voir refuser l'acc&egrave;s au Ch&acirc;teau... Notrak ne craint-il 
  pas un assaut de la part des &eacute;nerv&eacute;s??<br>
  <strong>Nerlash</strong> : Chi bienchur ! Il y a d&eacute;ch&agrave; euch des acchauts et les Fichs d'Uruk ont duch ripochter mais ils 
  n'aiment pas cha ! Quoi qu'il en choit, Moklar, lieutenant de Notrak ne refuche chamais de parlementer. Chi nous chommes 
  conchid&eacute;r&eacute;s comme nous chouhaitons l'&ecirc;tre nous chommes des interlocuteurs paichibles et raichonn&eacute;s!<br>
  <strong>Mishra</strong> : &Agrave; vrai dire, je les comprends un peu...<br>
  Seulement la restauration d'un Ch&acirc;teau, c'est pas donn&eacute;! Il faut bien trouv&eacute; une source de revenu pour 
  financer tout &ccedil;a.<br>
  Puis, ce serait bien dur pour les assaillants... les troupes Morbelines sont efficaces et bien organis&eacute;s!<br>
  Et je pense que Notrak s'est pr&eacute;par&eacute; &agrave; toutes &eacute;ventualit&eacute;s.<br>
  Enfin ce n'est que mon avis.<br>
  <strong>Bardamu</strong> : Existe t il une autre solution pour des aventuriers peu fortun&eacute;s qui souhaiteraient se rendre en Moria??<br>
  <strong>Nerlash</strong> : Che crois avoir r&eacute;pondu &agrave; chette quechtion, faichons parler notre raichon et mettons nos lames 
  au fourreau!<br>
  <strong>Bardamu</strong> : Nerlash...Pourquoi ne pas &ecirc;tre rest&eacute; aupr&egrave;s des tiens?<br>
  <strong>Nerlash</strong> : Les miench chont les Marcheurs et chont dich&eacute;min&eacute;s en petit nombre au Paych des Gel&eacute;es! 
  Je chuis remont&eacute; avec Michra car j'appr&eacute;chie cha compagnie et souhaite l'&eacute;pauler au mieuch!<br>
  <strong>Bardamu</strong> : Quels sont vos desseins &agrave; pr&eacute;sent?<br>
  <strong>Nerlash</strong> : Che chouhaite combattre Malchiar aux cot&egrave;ch des Dawalandaich!<br>
  <strong>Mishra</strong> : Hum... Rien de tr&egrave;s concret! Que des affaires personnelles &agrave; r&eacute;gler. Rien de bien 
  int&eacute;ressant en somme. Mais ne t'inqui&egrave;te pas, si nous devions &agrave; nouveau accomplir des &quot; 
  exploits &quot; tu en serais inform&eacute;!<br>
  <br>
  Le Ch&acirc;teau restera-t-il entre les mains morbelines? Suite au prochain num&eacute;ro.
   </p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="8" width="10"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bdi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="intercolonnes_bas"        ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bgi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
</div>

<div class="Niouzes" id="Niouzes">
 <table class="fond2" width="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="11">Les Niouzes des Souterrains</th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hdi"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes_haut"        ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="coin_hgi"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p><font class="txt">
    <strong>C'est la GUERRE !!! :</strong><br>
    Tout se passe au Masque de Mort. <br>
    Alors que les tueurs, lames aiguis&eacute;es au poing, r&ocirc;daient pr&egrave;s des escaliers montants,
	afin d'accueillir au troisi&egrave;me sous sol les jeunes aventuriers comme il se doit, 
	les Sillonneurs Gris, soutenus par divers alli&eacute;s du Bien (United Colors of Delain, Atomiques, Akatsuki, 
	Consortium minier Nain...) lanc&egrave;rent l'assaut contre les M&eacute;nestrels de la Lune d'Argent, 
	les Guerriers du Clair Obscur, le Group Massiv Annihilation, les Apprentis de l'Apocalypse.<br>
    Plusieurs tr&eacute;pas sont &agrave; d&eacute;plorer de part et d'autres... Enfin, surtout du c&ocirc;t&eacute; 
	des m&eacute;chants! Un massacre!<br>
    A noter : Tupac est pri&eacute; de rendre son banjo &agrave; Twiibliip, tr&egrave;s affect&eacute; par la perte de l'instrument.<br>
    &Ccedil;a vient de tomber : Les Sillonneurs Gris de nouveau en guerre contre les Nihilistes. Naine Blanche 
    c&eacute;dera-t-elle enfin aux avances de FIX??
    </font><hr><font class="txt">
    <br><strong>Le Prince des Vampires de Malkiar !</strong><br>
    Une exp&eacute;rience &quot;traumatisante&quot; &agrave; n'en pas douter, que la lutte difficile contre le Comte Dracula, 
    Vampire de son &eacute;tat. Un v&eacute;ritable monstre, donnant des sueurs froides &agrave; plus d'un! Mais, gr&acirc;ce 
    &agrave; la vaillance (ou &agrave; la t&eacute;m&eacute;rit&eacute;) de certains aventuriers, cette menace d'Halloween n'est 
    plus : en effet, un des guerriers pr&eacute;sents lors de l'ach&egrave;vement du monstre nous raconte que &quot;Une fois en 
    tas de cendre, il ne fait vraiment plus peur &agrave; personne...&quot; et avec un grand sourire rajoute &quot;Et c'est pas 
    Galarion qui dira le contraire, vu qu'il est juste arriv&eacute; pour l'achever!&quot;
    </font><hr><font class="txt">
    <br><strong>Histoires d'oeufs :</strong><br>
    Les aventuriers qui courent aux &eacute;tages sup&eacute;rieurs rapportent qu'en mourant, les basilics communs laissent tomber 
    des oeufs. Que ceux ci peuvent &ecirc;tre couv&eacute;s, et que lorsque l'oeuf &eacute;clos enfin apr&egrave;s tant de 
    g&acirc;terie, il en sort une surprise... A vous de tester!
    </font><hr><font class="txt">
    <br><strong>La revanche:</strong><br>
    Les Plopeurs Fous, r&eacute;cemment tomb&eacute;s sous les frappes des Vampires, r&eacute;agissent! Respectant jusqu'ici une 
    totale neutralit&eacute;, ils viennent de chahuter les Nihilistes. A qui le tour?
    </font><hr><font class="txt">
    <br><strong>Collection d'hiver:</strong><br>
    Ses courtisans se d&eacute;sesp&egrave;rent...<br>
    NaineBlanche, la fr&eacute;tillante et tr&egrave;s sexy Nihiliste c&egrave;de au froid hivernal et arbore d&eacute;sormais 
	une tenue de camouflage int&eacute;gral.<br>
    A ses soupirants &eacute;treints par l'&eacute;motion, elle r&eacute;torque :<br> 
	&quot;Ainsi, vous serez plus concentr&eacute;s sur le combat! Mouhahahaha...&quot;
    </font><hr><font class="txt">
	<br>
    <strong>Glups : </strong><br>
    Hum... Le Ch&acirc;teau, investi par les Morbelins &eacute;veille les convoitises. Certains aventuriers d&eacute;sireux 
	de se rendre en Moria, s'insurgent contre la taxe impos&eacute;e par les actuels occupants, et seraient tent&eacute;s de 
	lancer un assaut visant &agrave; lib&eacute;rer les lieux. L'un d'entre eux aurait m&ecirc;me propos&eacute; :<br>
	&quot;Je pencherai pour leur demander 3 000 Bzf par Morbelin qui veut rester en vie avant qu'on ne rentre... &quot;
    </font></p></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes"><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ligne_gauche_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p><font class="txt">
    <strong>Avis aux Affranchis :</strong><br>
    Le R&eacute;dempteur, vieillard acari&acirc;tre et amateur de clochettes dans lesquelles il emprisonne ses proies 
	&eacute;tendrait son champ d'action. Ob&eacute;issant aux exigences de l'Innommable, il ne menacerait plus seulement 
	les monstres &eacute;mancip&eacute;s, mais aussi leurs alli&eacute;s. Aventuriers, restez sur vos gardes, si la F&eacute;e 
	Clochette appara&icirc;t c'est que le ch&acirc;timent est proche!
    </font><hr><font class="txt">
    <strong>Belph&eacute;gor :</strong><br>
    L'apparition de cet esprit damn&eacute; a d'abord suscit&eacute; l 'effroi chez les aventuriers qui lui faisaient face 
	(Ep&eacute;es du Vent, la 7e Compagnie, les Mythes Guerriers, les Redout&eacute;s, les Trisk&egrave;les d'Argent, quelques 
	anciens Vikings, Renards et Aventuriers Aventureux). Puis, apr&egrave;s un assaut joliment coordonn&eacute;, Belphegor est 
	tomb&eacute;, Gorku des Trisk&egrave;les d'Argent lui ayant ass&eacute;n&eacute; le coup fatal.
    </font><hr><font class="txt">
    <strong>La Moria s'anime : </strong><br>
    Apr&egrave;s un an de qu&ecirc;tes qui les ont men&eacute;s d'un bout &agrave; l'autre des profondeurs, les Plopeurs Fous 
	tentent &agrave; nouveau d'affronter le Balrog. Les aventuriers se heurtent &agrave; une arm&eacute;e d'El&eacute;mentaires 
	(incandescents, majeurs de feu, enflamm&eacute;s, flamboyants, tous d&eacute;crits comme &quot;Lieutenants de la flamme&quot;), 
	ainsi qu'au Gardien de la Flamme, fant&ocirc;me puissant et mal&eacute;fique, peut-&ecirc;tre l'invocateur des 
	&eacute;l&eacute;mentaires. Le fouet du Balrog quant &agrave; lui n'a encore lac&eacute;r&eacute; personne.<br>
    Ah... Ah... Info de derni&egrave;re minute : le Balrog a remu&eacute;, et de nombreux tr&eacute;pas sont maintenant 
	&agrave; d&eacute;plorer...
    </font><hr><font class="txt">
    <strong>Les Taupes :</strong><br>
    Une alliance r&eacute;unissant plusieurs guildes s&eacute;virait au premier sous sol. C'est sous le nom de code : &quot; Taupe &quot; que les membres se reconna&icirc;traient entre eux. Leur but ? S'approprier l'&eacute;tage selon les dires des aventuriers qui les croisent. A noter que les Taupes ne semblent pas servir le Bien. Aux amateurs : des recrutements sauvages sont organis&eacute;s dans les &eacute;tages sup&eacute;rieurs.
    </font><hr><font class="txt">
    <strong>Du neuf !</strong><br>
    Les aventuriers qui courent dans l'Antichambre en sont encore tout retourn&eacute;s. Les monstres semblent en effet avoir suivi des stages intensifs de baffages, et sont plus enrag&eacute;s que jamais. Equip&eacute;s de nouvelles armes, ma&icirc;trisant de nouveaux sorts, ils s'agglutinent dans les moindres coins et recoins de l'&eacute;tage et provoquent l'affolement des guerriers, oblig&eacute;s d'&eacute;laborer de fines strat&eacute;gies pour en venir &agrave; bout. A d&eacute;couvrir absolument !<br>
    </font><hr><font class="txt">
    <strong>L'Affreux et les sorci&egrave;res :</strong><br>
    Il s'appelle Frankenstein, et il est &quot; Affreux, il est vraiment affreux et puis cette odeur f&eacute;tide... brrrr ignoble &quot;. Pourtant, les Sorci&egrave;res ne cessent plus de s'entasser &agrave; ses c&ocirc;t&eacute;s, l'aidant de leurs sorts, ou autre malices. Ecoutez plut&ocirc;t : victime d'un enchev&ecirc;trement, Frankestein a profit&eacute; d'une botte secr&egrave;te lanc&eacute;e par une de ses admiratrices. Ou encore : une autre sorci&egrave;re, tr&egrave;s joueuse, a lanc&eacute; un &quot; l&acirc;che &ccedil;a &quot; sur Hest, le d&eacute;lestant ainsi de son arme qu'elle a ensuite ramass&eacute;, privant d&eacute;finitivement l'Ourson de son jouet. <br>
    Si le jeu vous tente, rejoignez les Ma&icirc;tres Cartographes, et les Oursons, ainsi que Turin Turambar au sixi&egrave;me sous sol. Frankestein et ses sorci&egrave;res assurent le divertissement.
    </font></p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="8" width="10"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bdi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="intercolonnes_bas"        ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bgi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
</div>

<div class="PlacePublique" id="PlacePublique">
 <table class="fond2" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">Ce qui se dit en place publique :</th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p><font class="txt">
    Sorte de march&eacute;s aux cris et chuchotements, la place publique accueille des aventuriers de tout bord. Chacun s'y exprime dans la plus grande libert&eacute;. C'est l&agrave; que sont r&eacute;v&eacute;l&eacute;s les &eacute;v&eacute;nements qui secouent les bas fonds, les rumeurs qui se diffusent d'oreilles en bouches, c'est aussi l&agrave; que les d&eacute;bats font rage.<br>
    Ecoutons :<br>
    <strong>thillie</strong>, jeune humaine, fait son entr&eacute;e dans les souterrains. Guid&eacute;e par Apiera qui l'a investie d'une mission dont l'aboutissement semble ne pouvoir se r&eacute;aliser que dans les profondeurs, l'innocente humaine avance, observatrice et curieuse.
<br><br>
    <br><strong>R&eacute;gine</strong>, la Harpie lib&eacute;r&eacute;e du joug de Malkiar a cess&eacute; sa course folle aux c&ocirc;t&eacute;s des Ep&eacute;es du vent, dont elle a perdu la trace apr&egrave;s un repos m&eacute;rit&eacute;.
<br><br>
    <br><strong>Wengor</strong>, ambassadeur de l'Ange Noir recrute les archers et arbal&eacute;triers les plus habiles afin de former une arm&eacute;e d'&eacute;lite qu'il destine au &quot; mercenariat &quot;.
<br><br>
    <br>Divers objets jonchant le sol des bas fonds suscitent curiosit&eacute; et convoitise. Parmi eux, les boules de cristal d'arc. Il en existerait huit, compos&eacute;e chacune d'&eacute;toiles (Une &agrave; une &eacute;toile, une &agrave; deux &eacute;toiles...etc, jusqu'&agrave; huit) et auraient &eacute;t&eacute; sem&eacute;es par le lutin Mirreck. Un autre objet nomm&eacute; &quot; Gr&acirc;ce d'Apiera &quot;, cueilli par un Renard Mercenaire &eacute;veille le m&ecirc;me l'int&eacute;r&ecirc;t. Cependant, l'utilit&eacute; de ces trouvailles semble encore &eacute;chapper aux aventuriers qui les d&eacute;tiennent.
<br><br>
    <br><strong>Paladhe</strong>, aventurier &agrave; l'intelligence atrophi&eacute;e, chercherait le &quot; Robin des Bois des Souterrains &quot; (sa nouvelle id&eacute;e fixe), et pour cela, propose d'organiser un concours qui permettrait aux divers pr&eacute;tendants de se mesurer.
<br><br>
    <br><strong>Duron</strong>, nain chaud, dispense des cours d'approche et de s&eacute;duction. Femelles des profondeurs, prenez garde et &eacute;vitez les tenues rouges qui provoquent les transes du gourmand... Rrrrouarrrrrrr...
	</font></p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="8" width="10"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
</div>

<div class="Blagues" id="Blagues">
 <table class="fond2" height="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">Blagues</th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" class="del"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" class="del"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" class="del"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p><font class="txt">
    <strong>Histoire communautaire !</strong>
    <br>- Voil&agrave; Elendil l'elfe et Drugar le nain qui d&eacute;couvrent une lanterne magique!
      Ils la frottent fr&eacute;n&eacute;tiquement tous les deux et &hellip; en sort comme pr&eacute;vu le G&eacute;nie.
      De suite le G&eacute;nie les informe qu'ils ont droit chacun &agrave; un et un seul v&oelig;u...
    <br>Elendil l'elfe commence :
    <br>  * Moi, je veux qu'un &eacute;norme mur entoure la for&ecirc;t de Lorien. Je le souhaite vraiment solide et r&eacute;sistant afin de maintenir les autres races &agrave; l'ext&eacute;rieur et ainsi pouvoir consacrer nos vies au chant et &agrave; la po&eacute;sie sans craindre nos barbares voisins.
    <br>Le G&eacute;nie :
    <br>  * Voil&agrave; qui est fait !!! A toi Drugar, je t'&eacute;coute...
    <br>Drugar le nain r&eacute;pondit :
    <br>  * Dis moi, mon gars, es-tu vraiment certain que l'mur de l'autre est vraiment solide, r&eacute;sistant, &eacute;tanche hein?
    <br>* Bien s&ucirc;r.
	<br>* OK ! Remplis-le d'eau alors !!!<br>
    </font><hr><font class="txt">
    <strong>Histoire de nain :</strong>
    <br>- Alors tu vois, c'est un nain qui a 17 enfants...
    <br>  Elle est courte mais elle est bonne.
    </font></p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="8" width="10"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
</div>

<div class="Albert" id="Albert">
 <table class="fond2" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="11">Albert</th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hdi"              ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes_haut"    ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hgi"              ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
    <strong>3 - Heureux qui comme supplice...</strong>
    <br> - Raconte-moi encore comment t'en es arriv&eacute; l&agrave;, ...
    <br>Albert avait horreur de ce cocher grossier et malodorant qui ne pouvait s'emp&ecirc;cher de pouffer de rire &agrave; 
	chaque fois qu'il lui contait ses m&eacute;saventures. Bien que bien conscient de sa sup&eacute;riorit&eacute; intellectuelle, 
	Albert &eacute;tait fortement intimid&eacute; par son compagnon de route dont les bras avaient la taille de ses propres cuisses.
    <br> - Je vous l'ai d&eacute;j&agrave; racont&eacute; 5 fois, ...
    <br> - Pas grav'. J'aime les bonnes histoires.
    <br>Albert soupira et n'osant rien refuser &agrave; cet homme qui lui &eacute;tait n&eacute;cessaire, il reprit :
    <br> - En r&eacute;sum&eacute;, le roi Salmor'v a d&eacute;cid&eacute; d'envoyer un homme de confiance, ...
    <br>Le cocher tenta vainement de ne pas laisser para&icirc;tre son hilarit&eacute;.
    <br> - Un homme de confiance, donc, pour 
	</p></td>
  <td class="ecartement"    ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite_i"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_gauche_i"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"    ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
    aller collecter les imp&ocirc;ts dans les souterrains qui bordent son royaume.
    <br>Un rire &eacute;norme s'&eacute;chappa de la gorge du cocher (qui ne l'&eacute;tait pas moins). Albert en fut tout 
	courrouc&eacute; et c'est en col&egrave;re qu'il ajouta :
    <br> - Vous n'avez pas id&eacute;e de l'argent qui s'&eacute;change dans les souterrains. Des milliers d'aventuriers y 
	vont, des guildes marchandes s'y &eacute;tablissent, ... on y aurait m&ecirc;me trouv&eacute; de l'or, ... de l'or.
    <br>Le cocher s'essuya une larme d'all&eacute;gresse et passa quelques secondes &agrave; tenter de se contr&ocirc;ler. 
	Les yeux pleins de malice, il se tourna vers Albert et lui dit :
    <br> - Albert, t'aimes les surprises ?
    <br> - Euh, ... &agrave; ma f&ecirc;te, oui, pourquoi ?
    <br> - Alors t'adoreras les souterrains. Ce sera ta f&ecirc;te tous les jours.
    </p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="8" width="10"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bdi"             ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="intercolonnes_bas"     ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_bgi"              ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_bd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  </tr>
 </table>
</div>

<div class="DerniereMinute" id="DerniereMinute">
 <table class="fond2" height="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">DERNIERE MINUTE</th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="dualcolonne"><p>
    Le Balrog, apr&egrave;s avoir d&eacute;fonc&eacute; le plafond du 7&egrave;me sous sol, &eacute;tage o&ugrave; 
	il se divertissait avec les Plopeurs, court maintenant autour du Ch&acirc;teau, babines retrouss&eacute;es et 
	&eacute;cumantes, d&eacute;daignant magistralement les aventuriers pr&eacute;sents qui r&acirc;lent...
	Qui r&acirc;&acirc;&acirc;&acirc;&acirc;lent...
   </p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="8" width="10"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
</div>

<div class="ServerMaster" id="ServerMaster">
 <table class="fond2" height="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">EVENNEMENT</th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="quadricolonne"><p>
<strong>Quand un fou se heurte &agrave; un autre fou :</strong><br>
    Alors qu'un furieux , massue en main, avait p&eacute;n&eacute;tr&eacute; les profondeurs, d&eacute;truisant rageusement chaque mur, chaque &eacute;difice sur son passage, Servermaster a remis chaque pierre &agrave; sa place, et a ainsi sauv&eacute; les souterrains de la ruine. Cette chasse aura dur&eacute; deux jours complets. Saluons l'acharnement de Servermaster, insomniaque et affam&eacute; depuis l'&eacute;v&eacute;nement.
    </p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="8" width="10"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
</div>




</div>

</body>
</html>
