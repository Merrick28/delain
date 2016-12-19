<?php 
//$result = stripos($PHP_SELF,'sadsearch');
//if(!$result)
$sortie = ob_get_contents();
// ob_end_flush();
ob_end_clean();
echo preg_replace('/Moustiques sanguinaires \(n° \d+\)/', 'Moustiques sanguinaires', $sortie);

//$stmt->closeCursor(); // this is not even required
$stmt = null; // doing this is mandatory for connection to get closed
$pdo = null;