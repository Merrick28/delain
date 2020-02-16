<?php
include "blocks/_header_page_jeu.php";
$compte = new compte;
$compte = $verif_connexion->compte;
if (!$compte->is_admin())
{
    $contenu_page .= 'Vous n\'avez pas accès à cette page !';
} else
{
    $methode = get_request_var('methode', 'global');
    switch ($methode)
    {
        case "detail":
            $req  =
                'select hlog_id,to_char(hlog_date,\'YYYY/MM/DD hh24:mi:ss\') as hlog_date,hlog_ip from histo_log where hlog_compte = ' . $compte . ' order by hlog_date desc';
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0)
                $contenu_page .= '<p>Aucune connexion enregistrée.';
            else
            {
                $contenu_page .= '<table>
					<tr><td class="soustitre2"><strong>ID</strong></td><td class="soustitre2"><strong>Date</strong></td><td class="soustitre2"><strong>IP</strong></td>';
                while ($result = $stmt->fetch())
                    $contenu_page .= '<tr><td class="soustitre2"><a href="trc_id.php?id=' . $result['hlog_id'] . '">' . $result['hlog_id'] . '</a></td><td>' . $result['hlog_date'] . '</td><td class="soustitre2">' . $result['hlog_ip'] . '</td></tr>';
                $contenu_page .= '</table>';
            }
            $contenu_page .= '<p><a href="' . $_SERVER['PHP_SELF'] . '?methode=global&compte=' . $compte . '">Voir le global</a>';
            break;
        case "global";
            $req  =
                'select hlog_id,count(hlog_cod) as nombre,to_char(min(hlog_date),\'YYYY/MM/DD\') as date_min,to_char(max(hlog_date),\'YYYY/MM/DD\') as date_max from histo_log where hlog_compte = ' . $compte . ' group by hlog_id order by date_max DESC, date_min ASC';
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0)
                $contenu_page .= '<p>Aucune connexion enregistrée.';
            else
            {
                $contenu_page .= '<table>
					<tr><td class="soustitre2"><strong>ID</strong></td>
					<td class="soustitre2"><strong>Nombre</strong></td>
					<td class="soustitre2"><strong>Date Min</strong></td>
					<td class="soustitre2"><strong>Date Max</strong></td>';
                while ($result = $stmt->fetch())
                    $contenu_page .= '<tr><td class="soustitre2"><a href="trc_id.php?id=' . $result['hlog_id'] . '">' . $result['hlog_id'] . '</a></td>
					<td class="soustitre2">' . $result['nombre'] . '</td>
					<td>' . $result['date_min'] . '</td>
					<td>' . $result['date_max'] . '</td></tr>';
                $contenu_page .= '</table>';
            }
            $contenu_page .= '<p><a href="' . $_SERVER['PHP_SELF'] . '?methode=detail&compte=' . $compte . '">Voir le détail</a>';


    }


}
include "blocks/_footer_page_jeu.php";