<?php
include "blocks/_header_page_jeu.php";
if (!$db->is_admin($compt_cod)) {
    $contenu_page .= 'Vous n\'avez pas accès à cette page !';
} else {
    if (!isset($methode))
        $methode = 'global';
    switch ($methode) {
        case "detail":
            $req = 'select hlog_id,to_char(hlog_date,\'YYYY/MM/DD hh24:mi:ss\') as hlog_date,hlog_ip from histo_log where hlog_compte = ' . $compte . ' order by hlog_date desc';
            $db->query($req);
            if ($db->nf() == 0)
                $contenu_page .= '<p>Aucune connexion enregistrée.';
            else {
                $contenu_page .= '<table>
					<tr><td class="soustitre2"><strong>ID</strong></td><td class="soustitre2"><strong>Date</strong></td><td class="soustitre2"><strong>IP</strong></td>';
                while ($db->next_record())
                    $contenu_page .= '<tr><td class="soustitre2"><a href="trc_id.php?id=' . $db->f('hlog_id') . '">' . $db->f('hlog_id') . '</a></td><td>' . $db->f('hlog_date') . '</td><td class="soustitre2">' . $db->f('hlog_ip') . '</td></tr>';
                $contenu_page .= '</table>';
            }
            $contenu_page .= '<p><a href="' . $PHP_SELF . '?methode=global&compte=' . $compte . '">Voir le global</a>';
            break;
        case "global";
            $req = 'select hlog_id,count(hlog_cod) as nombre,to_char(min(hlog_date),\'YYYY/MM/DD\') as date_min,to_char(max(hlog_date),\'YYYY/MM/DD\') as date_max from histo_log where hlog_compte = ' . $compte . ' group by hlog_id order by date_max DESC, date_min ASC';
            $db->query($req);
            if ($db->nf() == 0)
                $contenu_page .= '<p>Aucune connexion enregistrée.';
            else {
                $contenu_page .= '<table>
					<tr><td class="soustitre2"><strong>ID</strong></td>
					<td class="soustitre2"><strong>Nombre</strong></td>
					<td class="soustitre2"><strong>Date Min</strong></td>
					<td class="soustitre2"><strong>Date Max</strong></td>';
                while ($db->next_record())
                    $contenu_page .= '<tr><td class="soustitre2"><a href="trc_id.php?id=' . $db->f('hlog_id') . '">' . $db->f('hlog_id') . '</a></td>
					<td class="soustitre2">' . $db->f('nombre') . '</td>
					<td>' . $db->f('date_min') . '</td>
					<td>' . $db->f('date_max') . '</td></tr>';
                $contenu_page .= '</table>';
            }
            $contenu_page .= '<p><a href="' . $PHP_SELF . '?methode=detail&compte=' . $compte . '">Voir le détail</a>';


    }


}
include "blocks/_footer_page_jeu.php";