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
## Nextcloud server patch
The app provides a custom CSP which the Nextcloud server currently does not support. The following patch adds this customization support:
```
 core/Controller/TwoFactorChallengeController.php | 12 +++++++++---
 1 file changed, 9 insertions(+), 3 deletions(-)

diff --git a/core/Controller/TwoFactorChallengeController.php b/core/Controller/TwoFactorChallengeController.php
index fd4811d3ff..ed4c4f45d4 100644
--- a/core/Controller/TwoFactorChallengeController.php
+++ b/core/Controller/TwoFactorChallengeController.php
@@ -1,4 +1,5 @@
 <?php
+
 /**
  * @copyright Copyright (c) 2016, ownCloud, Inc.
  *
@@ -29,6 +30,7 @@ use OC_Util;
 use OCP\AppFramework\Controller;
 use OCP\AppFramework\Http\RedirectResponse;
 use OCP\AppFramework\Http\TemplateResponse;
+use OCP\Authentication\TwoFactorAuth\IProvidesCustomCSP;
 use OCP\Authentication\TwoFactorAuth\TwoFactorException;
 use OCP\IRequest;
 use OCP\ISession;
@@ -135,7 +137,11 @@ class TwoFactorChallengeController extends Controller {
 			'redirect_url' => $redirect_url,
 			'template' => $tmpl->fetchPage(),
 		];
-		return new TemplateResponse($this->appName, 'twofactorshowchallenge', $data, 'guest');
+		$response = new TemplateResponse($this->appName, 'twofactorshowchallenge', $data, 'guest');
+		if ($provider instanceof IProvidesCustomCSP) {
+			$response->setContentSecurityPolicy($provider->getCSP());
+		}
+		return $response;
 	}
 
 	/**
@@ -173,8 +179,8 @@ class TwoFactorChallengeController extends Controller {
 
 		$this->session->set('two_factor_auth_error', true);
 		return new RedirectResponse($this->urlGenerator->linkToRoute('core.TwoFactorChallenge.showChallenge', [
-			'challengeProviderId' => $provider->getId(),
-			'redirect_url' => $redirect_url,
+				'challengeProviderId' => $provider->getId(),
+				'redirect_url' => $redirect_url,
 		]));
 	}
 
```
