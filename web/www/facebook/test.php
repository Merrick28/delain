<html>
    <head>
      <title>My Great Website</title>
    </head>
    <body>
      <div id="fb-root"></div>
      <script src="http://connect.facebook.net/en_US/all.js">
      </script>
      <script>
         FB.init({ 
            appId:'209789322423919', cookie:true, 
            status:true, xfbml:true 
         });

         FB.ui({ method: 'feed', 
         	name: 'Les souterrains de Delain',
         	link: 'http://www.jdr-delain.net',
         	caption: 'passage de niveau',
    			description: 'L\'aventurier Merrick a gagné un niveau et est passé niveau 50 !'
            });
      </script>
     </body>
 </html> 
