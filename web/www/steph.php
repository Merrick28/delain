<?php
$mavar = $_REQUEST['mavar'];
echo strlen($mavar);
echo "<hr>";
?>
<form method="post">
    <?php
    $mavar = '';
    for ($i = 0; $i < 1000000; $i++)
    {
        $mavar .= 'a';
    }
    echo '<input type="hidden" name="mavar" value="' . $mavar . '">';

    ?>
    <input type="submit" value="go">
</form>
