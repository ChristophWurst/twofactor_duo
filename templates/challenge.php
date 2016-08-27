<?php

require_once 'duo/lib/Web.php';

$sig_request = Duo\Web::signRequest($_['IKEY'], $_['SKEY'], $_['AKEY'], $_['user']);
script('duo', 'Duo-Web-v2');
style('duo', 'Duo-Frame');
?>
<iframe id="duo_iframe"
    data-host="<?php p($_['HOST']); ?>"
    data-sig-request="<?php p($sig_request); ?>"
    data-post-argument="challenge"
</iframe>
