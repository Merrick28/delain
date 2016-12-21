<?php

function choix_pub()
{
    $pub       = array(
// Google
       /* '<script type="text/javascript"><!--
         google_ad_client = "ca-pub-6632318064183878";
         /-* Forum *-/
         google_ad_slot = "0577168209";
         google_ad_width = 728;
         google_ad_height = 90;
         //-->
         </script>
         <script type="text/javascript"
         src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
         </script>',
         '<script type="text/javascript"><!--
         google_ad_client = "ca-pub-6632318064183878";
         /-* Forum *-/
         google_ad_slot = "0577168209";
         google_ad_width = 728;
         google_ad_height = 90;
         //-->
         </script>
         <script type="text/javascript"
         src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
         </script>', */
       '<script type="text/javascript"><!--
google_ad_client = "ca-pub-6632318064183878";
/* Jeu2 */
google_ad_slot = "4710933898";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>',
// Click in text
       /* '<!--
         // ClickInText(TM) - Classic Technology :
         // (fr) Pensez à vérifier que le site sur lequel vous installez ce script a bien été ajouté à votre compte ClickInText
         -->
         <script type="text/javascript" src="http://fr.classic.clickintext.net/?v=3.0&a=267&f=728x90"></script>
         <!--
         // ClickInText(TM) - Classic Technology : End
         -->',
         '<!--
         // ClickInText(TM) - Classic Technology :
         // (fr) Pensez à vérifier que le site sur lequel vous installez ce script a bien été ajouté à votre compte ClickInText
         -->
         <script type="text/javascript" src="http://fr.classic.clickintext.net/?v=3.0&a=267&f=728x90"></script>
         <!--
         // ClickInText(TM) - Classic Technology : End
         -->' */
    );
// maintenant on choisit
    $compt_pub = rand(1, count($pub)) - 1;
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
</script>';
    return $publicite;
}

//
//
//
function choix_pub_index()
{
    $pub       = array(
       0 =>
       '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                            <!-- Reponsive -->
                            <ins class="adsbygoogle"
                                 style="display:block"
                                 data-ad-client="ca-pub-6632318064183878"
                                 data-ad-slot="8485409096"
                                 data-ad-format="auto"></ins>
                            <script>
                                (adsbygoogle = window.adsbygoogle || []).push({});
                            </script>',
       1 =>
       '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- correspondant2 -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-6632318064183878"
     data-ad-slot="9594785090"
     data-ad-format="autorelaxed"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>'
    );
// maintenant on choisit
    $compt_pub = rand(1, 100);
//$compt_pub = 0 ;
    if ($compt_pub <= 80)
    {
        $idx = 0;
        $prefix = '<!-- pub normale -->';
    }
    else
    {
        $idx = 1;
        $prefix = '<!-- pub adaptatavie -->';
    }
    $publicite = $pub[$idx];
    return $prefix . $publicite;
}

