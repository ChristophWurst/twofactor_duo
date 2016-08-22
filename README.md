#Duo 2FA provider for ownCloud

##About
Two-factor authentication (2FA) framework was added to ownCloud 9.1. This project leverages this new framework to integrate Duo 2FA into ownCloud.

Currently, some modifications to the core TwoFactorAuthentication framework were necessary, specifically to allow the Duo "iframe" to be displayed on the page, due to the default CSP restrictions. The changes are included in my fork of the ownCloud core repo: https://github.com/elie195/core

##Requirements

- PHP >=5.6 (Duo SDK requirement) - See guide at the bottom for Ubuntu 14.04 instructions (*This doesn't seem like a hard requirement, successfully used PHP 5.4.16 on CentOS 7 as well*)
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

    ![Image of Duo app](https://github.com/elie195/duo_provider/raw/master/misc/duo.PNG)


##Notes

**Important:** Please disable the "*Notifications*" plugin if enabled. This plugin has been found to refresh the two-factor related pages, making it extremely difficult/impossible to complete the 2FA process.

###LDAP integration

If you're using LDAP, the 2FA won't work right off the bat, since ownCloud stores LDAP users with their UUID, so I'm not able to pass the plaintext username to Duo, and the authentication fails. See issue #2 for more details.

To change the LDAP settings so that the internal identifier uses the username instead of the UUID, do the following (I'm using AD LDAP, so the attributes are named accordingly): To configure this with AD LDAP, go into "Expert" mode in the ownCloud LDAP settings, and set "Internal Username Attribute" to "sAMAccountName". Note that this only affects new users.

###Misc

I have included an "AKEY" in the duo.conf.example file. The "AKEY" is an application-specific secret string. Feel free to generate your own "AKEY" by executing the following Python code:

    import os, hashlib
    print hashlib.sha1(os.urandom(32)).hexdigest()

Or if you're using Python3:

    import os, hashlib
    print(hashlib.sha1(os.urandom(32)).hexdigest())

You may then take this new AKEY and insert it into your customized duo.conf file.

This has been tested on ownCloud 9.2.1 (cloned from "master" branch of the official ownCloud repo) on a CentOS 7 server, as well as an Ubuntu 14.04 server where ownCloud was installed from packages (both with manually upgraded PHP: PHP 5.6.24 on CentOS 7, PHP 7.0.9-1 on Ubuntu 14.04). 

See https://duo.com/docs/duoweb for more info on the Duo Web SDK and additional details about the "AKEY" variable.

See https://www.digitalocean.com/community/tutorials/how-to-upgrade-to-php-7-on-ubuntu-14-04 for a PHP upgrade guide for Ubuntu 14.04

Checkout my ownCloud Application page: https://apps.owncloud.com/content/show.php?content=174747
