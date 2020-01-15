<?php

function choix_pub()
{
    $pub       = array(

       "<script type=\"text/javascript\">
    var vglnk = {key: 'e3e57e29280f5110ef7f3b37447871a1'};
    (function(d, t) {
        var s = d.createElement(t);
            s.type = 'text/javascript';
            s.async = true;
            s.src = '//cdn.viglink.com/api/vglnk.js';
        var r = d.getElementsByTagName(t)[0];
            r.parentNode.insertBefore(s, r);
    }(document, 'script'));
</script>",
'<script type="text/javascript"> var infolinks_pid = 3230751; var infolinks_wsid = 0; </script> <script type="text/javascript" src="//resources.infolinks.com/js/infolinks_main.js"></script>'
    );
// maintenant on choisit
    $compt_pub = rand(1, count($pub)) - 1;
 /**
  * Old google

   
    $publicite = "<!-- debut pub $compt_pub / " . count($pub) . "-->" . $pub[$compt_pub] . "<!-- fin pub -->";

    $publicite = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Reponsive -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-6632318064183878"
     data-ad-slot="8485409096"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';*/
$publicite = "<!-- debut pub $compt_pub / " . count($pub) . "-->" . $pub[$compt_pub] . "<!-- fin pub -->";
    return $publicite;
}

//
//
//
function choix_pub_index()
{
   $pub       = array(

       "<script type=\"text/javascript\">
    var vglnk = {key: 'e3e57e29280f5110ef7f3b37447871a1'};
    (function(d, t) {
        var s = d.createElement(t);
            s.type = 'text/javascript';
            s.async = true;
            s.src = '//cdn.viglink.com/api/vglnk.js';
        var r = d.getElementsByTagName(t)[0];
            r.parentNode.insertBefore(s, r);
    }(document, 'script'));
</script>",
'<script type="text/javascript"> var infolinks_pid = 3230751; var infolinks_wsid = 0; </script> <script type="text/javascript" src="//resources.infolinks.com/js/infolinks_main.js"></script>'
    );
// maintenant on choisit
    $compt_pub = rand(1, count($pub)) - 1;
 /**
  * Old google

   
    $publicite = "<!-- debut pub $compt_pub / " . count($pub) . "-->" . $pub[$compt_pub] . "<!-- fin pub -->";

    $publicite = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Reponsive -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-6632318064183878"
     data-ad-slot="8485409096"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';*/
$publicite = "<!-- debut pub $compt_pub / " . count($pub) . "-->" . $pub[$compt_pub] . "<!-- fin pub -->";

    return $publicite;
}

