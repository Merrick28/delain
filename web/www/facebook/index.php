<html>
    <head>
      <title>My Facebook Login Page</title>
    </head>
    <body>
      <div id="fb-root"></div>
      <script src="http://connect.facebook.net/en_US/all.js"></script>
      <script>
         FB.init({ 
            appId:'209789322423919', cookie:true, 
            status:true, xfbml:true 
         });
      </script>
      <fb:login-button perms="publish_stream">Login with Facebook</fb:login-button>
    </body>
 </html>
