<?php 
include "counter.php";
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>La Voix des Souterrains - n&deg;7 - Janvier 2006</title>
    <link href="CSS/voix007.css" rel="stylesheet" type="text/css">
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


<body class="fond1" onLoad="switchDiv('Guildes',true);switchDiv('Croustillant',false);">



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

<div class="InterviewMalkiar" id="InterviewMalkiar">
 <table class="fond2" width="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="11">Interview : MALKIAR</th>
 </tr>
 <tr>
  <td class="coin_hg"                ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="9"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"                ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="dualcolonne" colspan="7"><p><em>
    Deux D&eacute;mons se pos&egrave;rent devant Bardamu, &eacute;ructant quelques grognements barbares, avant de saisir 
    leur victime par la barbe. Ils le tra&icirc;n&egrave;rent, les yeux band&eacute;s, jusqu&rsquo;&agrave; Son antre. 
    L&agrave;, les deux brutes aboy&egrave;rent de nouveau et jet&egrave;rent le nain &agrave; Ses pieds&hellip; La 
    Cr&eacute;ature &eacute;mit un &eacute;clat de rire si tonitruant que les traits du journaleux se fig&egrave;rent en 
    une grimace de douleur. Puis, le silence. Bardamu &ocirc;ta le bandeau qui entourait sa t&ecirc;te, mais ne 
    per&ccedil;ut que l&rsquo;obscurit&eacute;&hellip; Profonde, glaciale, p&eacute;n&eacute;trante&hellip; Il frissonna, 
    n&rsquo;osant plus remuer ne serait ce que le petit doigt. Il ne sentait que Son souffle l&rsquo;envelopper tout 
    entier&hellip; Si puissant que le nain vacillait &agrave; chacune de Ses respirations&hellip;
  </em></p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="10" width="10"></td>
 </tr>
  <tr>
   <td class="coin_bg"               ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="9"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"               ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 <tr>
  <th colspan="11"><img src="img_archives/del.gif" height="8" width="10"></th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hdi"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes_haut"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hgi"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
    <strong>Bardamu</strong> : Hum&hellip; Qui est ce?<br>
    <strong>Grosse Voix</strong> : Je suis le ma&icirc;tre de ces souterrains, et tu devrais le savoir! Qui es-tu insignifiante 
    petite chose pour te permettre de t&rsquo;avancer devant Ma magnificence! Je t&rsquo;&eacute;craserais comme 
    un cloporte que tu es! Tout le monde Me craint, et les larves rampent devant Ma grandeur.<br>
    <strong>Bardamu</strong> : Ah? Malkiar??<br>
	<br>
    <em>Bien qu&rsquo;il ait sollicit&eacute; cette interview, la d&eacute;glutition du nain se fit &acirc;pre et 
    douloureuse. Il ne s&rsquo;attendait pas &agrave; ce que Malkiar se manifeste un jour&hellip; Pas CE jour&hellip;</em><br>
	<br>
    Waow&hellip; Ok, attaquons: parmi Tes rejetons, quels sont ceux qui ont ta pr&eacute;f&eacute;rence?<br>
    <strong>Malkiar</strong> : Evidemment les d&eacute;mons sont Mes troupes les plus fid&egrave;les. Les morbelins ne sont que 
    des rejets, des rebuts d'humains! Mes arm&eacute;es d&rsquo;Orks envahiront ce monde et le mettront &agrave; feu 
    et &agrave; sang! Mais surtout, vous n'&ecirc;tes que peu de choses devant Moi, et vous ne connaissez rien de 
    Mon pouvoir. Tremblez car Mes l&eacute;gions les plus sauvages se d&eacute;verseront sur vos terres, des troupes 
    qui se constituent jour apr&egrave;s jour, et qui prendront vos rejetons comme nourriture.<br>
    <strong>Bardamu</strong> : Nourriture?? <br>
    Bref&hellip;Nerlash a pr&eacute;cis&eacute; qu&rsquo;il te &laquo; picherai dechus &raquo; si vos chemins 
    se croisaient&hellip; Une r&eacute;action?<br>
    <strong>Malkiar</strong> : Vos questions stupides risquent de vous co&ucirc;ter tr&egrave;s cher. Vous n'&ecirc;tes 
    ici que parce que Je le veux bien!<br>
    <strong>Bardamu</strong> : Hum&hellip; Oui&hellip; Encha&icirc;nons: R&eacute;gine la Harpie, quant &agrave; elle, 
    plut&ocirc;t que de tuer, berce les guerriers de ses chants m&eacute;lodieux&hellip; Dis? C&rsquo;est un peu le 
    bronx dans tes rangs, non?<br>
    <strong>Malkiar</strong> :  Ma patience a des limites que vous &ecirc;tes en train de franchir! Parlez plut&ocirc;t de 
    Mes fid&egrave;les lieutenants qui mettent en place les conditions de Mon grand retour. De vos semblables qui 
    font dans leurs chausses d&egrave;s que Mon fid&egrave;le Ing&eacute;lo se pr&eacute;sente.<br>
    </p></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_gauche_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
    <strong>Bardamu</strong> : Ah oui! Ingelo&hellip; Celui qui s&rsquo;amuse &agrave; r&eacute;aliser des colliers avec les 
    t&ecirc;tes de ses victimes en guise de perles&hellip; MA T&Ecirc;TE S&rsquo;EN SOUVIENT ENCORE!! Hum&hellip; 
    D&eacute;sol&eacute;, un moment d&rsquo;&eacute;garement&hellip;<br>
    <strong>Bardamu</strong> :  Bien, t&rsquo;arrive-t-il de pactiser avec des aventuriers?<br>
    <strong>Malkiar</strong> :  Les aventuriers sont l&agrave; pour Me servir! Bient&ocirc;t ils seront tous &agrave; Mes pieds et formeront 
    une autre composante de Mes arm&eacute;es. Tels des pantins, Je les enverrais punir ceux qui pensent diriger ce 
    monde de pitres.<br>
    <strong>Bardamu</strong> : En existe-t-il certains que tu hais particuli&egrave;rement ou au contraire, que tu estimes?<br>
    <strong>Malkiar</strong> : Aucun ne m&eacute;rite l&rsquo;estime de Malkiar. Tous ne sont que des choses qui servent 
    Mon dessein. L&agrave; o&ugrave; vous vous tra&icirc;nez de peur, Moi je Me gausse de votre insignifiance.<br>
    <strong>Bardamu</strong> : Quel est ton but ultime, Malkiar?<br>
    <strong>Malkiar</strong> : Mon retour est annonc&eacute;, et cela devrait te suffire esclave. Mon pouvoir augmente de 
    jour en jour. Implore plut&ocirc;t Ma piti&eacute; et mets toi &agrave; Mon service!<br>
    <strong>Bardamu</strong> : A ton service? Te servir?? Non mais, &ccedil;a va pas la t&ecirc;t&hellip; Huuuuuum&hellip;
    Crains-tu quoique ce soit dans ces bas-fonds?<br>
    <strong>Malkiar</strong> : Esp&egrave;ce de cloporte, vois tu la peur dans cet endroit. Ah sans doute la tienne!<br>
    <strong>Bardamu</strong> : J&rsquo;imagine que tu ne vis pas ici&hellip;O&ugrave; r&eacute;sides-tu?<br>
    <strong>Malkiar</strong> : Dans un endroit o&ugrave; feu et sauvagerie sont la normalit&eacute;, endroit que tu vas 
    bient&ocirc;t rejoindre!<br>
    <strong>Bardamu</strong> : Attends! J&rsquo;ai pas fini&hellip;<br>
    Y a-t-il une &laquo; Malkiarette &raquo; dans ta vie??<br>
    <strong>Malkiar</strong> : Voil&agrave; la question stupide de trop. Ingelo, va Me chercher la t&ecirc;te de cette chose que Je 
    go&ucirc;te pour voir si un cerveau pourri a un go&ucirc;t d&eacute;licat pour Mon palais.<br>
    <br>
    <em>Bien &eacute;videmment, Bardamu &eacute;tait d&eacute;j&agrave; loin lorsque Malkiar aboya sa derni&egrave;re 
    r&eacute;plique.</em>
	</p></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" class="del"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bdi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="intercolonnes_bas"><img src="img_archives/del.gif" height="8" width="10"></td>
   <td class="coin_bgi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
</div>

<div class="InterviewPlanctons" id="InterviewPlanctons">
 <table class="fond2" width="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">Correspondance : LES PLANCTONS</th>
 </tr>
 <tr>
  <td class="coin_hg"                ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"                ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="colonne"><p><em>
    Bardamu se d&eacute;sesp&eacute;rait encore de la l&eacute;thargie des Planctons, lorsque le brouhaha
    d&eacute;versa son lot de rumeurs&hellip;Les micro organismes fr&eacute;tillaient de nouveau!<br>
    L&rsquo;&oelig;il p&eacute;tillant, le nain fouilla les coins et recoins des bas fonds et&hellip; Rien&hellip;
    Lass&eacute; d&rsquo;user vainement ses pas, le journaleux expectora quelques r&acirc;les, barbouilla un vieux 
    parchemin, glissa le tout au fond d&rsquo;une fiole, et jeta son espoir &agrave; la mer.<br>
    L&rsquo;entremetteuse turquoise, amus&eacute;e et joueuse, promena la fiole de Bardamu &agrave; Traktopel et 
    de Copper-Ado &agrave; Bardamu. Ce dernier, f&eacute;brile, d&eacute;plia le parchemin revenu, et &Ocirc; 
    surprise, les Planctons avaient r&eacute;pondu&hellip;
  </em></p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="10" width="10"></td>
 </tr>
  <tr>
   <td class="coin_bg"               ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"               ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 <tr>
  <th colspan="5"><img src="img_archives/del.gif" height="8" width="10"></th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
    <em>Salutations. Si par miracle, cette missive, confi&eacute;e aux flots, arrivait jusqu&rsquo;&agrave; vous, merci
    d&rsquo;ins&eacute;rer vos r&eacute;ponses entre les questionsque voici:</em><br>
    <br>
    <em>- Vous &eacute;tiez parmi les premiers &agrave; p&eacute;n&eacute;trer les profondeurs&hellip; Quels sont les 
    &eacute;v&eacute;nements qui vous ont le plus touch&eacute;s, terrifi&eacute;s, amus&eacute;s&hellip;?</em><br>
    <font class="planctons">On est descendus entre le Verbe et l&rsquo;Inondation. A l&rsquo;&eacute;poque du siphon du 0/0&hellip; 
    Une fa&ccedil;on romantique de faire connaissance &agrave; grands coups de postillons. C&rsquo;&eacute;tait 
    dr&ocirc;le d&rsquo;&ecirc;tre les uns sur les autres sauf pour ceux d&rsquo;en bas.<br>
    Pour ce qui est des souvenirs, ne compte pas nous passer la brosse &agrave; reluire, nos &eacute;cailles 
    ne mangent pas de ce pain-l&agrave;.</font><br>
    <em>- Des organismes marins dans les souterrains&hellip;Etrange, non?</em><br>
    <font class="planctons">Je peux en dire autant sur les Albatros, les Ph&eacute;nix et les Guerriers de la Savonnette&hellip;</font><br>
    <em> - Pourquoi vous &ecirc;tre &eacute;gar&eacute;s ici?</em><br>
    <font class="planctons">A cause de la grande s&eacute;cheresse&hellip; Ou si tu pr&eacute;f&egrave;res, on conna&icirc;t le pourquoi et on 
    cherche le comment. Et puis on a notre auberge et son personnel&hellip;</font><br>
    <em> - Vous fuyez le devant de la sc&egrave;ne&hellip;La renomm&eacute;e ne vous tente pas?</em><br>
    <font class="planctons">Rien &agrave; voir. La renomm&eacute;e, c&rsquo;est comme l&rsquo;encre pour les calamars. 
    C&rsquo;est un syst&egrave;me de d&eacute;fense, une fa&ccedil;on de passer entre les gouttes. Tu causes, 
    tu causes, tu causes pour te faire un nom et t&rsquo;&eacute;viter des gnons. Pour garder ton lopin, ta femme, 
    tes animaux de compagnie&hellip; Pour &ecirc;tre craint et pas en foutre une ram&eacute;e. Nous, on s&rsquo;en 
    lave les mains de la renomm&eacute;e et &agrave; part quelques exceptions, la r&eacute;alit&eacute; est bien 
    diff&eacute;rente des courriers du c&oelig;ur. Faut les voir au r&eacute;veil, tes idoles&hellip; Chez nous, soit 
    t&rsquo;es cool, soit tu coules et on ne mord plus aux hame&ccedil;ons&hellip;</font><br>
    <em> - Vous brisez les lois de la cha&icirc;ne alimentaire&hellip;Vous devriez servir de repas, or l&agrave;, 
    c&rsquo;est vous qui d&eacute;vorez de l&rsquo;aventurier! Ne craignez-vous pas d&rsquo;ab&icirc;mer 
    l&rsquo;harmonie &eacute;tablie pas la Nature?</em><br>
    <font class="planctons">Les c&eacute;tac&eacute;s et les cachalots, on s&rsquo;en m&eacute;fie&hellip; Les aventuriers? Je vois pas 
    de quoi tu parles&hellip; Et puis l&rsquo;harmonie, les lois de la Nature&hellip; Tu devrais arr&ecirc;ter 
    les infusions de camomille et prendre des bains d&rsquo;orties. Tu bouffes ou t&rsquo;es bouff&eacute;, soit, 
    mais l&rsquo;Harmonie comme tu dis, c&rsquo;est un fantasme de puceau dans une chambre aux volets ferm&eacute;s.</font><br>
    <em> - Comment r&eacute;agissent les aventuriers qui passent entre vos mains?</em><br>
    <font class="planctons">Les aventuriers? Mais de quels aventuriers tu causes&hellip;? Fa&ccedil;on jeux de d&eacute;s dans une auberge 
    avec des litres de bi&egrave;re et des danseuses, des fers aux pieds ? Sois pas na&iuml;f, avec le Balrog ou la 
    fifille &agrave; Nini la d&eacute;esse, ceux qui portent les salopettes travaillent pendant que les autres 
    s&rsquo;engraissent. Malkiar, c&rsquo;est la poutre dans l&rsquo;&oelig;il du Kraken&hellip; Et ici bas, ce 
    sont les gros moines aux mains moites qui font fortune&hellip;</font><br>
    <em> - Qui sont vos amis&hellip;Et vos ennemis dans les profondeurs?</em><br>
    <font class="planctons">A force de d&eacute;river, on a crois&eacute; des poussahs, des pousse-toi de l&agrave;, des 
    fils &agrave; papa-ladin qui peut et des p&egrave;res la vertu comme s&rsquo;il en pleuvait. Le 
    b&eacute;b&eacute;-&eacute;puisette, &ccedil;a va encore tant qu&rsquo;il nage pas dans notre bocal. Les guildes 
    font du commerce mais parfois, que ce soit Parflor, Kaali ou le soldat inconnu par exemple, si tu sais aligner 
    trois mots sans dire que des conneries, on peut s&rsquo;entendre. Que tu sois carnivore ou non.</font><br>
    <em> - Vous &ecirc;tes de gros r&acirc;leurs, non??</em><br>
    <font class="planctons">Faut pas nous faire prendre un radeau pour un glorieux trois-m&acirc;ts. Ca fait longtemps 
    qu&rsquo;on assiste aux demi-tours des uns et des autres. L&rsquo;ouverture des sceaux, ta gazette, la prise 
    du ch&acirc;teau ou l&rsquo;antre du Balrog, c&rsquo;est toujours mieux de loin. Nous, on d&eacute;nonce parfois 
    les manipulations et encore, on fait pas trop de vagues.</font><br>
    <em> - Vous disputez-vous de temps &agrave; autres?</em><br>
    <font class="planctons">Souvent. Sur le choix de la bouffe par exemple, certains consid&egrave;rent qu&rsquo;il faut 
    prendre le menu et d&rsquo;autres la carte.</font><br>
    <em> - Avez-vous des anecdotes &agrave; nous raconter?</em><br>
    <font class="planctons">Des anecdotes&hellip; Mais tu ressors la brosse &agrave; reluire&hellip; T&rsquo;as besoin de 
    quelque chose ? T&rsquo;as perdu quelqu&rsquo;un en route?</font><br>
    <em> - Parlons d&eacute;co : celle des bas-fonds conna&icirc;t certaines &eacute;volutions sous l&rsquo;effet 
    de votre enthousiasme&hellip;Les murs changent de couleur, les guerriers stress&eacute;s profitent de vos 
    balan&ccedil;oires con&ccedil;ues avec les tripes de vos victimes, sans oublier les piscines avec plongeoirs 
    que vous creusez &ccedil;a et l&agrave;&hellip;Est-ce votre passion ? Pr&eacute;voyez-vous d&rsquo;autres 
    am&eacute;nagements?</em><br>
    <font class="planctons">C&rsquo;est d&rsquo;esth&eacute;tique que tu parles et c&rsquo;est un sujet s&eacute;rieux la 
    po&eacute;sie. Quand tu racles la surface, tu trouves du poisson pan&eacute; ou des perles&hellip; La mie ne 
    prot&egrave;ge pas des ar&ecirc;tes, c&rsquo;est une histoire &agrave; dormir debout. Oui, on est sur un projet 
    pour fermer les dispensaires de l&rsquo;antichambre des Enfer&hellip;</font><br>
    <em> - Comptez-vous agrandir la famille des Planctons?</em><br>
    <font class="planctons">On a &eacute;t&eacute; 12 &agrave; une &eacute;poque&hellip; mais la mar&eacute;e bleue est une 
    vraie menace. Quand y a trop de planctons, ca emp&ecirc;che la faune de fauner et la flore de florir.</font><br>
    <em> - Parlons sentiments&hellip;Copper, j&rsquo;ai ou&iuml; dire que tu avais un certain faible pour les 
    vampirettes&hellip; Tu me racontes?</em><br>
    <font class="planctons">C&rsquo;est un secret pour personne, c&rsquo;est &eacute;crit sur les murs du ch&acirc;teau. Et 
    l&agrave;, tu parles de S&eacute;r&eacute;na alors &eacute;vite de glousser et de gazouiller&hellip; Pour 
    S&eacute;r&eacute;na, malheureusement, les autres planctons ont d&ucirc; lui tendre un pi&egrave;ge pour me faire 
    sortir d&rsquo;un cachot. Aux derni&egrave;res nouvelles, elle r&eacute;glait des affaires &agrave; la surface&hellip; 
    Et si r&eacute;pondre pour ta feuille de chou doit bien servir &agrave; quelque chose, c&rsquo;est &agrave; lui 
    rappeler que je l&rsquo;attends longtemps toujours &eacute;ternellement et plus si affinit&eacute;s.</font><br>
    <em> - Et aujourd&rsquo;hui? O&ugrave; en &ecirc;tes-vous? Amoureux?</em><br>
    <font class="planctons">Sans commentaire. Tu sais, on est tous les enfants d&rsquo;un &oelig;uf unique. Le sexe, 
    l&rsquo;identit&eacute;, les sentiments, quand t&rsquo;as des millions de fr&egrave;res et s&oelig;urs, 
    c&rsquo;est pas compliqu&eacute;. Pour pondre, faut pas &ecirc;tre sorcier. </font><br>
    <em> - Autre chose?</em><br>
    <font class="planctons">Je sais bien que tu vas d&eacute;former nos propos et mettre du sel l&agrave; o&ugrave; ca nous 
    g&ecirc;ne, des guirlandes l&agrave; o&ugrave; ca t&rsquo;arrange, mais regarde autour de toi ce que sont devenus 
    les aventuriers &ndash; comme tu t&rsquo;amuses &agrave; les appeler &ndash;, depuis que les familiers sont 
    tomb&eacute;s de l&rsquo;arbre&hellip; <br>
    Les souterrains pouponnent et les biberons ont remplac&eacute; les haches &agrave; deux mains. Les 
    &laquo; je suis bla bla bla &raquo; et mon ma&icirc;tre &laquo; bla bla bla si vous me touchez &raquo;, dois-je 
    t&rsquo;expliquer qui a pris le pouvoir ? Qui a du persil dans le nez ? Ou t&rsquo;en es-tu rendu compte tout seul? 
    Sur ce&hellip;</font><br>
    <br>
    <em> Ainsi le &laquo;guerrier de la savonnette rose&raquo; avait il obtenu satisfaction&hellip;restait plus 
    qu&rsquo;&agrave; mettre en page cette prose r&eacute;cr&eacute;ative&hellip;</em>
	</p></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" class="del"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 </table>
</div>

<div class="InterviewKhazad" id="InterviewKhazad">
 <table class="fond2" width="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="11">Interview : PERDU DE RECHERCHE</th>
 </tr>
 <tr>
  <td class="coin_hg"                ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="9"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"                ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="dualcolonne" colspan="7"><p><em>
    Ghanst se promenait dans le cr&acirc;ne&hellip; enfin, promener&hellip; Ghanst atteignit p&eacute;niblement 
    l&rsquo;&eacute;choppe dite du cr&acirc;ne (ou C&amp;A pour les pointilleux) pour tenter de trouver soit un 
    anneau de pouvoir permettant d&rsquo;asservir les morts vivants, soit un artefact de protection de zone contre 
    le mal (port&eacute;e douze lieues au minimum), soit une nouvelle chope pour remplacer celle qui 
    s&rsquo;&eacute;tait bris&eacute;e lors du combat. Apercevant un visage inconnu, une naine rousse, elle 
    s&rsquo;&eacute;tonna&hellip;
  </em></p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="10" width="10"></td>
 </tr>
  <tr>
   <td class="coin_bg"               ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="9"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bd"               ><img src="img_archives/del.gif" height="10" width="10"></td>
  </tr>
 <tr>
  <th colspan="11"><img src="img_archives/del.gif" height="8" width="10"></th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hdi"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes_haut"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hgi"               ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="coin_hd"               ><img src="img_archives/del.gif" height="8" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
  <strong>Ghanst</strong> : L&rsquo;est plus l&agrave; le g&eacute;rant habituel? <br>
  <strong>Khaz&acirc;d</strong> : Non, mon pr&eacute;d&eacute;cesseur a d&ucirc; partir. Je viens de reprendre son 
  &eacute;choppe et car j'ai &eacute;t&eacute; nomm&eacute;e par le nouveau responsable de la Caravane, 
  Messire Le Pacifique <br>
  <strong>Ghanst</strong> : Le Pacifique? S&rsquo;il m&eacute;rite son surnom, il f'ra pas long feu dans les souterrains.<br>
  <strong>Khaz&acirc;d</strong> : Pas long-feu? Son temp&eacute;rament est un peu (trop) volcanique en fait. Il n'en a que le nom, 
  mais ce n'est pas dans sa nature de se laisser faire. Il serait plut&ocirc;t du genre je-tape-pour-calmer-les-gens 
  Mais c'est peut-&ecirc;tre gr&acirc;ce &agrave; lui que la caravane n'aura pas sombrer cet &eacute;t&eacute;. Il l'a 
  reprise de main de ma&icirc;tre alors que tous les ma&icirc;tres marchands &eacute;taient injoignables, et il 
  &agrave; fait le m&eacute;nage dans chacune d'elles avant de les distribuer aux caravaniers.<br>
  <strong>Ghanst</strong> : J'ai entendu dire, qu'pour faire partie d'la caravane, fallait avoir une grande gueule et la dalle en 
  pente. Ca explique p't&ecirc;tre le d&eacute;clin, non?<br>
  <strong>Khaz&acirc;d</strong> : Bien s&ucirc;r qu'il faut &ecirc;tre tout &ccedil;a (nous sommes des marchands). 
  <em>* devant l&rsquo;air ahuri de Ghanst, Khaz&acirc;d poursuit *</em><br>
  <strong>Khaz&acirc;d</strong> : Si on ne l'&eacute;tait pas, on aurait vite fait de rater une affaire parce qu'on n&rsquo;aurait 
  pas r&eacute;pondu &agrave; une tentative d'intimidation ou parce qu'on aurait rouler sous la table alors que nous 
  tentions de faire boire nos clients. Et puis ce sont les int&eacute;r&ecirc;ts de Sa Majeste Hormandre III que 
  nous d&eacute;fendons : il ne faut pas se laisser faire. <br>
  <strong>Ghanst</strong> : De l&agrave; &agrave; vider sa cave&hellip; et puis&hellip; j&rsquo;ai entendu dire que 
  l&rsquo;bizutage, chez vous, &eacute;tait pas piqu&eacute; des vers. Que de petits sanguinous se balladaient 
  &agrave; droite et &agrave; gauche dans les souterrains suite &agrave;&hellip; hum, enfin tu vois. <br>
  <strong>Khaz&acirc;d</strong> : Bizutage? Je vois pas de quoi tu veux parler. On descend juste quelques f&ucirc;ts de bi&egrave;re 
  afin d'&eacute;prouver un peu les nouveaux, histoire de voir s'ils vont tenir la distance. <br>
  <strong>Ghanst</strong> : Ah&hellip; <em>* visiblement all&ecirc;ch&eacute;e par le mot bi&egrave;re, mais d&eacute;&ccedil;ue 
  par ailleurs *</em><br>
  Moi, c'est Ghanst. <em>* tend une main *</em><br>
  C'est quoi ton nom?<br>
  <strong>Khaz&acirc;d</strong> : Moi c'est Khaz&acirc;d (et non sanguin ne m&rsquo;est encore jamais tomb&eacute; dessus). 
  Et puis, il est pas encore n&eacute; celui qui voudra abuser de moi&hellip; (hips)<br>
  <strong>Ghanst</strong> : Khaz&acirc;d? Ca m&rsquo;dit quelque chose&hellip; vaguement. Aaaah, oui&hellip; On m&rsquo;a 
  parl&eacute; de toi. Des chevaliers de Justice. T&rsquo;es pas une tueuse de farfadets, toi?<br>
  <strong>Khaz&acirc;d</strong> : Enfin&hellip; oui je l'avoue, un peu au d&eacute;but. Mais vous savez, quand vous passez plus d'un 
  mois avec ces petites b&ecirc;tes, on s'y attache. Du coup un jour, plut&ocirc;t que de se taper dessus, je leur ai 
  proposer de passer prendre une bi&egrave;re &agrave; la taverne de la Caravane. J'aurais jamais pens&eacute; qu'ils 
  se seraient point&eacute; &agrave; 600. En plus ils ont mis un sacr&eacute; bazar, ils nous ont vid&eacute; la cave. 
  C'est &agrave; coup de pied aux fesses qu'on les a fait sortir sinon ils restaient pour la semaine. Le plus navrant,
  c'est qu'apr&egrave;s, ils m'ont suivie et ont commenc&eacute; &agrave; faire la tourn&eacute;e des taverne avec moi, 
  &agrave; commencer par celle des Chevaliers de Justice.<br>
  <em>* elle &eacute;met un petit rire joyeux &agrave; ce souvenir *</em><br>
  <strong>Khaz&acirc;d</strong> : Oli &eacute;tait furieux de cette invasion, surtout qu'ils ont pas eu la main 
  l&eacute;g&egrave;re sur leur cave non plus. Et puis, plus &ccedil;a boit ces bestioles, plus y en a. 
  A croire qu'ils se reproduisent comme des lapins!<br>
  <strong>Ghanst</strong> : Ah, oui&hellip; les farfadets, j'ai connu. Z&rsquo;aiment peut-&ecirc;tre la bi&egrave;re, 
  mais t'as pas un truc pour les chasser?<br>
  <strong>Khaz&acirc;d</strong> : Ce qui marche le mieux, c'est de dire que c'est tourn&eacute;e g&eacute;n&eacute;rale dans une autre 
  taverne. <br>
  <em>* r&eacute;fl&eacute;chit deux secondes *</em><br>
  <strong>Khaz&acirc;d</strong> : Pourquoi j'ai jamais indiqu&eacute; celle des contrebandiers? <br>
  <strong>Ghanst</strong> : <em>* petit regard malicieux *</em><br>
  Et vous n'avez jamais essay&eacute; de leurs vendre quelque chose? Des 
  dictionnaires&hellip; des armoires normandes&hellip;<br>
  <strong>Khaz&acirc;d</strong> : Je sais qu'ils aiment bien les grandes lame et les chaises. Mais les chaises, &ccedil;a part trop 
  vite, on peut pas en profiter tr&egrave;s longtemps.<br>
  <strong>Ghanst</strong> : Les chaises?<br>
  <strong>Khaz&acirc;d</strong> : les chaises de la taverne, ils sont partis avec tout le mobilier et ils ont m&ecirc;me pas 
  laiss&eacute; de pourboire.<br>
  <strong>Ghanst</strong> : La caravane aurait eu le dessous dans une transaction? C'est une premi&egrave;re, &ccedil;a&hellip;<br>
  <strong>Khaz&acirc;d</strong> : ces bestioles sont encore plus grippe-sous que nous, alors je vous laisse imaginer. Dis, &ccedil;a 
  fait 5 minutes qu'on cause et je commence d&eacute;j&agrave; &agrave; te tutoyer. Ca te d&eacute;range pas, Ghanst?<br>
  <strong>Ghanst</strong> : Hein, qui &ccedil;a moi? Oh, non, pas du tout. J'te tutoie avec plaisir.<br>
  <em>* regarde Khaz&acirc;d un instant *</em><br>
  On m'a parl&eacute; d'un naine braillarde et rousse qui serait rentr&eacute;e comme une furie dans l'auberge des 
  CdJ en gueulant des exigences&hellip; c'&eacute;tait toi?<br>
  <strong>Khaz&acirc;d</strong> : <em>* compte sur ses doigts *</em><br>
  - une naine? Oui.<br>
  - rousse? Oui.<br>
  - en furie? Oui.<br>
  - dans l'auberge des CdJ? Oui.<br>
  - En gueulant? Oui. (tout le temps, en fait)<br>
    </p></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_gauche_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p>
  Quant aux exigences, je pense que tu parles de l'affaire que j'ai avec un des &eacute;cuyers des CdJ?<br>
  <strong>Ghanst</strong> : J'ai pas eu beaucoup d'd&eacute;tails, mais j'suppose que oui. Un de ces b&eacute;ni-oui-oui t'as fait des 
  probl&egrave;mes?<br>
  <strong>Khaz&acirc;d</strong> : C'est plus grave que &ccedil;a. J'accuse leur &eacute;cuyer Dorago Von Valentir d'avoir enlever la 
  fille de mon ancien ma&icirc;tre marchand, Messire Akadie. Avoue que &ccedil;a fout mal d'&ecirc;tre accus&eacute;es 
  d'enl&egrave;vement pour des personnes qui pr&ecirc;chent la justice &agrave; tour de bras. Nous sommes tomb&eacute;s 
  d'un commun accord avec les dirigeant des Chevaliers de Justice d'enqu&ecirc;ter plus en avant car celui-ci 
  pr&eacute;tend &ecirc;tre innocent. De plus, il se trouve que plusieurs personnages importants des souterrains 
  seraient m&ecirc;l&eacute;s &agrave; tout &ccedil;a, notamment 3 personnalit&eacute;s de l'escorte de L'archidiacre 
  Sim&eacute;on.<br>
  <strong>Ghanst</strong> : Moi, j'aurais appliqu&eacute; la m&eacute;thode de par chez moi: un coup sur le coin de la gueule. <br>
  T'veux un coup d'main?<br>
  <strong>Khaz&acirc;d</strong> : le probl&egrave;me n'est pas de lui taper dessus, c'est une v&eacute;ritable crevette, mais c'est de 
  retrouver la fille. En plus, &eacute;tant dans la caravane, je me devais de trouver une solution diplomatique &agrave; 
  tout &ccedil;a vis-&agrave;-vis des CdJ. T'y crois toi une naine en tr&egrave;s de faire de la diplomatie, et 
  pourquoi pas une elfe qui boit de la bi&egrave;re??? (Oups&hellip; Pardon Vard, je ne voulais pas te vexer)<br>
  <strong>Ghanst</strong> : Mais pourquoi l'accuses-tu lui? C'est le premier qui t'es tomb&eacute; sous la main quand tu &eacute;tais 
  en rogne?<br>
  <strong>Khaz&acirc;d</strong> : Il a &eacute;t&eacute; aper&ccedil;u plusieurs fois en train de r&ocirc;der autour de la demeure de mon 
  ma&icirc;tre. Pour preuve, j'ai un t&eacute;moin digne de fois, Arkish Drulak, tu sais l'escorteur de l'archidiacre.<br>
  <strong>Ghanst</strong> : Hmm&hellip; je ne le connais pas personnellement, mais j'ai d&ucirc; l'apercevoir &agrave; un moment. 
  Accompagn&eacute; de deux efles bizarres, pas franchement sympa.<br>
  <strong>Khaz&acirc;d</strong> : je suis d'accord avec toi, plut&ocirc;t belliqueux les frangins Del'Armgo. En plus ils 
  para&icirc;traient que ce sont de v&eacute;ritables elfes noirs. On se demande comment ils sont rentr&eacute;s 
  dans l&rsquo;escorte. En plus Dorago Von Valentir pr&eacute;tend que ce sont eux les coupables mais pour le 
  moment, il n'a toujours pas de preuves.<br>
  <strong>Ghanst</strong> : Et, la personne qui avait &eacute;t&eacute; enlev&eacute;e&hellip; elle est enterr&eacute;e o&ugrave;? 
  Ca m'fait d'la peine c&rsquo;t histoire. J'voudrais m'recuillir sur sa tombe et verser une pt'ite bi&egrave;re 
  dessus en priant Tonto.<br>
  <strong>Khaz&acirc;d</strong> : Si elle est morte, son assasin le paiera de sa vie, j'en fais le serment. Mais pour le 
  moment, je pr&eacute;f&egrave;re ne pas penser au pire.<br>
  <strong>Ghanst</strong> : Oh? Elle ne s'rait pas morte? Si j'la vois, j'te fais signe. Elle ressemble &agrave; quoi?<br>
  <strong>Khaz&acirc;d</strong> : Elle s'appelle Brun&eacute;hilde, c'est une humaine 1.70m, blonde aux yeux bleus. 
  Si vous la croisez, fa&icirc;tes moi signe, je suis preneuse de toutes informations. R&eacute;compense &agrave; 
  la cl&eacute;. Je sais qu'elle a &eacute;t&eacute; enlev&eacute;e &agrave; l'ext&eacute;rieur et cach&eacute;e 
  dans les souterrains apr&egrave;s.<br>
  <strong>Ghanst</strong> : Et&hellip; en secouant les elfes noirauds&hellip; on obtiendrait pas quelques infos?<br>
  <strong>Khaz&acirc;d</strong> : C'est en cours chez les CdJ. La crevette de Dorago s'est mis en t&ecirc;te de les faire parler en 
  discutant avec eux. Pour un futur Paladin, &ccedil;a promet. En tout cas, il n'en tire rien, je m'en occuperai 
  personnellement.<br>
  <strong>Ghanst</strong> : Bien parl&eacute;.<br>
  <em>* regarde les &eacute;tag&egrave;res bien garnies *</em><br>
  Pffft&hellip; &Ccedil;a donnerait presque envie d'postuler chez vous. S&eacute;curit&eacute; d'emploi, bourse plus jamais 
  vide&hellip;<br>
  <strong>Khaz&acirc;d</strong> : Tout le monde est le bienvenu, mais en ce moment c'est un petit peu tendu &agrave; la caravane.<br>
  <strong>Ghanst</strong> : J'imagine&hellip; Si les farfadets ont vid&eacute; vot' cav'.<br>
  <strong>Khaz&acirc;d</strong> : Non, pas du tout. C'est pour le renouvellement de la concession des &eacute;choppes. Le bail d'un an 
  de la Caravane est &agrave; son terme et sa Majest&eacute; ne nous a toujours pas recontact&eacute; pour le renouveler.<br>
  <strong>Ghanst</strong> : Ehe&hellip; Et la concession des auberges, c'est le m&ecirc;me terme?<br>
  <strong>Khaz&acirc;d</strong> : Euh? Non je crois pas.<br>
  <strong>Ghanst</strong> : Bon, c'est pas tout &ccedil;a&hellip; Un anneau de pouvoir ou un art&eacute;fact de protection contre le 
  mal, tu as?<br>
  Ou une chope neuve?<br>
  <strong>Khaz&acirc;d</strong> : D&eacute;sol&eacute;, mon dernier anneau de pouvoir est partie ce matin. Par contre si tu veux, je 
  t'invite &agrave; boire un coup, un aventurier &agrave; laisser son tonneau dans mon &eacute;choppe. P't-&ecirc;tre 
  trop lourd pour lui?<br>
  <strong>Ghanst</strong> : Sans doute&hellip; mais il aurait fallu lui dire qu'il suffisait d'le vider d'abord. Oh, en parlant de 
  bons &agrave; riens, j'connais un scribouillards qu'sait m&ecirc;me pas s'battre. J'lui toucherai un mot d'ton 
  histoire d'avec Brun&eacute;hilde. <br>
  Pt'&ecirc;t-qu'il pourra enfin gagner sa vie en faisant queque chose d'utile.<br>
  <br>
  <em>* Ghanst suit alors Khaz&acirc;d vers l'arri&egrave;re boutique pour montrer la seule magie qu&rsquo;elle 
  ma&icirc;trisait parfaitement : la transformation de bi&egrave;re en&hellip; hum, en rien, en fait *</em>
	</p></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" class="del"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="coin_bdi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="intercolonnes_bas"><img src="img_archives/del.gif" height="8" width="10"></td>
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
    Dans cette septi&egrave;me parution de la Voix, nous vous proposons deux interviews totalement 
	inattendues, et obtenues laborieusement!<br>
	<br>
    La premi&egrave;re l&egrave;ve le voile sur le GRAND m&eacute;chant : Malkiar, qui enfin 
	s&rsquo;est livr&eacute; sans d&eacute;vorer tout cru son interlocuteur&hellip; Quoique, 
	c&rsquo;&eacute;tait tr&egrave;s limite&hellip;<br>
	<br>
    La seconde nous a &eacute;t&eacute; offerte par les Planctons. Acides, piquants, sagaces&hellip;<br> 
	Bref, les amateurs s&rsquo;en d&eacute;lecteront. <br>
    Les autres bouderont!<br>
    <br>
    Vous appr&eacute;cierez &eacute;galement la suite des p&eacute;rip&eacute;ties d&rsquo;Albert, les niouzes, 
	les indiscr&eacute;tions du p&rsquo;tit Paul dont les oreilles pourraient un jour se tendre dans votre 
	direction (restez sur vos gardes!), ainsi qu&rsquo;un distrayant papotage entre Ghanst, m&eacute;chamment 
	imbib&eacute;e, et Khaz&acirc;d, d&eacute;finitivement pipelette!<br>
	<br>
    Hum&hellip; Puis, une triste nouvelle : votre gazette conna&icirc;t des jours difficiles. Un article en 
	fin de page vous en dira plus. <br>
	<br>
    La r&eacute;daction vous souhaite un agr&eacute;able moment!<br>
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
  <th colspan="5">Petites Annonces</th>
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
Pour que chaque guilde soit r&eacute;pertori&eacute;e, chaque chef de guilde est tenu d&acute;insérer 
dans les registres un parchemin qui contient le descriptif de son clan. <br>
Oorg&ugrave;n, administrateur 
des Sillonneurs Gris, organise un concours qui r&eacute;compensera le parchemin le plus adroitement 
&eacute;labor&eacute;. Si l&acute;exp&eacute;rience vous tente, en tant que participant ou en tant que 
membre du jury, rendez vous en place publique, dans la salle &quot;idées&quot;. 
Le d&eacute;tail de l&acute;organisation y est expliqu&eacute;.
     </font><hr><font class="txt">
Les Adamantiums renaissent de leurs cendres!<br>
La guilde change de concept et se veut d&eacute;sormais exclusivement tourn&eacute;e vers l&acute;archerie 
&eacute;litiste soutenue par la magie. La gente f&eacute;minine y est tr&egrave;s repr&eacute;sent&eacute;e...<br> 
La gente masculine est toutefois la bienvenue, sous r&eacute;serve de ne pas amplifier certains traits de caract&egrave;res 
d&eacute;j&agrave; trop pr&eacute;sents dans la compagnie. 

Les recrutements sont ouverts : tout archer de mérite sera le bienvenue ! Mais étant donné la brusque descente dans les 
profondeurs des membres courants de la guilde, le regroupement ne sera pas nécessairement des plus faciles et les anciens 
pourront être dans l'impossibilité d'aider les plus jeunes. L'aptitude à la survie dans les profondeurs des souterrains est 
donc un pré-requis indispensable pour les postulants! 

L'objectif avoué de cette formation d'archer est simple: En finir une bonne fois pour toute avec le démon responsable de notre 
présence en ces lieux.
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
     <strong>La Fin du Morbelin</strong><br>
     Quelques aventuriers maladroits ont assur&eacute; le tr&eacute;pas de Vigor, Shaman Morbelin lib&eacute;r&eacute;
     du joug de Malkiar. Apr&egrave;s quelques &eacute;changes houleux et quelques fessades, ses protecteurs, 
     m&eacute;chamment &eacute;branl&eacute;s, ont eux, assur&eacute; sa vengeance.<br>
     Interrog&eacute; par la r&eacute;daction, Alliance de la Confr&eacute;rie des ombres (celui qui 
     ass&eacute;na le coup fatal &agrave; Vigor) en &eacute;tait sinc&egrave;rement d&eacute;sol&eacute;. Le 
     jeune elfe ignorait en effet tout de la condition du Morbelin&hellip;<br>
     </font><hr><font class="txt">
     <strong>Les Jeux du Balrog</strong><br>
     Apr&egrave;s avoir ab&icirc;m&eacute; le plafond du 7 &egrave;me sous sol afin de se hisser aux portes du 
     Ch&acirc;teau, le Balrog ma&icirc;trise enfin l&rsquo;art de monter et descendre des escaliers. Exalt&eacute; 
     par cette d&eacute;couverte, il s&rsquo;est amus&eacute; &agrave; grimper, puis &agrave; d&eacute;gringoler
     un certain nombre de fois les marches qui s&eacute;parent le 6 &egrave;me sous sol de l&rsquo;Antichambre, 
     le tout sous les regards incr&eacute;dules des aventuriers t&eacute;moins de l&rsquo;enthousiasme du colosse 
     &eacute;b&egrave;ne.<br>
     </font><hr><font class="txt">
     <strong>Les Autres Jeux du Balrog</strong><br>
     A pr&eacute;sent install&eacute; au qutri&egrave;me sous-sol, la cr&eacute;ature avance d&rsquo;un pas s&ucirc;r 
     parmi les gel&eacute;es, n&rsquo;omettant pas de g&acirc;ter les aventuriers qui croisent son chemin. Certains d'entre eux 
	 se sont m&ecirc;me jet&eacute;s sur la b&ecirc;te avant d'&ecirc;tre d&eacute;figur&eacute;s.<br>
	 Les fous&hellip;<br>
     Ses desseins restent &eacute;nigmatiques pour tous. Mais, y a t il quelque chose &agrave; comprendre? <br>
	 Peut-&ecirc;tre est il tout simplement devenu fou&hellip; <br>
	 FUYEZ!!!<br>
     </font><hr><font class="txt">
     <strong>Dressage difficile</strong><br>
     Marsupilamie de la Guilde de Derym en bave avec son Basilic Commun&hellip;<br>
	 Apr&egrave;s avoir recueilli l&rsquo;&oelig;uf et l&rsquo;avoir couv&eacute;, un b&eacute;b&eacute; Basilic en est sorti. <br>
	 Depuis, la bestiole teigneuse s&rsquo;obstine &agrave; lac&eacute;rer l&rsquo;elfette de ses griffes et de ses crocs. 
     Marsupilamie r&eacute;torque, prenant soin de ne pas blesser l&rsquo;intr&eacute;pide. Quelle fin pour cette 
     lutte insens&eacute;e qui oppose &laquo;b&eacute;b&eacute;&raquo; &agrave; &laquo;maman&raquo; et qui dure 
     depuis des jours&hellip;<br>
     </font><hr><font class="txt">
     <strong>Les Morbelins tuent le commerce</strong><br>
     L&rsquo;auberge &laquo;Au Plancton prisonnier&raquo; souffre de la p&eacute;nurie de voyageurs qui osent 
     s&rsquo;aventurer dans le forteresse du 6 &egrave;me sous sol. Pendant que les Morbelins s&rsquo;engraissent 
     avec une taxe prohibitive (pour rappel: 3000 bzf sont exig&eacute;s pour p&eacute;n&eacute;trer le Ch&acirc;teau), 
     les caisses de la taverne des Planctons se vident&hellip;D&eacute;sesp&eacute;r&eacute;ment&hellip;<br>
     </font><hr><font class="txt">
     <strong>Le myst&egrave;re</strong><br>
     Les KAA ont un moment disparu de la circulation. Ils auraient &eacute;t&eacute; t&eacute;l&eacute;port&eacute;s 
     &agrave; un &eacute;tage myst&eacute;rieux o&ugrave; s&eacute;viraient des D&eacute;mons Mineurs.<br>
    </font><hr><font class="txt">
    <strong>Le boulet d'or :</strong><br>
	La Voix tient &agrave; rendre un hommage tout particulier &agrave; Soreclis, qui a l'issue d'une lutte acharn&eacute;e 
	a remport&eacute; le titre de boulet d'or&hellip; Titre tr&egrave;s largement m&eacute;rit&eacute; par ce farfelu. 
	Le suspens intense de cette &eacute;lection aura provoqu&eacute; de nombreuses crises de nerfs parmi les votants qui en 
	sont encore tout retourn&eacute;s&hellip;
    </font></p></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes"><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ligne_gauche_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p><font class="txt">
     <strong>M&eacute;daillon par ci, m&eacute;daillon par l&agrave;</strong><br>
     L&rsquo;ouverture des escaliers qui m&egrave;nent dans l&rsquo;Antichambre s&rsquo;annonce tendue: deux 
     m&eacute;daillons chez les &laquo;gentils&raquo;, et un m&eacute;daillon chez les &laquo;m&eacute;chants&raquo;. 
     Cette affaire va t elle trouver son issue dans la guerre??<br>
     </font><hr><font class="txt">
     <strong>Le Conseil des Sages</strong><br>
     Les Plopeurs Fous, apr&egrave;s avoir subi de lourdes pertes face au Balrog, ont sollicit&eacute; l&rsquo;aide 
     de Moklar qui occupe le Ch&acirc;teau du 6 &egrave;me sous sol avec ses troupes. Celui ci a r&eacute;uni le 
     conseil des Sages, qui a d&eacute;cid&eacute; d&rsquo;acc&eacute;der &agrave; la demande des Plopeurs. Mais, 
     coup de th&eacute;atre, Foman aurait d&eacute;clin&eacute; l&rsquo;offre! Les Plopeurs attendraient que la 
     cr&eacute;ature, qui se prom&egrave;ne maintenant dans l&rsquo;Antichambre, regagne les profondeurs pour 
     s&rsquo;y frotter&hellip;<br>
     </font><hr><font class="txt">
     <strong>Le rouge et le noir</strong><br>
     Monsieur Jacques s&eacute;vit de nouveau. Il a l&acirc;ch&eacute; ses lutins turbulents dans les profondeurs. 
     Ces derniers s&egrave;ment ici et l&agrave; des cadeaux. Trois choix sont alors propos&eacute;s aux aventuriers : 
     offrir le cadeau &agrave; un lutin rouge (r&eacute;serv&eacute; aux amateurs du p&egrave;re N&ouml;el), offrir 
     le cadeau &agrave; un lutin noir (r&eacute;serv&eacute; aux amateurs des fac&eacute;ties de monsieur Jacques), 
     ou garder &eacute;go&iuml;stement le cadeau. A vos risques et p&eacute;rils!<br>
     A noter: les lutins feraient aussi les poches des aventuriers&hellip;<br>
    </font><hr><font class="txt">
    <strong>L&acute;ensorcel&eacute;e :</strong><br>
    Le Balrog&hellip; Encore&hellip;<br>
    La solution pour en venir à bout se trouve peut être au sixième sous sol, étage où erre Nhyima encore sous linfluence 
	dun puissant rituel. Il semblerait que deux questions adroitement posées à la jeune femme suffiraient à la libérer, et 
	à entraver les desseins du Balrog qui restent bien énigmatiques!<br>
    Où court il ainsi?? Poursuit-il un aventurier en particulier??<br>
    Bref, si dans vos poches se trouve un objet susceptible de lintéresser, prenez garde!!
    </font><hr><font class="txt">
    <strong>Au bonheur des Noobs :</strong><br>
	Une nouvelle guilde vient de faire son apparition. Elle a pour but de former des nouveaux arrivants des souterrains aux 
	rudiments de la vie p&eacute;rilleuse de nos ch&egrave;res galeries obscures. Elle forme aussi bien les assassins de monstres que les 
	tueurs d'aventuriers et se veut au plus possible neutre. Bien que l'&eacute;quipe enseignante soit plutot d'origine bisounours,
	parmis les &eacute;l&egrave;ves actuels ont trouve un jeune de la Horde et quelques instructeurs de la Horde se trouvent aussi &agrave;
	leurs cot&eacute;s. Il semblerai que le Directeur de ce Centre de Formation Neutre cherche des alliances avec une quantit&eacute;
	non n&eacute;gligeable de guildes de tous horizons pour améliorer l'ambiance des classes<br>
    </font><hr><font class="txt">
    <strong>Derni&egrave;re minute :</strong><br>
	 le Ch&acirc;teau enfin accessible! Les Morbelins n'exigeraient plus aucune taxe et laisseraient les aventuriers entrer et 
	 sortir gratuitement. Par contre un bruit de couloir laisserait entendre que les monstres devraient eux payer&hellip;<br>
	 Malikar aurait m&ecirc;me re&ccedil;u une carte d'abonnement.
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

<div class="PtitPaul" id="PtitPaul">
 <table class="fond2" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">P'tit Paul :</th>
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
    Le P'tit Paul &eacute;coute toujours aux portes... Qu'a-t-il bien pu entendre?!?<br>
    <br>
    <strong> Le Nain, la Belle, et les DPs !</strong><br>
    Non contente de gagner le concours d&rsquo;embrassade des DPs, Erynmiriel se met &agrave; 
	&quot;poutouner&quot; du nain. Le dernier en date (Bardamu selon les sources) ayant 
	interpr&eacute;t&eacute; ce geste comme une chaude invitation, s&rsquo;est jet&eacute; sur Eryn. 
	Ce &agrave; quoi la belle ( &laquo; Le meilleur des DPs !! &raquo; selon le nain excit&eacute;) 
	quelque peu furibonde, aurait r&eacute;torqu&eacute;&nbsp;: &laquo;&nbsp;si j&rsquo;avais une 
	envie &agrave; satisfaire, je n'irais certainement pas chercher une toute petite bistouquette de 
	nain !&quot;.<br>
    <br>
    <strong>Faites l'amour, pas la guerre!</strong><br>
    Alors que la guerre se pr&eacute;pare au -4, entre une &quot;petite&quot; coalition de Gilead, 
	d'Aventurier Aventureux et de Balgurien contre un groupe de Main du Mal, des petites histoires 
	d'amours semblent s'&eacute;laborer dans les coulisses. <br>
    Certains t&eacute;moins affirment avoir vu Chaton, un Nain des Gilead, et Topgun Green, une Elfe 
	de la m&ecirc;me guilde, dans un coin de tente... en tout cas, les rumeurs circulent de plus en 
	plus... <br>
    <br>
    <strong> Loin des yeux&hellip;&nbsp;: </strong><br>
    &laquo;&nbsp;C&rsquo;est chaud bouillant&nbsp;!&nbsp;&raquo; fut le cri d&rsquo;Athalante en 
	r&eacute;ponse &agrave; la question&nbsp;: &laquo;&nbsp;T&rsquo;en est o&ugrave; avec le 
	Valeureux&nbsp;&raquo;. Cependant, la guerri&egrave;re Chevalier de Justice se 
	d&eacute;sesp&egrave;re. Une mission la r&eacute;clame au loin et l&rsquo;oblige a 
	abandonn&eacute; Korgrim, administrateur de la MDM. Leur love story r&eacute;sistera t il &agrave; 
	cette s&eacute;paration forc&eacute;e&nbsp;? <br>
    <br>
    <strong>La Belle et la Brute&nbsp;:</strong><br>
    Aknoth et Wiiip, tous deux Mercenaires F&eacute;&eacute;runiens, et fraichement unis flirtent 
	avec le bonheur. La larme &agrave; l&rsquo;&oelig;il, Aknoth s&rsquo;&eacute;meut&nbsp;: 
	&laquo;&nbsp; Et mon espoir, c'est Wiiip&hellip;&nbsp;&raquo;. Que c&rsquo;est mignon&nbsp;!<br>
    <br>
    <strong>Friponneries chez les m&eacute;chants :</strong><br>
    Carole, elfette qui jadis courait derri&egrave;re le P&egrave;re Simeon, se serait abandonn&eacute;e deux nuits durant 
	dans les bras d&rsquo;un KAA. Comment r&eacute;agira l&rsquo;&eacute;poux cocufi&eacute;, le Gilead Claymore, qui comme 
	attendu, ne fut pas convi&eacute; aux festivit&eacute;s? <br>
    Depuis, un bracelet orne, d&rsquo;ailleurs, le poignet de la coquine&hellip;<br>
    Les Affranchis aussi se livreraient &agrave; quelques divertissements libertins&hellip; Caro-Lina se r&eacute;jouirait 
	de l&rsquo;&eacute;treinte passionn&eacute;e de Bhlown, pendant que Carolyn c&egrave;derait au charme de M&acirc;lin (qui 
	porte tr&egrave;s bien son nom&hellip;), et que C&eacute;linelablanche se d&eacute;lecterait des app&eacute;tissantes 
	sucreries offertes par Draloc&hellip; 
	Quant &agrave; Arkantis, elle, a choisi la fougue de punaise79 pour profiter de quelques moments torrides. 
    Les rumeurs grandissantes n&rsquo;&eacute;pargnent pas non plus Kilreed. ZeDawn l&rsquo;aurait &eacute;mu au point 
	qu&rsquo;il en serait tomb&eacute; raide dingue&hellip;<br>
    Pour finir, Azarelle et Mohi profiteraient de l&rsquo;absence de Fullkro, amant officiel de la contrebandi&egrave;re, 
	pour fol&acirc;trer sauvagement&hellip;</p></td>
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

<div class="MotsCultes" id="MotsCultes">
 <table class="fond2" height="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">MOTS CULTES &amp; PETITS DELIRES</th>
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
    <em>Les boulets ne s&eacute;vissent pas qu&rsquo;en place publique&hellip;Les souterrains aussi profitent de 
    quelques sp&eacute;cimens&hellip;</em><br>
    <br>
    <strong>La peluche &eacute;nerv&eacute;e</strong> :<br>
    C&rsquo;est l&rsquo;histoire (authentique) d&rsquo;une peluche &eacute;mancip&eacute;e qui erre dans 
    l&rsquo;Antichambre. Elle s&rsquo;ennuie. Lorsqu&rsquo;enfin, elle trouve un jouet pour se distraire: un 
    aventurier emp&ecirc;tr&eacute; dans un combat titanesque. Elle s&rsquo;approche et tabasse (notons 
    qu&rsquo;avant sa mort, son propi&eacute;taire l&rsquo;avait boost&eacute;e &agrave; mort&hellip;). 
    L&rsquo;aventurier apr&egrave;s avoir essuy&eacute; quelques baffes, se tape un coup de sang et &eacute;crit 
    au ma&icirc;tre de l&rsquo;animal esseul&eacute; :<br>
    &quot;Pourrais tu faire en sorte que ton familier ne m'attaque pas? Nous avons d&eacute;j&agrave; fort a 
    faire avec tous ces d&eacute;mons sans en plus nous en occuper<br>
    Merci d'avance<br>
    Va landastel, pour la compagnie des astres&quot;<br>
    <u>R&eacute;ponse du propri&eacute;taire</u> : &quot;Je n'ai plus aucun contr&ocirc;le sur ma peluche mon ami : 
    je viens de crever !<br>
    Fais attention, c'est une brute&hellip;&quot;<br>
    <br>
    <strong>Le rythme dans la peau</strong> :<br>
    Alors que Frankeinstein (une m&eacute;chante brutasse) s&rsquo;amusait avec Blackrider, meneur des Bardes 
    d&rsquo;Anastasia et Soreclis, Boulet DP, les deux aventuriers se sont mis &agrave; entonner quelques:<br>
    &quot;Vas Franky c&rsquo;est bon<br>
    Vas y Franky, c&rsquo;est bon bon boooooon&hellip;&quot;<br>
    Le tout sous le nez d&rsquo; un Frankenstein quelque peu h&eacute;b&eacute;t&eacute;, pendant que le reste 
    des assaillants se tr&eacute;moussaient sur cet air chant&eacute; a capela 
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
  <th colspan="5">Albert</th>
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
    <strong>4 - Oiseau : animal qui vole mais quon plume</strong><br>
 - C&rsquo;est l&agrave; ? <br>
- Oui. <br>
Maintenant qu&rsquo;Albert se trouvait &agrave; la lisi&egrave;re du domaine qui recouvrait les fameux souterrains de Delain, il &eacute;prouvait un soulagement intense &agrave; l&rsquo;id&eacute;e de pouvoir enfin se s&eacute;parer de ce cocher dont il ne partageait pas l&rsquo;humour. <br>
- Bien, je vous remercie pour votre aide. <br>
Albert fit mine de sortir la bourse de son sac pour octroyer au cocher une g&eacute;n&eacute;reuse prime de 2 brouzoufs, mais celui-ci l&rsquo;arr&ecirc;ta dans son geste : <br>
- Laisse tomber. J&rsquo;ai d&eacute;j&agrave; &eacute;t&eacute; bien pay&eacute; au d&eacute;part et tu m&rsquo;as bien fait rire. Maintenant tu vas par l&agrave;, tu passes la vall&eacute;e et tu y seras. Ensuite, &hellip; c&rsquo;est toi qui vois. <br>
Le cocher remonta sur son chariot, salua Albert et s&rsquo;en f&ucirc;t. Apr&egrave;s quelques dizaines de m&egrave;tres, il arr&ecirc;ta son attelage puis, se tournant, cria : <br>
- Et bonne f&ecirc;te. <br>
- Merci. Mais ce n&rsquo;&eacute;tait pas aujourd&rsquo;hui &ndash; r&eacute;pondit Albert, un peu interloqu&eacute; et se servant de ses mains comme d&rsquo;un porte-voix. <br>
Cette phrase suscita &agrave; nouveau le rire du cocher. Albert rassembla ses affaires, passa la vall&eacute;e, vit un poste de garde en ruine puis, h&eacute;sitant sur le chemin &agrave; suivre et d&rsquo;une humeur exceptionnellement joueuse, d&eacute;cida de laisser le sort d&eacute;cider du chemin &agrave; suivre. Voulant tirer &agrave; pile ou face, il fouilla son sac &agrave; la recherche de sa bourse, mais sans succ&egrave;s. On lui avait d&eacute;rob&eacute; son bien. <br>
Cette nuit l&agrave;, il ne pu trouver le sommeil. Ses pens&eacute;es &eacute;taient constamment interrompues par l&rsquo;image du cocher, hilare criant : SURPRISE. 
    </p></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite"><img src="img_archives/del.gif" height="8" width="10"></td>
  </tr> 
  <tr>
   <td class="coin_bg"              ><img src="img_archives/del.gif" height="10" width="10"></td>
   <td class="ligne_bas" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
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
&Agrave; la suite de défections importantes dans nos effectifs, nous nous retrouvons desormais dans l'incapacité technique de 
poursuivre les parutions au rythme actuel. Les parutions vont donc devenir plus rare car l'effectif n'est plus suffisant pour 
permettre la réalisation du travail impliqué par la Voix. Il est donc fort probable que nous cessions totalement de paraitre dans les 
prochains mois. La seule solution pour assurer la pérénité de la Voix des Souterrains serait l'arrivée de personnes ayant un peu de 
temps libre et de volonté d'aider. Je sais que vous êtes nombreux à avoir une envie plus ou moins forte de participer ou d'être reconnu
pour votre talent. Je vais vous laissez la chance de pouvoir passer de l'autre coté du rideau, pouvoir écrire pour la Voix et la faire 
vivre. Ecrivez moi pour nous rejoindre : <br>
 par mail <a href="mailto:fra.diavolo@gmail.com">Fra Diavolo</a><br>
 sur le forum : Fra Diavolo<br>
 sur le jeu : Fra Diavolo<br>
 sur MSN : fra_diavolo_sa7@hotmail.com<br>
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

<div class="Guildes" id="Guildes">
 <table class="fond2" height="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">De GUILDES en GUILDES</th>
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
    <em>Cette rubrique est destin&eacute;e &agrave; pr&eacute;senter les diverses guildes r&eacute;pertori&eacute;es 
    dans les registres des souterrains.</em>
    A la question: qui &ecirc;tes vous? &laquo;, voici ce que les interview&eacute;s r&eacute;pondent:<br>
    <br>
    <strong>Le Clan Bayushi</strong><br>
    <u>Big boss</u> : Kachiko<br>
    <u>Effectif</u> : 15<br>
    <u>Commentaire</u> : &laquo;Le principal but du Clan Bayushi est pour le moment de faire conna&icirc;tre 
    le code d&rsquo;honneur des samurais, le Bushido. Ce code s&rsquo;articule autour de 7 vertus principales 
    que sont l&rsquo;honneur, le devoir, la justice, la courtoisie, le courage, la compassion et la loyaut&eacute;.<br>
    Lorsque l&rsquo;heure sera venue, j&rsquo;esp&egrave;re bien que nous deviendrons une puissance politique 
    importante, libre et ind&eacute;pendante. Nous ne servons ni Sa Majest&eacute; Hormandre, ni le roi 
    Salm&rsquo;o&rsquo;rv. Le but du Clan est de faire ouvrir les yeux aux aventuriers. Pourquoi pr&ecirc;ter 
    all&eacute;geance &agrave; des gens qui ne connaissent rien des souterrains, qui n&rsquo;y sont jamais 
    all&eacute;s? Nous sommes sur le terrain, et nous voulons faire comprendre aux aventuriers qu&rsquo;il y a 
    une autre alternative &agrave; Hormandre ou &agrave; Salm&rsquo;o&rsquo;rv. Cette alternative, c&rsquo;est 
    &agrave; nous, aventuriers qui foulons le sol des souterrains chaque jour, de la construire. Et c&rsquo;est 
    ce que j&rsquo;ai commenc&eacute; &agrave; faire, avec le Clan Bayushi.<br>
    Kachiko&raquo;<br>
    <strong>Messages de Kachiko</strong> : &laquo;A tous les aventuriers ayant un tant soit peu d&rsquo;honneur, et qui 
    recherche une guilde neutre, active mais ind&eacute;pendante des puissances existantes, le Clan Bayushi vous 
    accueillera &agrave; bras ouverts.<br>
    De plus, en tant que Valsharess des Valkyries, je rappelle &agrave; toutes les femmes des souterrains que notre 
    groupe n&rsquo;est pas mort. Les Valkyries sont toujours actives et constituent le groupe inter guildes neutre le 
    plus int&eacute;ressant pour vous. Bonnes ou mauvaises, si vous voulez d&eacute;fendre votre f&eacute;minit&eacute; 
    et si vous &ecirc;tes tol&eacute;rante, alors n&rsquo;h&eacute;sitez pas &agrave; rejoindre les Valkyries. Elles 
    vous apporteront souvent une aide pr&eacute;cieuse et vous permettront d&rsquo;&eacute;tendre vos contacts et de 
    glaner moult informations&raquo;
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

<div class="Croustillant" id="Croustillant">
 <table class="fond2" height="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">FRIPONNERIE CHEZ LES M&Eacute;CHANTS</th>
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
    Carole, elfette qui jadis courait derri&egrave;re le P&egrave;re Simeon, se serait abandonn&eacute;e deux nuits durant 
	dans les bras d&rsquo;un KAA. Comment r&eacute;agira l&rsquo; &eacute;poux cocufi&eacute;, le Gilead Claymore, qui comme 
	attendu, ne fut pas convi&eacute; aux festivit&eacute;? <br>
    Depuis, un bracelet orne, d&rsquo;ailleurs, le poignet de la coquine&hellip;<br>
	<br>
    Les Affranchis aussi se livreraient &agrave; quelques divertissements libertins&hellip; Caro-Lina se r&eacute;jouirait 
	de l&rsquo;&eacute;treinte passionn&eacute;e de Bhlown, pendant que Carolyn c&egrave;derait au charme de M&acirc;lin (qui 
	porte tr&egrave;s bien son nom&hellip;), et que C&eacute;linelablanche se d&eacute;lecterait des app&eacute;tissantes 
	sucreries offertes par Draloc&hellip;<br>
	Quant &agrave; Arkantis, elle, a choisi la fougue de punaise79 pour profiter de quelques moments torrides.<br>
    Bref&hellip; Les soupirs, voire les hurlements, g&eacute;mis sous les tentes la nuit venue sont une souffrance pour les 
	&acirc;mes esseul&eacute;es qui accompagnent ces chahuteurs&hellip;<br>
    Les rumeurs grandissantes n&rsquo;&eacute;pargnent pas non plus Kilreed. ZeDawn l&rsquo;aurait &eacute;mu au point 
	qu&rsquo;il en serait tomb&eacute; raide dingue&hellip;<br>
    Pour finir, Azarelle et Mohi profiteraient de l&rsquo;absence de Fullkro, amant officiel de la contrebandi&egrave;re, 
	pour fol&acirc;trer sauvagement&hellip;</p></td>
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


<div class="Benway" id="Benway">
 <table class="fond2" height="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">Etymologie selon Benway</th>
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
  Bonjour aux uns.<br><br>
  Poutrer ou se faire poutrer&hellip; Quoi ca? L&rsquo;un est l'enviol&eacute;e c&eacute;leste, l&rsquo;autre une 
  invitation pour faire du petit bois. Autant la premi&egrave;re image, tout finaude, mains sur les hanches et 
  poutre dress&eacute;e est facile pour un satire &agrave; grande bouche autant la 2&egrave;me m&rsquo;incline 
  &agrave; r&eacute;fl&eacute;chir.<br>
  Se faire poutrer : le payer cash, pas de ch&egrave;que en bois. La poutre est toujours plus facile &agrave; 
  enfoncer dans l&rsquo;&oelig;il du voisin. Mais pourquoi une poutre et pas un m&acirc;t?<br>
  Se faire mater, 
  c&rsquo;est pas si loin non plus et pour le po&egrave;te des liti&egrave;res, c&rsquo;est tout aussi flatteur 
  un m&acirc;t dress&eacute;.<br>
  Alors pourquoi la poutre?<br>
  Parce qu&rsquo;apparente, vermoulue, grignot&eacute;e par 
  les termites. A cause du son explosif mais alors pour les dames : se faire poudrer, r&eacute;duire en 
  poussi&egrave;re, en cendre.<br>
  Non. A cause du bois? De la charpente? Du poteau, du pilier, de la solive&hellip; <br>
  J&rsquo;en sais poutrement rien.<br>
  La seul chose? <br><br>
  La prochaine fois, promis, je planche avant de l&rsquo;ouvrir.
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

<div class="Hommage" id="Hommage">
 <table class="fond2" height="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="5">Le coin des Hommages</th>
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
    <u>Hommage &agrave; la peluche de Minouchka</u> : Le tr&eacute;pas de son invocatrice, Minouchka elfette 
    Ma&icirc;tre Cartographe, a rendu sa libert&eacute; &agrave; l&rsquo;une des plus puissante peluche des 
    souterrains. Apr&egrave;s avoir b&eacute;n&eacute;fici&eacute; de quelques soins dans le dispensaire 
    situ&eacute; au sud du Ch&acirc;teau, le malheureux animal est tomb&eacute; sur BAFFE&hellip;Dont voici 
    le commentaire: &laquo; Le Familier de Minouchka est venu voir Baffe , il vient de mourir&hellip;&raquo;.<br>
	<br>
    <u>Hommage &agrave; Ed-Jak</u> : Menant une vie d&rsquo;errance, le Mercenaire esseul&eacute; a succomb&eacute; 
    maintes fois aux baffes des bestioles dop&eacute;es qui s&eacute;vissent dans l&rsquo;Antichambre. Respectons 
    cependant son courage&hellip;L&rsquo;aventurier s&rsquo;est obstin&eacute; &agrave; refuser toute aide et 
    &agrave; batailler &agrave; mains nues: &laquo;Maintenant qu'il y a ENFIN du d&eacute;fi, j'vais pas me 
    faciliter la t&acirc;che, non plus&hellip; C'est mal me connaitre !&raquo;. Un fou!
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

<div class="Blagues" id="Blagues">
 <table class="fond2" height="100%" cellpadding="0" cellspacing="0">
 <tr>
  <th colspan="11">BLAGUES</th>
 </tr>
 <tr>
  <td class="coin_hg"               ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="coin_hdi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="intercolonnes_haut"    ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="coin_hgi"              ><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ligne_haut" colspan="3"><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="coin_hd"              ><img src="img_archives/del.gif" height="10" width="10"></td>
 </tr>
 <tr>
  <td class="ligne_gauche"><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"  ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p><font class="txt">
3 jeunes trolls de 6, 8 et 10 ans sont assis autour du repas avec leurs parents.<br>
- Mange ta soupe de sangsue! dit le p&egrave;re troll &agrave; l'a&icirc;n&eacute;.<br>
- J'en veux pas, elle est d&eacute;gueulasse. Tu peux te la foutre o&ugrave; je pense.<br>
Le p&egrave;re lui balance 2 beignes.<br>
- Pourquoi tu le frappes? fait le cadet. S'il aime pas cette soupe, c'est son droit. T'es tout le temps en train de l'emmerder.<br>
Lui aussi prend deux gifles. Alors le petit troll de 6 ans se prot&egrave;ge le visage.<br>
- Pourquoi tu fais &ccedil;a? Si tu es respectueux avec moi, je n'ai pas de raison de te gifler.<br>
- Je sais, r&eacute;pond le petit troll. Mais je me m&eacute;fie, t'es tellement con.<br>
    </font></p></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ligne_droite_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="intercolonnes"><img src="img_archives/del.gif" height="10" width="10"></td>
  <td class="ligne_gauche_i" ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="ecartement"   ><img src="img_archives/del.gif" height="8" width="10"></td>
  <td class="colonne"><p><font class="txt">
Une jeune m&egrave;re Orc pr&eacute;sente son b&eacute;b&eacute; &agrave; une autre Orc :<br>
- C'est tout le portrait de son p&egrave;re !<br>
- Ne t'inqui&egrave;te pas, l'essentiel, c'est qu'il est en bonne sant&eacute;<br>
<br>
     </font><hr><font class="txt"><br>
C'est deux nains qui trouvent une bourse remplie de brouzoufs.<br>
- Viens, on va se payer la cuite de notre vie et un bon repas.<br>
- Pourquoi, t'as faim toi ?
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


</div>

</body>
</html>
