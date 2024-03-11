<?php
$verif_connexion::verif_appel();
function CalcPhase()
{
    $DateRef   =
        mktime(8, 53, 0, 7, 29, 2003); //date de référence : le 29/07/2003 à 6:53:00 GMT il y a eu une pleine lune
    $Aujourdui = mktime((int)date("H"),(int)date("i"),(int)date("H"), date("m"), (int)date("d"), (int)date("Y")); //date du jour
    $msDiff    = ($Aujourdui - $DateRef) * 1000 + MSPARJOUR; //on calcule la différence en millisecondes
    $phase     = ($msDiff * 100) / (SYNODIC * MSPARJOUR); //on calcule le pourcentage de la phase

    while ($phase > 100) // tant que c'est supérieur à 100, on retire 100
    {
        $phase -= 100;
    }
    return $phase;
}

function ImgPhase($phase)
{
    $NumImage = round(24 * $phase / 100, 0); // on convertit le pourcentage en age de la lune (sur 29 jours)
    if ($NumImage == 0)
    {
        $NumImage = 1;
    }
    return 'http://www.jdr-delain.net/images/lune_' . $NumImage . '.png';
}

function PartEntier($phase)
{
    if ($phase <= 50.0)
    {
        $plein = $phase * 2;
    } else
    {
        $plein = (100 - $phase) * 2;
    }
    return $plein;
}

function NommerPhase($phase)
    //convertit le pourcentage de lunaison en mots
{
    if ($phase >= 0 && $phase < 2.5)
    {
        $NomPhase = "<strong>Nouvelle Lune
		</strong><br><br>Cette phase est particulièrement propice à la récupération de composants, et notammant les plus rares.";
    } else if ($phase >= 2.5 && $phase < 22.5)
    {
        $NomPhase = "<strong>Premier Croissant
		</strong><br><br>Phase peu fertile, on peut trouver des composants de potion, mais ceux ci sont à trier scrupuleusement, rendant la recherche plus pauvre";
    } else if ($phase >= 22.5 && $phase < 27.5)
    {
        $NomPhase = "<strong>Premier Quartier
		</strong><br><br>A cette période, la recherche est plutôt bonne, un bon chercheur saura toujours s'y retrouver. Moins propice que la nouvelle lune ou pleine lune, cela reste quand même l'une des meilleures phase de récupération d'ingrédients pour les potions.";
    } else if ($phase >= 27.5 && $phase < 47.5)
    {
        $NomPhase = "<strong>Lune gibbeuse
		</strong><br><br>Cette phase lunaire est assez neutre. Certains y trouveront leur compte, mais d'autres la trouveront bien pauvre comparée aux nouvelles lunes et pleines lunes.";
    } else if ($phase >= 47.5 && $phase < 52.5)
    {
        $NomPhase = "<strong>Pleine Lune
		</strong><br><br>Cette phase est particulièrement propice à la récupération de composants, et notammant les plus rares.";
    } else if ($phase >= 52.5 && $phase < 73.5)
    {
        $NomPhase = "<strong>Lune gibbeuse.
		</strong><br><br>Cette phase lunaire est assez neutre. Certains y trouveront leur compte, mais d'autres la trouveront bien pauvre comparée aux nouvelles lunes et pleines lunes.";
    } else if ($phase >= 73.5 && $phase < 77.5)
    {
        $NomPhase = "<strong>Dernier quartier
		</strong><br><br>A cette période, la recherche est plutôt bonne, un bon chercheur saura toujours s'y retrouver. Moins propice que la nouvelle lune ou pleine lune, cela reste quand même l'une des meilleures phase de récupération d'ingrédients pour les potions.";
    } else if ($phase >= 77.5 && $phase < 97.5)
    {
        $NomPhase = "<strong>Dernier croissant
		</strong><br><br>Phase peu fertile, on peut trouver des composants de potion, mais ceux ci sont à trier scrupuleusement, rendant la recherche plus pauvre";
    } else
    {
        $NomPhase = "<strong>Nouvelle Lune
		</strong><br><br>Cette phase est particulièrement propice à la récupération de composants, et notammant les plus rares.";
    }
    return $NomPhase;
}

function JoursAvantNL($phase) //calcule le nombre de jours avant la nouvelle Lune
{
    return round((1 - $phase / 100) * SYNODIC, 2);
}

function JoursAvantPL($phase) //calcule le nombre de jours avant la pleine Lune
{
    if ($phase < 50)
    {
        return round((0.5 - $phase / 100) * SYNODIC, 2);
    } else
    {
        return round((1.5 - $phase / 100) * SYNODIC, 2);
    }
}
