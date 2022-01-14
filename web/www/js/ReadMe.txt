Password Widget 1.0
-------------------

  Author: Prasanth, <info@html-form-guide.com>
          http://www.html-form-guide.com/

  This program is free software published under the
  terms of the GNU Lesser General Public License.

  For the entire license text please refer to
  http://www.gnu.org/licenses/lgpl.html

Contents
---------

  pwdwidget.js    -- the main JavaScript file for the password widget
  pwdwidget.css   -- the style elements for the password widget
  sample-login-form.html -- A sample login form 
  sample-reg-form.html   -- A sample registration form

Usage
---------
First, link to the style sheet and the JavaScript file:

<link rel="STYLESHEET" type="text/css" href="pwdwidget.css" />
<script src="pwdwidget.js" type="text/javascript"></script>

Then for password field, use the code below:
<label for='regpwd'>Password:</label> <br />
<div class='pwdwidgetdiv' id='thepwddiv'></div>
<script  type="text/javascript" >
var pwdwidget = new PasswordWidget('thepwddiv','regpwd');
pwdwidget.MakePWDWidget();
</script>
<noscript>
<div><input type='password' id='regpwd' name='regpwd' /></div>		
</noscript>


 
Homepage
---------

  For details and latest versions visit:
  http://www.html-form-guide.com/