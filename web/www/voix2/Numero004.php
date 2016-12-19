<?php 
include "counter.php";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>La Voix des Souterrains - n&deg;4 - Septembre 2005</title>
<link href="CSS/voix004.css" rel="stylesheet" type="text/css">
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


<body onLoad="switchDiv('Titre',true);
switchDiv('Nyouzes',true);
switchDiv('PA_1',true);
switchDiv('PD_1',true);
switchDiv('Pubs',true);
switchDiv('Contact',true);
switchDiv('Pub_TR_fond',true);
switchDiv('Pub_TR',true);
switchDiv('Interview1',true);
switchDiv('Interview2',true);
switchDiv('Edito',true);
switchDiv('Albert',true);
switchDiv('Courrier',true);">



<div class="fond">
<div class="titre" id="Titre">
	<a class="blanc" href="index.html">
    <img src="Titres/Titre3.jpg" class="titre_img">
  </a>
</div>

<!--
.gauche2 {
	position: absolute;
	height: 1900px;
	width: 598px;
	top: 1030px;
	left:0px;
}

<div class="gauche2" id="Interview1">
    <table class="interview1">
      <tr class="interview1_titre">
        <td colspan="3" class="interview_titre">L'INTERVIEW DE LA SEMAINE : <br>
LE BATELEUR
		</td>
      </tr>
      <tr class="interview1_intro">
        <td colspan="3" class="interview1_intro">
Lorsque Bardamu reçut cette missive, il en resta ahuri.<br>

Aeswen y réclamait une interview, étalant avec un aplomb peu ordinaire, ses talents de guerrier, mage, rh&eacute;teur... 
<br>Beau mec... Bref, tout y était !<br>
Un sourire espiègle se dessina sur les lèvres du nain, tout à fait disposé à combler l’efle...
		</td>
      </tr>
      <tr class="interview1_corps">
        <td class="interview1_corps">
<b>Bardamu</b> : Aeswen... Sois le bienvenu. Installe-toi, et attaquons !
<br>Alors…Pourquoi solliciter cette interview ? 
<br><b>Aeswen</b> :Tout simplement parce que je le mérite, et que si tout le monde n'a pas entendu 
parler de moi - ce qui est un grand tort - et bien ce sera chose faite.   
<br><i>* sourire carnassier *</i> 
<br>En fait je crois que j'aime bien parler de moi, et puis j'aime bien que les autres parlent de moi aussi. 
<br><i>* regard brillant *</i>
<br><b>Bardamu</b> : Aurais-tu soif de reconnaissance, Aeswen ? ?  
<br><i>* œil malicieux et amusé *</i>
<br><b>Aeswen</b> : J’aime être le centre d’intérêt plutôt. Je dois être un peu <i>* toussote exprès *</i> 
égocentrique, ou quelque chose comme ça. Je suis plutôt solitaire, donc rechercher la reconnaissance... 
Je sais pas... Je parle de mon enfance ou on enchaîne ? 
<br><i>* tire la langue *</i>
<br><b>Bardamu</b> : Euh... Oui, enchaînons...
<br>As-tu toujours été Mercenaire ? 
<br><b>Aeswen</b> : Ho non ! Point du tout mon cher ! 
<br>J'ai été longtemps sans attache, vivant au jour 
le jour sans me préoccuper du lendemain. Oups... ben c'est encore le cas aujourd'hui. 
Quoiqu'il en soit, j'ai par la suite rejoint un ami, chef des Larmes de Seluné à l'époque. 
Cette guilde proposait que chacun soit libre de son destin, et ne prônait pas les sempiternels 
&quot;Justice&quot; ou &quot;Bien et Mal&quot;. Mais je cherchais plus d'aventure, plus de risques, 
plus de force... Les Mercenaires se sont alors trouvés sur mon chemin - ou plutôt moi sur le leur - 
et j'ai fait en sorte de me faire une place parmi eux, ce qui s'est fait assez vite et bien. 
En fait peut-on vraiment dire que je suis un véritable mercenaire... hmm... 
<br><b>Bardamu</b> : Ce &quot;matricule&quot;  t’encombrerait-il ? ?
<br><b>Aeswen</b> : Absolument pas ! Je l’assume totalement, l’ayant choisi moi-même. 
Seulement je ne me trouve pas parmi eux pour l’argent, parce que j’aime la traque ou parce que 
j’ai besoin d’une excuse pour laisser libre cours à mes envies de faire couler le sang. 
Et le pire c’est qu’ils sont au courant. Je crois que la guilde des Mercenaires est 
vraiment intéressante, faite de gens intéressants, tous différents les uns des autres et 
dont les véritables buts diffèrent. Nous devons avoir quelque chose en commun, 
mais est-ce bien prudent de s’avancer sur sa nature ?
<br><b>Bardamu</b> : Qui t’a recruté ? 
<br><b>Aeswen</b> : Je me suis autorecruté si je puis dire, mais Kaali et ses compagnons de route ont 
facilité mon adhésion au groupe si ma mémoire est bonne. Je n'ai pas attendu qu'on vienne me chercher. 
Je sais ce que je veux, et mon verbe suffit généralement à l'obtenir. Et si comme dans le cas des 
Mercenaires cela colle parfaitement à mes objectifs, c'est encore plus simple.  
<br><b>Bardamu</b> : Pries-tu Balgur, toi aussi ? 
<br><b>Aeswen</b> : Allons, cette question n'honore pas ta vivacité d'esprit... 
<br><b>Bardamu</b> :  <i>* large sourire *</i>
<br>Tes paroles doivent sans doute anesthésier mon raisonnement... &quot;Narcisse&quot;
<br><i>* regard provocateur *</i>
<br><i>* Aeswen se lève et fait une légère révérence comique, puis se rassoie *</i>
<br><b>Aeswen</b> : Non mon bon ami, je ne prie pas Balgur ! Je ne prie personne et je suis mon propre maître. Par contre Balgur est un dieu qui a des affinités avec moi.  *regard rieur*
<br> S'il venait me rendre visite je suis sûr qu'on s'entendrait. Cela ne va pas plus loin ! 
<br><b>Bardamu</b> : Tu n’as pas toujours été un tueur... Qu’est-ce qui t’a poussé à franchir le pas ? 
<br><b>Aeswen</b> : Je ne sais pas ce que tu appelles un tueur... Néanmoins la réponse à ta question 
est soit &quot;non, je n’ai jamais été un tueur, et n'en suis toujours pas un&quot;, soit 
&quot;oui j'ai toujours été un tueur&quot;, à toi de choisir. Ma ligne de conduite n'a jamais changé, 
et le fait d'entrer parmi les Mercenaires ne l'a donc pas modifiée. Je ne sais pas quelle vision 
tu as de cette guilde, mais je crains qu'il te fasse un peu plus de recul pour l'appréhender. 
<br><b>Bardamu</b> : Tu fais erreur Aeswen... Je sais apprécier un ennemi de qualité.  
<br><i>* &oelig;eil sombre et pénétrant *</i>
<br><b>Aeswen</b> : Alors je crains que tu ne sois pas sur la bonne longueur d’onde. 
        </td>
        <td class="interview1_corps">
Nous ne sommes les ennemis de personne, ceux qui nous étiquettent comme tels n’ont pas tout compris. 
Si tu veux parler d’adversaires de qualité, alors là je suis déjà plus d’accord.
<br><b>Bardamu</b> : Soit... 
<br><i>* Adresse un sourire ambigu à l’elfe *</i>
<br>Te concernant, j’accepte le terme « adversaire »...
Aimes-tu tuer ? 
<br><b>Aeswen</b> : J'aime affronter de valeureux adversaires et leur prouver que je suis le meilleur. 
Cela peut - et le plus souvent doit - aller jusqu'à la mort, c'est ainsi que vont les choses. 
Le fait de tuer ne me procure pas un plaisir particulier, car c'est tout le combat et le frisson 
qui l’accompagne qui sont importants et que je recherche. Un vrai guerrier n'acceptera pas 
qu'on lui laisse la vie sauve... Il n'y a que les lâches et les faibles qui fuient l'issue fatale ! 
<br><b>Bardamu</b> : Hum... Hum... De &quot;valeureux adversaires&quot; dis-tu ?? 
Bien souvent, tu assènes le coup fatidique sur plus faible que toi... Pourtant...
<br><b>Aeswen</b> : Bien souvent ? Et puis-je savoir d’où tu tiens cette information ?  
<br><i>* Plisse les yeux *</i>
<br>Si tu veux parler des Harpies ou autre Diablotin qui barrent ma route, alors oui; 
mais cela tient plus du ménage que du combat. Quant aux aventuriers que j’affronte, 
s’ils n’étaient pas « valeureux » ils n’auraient pu se trouver aussi bas dans les souterrains, 
et lorsqu’ils se sont égarés je les aide à retrouver leur chemin. Non, je n’affronte pas les plus 
faibles que moi – sauf raison particulière – car cela n’est d’aucun intérêt. 
<br><i>* Bardamu s’amuse beaucoup des propos entourloupeurs tenus pas son invité *</i>
<br><b>Bardamu</b> : Tu es Maître d’armes, Maître Mage, Héros Sacré …
<br>Tu cherches à impressionner ou quoi ? ? 
<br><b>Aeswen</b> : Les choses vont d'elles-mêmes. On ne peut pas vraiment dire que j'ai poursuivi 
avidement cette débauche de renommée, mais j'avoue très bien m'en contenter. Et ces titres je ne me 
les suis pas donnés moi-même, on me les a attribué au cours du temps. Je dois donc les mériter, non ?  
<br><i>* sourire narquois *</i>
<br><b>Bardamu</b> : Sans doute…
<br>Mais, <i>* ton railleur *</i>  déploierais-tu la même assurance sans tes alliés ?   
<br><b>Aeswen</b> : C’est justement dans les affrontements en un contre un que je peux développer 
tout mon potentiel. Ma Magie et mes capacités naturelles m’assurent de grandes chances de victoire et 
au pire une égalité. Si maintenant tu t’imagines que je pense être capable d’annihiler seul une armée, 
tu te trompes. Je suis un archer, et je peux me débrouiller pour rester hors de portée 
du danger autant que possible, mais je n’ai pas la prétention d’être invincible. 
Seul on est une proie trop fragile : un passage, cinq aventuriers, et c’en est fini de moi. 
En groupe on fait réfléchir un peu plus ceux qui sont en mal de prestige et de sensations fortes. 
Et bizarrement ces gens nous attaquent souvent à trois contre un... de vrais guerriers...
<br><b>Bardamu</b> : Euh…Je note aussi : « Bateleur »…mmm…Ca, ça le fait moins…Non ? 
<br><b>Aeswen</b> : L'Arcane Majeur du Bateleur s'applique très bien à ma manière de voir l'existence. 
Un autre nom que l'on donne au Bateleur est le &quot;Magicien&quot;. Peut-être que cela le fait plus comme ça ? 
Et pour ta gouverne, c'est l'arcane majeur n°1... Comme tous les arcanes il a son rôle à jouer et 
il est complémentaire avec les autres. 
<br><b>Bardamu</b> : « Bateleur », dans mon jargon, signifie « pitre » !  
<br>Mais, de toute évidence, nous ne parlons pas le même langage…
<br><b>Aeswen</b> : Le Tarot et ses Arcanes... des clés pour comprendre l’univers et l’être. 
Chaque Mercenaire est détenteur de l’un des Arcanes Majeures. Je te laisserai te cultiver à ce sujet.  
<br><i>* léger sourire moqueur *</i>
<br><b>Bardamu</b> : Je crains d’avoir besoin d’ un « maître » pour mon initiation...
<br><i>* regard pétillant *</i>
<br>Pourquoi refuser d’élever une peluche ? Peut-être considères-tu que certains de tes « amis » se substituent avec talent à ces petits animaux de compagnie ? ? *petit rire*
<br><b>Aeswen</b> : Une peluche ! C’est exactement ça ! Je n’ai pas besoin de m’encombrer d’une créature 
qui piaille, qui court entre les jambes et qui a de 
        </td>
        <td class="interview1_corps">
grands yeux qui pétillent. 
Et semblablement tu n’as pas compris ce que je dis depuis le début. Je reconnais que je suis moins efficace que ces aventuriers accompagnés de leur familier, cependant cela ne me convient pas ! Seul ma force propre importe, je n’ai besoin d’aide d’aucune sorte.
<br><b>Bardamu</b> : Ok…Ok…T’agite pas tant, tu me rends nerveux !
<br>Qui bénéficie de ta confiance dans ces bas-fonds ? Qui sont tes amis ?
<br><b>Aeswen</b> : Confiance, un beau mot tout plein de subtilités. Je ne fais bien sûr pas confiance à tout le monde de la même manière, mais ça c’est mon problème. En fait il s’agit plus d’un respect mutuel. Je connais une grande partie des « puissants » qui parcourent les souterrains, et nous nous respectons comme il se doit. Lorsque nous nous croisons, une bonne poignée de main et une chope de bière, et nous reprenons chacun notre route. Rien ne dit que prochainement nous ne nous retrouverons pas face à face selon les circonstances, mais c’est ainsi que je vois les choses. Regarde nous deux, nous avons failli nous combattre il y a peu, et voilà que maintenant nous discutons comme des gens civilisés ; c’est à peu près ça ! Je n’ai pas besoin de citer de nom, mes amis se reconnaîtront.
<br><b>Bardamu</b> : Après quoi cours-tu ?
<br><b>Aeswen</b> : La Force, le Pouvoir, le dépassement de soi... Vivacité, créativité, volonté, astuce, dextérité, originalité, initiative, voilà ce qui anime le Bateleur, voilà ce qui m'anime ! Je maîtrise les armes et la magie, et ce pour moi seul... Je participe à une cause ou à une quête, j'accompagne des aventuriers, tant que j'y trouve un intérêt. Je reconnais la valeur des gens par leur force, force au combat ou force de caractère... Je ne trahis personne, pour la simple et bonne raison que je ne demande à personne de m'accorder sa confiance. Je cours, mais je sais aussi de temps en temps m'arrêter pour apprécier les choses accomplies et sentir le monde vibrer autour de moi. 
<br><b>Bardamu</b> : Hmm…Le monde ne tourne cependant pas autour de toi…Bateleur ! 
<br><b>Aeswen</b> : Et bien c’est un tort ! Hahaha !!  *immense sourire joyeux* 
<br><b>Bardamu</b> : Bien…Aeswen…Dis moi ? C’est le grand rassemblement en ce moment ? Quel dessein machiavélique allez-vous servir…Cette fois ? ?
<br><b>Aeswen</b> : Ha bon ? Mince, zutre, flûte et caca boudin ! Voilà qu’ils font encore la fête sans moi ! Si tu as des informations, donne-les moi sinon je vais être en retard ! 
<br><i>* soudain l’esprit ailleurs *</i> 
<br>Kaali et ses idées... les égoûts... j’te jure ! J’arriverai jamais à faire partir cette odeur, même après une dizaine de douches. 
Non très cher, il n’y a pas de rassemblement prévu pour les temps à venir...
<br><b>Bardamu</b> : Ah ? C’est donc toi… ? ?  
<br><i>* toussote *</i>
<br>Poursuivons avec un peu de légèreté. Virginie fut ton amante…Comment as-tu réagi à son départ ?
<br><i>* Aeswen Ecarquille les yeux puis jette des regards apeurés de tous côtés, soudain très mal *</i>
<br><b>Aeswen</b> : Hein ? Quoi !? Comment es-tu au courant ? Personne ne savait sinon nous deux !! C’est très embêtant tout ça, ça va nuire salement à ma réputation  
<br><i>* inspire un grand coup de dépit *</i>
<br>Tant pis, je suppose que c’est trop tard ! Oui, nous étions assez proches  
<br><i>* regard mutin *</i>
<br>D’ailleurs si je la revois elle aura droit à une bonne fessée. Partir sans même me dire adieu ! Mon pauvre petit cœur fragile... 
<br><i>* se gratte le nez *</i> 
<br><b>Bardamu</b> : Et aujourd’hui…Où en es-tu avec les Femmes ? L’une d’entre elles a-t-elle su te faire chavirer ?
<br><b>Aeswen</b> : Pas le moins du monde, célibataire jusqu’au bout des ongles. Mais qui sait ce que les souterrains me réservent ? Peut-être qu’après une grande bataille, après des éclats de voix joyeux et enflammés, une aventurière au regard clair saura-t-elle attirer mon attention ? Notre relation ne sera pas conventionnelle, ça je peux te l’assurer !
<br><b>Bardamu</b> : Héhéhé ! ! !  Je n’en doute pas un instant…Cependant, épargnons les détails à nos lecteurs…Hum…
<br>Aeswen…Merci… 
<br><i>* salue l’elfe d’un mouvement de tête, quelque peu étourdi par tant de vanité! *</i>
        </td>
	  </tr>
    </table>
</div>
-->

<div class="gauche2" id="Interview1">
    <table class="interview1">
      <tr class="interview1_titre">
        <td class="interview_titre" width="598px">L'INTERVIEW DE LA SEMAINE : <br>
LE BATELEUR
		</td>
      </tr>
      <tr class="interview1_intro">
        <td class="interview1_intro" >
Lorsque Bardamu reçut cette missive, il en resta ahuri.<br>

Aeswen y réclamait une interview, étalant avec un aplomb peu ordinaire, ses talents de guerrier, mage, rh&eacute;teur... 
<br>Beau mec... Bref, tout y était !<br>
Un sourire espiègle se dessina sur les lèvres du nain, tout à fait disposé à combler l’efle...
		</td>
      </tr>
</table>
</div>

<div class="gauche21" id="Interview1">
    <table class="interview1">
      <tr class="interview1_corps">
        <td class="interview1_corps" width="194px">
<b>Bardamu</b> : Aeswen... Sois le bienvenu. Installe-toi, et attaquons !
<br>Alors…Pourquoi solliciter cette interview ? 
<br><b>Aeswen</b> :Tout simplement parce que je le mérite, et que si tout le monde n'a pas entendu 
parler de moi - ce qui est un grand tort - et bien ce sera chose faite.   
<br><i>* sourire carnassier *</i> 
<br>En fait je crois que j'aime bien parler de moi, et puis j'aime bien que les autres parlent de moi aussi. 
<br><i>* regard brillant *</i>
<br><b>Bardamu</b> : Aurais-tu soif de reconnaissance, Aeswen ? ?  
<br><i>* œil malicieux et amusé *</i>
<br><b>Aeswen</b> : J’aime être le centre d’intérêt plutôt. Je dois être un peu <i>* toussote exprès *</i> 
égocentrique, ou quelque chose comme ça. Je suis plutôt solitaire, donc rechercher la reconnaissance... 
Je sais pas... Je parle de mon enfance ou on enchaîne ? 
<br><i>* tire la langue *</i>
<br><b>Bardamu</b> : Euh... Oui, enchaînons...
<br>As-tu toujours été Mercenaire ? 
<br><b>Aeswen</b> : Ho non ! Point du tout mon cher ! 
<br>J'ai été longtemps sans attache, vivant au jour 
le jour sans me préoccuper du lendemain. Oups... ben c'est encore le cas aujourd'hui. 
Quoiqu'il en soit, j'ai par la suite rejoint un ami, chef des Larmes de Seluné à l'époque. 
Cette guilde proposait que chacun soit libre de son destin, et ne prônait pas les sempiternels 
&quot;Justice&quot; ou &quot;Bien et Mal&quot;. Mais je cherchais plus d'aventure, plus de risques, 
plus de force... Les Mercenaires se sont alors trouvés sur mon chemin - ou plutôt moi sur le leur - 
et j'ai fait en sorte de me faire une place parmi eux, ce qui s'est fait assez vite et bien. 
En fait peut-on vraiment dire que je suis un véritable mercenaire... hmm... 
<br><b>Bardamu</b> : Ce &quot;matricule&quot;  t’encombrerait-il ? ?
<br><b>Aeswen</b> : Absolument pas ! Je l’assume totalement, l’ayant choisi moi-même. 
Seulement je ne me trouve pas parmi eux pour l’argent, parce que j’aime la traque ou parce que 
j’ai besoin d’une excuse pour laisser libre cours à mes envies de faire couler le sang. 
Et le pire c’est qu’ils sont au courant. Je crois que la guilde des Mercenaires est 
vraiment intéressante, faite de gens intéressants, tous différents les uns des autres et 
dont les véritables buts diffèrent. Nous devons avoir quelque chose en commun, 
mais est-ce bien prudent de s’avancer sur sa nature ?
<br><b>Bardamu</b> : Qui t’a recruté ? 
<br><b>Aeswen</b> : Je me suis autorecruté si je puis dire, mais Kaali et ses compagnons de route ont 
facilité mon adhésion au groupe si ma mémoire est bonne. Je n'ai pas attendu qu'on vienne me chercher. 
Je sais ce que je veux, et mon verbe suffit généralement à l'obtenir. Et si comme dans le cas des 
Mercenaires cela colle parfaitement à mes objectifs, c'est encore plus simple.  
<br><b>Bardamu</b> : Pries-tu Balgur, toi aussi ? 
<br><b>Aeswen</b> : Allons, cette question n'honore pas ta vivacité d'esprit... 
<br><b>Bardamu</b> :  <i>* large sourire *</i>
<br>Tes paroles doivent sans doute anesthésier mon raisonnement... &quot;Narcisse&quot;
<br><i>* regard provocateur *</i>
<br><i>* Aeswen se lève et fait une légère révérence comique, puis se rassoie *</i>
<br><b>Aeswen</b> : Non mon bon ami, je ne prie pas Balgur ! Je ne prie personne et je suis mon propre maître. Par contre Balgur est un dieu qui a des affinités avec moi.  *regard rieur*
<br> S'il venait me rendre visite je suis sûr qu'on s'entendrait. Cela ne va pas plus loin ! 
<br><b>Bardamu</b> : Tu n’as pas toujours été un tueur... Qu’est-ce qui t’a poussé à franchir le pas ? 
<br><b>Aeswen</b> : Je ne sais pas ce que tu appelles un tueur... Néanmoins la réponse à ta question 
est soit &quot;non, je n’ai jamais été un tueur, et n'en suis toujours pas un&quot;, soit 
&quot;oui j'ai toujours été un tueur&quot;, à toi de choisir. Ma ligne de conduite n'a jamais changé, 
et le fait d'entrer parmi les Mercenaires ne l'a donc pas modifiée. Je ne sais pas quelle vision 
tu as de cette guilde, mais je crains qu'il te fasse un peu plus de recul pour l'appréhender. 
<br><b>Bardamu</b> : Tu fais erreur Aeswen... Je sais apprécier un ennemi de qualité.  
<br><i>* &oelig;eil sombre et pénétrant *</i>
<br><b>Aeswen</b> : Alors je crains que tu ne sois pas sur la bonne longueur d’onde. 
        </td>
	  </tr>
    </table>
</div>
<div class="gauche22" id="Interview1">
    <table class="interview1">
      <tr class="interview1_corps">
        <td class="interview1_corps" width="194px">
Nous ne sommes les ennemis de personne, ceux qui nous étiquettent comme tels n’ont pas tout compris. 
Si tu veux parler d’adversaires de qualité, alors là je suis déjà plus d’accord.
<br><b>Bardamu</b> : Soit... 
<br><i>* Adresse un sourire ambigu à l’elfe *</i>
<br>Te concernant, j’accepte le terme « adversaire »...
Aimes-tu tuer ? 
<br><b>Aeswen</b> : J'aime affronter de valeureux adversaires et leur prouver que je suis le meilleur. 
Cela peut - et le plus souvent doit - aller jusqu'à la mort, c'est ainsi que vont les choses. 
Le fait de tuer ne me procure pas un plaisir particulier, car c'est tout le combat et le frisson 
qui l’accompagne qui sont importants et que je recherche. Un vrai guerrier n'acceptera pas 
qu'on lui laisse la vie sauve... Il n'y a que les lâches et les faibles qui fuient l'issue fatale ! 
<br><b>Bardamu</b> : Hum... Hum... De &quot;valeureux adversaires&quot; dis-tu ?? 
Bien souvent, tu assènes le coup fatidique sur plus faible que toi... Pourtant...
<br><b>Aeswen</b> : Bien souvent ? Et puis-je savoir d’où tu tiens cette information ?  
<br><i>* Plisse les yeux *</i>
<br>Si tu veux parler des Harpies ou autre Diablotin qui barrent ma route, alors oui; 
mais cela tient plus du ménage que du combat. Quant aux aventuriers que j’affronte, 
s’ils n’étaient pas « valeureux » ils n’auraient pu se trouver aussi bas dans les souterrains, 
et lorsqu’ils se sont égarés je les aide à retrouver leur chemin. Non, je n’affronte pas les plus 
faibles que moi – sauf raison particulière – car cela n’est d’aucun intérêt. 
<br><i>* Bardamu s’amuse beaucoup des propos entourloupeurs tenus pas son invité *</i>
<br><b>Bardamu</b> : Tu es Maître d’armes, Maître Mage, Héros Sacré …
<br>Tu cherches à impressionner ou quoi ? ? 
<br><b>Aeswen</b> : Les choses vont d'elles-mêmes. On ne peut pas vraiment dire que j'ai poursuivi 
avidement cette débauche de renommée, mais j'avoue très bien m'en contenter. Et ces titres je ne me 
les suis pas donnés moi-même, on me les a attribué au cours du temps. Je dois donc les mériter, non ?  
<br><i>* sourire narquois *</i>
<br><b>Bardamu</b> : Sans doute…
<br>Mais, <i>* ton railleur *</i>  déploierais-tu la même assurance sans tes alliés ?   
<br><b>Aeswen</b> : C’est justement dans les affrontements en un contre un que je peux développer 
tout mon potentiel. Ma Magie et mes capacités naturelles m’assurent de grandes chances de victoire et 
au pire une égalité. Si maintenant tu t’imagines que je pense être capable d’annihiler seul une armée, 
tu te trompes. Je suis un archer, et je peux me débrouiller pour rester hors de portée 
du danger autant que possible, mais je n’ai pas la prétention d’être invincible. 
Seul on est une proie trop fragile : un passage, cinq aventuriers, et c’en est fini de moi. 
En groupe on fait réfléchir un peu plus ceux qui sont en mal de prestige et de sensations fortes. 
Et bizarrement ces gens nous attaquent souvent à trois contre un... de vrais guerriers...
<br><b>Bardamu</b> : Euh…Je note aussi : « Bateleur »…mmm…Ca, ça le fait moins…Non ? 
<br><b>Aeswen</b> : L'Arcane Majeur du Bateleur s'applique très bien à ma manière de voir l'existence. 
Un autre nom que l'on donne au Bateleur est le &quot;Magicien&quot;. Peut-être que cela le fait plus comme ça ? 
Et pour ta gouverne, c'est l'arcane majeur n°1... Comme tous les arcanes il a son rôle à jouer et 
il est complémentaire avec les autres. 
<br><b>Bardamu</b> : « Bateleur », dans mon jargon, signifie « pitre » !  
<br>Mais, de toute évidence, nous ne parlons pas le même langage…
<br><b>Aeswen</b> : Le Tarot et ses Arcanes... des clés pour comprendre l’univers et l’être. 
Chaque Mercenaire est détenteur de l’un des Arcanes Majeures. Je te laisserai te cultiver à ce sujet.  
<br><i>* léger sourire moqueur *</i>
<br><b>Bardamu</b> : Je crains d’avoir besoin d’ un « maître » pour mon initiation...
<br><i>* regard pétillant *</i>
<br>Pourquoi refuser d’élever une peluche ? Peut-être considères-tu que certains de tes « amis » se substituent avec talent à ces petits animaux de compagnie ? ? *petit rire*
<br><b>Aeswen</b> : Une peluche ! C’est exactement ça ! Je n’ai pas besoin de m’encombrer d’une créature 
qui piaille, qui court entre les jambes et qui a de 
        </td>
	  </tr>
    </table>
</div>
<div class="gauche23" id="Interview1">
    <table class="interview1">
      <tr class="interview1_corps">
        <td class="interview1_corps">
grands yeux qui pétillent. 
Et semblablement tu n’as pas compris ce que je dis depuis le début. Je reconnais que je suis moins efficace que ces aventuriers accompagnés de leur familier, cependant cela ne me convient pas ! Seul ma force propre importe, je n’ai besoin d’aide d’aucune sorte.
<br><b>Bardamu</b> : Ok…Ok…T’agite pas tant, tu me rends nerveux !
<br>Qui bénéficie de ta confiance dans ces bas-fonds ? Qui sont tes amis ?
<br><b>Aeswen</b> : Confiance, un beau mot tout plein de subtilités. Je ne fais bien sûr pas confiance à tout le monde de la même manière, mais ça c’est mon problème. En fait il s’agit plus d’un respect mutuel. Je connais une grande partie des « puissants » qui parcourent les souterrains, et nous nous respectons comme il se doit. Lorsque nous nous croisons, une bonne poignée de main et une chope de bière, et nous reprenons chacun notre route. Rien ne dit que prochainement nous ne nous retrouverons pas face à face selon les circonstances, mais c’est ainsi que je vois les choses. Regarde nous deux, nous avons failli nous combattre il y a peu, et voilà que maintenant nous discutons comme des gens civilisés ; c’est à peu près ça ! Je n’ai pas besoin de citer de nom, mes amis se reconnaîtront.
<br><b>Bardamu</b> : Après quoi cours-tu ?
<br><b>Aeswen</b> : La Force, le Pouvoir, le dépassement de soi... Vivacité, créativité, volonté, astuce, dextérité, originalité, initiative, voilà ce qui anime le Bateleur, voilà ce qui m'anime ! Je maîtrise les armes et la magie, et ce pour moi seul... Je participe à une cause ou à une quête, j'accompagne des aventuriers, tant que j'y trouve un intérêt. Je reconnais la valeur des gens par leur force, force au combat ou force de caractère... Je ne trahis personne, pour la simple et bonne raison que je ne demande à personne de m'accorder sa confiance. Je cours, mais je sais aussi de temps en temps m'arrêter pour apprécier les choses accomplies et sentir le monde vibrer autour de moi. 
<br><b>Bardamu</b> : Hmm…Le monde ne tourne cependant pas autour de toi…Bateleur ! 
<br><b>Aeswen</b> : Et bien c’est un tort ! Hahaha !!  *immense sourire joyeux* 
<br><b>Bardamu</b> : Bien…Aeswen…Dis moi ? C’est le grand rassemblement en ce moment ? Quel dessein machiavélique allez-vous servir…Cette fois ? ?
<br><b>Aeswen</b> : Ha bon ? Mince, zutre, flûte et caca boudin ! Voilà qu’ils font encore la fête sans moi ! Si tu as des informations, donne-les moi sinon je vais être en retard ! 
<br><i>* soudain l’esprit ailleurs *</i> 
<br>Kaali et ses idées... les égoûts... j’te jure ! J’arriverai jamais à faire partir cette odeur, même après une dizaine de douches. 
Non très cher, il n’y a pas de rassemblement prévu pour les temps à venir...
<br><b>Bardamu</b> : Ah ? C’est donc toi… ? ?  
<br><i>* toussote *</i>
<br>Poursuivons avec un peu de légèreté. Virginie fut ton amante…Comment as-tu réagi à son départ ?
<br><i>* Aeswen Ecarquille les yeux puis jette des regards apeurés de tous côtés, soudain très mal *</i>
<br><b>Aeswen</b> : Hein ? Quoi !? Comment es-tu au courant ? Personne ne savait sinon nous deux !! C’est très embêtant tout ça, ça va nuire salement à ma réputation  
<br><i>* inspire un grand coup de dépit *</i>
<br>Tant pis, je suppose que c’est trop tard ! Oui, nous étions assez proches  
<br><i>* regard mutin *</i>
<br>D’ailleurs si je la revois elle aura droit à une bonne fessée. Partir sans même me dire adieu ! Mon pauvre petit cœur fragile... 
<br><i>* se gratte le nez *</i> 
<br><b>Bardamu</b> : Et aujourd’hui…Où en es-tu avec les Femmes ? L’une d’entre elles a-t-elle su te faire chavirer ?
<br><b>Aeswen</b> : Pas le moins du monde, célibataire jusqu’au bout des ongles. Mais qui sait ce que les souterrains me réservent ? Peut-être qu’après une grande bataille, après des éclats de voix joyeux et enflammés, une aventurière au regard clair saura-t-elle attirer mon attention ? Notre relation ne sera pas conventionnelle, ça je peux te l’assurer !
<br><b>Bardamu</b> : Héhéhé ! ! !  Je n’en doute pas un instant…Cependant, épargnons les détails à nos lecteurs…Hum…
<br>Aeswen…Merci… 
<br><i>* salue l’elfe d’un mouvement de tête, quelque peu étourdi par tant de vanité! *</i>
        </td>
	  </tr>
    </table>
</div>



<div class="droite1" id="Edito">
      <table class="edito">
          <tr class="edito_titre">
            <td  class="edito_titre">EDITO</td>
          </tr>
          <tr class="edito_corps">
            <td class="edito_corps">
Ce tout nouveau numéro accueille Albert, héros d’un feuilleton delainien dont vous pourrez suivre les péripéties à chaque parution.
<br>La Voix vous présente également un aventurier exubérant qui, chose extrêmement rare, détient le double titre de Maître Mage et Maître d’armes.
<br>Vous découvrirez aussi les Niouzes qui relatent les événements marquants qui ont secoué les différents étages des souterrains.
<br>Sans oublier une interview consacrée aux jeunes aventuriers.

<br>Pour finir, je vous rappelle que la Voix vous appartient. Si vous souhaitez participer plus activement à sa vie, ses portes vous sont ouvertes. 
<br>
<br>Bonne lecture à tous !
<br>
<br><b>La rédaction.</b>
            </td>
          </tr>
    </table>
</div>

<div class="droite2" id="Interview2">
    <table class="interview2">
      <tr class="interview2_titre">
        <td colspan="3" class="interview2_titre">INTERVIEW<br>
LA NOUVELLE GENERATION
		</td>
      </tr>
      <tr class="interview2_intro">
        <td colspan="3" class="interview2_intro">
Mandrak, elfe du Peuple des Loups, et l’humain Jildrake, <br>
fraîchement débarqués dans les profondeurs, se livrent à Bardamu. 
<br>Tous trois sont confortablement installés dans une auberge gorgée d'imbibés...<br>&nbsp;
		</td>
      </tr>
      <tr class="interview2_corps">
        <td class="interview2_corps">
<b>Bardamu</b> :  Salut messieurs... 
<br>GARGOTIER !!?? Trois chopines!!!
Bien... Alors, comment avez-vous atterri dans les profondeurs ?
<br><b>Mandrak</b> : Ce qui m'a attiré dans ces sombres souterrains, c'est l'ennui que me 
procurait la lecture de ces vieux grimoires et parchemins à la Tour des Mages... 
Bref, ma curiosité exacerbée et le manque d'exercice physique !!!
(Jildrake se concentre sur les danseuses affriolantes qui se tortillent devant lui, et semble 
quelque peu effrayer par leur souplesse…Bref, il en reste sans voix…Bardamu s’en inquiète, 
puis se resaisit…)
<br><b>Bardamu</b> :  Euh…Quels sont vos tout premiers sentiments ?
<br><b>Mandrak</b> : Mes premiers sentiments par rapport aux souterrains... Et bien j'ai vite pu 
constater qu'ils sont truffés de vils personnes animés par la soif de détruire son prochain...
Que du bonheur en compte...
<br><b>Bardamu</b> :  mmm…Quels objectifs vous êtes-vous fixez ?
<br><b>Mandrak</b> : Mes objectifs sont de rencontrer des aventuriers, lier des amitiés et défendre 
les loups égarés par la folie de Malkiar.
(Jildrake toujours envoûté par les accrobaties des ballerines garde le silence…)
<br><b>Bardamu</b> : Des amis, déjà ? ?
<br><b>Mandrak</b> : Bien sûr ! Par le grand Sachem plutôt deux fois qu'une !! Les amitiés sont vitales 
dans ce monde rempli de créatures malsaines. Nous avons même créé une Ligue, Le Peuple des Loups. 
Tout frais de cet été !
<br><b>Jildrake</b> : Pas encore, mais je pense que ça viendra tout seul avec un peuu de temps...
<br><b>Bardamu</b> :  Ah ! Jildrake... Te revoilà parmi nous ! Bien…Avez-vous peur, ou avancez-vous 
dans la sérénité ?
<br><b>Mandrak</b> : Peur de quoi Morbleu ? De ces créatures maléfiques, de ces aventuriers mal 
intentionnés ? Mouarf, non, je pense errer en toute tranquillité dans les souterrains. 
        </td>
        <td class="interview2_corps">
Mais bon, je viens d'arriver, si jamais tu me reposes la question dans un peu de temps, 
ma réponse pourrait changer…
<br><b>Jildrake</b> : Peur non! Serein, moui...
<br>Curieux plutot ...
<br><b>Bardamu</b> : Bah... Si une jambe levée par une « entraîneuse » te laisse rêveur, t’es pas au bout de tes surprises !
Et…Vous destinez-vous au Bien, au Mal... ?
<br><b>Mandrak</b> : Le bien, le Mal? Pour qui, pour quoi... A vrai dire la philosophie n'est pas mon fort. Ce qui importe c'est l'exactitude de mes sorts ! Mais bon, je pense être considéré comme &quot;gentil&quot; par mes pairs..
<br><b>Jildrake</b> : L'avenir le dira...
<br><b>Bardamu</b> : Etes-vous déçus ou enthousiasmés par les lieux?
<br><b>Mandrak</b> : Les lieux sont ,certes, mal famés mais je ne désespère pas de trouver l'Eden où tous les peuples et races vivent ensemble, main dans la main.. Dans la joie et l'allégresse !
<br><b>Bardamu</b> : T’es sérieux?? Euh... Hum... Vous promenez-vous souvent en place publique? 
<br><b>Mandrak</b> : J’y suis passé, surtout au début, maintenant je n'en ai plus utilité..Peut-être ai-je tort....
<br><b>Jildrake</b> : Non, pas pour le moment
<br><b>Bardamu</b> : Ah ! Bah je vous le recommande vivement…Beaucoup d’événements y sont révélés…Autre chose ?
<br><b>Mandrak</b> : Savez-vous que Malkiar porte un T-shirt rose étriqué et se balade toujours 
accompagné d'un caniche nain?  Euh, c'est comme ça que je l'ai rêvé!!
(Jildrake et Bardamu écarquillent de grands yeux incrédules... Et tentent de retrouver leurs esprits distraits par la fantaisie de l’elfe.)
<br><b>Jildrake</b> : Pour l'instant non, je ne suis pas là depuis longtemps !
<br><b>Bardamu</b> : Je vous remercie de vous être livrés... Arffff... 
Les contorsionnistes ont fini 
leur show... Tu vas t’en remettre Jildrake ??
<br>TAVERNIER !!! Un echopine pour mon ami !!
        </td>
	  </tr>
    </table>
</div>

<div class="gauche1" id="Albert">
    <table class="albert">
      <tr>
        <td class="albert_titre">
		  ALBERT
		</td>
      </tr>
      <tr class="albert_intro">
        <td class="albert_intro">
1 - On abat bien les aventuriers
		</td>
      </tr>
      <tr class="albert_corps">
        <td class="albert_corps">
Albert pensa tristement que le temps était venu pour lui de changer de monture. Il ne pouvait nier qu’elle l’ait bien et longuement servi. 
Combien d’années déjà ? Trop se dit-il en la regardant. 
Il se frotta l’arête douloureuse de son nez avec un doigt en repensant aux difficultés qu’il avait dû affronter pour parvenir à sa situation actuelle. 
N’avait-il pas un peu gâché sa vie par son obsession ? 
Mais qu’aurait-il pu faire d’autre ? F
ermier, trimant jours après jours, le dos courbé ? 
Marchand, piaillant dans une foire les mérites illusoires d’un produit banal ? 
Non, non, il n’y avait rien à regretter. Il regarda à nouveau sa paire de lunette usée. 
Il était vraiment temps d’en changer la monture. 
<br> - Maître Saquet ? 
<br> - Oui, Albert. Quoi encore ? 
<br> - Je devrais passer chez l’oculiste pour qu’il me fasse de nouvelles lunettes. 
Comme son magasin ferme dans peu de temps, je me demandais si je pouvais partir un peu plus tôt, ... 
<br> - Faites comme vous voulez, mais les dossiers que je vous ai confiés doivent être terminés pour demain soir. 
Albert se leva rapidement et se dirigea vers la porte en se promettant de se montrer digne de la confiance de Monsieur Saquet. 
De nos jours, il était si difficile de trouver un poste de comptable comme le sien.
		</td>
      </tr>
  </table>
</div>

<div class="centre1" id="Nyouzes">
    <table class="horoscope">
      <tr class="horoscope_titre">
        <td colspan="3" class="horoscope_titre">
Les Nyouzes des Souterrains.
		</td>
      </tr>
      <tr class="horoscope_corps">
        <td class="horoscope_corps">
	      Aux &eacute;tages sup&eacute;rieurs, le m&ecirc;me sc&eacute;nario se r&eacute;p&eacute;te &agrave; l&rsquo;infini&nbsp;: les tueurs tuent, les voleurs volent et les miliciens grondent tous ces effront&eacute;s. 
		  <br>Cependant, la cuv&eacute;e &laquo;&nbsp;nouveaux d&eacute;barqu&eacute;s dans les bas-fonds&nbsp;&raquo; annonce un vent de fra&icirc;cheur. Les derniers descendus auraient un certain bagou et en useraient avec fougue&hellip;
	      <br>On annonce par ailleurs une rentr&eacute;e sereine pour les &eacute;tudiants de Diwan, pendant que les Anges Noirs d&eacute;clarent la guerre aux Karrig An Ankou qui se f&eacute;licitent d&rsquo;occuper les trois premi&egrave;res places au classement des serial killers les plus assidus. 
		  <br>Quant &agrave; Psyquick, il dispense son enseignement &agrave; l&rsquo;engeance malkiaresque qui, sous le charme, combat au c&ocirc;t&eacute; du tortionnaire.
		</td>
        <td rowspan="2" class="horoscope_corps">
	      Au Masque de Mort&hellip;WAOW&hellip;Que d&rsquo;&eacute;v&eacute;nements&nbsp;!
	      Apr&egrave;s avoir &eacute;chang&eacute; son ouvrage &eacute;nigmatique contre l&rsquo;anneau de Ventarion d&rsquo;Athalante, Ingeloakmaslimillizan a disparu bien plus sobrement qu&rsquo;il &eacute;tait apparu. On apprend d&rsquo;ailleurs que seul un fid&egrave;le de Balgur serait apte &agrave; comprendre le manuscrit. Balgur dont le temple a &eacute;t&eacute; profan&eacute;&nbsp;! Plusieurs objets y ont &eacute;t&eacute; d&eacute;rob&eacute;s sous le nez de la garde post&eacute;e aux abords du temple. Le Dieu gronde, et Kaali aurait promis de punir les audacieux dont les identit&eacute;s ne nous sont pas d&eacute;voil&eacute;es.
	      Mais, c&rsquo;est un peu la d&eacute;ception &agrave; cet &eacute;tage o&ugrave; nous attendions une guerre qui semblerait-il n&rsquo;&eacute;clatera jamais entre CDJ et Balguriens. Ben alors&nbsp;?&nbsp;? 
	      Autre histoire farfelue&nbsp;: deux CDJ, Nastywoks et Spank, fid&egrave;les escorteurs d&rsquo;Ecate et de D&eacute;lia, ont enlev&eacute; les deux fillettes&nbsp;!&nbsp;! Nastywoks a ouvert leurs c&oelig;urs pendant que Spank m&eacute;langeait leur sang. De cet &eacute;trange rituel est n&eacute; le Temple de Falis qui abritent les deux ressuscit&eacute;es. Les deux jeunes femmes ont recouvr&eacute; leur sant&eacute;, mais sont contraintes &agrave; se murer dans le Temple de Falis qui les prot&egrave;ge de la col&egrave;re de Balgur.
	      Les fid&egrave;les d&rsquo;Ecatis, quant &agrave; eux, se font discrets&hellip;A quand un temple pour accueillir les adorateurs de la sombre D&eacute;esse&nbsp;?&nbsp;?
		</td>
        <td rowspan="2" class="horoscope_corps">
	      Un &eacute;tage en dessous, le Pays des Gel&eacute;es est secou&eacute; par une dr&ocirc;le de guerre qui &eacute;clate et r&eacute;-&eacute;clate &agrave; chaque rencontre entre Nihilistes et Sillonneurs Gris. Un premier assaut des Nihilistes leur avait apport&eacute; une victoire &eacute;crasante. Mais, les Sillonneurs, refusant de plier l&rsquo;&eacute;chine ont lanc&eacute; une contre offensive, raflant plusieurs vies MDMiennes. Puis, chaque groupe, redoutant la fr&eacute;n&eacute;sie de l&rsquo;autre, s&rsquo;est pr&eacute;cipit&eacute; dans le Temple de Balgur (dont les &eacute;difices sont d&eacute;cid&eacute;ment tr&egrave;s fr&eacute;quent&eacute;s ces derniers temps&hellip;). Cet entassement forc&eacute; aurait exalt&eacute; les sentiments du Sillonneur Grubas qui a d&eacute;ploy&eacute; sa passion sur la t&ecirc;te de Naine Blanche. La pauvre a du aussi encaisser les avances de FIX&hellip;En p&acirc;moison devant la lingerie fine de la naine sans barbe. Malheureusement tout ce d&eacute;ballage de s&eacute;duction s&rsquo;est sold&eacute; par un &eacute;chec cuisant. Naine Blanche s&rsquo;en est all&eacute;e, fid&egrave;le aux siens, abandonnant les deux &eacute;pris sur le carreau... 
	      La qu&ecirc;te des m&eacute;daillons, quant &agrave; elle s&rsquo;est enfin achev&eacute;e. Les porteurs qui ont mis des lustres &agrave; se trouver ont ouvert les marches, lib&eacute;rant un flot d'aventuriers fougueux dans l&rsquo;Antichambre. Certains doivent d&eacute;j&agrave; se frotter les mains&hellip;
        </td>
	  </tr>
      <tr class="horoscope_corps">
	    <td class="horoscope_corps" align="center" valign="middle">
		  <a href="pubs/Trolls_Royce-Silver_Goule.jpg" target="_blank" class="blanc">
		    <img src="pubs/Trolls_Royce-Silver_Goule.jpg" height="188" width="10">
		  </a>
		</td>
	  </tr>
      <tr class="horoscope_corps">
        <td class="horoscope_corps">
	      Encore plus profond, les Valshcezc (ceux qui se frottent les mains&hellip;), renouant avec leurs amiti&eacute;s pass&eacute;es, trucident a tour de bras les &eacute;gar&eacute;s qui osent s&rsquo;aventurer dans <strong>l&rsquo;Antichambre de l&rsquo;Enfer</strong>. Leur euphorie ajout&eacute;e aux nouveaux dons dont b&eacute;n&eacute;ficient les D&eacute;mons Mineurs rendant le cinqui&egrave;me sous sol impraticable.
		</td>
        <td class="horoscope_corps">
	      Enfin, les &eacute;tages inf&eacute;rieurs sont d&eacute;sert&eacute;s. Les morbelins ont effectu&eacute; quelques travaux &agrave; <strong>l&rsquo;entr&eacute;e du Ch&acirc;teau</strong>, rendant son acc&egrave;s difficile aux aventuriers peu fortun&eacute;s. Pire, il ne suffirait plus de s&rsquo;acquitter du droit d&rsquo;entr&eacute;e pour y p&eacute;n&eacute;trer, il faudrait aussi convaincre les Morbelins dont les humeurs sont souvent rageuses&hellip;
		</td>
        <td class="horoscope_corps">
          Pour finir, une note de po&eacute;sie&nbsp;: Le p&egrave;re Simeon en personne a c&eacute;l&eacute;br&eacute; deux unions dans le temple d&rsquo;Io du Cr&acirc;ne. Ainsi Carole, elfette escorteur de l&rsquo;Archidiacre, a-t-elle jur&eacute; fidelit&eacute; &agrave; Claymore, Paladin nain, Capitaine de Gilead , pendant que X&eacute;l&egrave;s, Chevalier elfe, Lieutenant des Terres Perdues de Gilead engageait sa vie &agrave; celle d&rsquo; Elanor, elfette forgeron des Gilead. Vive les mari&eacute;s&nbsp;!&nbsp;!
		</td>
	  </tr>
    </table>
</div>


<div class="centre3" id="Pubs">
	<table class="pub">
      <tr>
        <td colspan="2" class="pub_titre">Pubs</td>
      </tr>
      <tr class="pub_corps">
        <td class="pub_corps">
<center><b>La Casa da FANO</b></center>

Fano, marchant et Gérant de la boutique situé au nord-est de l’antichambre bénéficie d’une solide expérience.
Son commerce vous propose un grand choix d'objets divers et de bonne qualité. 
<br>Précipitez-vous ! !
	    </td>
        <td class="pub_corps">
		   Vous sentez le poisson pourri ? 
		   On ne peut rien pour votre stupidit&eacute; qui vous a fait garder des poissons pendant une &eacute;ternit&eacute;. 
		   Par contre, le spray vous permettra d'exhaler une odeur bien plus rafra&icirc;chissante.<br>
           <center><span class="Mirreck_spray">Mirreck spray!!!</span></center>
        </td>
      </tr>
    </table>
</div>

<div class="droite11" id="Courrier">
	<table class="courrier">
	  <tr class="courrier_titre">
        <td class="courrier_titre">
		Le Courrier des Lecteurs
		</td>
	  </tr>
	  <tr class="courrier_corps">
          <td class="courrier_corps">
<b>Familier d'Akasha</b>:
 hiiik hiik hiiik couic couic! 
<br>
<br>(dans un soucis de clarté Akasha nous livre la version traduite): 
<br>&quot;bonjour je viens d'emerger des ténébres. Mon nom est Mary, Bloody Mary. 
Grace à mes deux tentacules je peux lancer des etincelles sur mes amis et 
cela les rend plus... COUIC&quot;
<br>(fin de la vie de Mary, renvoyé d'où il 	venait par le coup de pate d'une bete trop grosse pour lui)

<br>
<br>	        <b>La Voix des Souterrains</b> :<br><br>
			Une aventure qui arrive malheuresement trop souvent, comme ont pu le constater certains membres de notre &eacute;quipe.<br>
			L'équipe toute enti&egrave;re s'associe &agrave; votre peine.<br>
            La r&eacute;daction.

         </td>
	  </tr>
	</table>
</div>

<div class="droite12" id="Contact">
  <table class="musique">
    <tr class="musique_corps">
	  <td class="musique_titre">
	    Pour nous contacter :<br>
		<a href="mailto:voix@jdr-delain.net">La Voix des Souterrains</a>
      </td>
	  <td class="musique_titre">
	  Vous &ecirc;tes un aventurier en quete de monnaie? <br>
	  <a href="mailto:voix@jdr-delain.net?subject='PetiteAnnonce'">Envoyez nous vos annonces</a> 
      </td>
	  <td class="musique_titre">
	  Votre guilde tiens commerce d'artisanat local?<br>
  		<a href="mailto:voix@jdr-delain.net?subject='Publicit&eacute;'">Envoyez nous vos publicit&eacute;s</a>
      </td>
	  <td class="musique_titre">
Vous avez croisé des aventuriers loquaces dont les paroles sont dignes d'un Van Damme ? <br>
  		<a href="mailto:voix@jdr-delain.net?subject='VanDamme'">Ecrivez-nous !</a>
	  </td>
	</tr>
  </table>
</div>

<div class="droite3" id="PA_1">
    <table class="musique">
      <tr>
        <td class="musique_titre">
LA VOIX ANNONCE
		</td>
        <td  class="musique_titre">
SONDAGE
		</td>
      </tr>
      <tr class="musique_corps">
        <td rowspan="5" class="musique_corps" style="vertical-align:top">
La Voix solicite votre participation pour:
<br>
<br>-Les INTERVIEWS : Vous subissez une révolution? Votre ennemi de toujours vous poursuit avec acharnement? Vous venez de débarquer dans les Souterrains? Vous vivez une grande love story? Votre peluche vous en fait voir de toutes les couleurs? Vous désirez vous faire connaître?
<br><br>Autant de raisons pour rencontrer nos interviewers!!
<br>-Les NIOUZES : Une scène étrange se déroule sous votre nez? Une guerre éclate sous vos yeux? Des monstres nouveaux apparaissent? L’archtecture se modifie? Une cérémonie est fêtée (mariage, naissance...)?
<br>Si vous êtes témoins d’événements susceptibles de faire l’actualité, pensez à La Voix!!!
<br>
<br>-ANNONCES, PUBS... : Vous souhaitez bénéficier d’un peu de pub? Vous souhaitez lancer un défi, ou déclarer une guerre?
<br>
<br>N’hésitez plus!! Ecrivez à la <a href="mailto:voix@jdr-delain.net">Voix</a>!!
		</td>
        <td class="musique_corps" height="180">
La Voix lance son premier sondage : 
<br>LE TOP 10 des guildes les plus originales.
<br>
<br><b> - Selon vous, quelles guildes méritent de rentrer dans ce classement ?</b>
<br>
<br>La Voix attend vos avis, et diffusera dans un numéro futur le résultat du sondage.
		</td>
      </tr>
      <tr class="musique_titre">
        <td class="musique_titre">
		 RiJD
		</td>
      </tr>
      <tr class="musique_corps">
        <td class="musique_corps" height="140">
		Les 8 et 9 Octobre, en la cité de Toulouse, s'est tenu une Réunion pour les consciences qui vous dirigent. On appelle ceci une Réunion Inter Joueur de Delain. <br>
        Vous trouverez le résumé de ce qui s'est passé sur le <a href="http://www.jdr-delain.net/forum/">forum.</a>
		</td>
      </tr>
      <tr class="musique_titre">
        <td class="musique_titre">
		 La Voix ... IRL
		</td>
      </tr>
      <tr class="musique_corps">
        <td class="musique_corps">
          A l'occasion de la RiJD de Toulouse, l'équipe de la Voix à réalisé une version papier. Le texte est disponible au <a href="http://www.jdr-delain.net/voix/">kiosque.</a>
		</td>
      </tr>
    </table>
</div>

<div class="droite4" id="PD_1">
    <table class="dedicace">
      <tr>
        <td class="dedicace_titre" colspan="2">
Petits D&eacute;lires
		</td>
      </tr>
      <tr class="dedicace_corps">
        <td class="dedicace_corps">
Un nain à un elfe : 
<br>- Que représentent pour toi 1000 ans? 
<br>- Un an. 
<br>- Et 1000 brouzoufs? 
<br>- Un brouzouf. 
<br>- Alors tu peux me filer 1000 brouzoufs? 
<br>- Bien sûr. Dans un an.
		</td>
        <td class="dedicace_corps">
- Quelle sont les différences entre un vampire et un caravanier ? 
<br>- Le vampire ne vous suce le sang que la nuit, et seulement jusqu'à votre mort.
		</td>
      </tr>
    </table>
</div>

<div class="droite5" id="PA_1">
    <table class="annonce">
      <tr class="annonce_titre">
        <td class="annonce_titre" colspan="2">
		Petites Annonces
		</td>
      </tr>
      <tr class="annonce_corps">
        <td class="annonce_corps">
A vous les cuites dans les tavernes humaines! 
<br>Faussaire tr&egrave;s dou&eacute; vends faux papiers d'identit&eacute; pour d&eacute;mons mineurs, contacter la Voix qui fera suivre. 
<br>Petits malins ou ancien responsable de la milice, s'abstenir.	
        </td>
        <td class="annonce_corps">
Trouv&eacute;e, petite culotte rose ayant quelque peu servie.
<br>Le petite fille qui l'a perdue pourra la retirer aupr&egrave;s de Lipou
		</td>
      </tr>
    </table>
</div>

<div class="gauche3b" id="Pub_TR_fond">
	<table class="horoscope">
	  <tr>
	    <td class="horoscope_corps">
<br>		..............................
		..............................
		</td>
	  </tr>
	</table>
</div>

<div class="gauche3" id="Pub_TR">
	<table class="pub">
	  <tr>
	    <td class="pub_corps" align="center" valign="middle" width="191">
		  <a href="pubs/Trolls_Royce-Silver_Goule.jpg" target="_blank" class="blanc">
		    <img src="pubs/Trolls_Royce-Silver_Goule.jpg" height="188" width="185">
		  </a>
		</td>
	  </tr>
	</table>
</div>

</div>

</body>
</html>
