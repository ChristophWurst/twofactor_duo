# twofactor_duo
Experimental Duo two-factor auth provider for Nextcloud

## Configuration
Add your duo configuration to your Nextcloud's `config/config.php` fils:
```
'twofactor_duo' => [
    'IKEY' => 'xxx',
    'SKEY' => 'yyy',
    'HOST' => 'zzz',
    'AKEY' => '123',
  ],
```

