<?php
session_start(); 
$_SESSION=array(); // Clear all session variables when logging off
//Logout script below from: https://stackoverflow.com/questions/35883692/google-sign-in-api-how-do-i-log-someone-out-with-php
?>
<html>
    <head>
        <meta name="google-signin-client_id" content="<?php echo($client_id)?>">
    </head>
    <body>
        <script src="https://apis.google.com/js/platform.js?onload=onLoadCallback" async defer></script>
        <script>
            window.onLoadCallback = function(){
                gapi.load('auth2', function() {
                    gapi.auth2.init().then(function(){
                        var auth2 = gapi.auth2.getAuthInstance();
                        auth2.signOut().then(function () {
                            document.location.href = 'index.php';
                        });
                    });
                });
            };
        </script>
    </body>
</html>
