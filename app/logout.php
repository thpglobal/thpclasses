<?php
session_start(); 
$_SESSION=array(); // Clear all session variables when logging off
// unset cookies per reference:
// https://stackoverflow.com/questions/2310558/how-to-delete-all-cookies-of-my-website-in-php
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}
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
