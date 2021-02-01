<?php
$verif_connexion::verif_appel();
if (!isset($sort))
{
    $sort    = 'duree';
    $sens    = 'desc';
    $nv_sens = 'asc';
}
if (!isset($sens))
{
    $sens = 'desc';
}
$autresens = $_POST['autresens'];
if (!isset($autresens))
{
    $autresens = 'desc';
}
if (($sens != 'desc') && ($sens != 'asc'))
{
    echo "<p>Anomalie sur sens !";
    exit();
}
if (($sort != 'compteur') && ($sort != 'duree'))
{
    echo "<p>Anomalie sur tri !";
    exit();
}
if ($sort == 'compteur')
{
    $req = $req . " order by compteur $sens";
    if ($sens == 'desc')
    {
        $sens      = 'asc';
        $autresens = 'desc';
    } else
    {
        $sens      = 'desc';
        $autresens = 'asc';
    }
}
if ($sort == 'duree')
{
    $req = $req . " order by temps_cumule $sens";
    if ($sens == 'desc')
    {
        $sens      = 'asc';
        $autresens = 'desc';
    } else
    {
        $sens      = 'desc';
        $autresens = 'asc';
    }
}

$stmt = $pdo->query($req);
