<?php
/*
* FROM ZEND FRAMEWORK DOCS:
  AuthSub allows authentication to the calendar servers via 
  a Google proxy server. This provides the same level of convenience 
  as ClientAuth but without the security risk, making this an 
  ideal choice for web-based applications.
/*
* Retrieve the current URL so that the AuthSub server knows where to
* redirect the user after authentication is complete.
*/
function getCurrentUrl()
{
    global $_SERVER;
 
    // Filter php_self to avoid a security vulnerability.
    $php_request_uri =
        htmlentities(substr($_SERVER['REQUEST_URI'],
                            0,
                            strcspn($_SERVER['REQUEST_URI'], "\n\r")),
                            ENT_QUOTES);
 
    if (isset($_SERVER['HTTPS']) &&
        strtolower($_SERVER['HTTPS']) == 'on') {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }
    $host = $_SERVER['HTTP_HOST'];
    if ($_SERVER['HTTP_PORT'] != '' &&
        (($protocol == 'http://' && $_SERVER['HTTP_PORT'] != '80') ||
        ($protocol == 'https://' && $_SERVER['HTTP_PORT'] != '443'))) {
        $port = ':' . $_SERVER['HTTP_PORT'];
    } else {
        $port = '';
    }
    return $protocol . $host . $port . $php_request_uri;
}
 
/**
* Obtain an AuthSub authenticated HTTP client, redirecting the user
* to the AuthSub server to login if necessary.
*/
function getAuthSubHttpClient()
{
    global $_SESSION, $_GET;
 
    // if there is no AuthSub session or one-time token waiting for us,
    // redirect the user to the AuthSub server to get one.
    if (!isset($_SESSION['sessionToken']) && !isset($_GET['token'])) {
        // Parameters to give to AuthSub server
        $next = getCurrentUrl();
        $scope = 'http://www.google.com/m8/feeds/contacts/default/full';
        $secure = false;
        $session = true;
 
        // Redirect the user to the AuthSub server to sign in
 
        $authSubUrl = Zend_Gdata_AuthSub::getAuthSubTokenUri($next,
                                                             $scope,
                                                             $secure,
                                                             $session);
         header("HTTP/1.0 307 Temporary redirect");
 
         header("Location: " . $authSubUrl);
 
         exit();
    }
 
    // Convert an AuthSub one-time token into a session token if needed
    if (!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
        $_SESSION['sessionToken'] =
            Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
    }
 
    // At this point we are authenticated via AuthSub and can obtain an
    // authenticated HTTP client instance
 
    // Create an authenticated HTTP client
    $client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);
    return $client;
}
// -> Script execution begins here - scripts that use include <-
?>