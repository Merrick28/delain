<?php
$type_lieu = 24;
$nom_lieu = 'une dalle magique';

define('APPEL', 1);
include "blocks/_test_lieu.php";


if ($erreur == 0)
{
    ?>
    <p><strong>
            Ces dalles ressemblent aux autres de prime abord malgré des couleurs changeantes. Pourtant, un observateur
            un
            tant soit peu versé dans les arts magiques peut constater la puissance de flux magiques entremêlés à la
            surface
            des pavés.<br>
            D'ailleurs, à y regarder de plus près, il constatera que ce n'est pas de la magie, mais bien plutôt de
            l'anti-magie. A croire que certains sortilèges pourraient être bloqués ... Cela serait-il une réponse
            apportée
            par les Shamans morbelins pour maitriser cet art ?
            <br><br>

    <?php
}

