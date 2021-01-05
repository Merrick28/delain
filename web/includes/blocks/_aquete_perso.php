<?php
foreach ($perso_journaux as $k => $journal)
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