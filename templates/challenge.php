<?php

include 'duo/duo.conf';
require_once 'duo/lib/Web.php';

$sig_request = Duo\Web::signRequest(IKEY, SKEY, AKEY, $_['user']);
script('duo', 'Duo-Web-v2');
style('duo', 'Duo-Frame');
?>
<iframe id="duo_iframe"
    data-host="<?php p(HOST); ?>"
    data-sig-request="<?php p($sig_request); ?>"
    data-post-argument="challenge"
</iframe>
