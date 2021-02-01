<?php
header('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Content-Type: text/xml');
include "includes/classes.php";
$pdo = new bddpdo();

$ls_urlNews         = "http://www.jdr-delain.net/";
$ls_titleNews       = "Les souterrains de Delain";
$ls_descriptionNews = "Les nouvelles des souterrains";
$ls_mailWebmaster   = "merrick@jdr-delain.net";

$ls_return = '<' . '?xml version="1.0" encoding="UTF-8" ?' . '>
<rss version="0.92">
<channel>
<docs>http://backend.userland.com/rss092</docs>
<title>' . $ls_titleNews . '</title>
<link>' . $ls_urlNews . '</link>
<description>' . $ls_descriptionNews . '</description>
<language>fr</language>
<pubDate>' . date('r') . '</pubDate>
<lastBuildDate>' . date('r') . '</lastBuildDate>';

$recherche = "SELECT news_cod,news_titre,news_texte,to_char(news_date,'DD/MM/YYYY') as date_news,news_auteur,coalesce(news_mail_auteur,'merrick@jdr-delain.net') as news_mail_auteur
	FROM news order by news_cod desc
	limit 10 offset 0";
$stmt      = $pdo->query($recherche);

while ($result = $stmt->fetch())
{
    $ls_return    .= '	<item>
		<title>' . htmlspecialchars($result['news_titre']) . '</title>
		<link>https://www.jdr-delain.net/?news=' . $result['news_cod'] . '</link>';
    $texte_auteur = 'Auteur : ';
    if ($result['news_mail_auteur'] != "")
    {
        $texte_auteur .= '<a href="mailto:' . $result['news_mail_auteur'] . '">';

    }

    $texte_auteur .= $result['news_mail_auteur'];
    if ($result['news_mail_auteur'] != "")
    {
        $texte_auteur .= '</a>';

    }

    $texte_auteur .= '<br />Posté le : ' . $result['date_news'] . '<br />------<br />';
    $texte_auteur = htmlspecialchars($texte_auteur);
    $ls_return    .= '	<description>' . $texte_auteur . htmlspecialchars($result['news_texte']) . '</description>
		</item>  ';
}
$ls_return .= '</channel>';
$ls_return .= '</rss>';
echo $ls_return;
die('');

