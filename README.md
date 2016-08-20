#Duo 2FA provider for ownCloud

##About
Two-factor authentication (2FA) framework was added to ownCloud 9.1. This project leverages this new framework to integrate Duo 2FA into ownCloud.

Currently, some modifications to the core TwoFactorAuthentication framework were necessary, specifically to allow the Duo "iframe" to be displayed on the page, due to the default CSP restrictions. The changes are included in my fork of the ownCloud core repo: https://github.com/elie195/core

##Requirements

- PHP 5.6 (Duo SDK requirement) - See guide at the bottom for Ubuntu 14.04 instructions
- Duo application settings (IKEY, SKEY, HOST)
- ownCloud core installation patched with changes from my fork (https://github.com/elie195/core). See installation instructions below.
    
##Installation

1. Patch your ownCloud installation (necessary while changes to the main ownCloud repo haven't been merged yet):

    ```
    sudo apt-get install -y git
    cd /var/www/owncloud
    wget -O elie195.patch https://github.com/owncloud/core/compare/master...elie195:master.patch
    git apply elie195.patch
    ```

2. Clone this repo to the 'apps/duo' directory of your ownCloud installation. i.e.:

    ```
    cd /var/www/owncloud/apps && git clone https://github.com/elie195/duo_provider.git duo
    ```
    
3. Customize duo.conf (insert your own IKEY, SKEY, HOST values):

    ```
    cp duo/duo.conf.example duo/duo.conf
    ```
    
4. Enable the app in the ownCloud GUI

    ![Image of Duo app](https://github.com/elie195/duo_provider/misc/duo.PNG)

I have included an "AKEY" in the duo.conf.example file. The "AKEY" is an application-specific secret string. Feel free to generate your own "AKEY" by executing the following Python code:

    import os, hashlib
    print hashlib.sha1(os.urandom(32)).hexdigest()

Or if you're using Python3:

    import os, hashlib
    print(hashlib.sha1(os.urandom(32)).hexdigest())

This has been tested on ownCloud 9.2.1 (cloned from "master" branch of the official ownCloud repo) on a CentOS 7 server with manually upgraded PHP. More detailed installation instructions to come.

See https://duo.com/docs/duoweb for more info on the Duo Web SDK and additional details about the "AKEY" variable.
See https://www.digitalocean.com/community/tutorials/how-to-upgrade-to-php-7-on-ubuntu-14-04 for a PHP upgrade guide for Ubuntu 14.04
