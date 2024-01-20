<?php
foreach ($perso_journaux as $k => $journal)
{
    // affichage du journal seulement s'il y a des trucs à lire,on va compacter les lignes vides (ou ne contenant que des caractères non-imprimables)!
    if ( str_replace("<br/>", "", str_replace("</p>", "", str_replace("<p>", "", str_replace(" ", "", strtolower(preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $journal->aqpersoj_texte)))))) != "")
    {
        // Seulement les pages non-lues
        if (($journal->aqpersoj_lu == 'N'))
        {
            $journal_quete .= "<div style='background-color: #BA9C6C;'>" . $journal->aqpersoj_texte . "<br></div>";
        }

        if ($lire == 'O' && $journal->aqpersoj_lu == 'N')
        {
            $journal->aqpersoj_lu = 'O';
            $journal->stocke();
        }        
    }

}
