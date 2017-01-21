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
```patch
 core/Controller/TwoFactorChallengeController.php   | 12 ++++++--
 .../TwoFactorAuth/IProvidesCustomCSP.php           | 33 ++++++++++++++++++++++
 2 files changed, 42 insertions(+), 3 deletions(-)

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
 
diff --git a/lib/public/Authentication/TwoFactorAuth/IProvidesCustomCSP.php b/lib/public/Authentication/TwoFactorAuth/IProvidesCustomCSP.php
new file mode 100644
index 0000000000..bf6a8a1bcc
--- /dev/null
+++ b/lib/public/Authentication/TwoFactorAuth/IProvidesCustomCSP.php
@@ -0,0 +1,33 @@
+<?php
+
+/**
+ * @author Christoph Wurst <christoph@winzerhof-wurst.at>
+ *
+ * @license GNU AGPL version 3 or any later version
+ *
+ * This program is free software: you can redistribute it and/or modify
+ * it under the terms of the GNU Affero General Public License as
+ * published by the Free Software Foundation, either version 3 of the
+ * License, or (at your option) any later version.
+ *
+ * This program is distributed in the hope that it will be useful,
+ * but WITHOUT ANY WARRANTY; without even the implied warranty of
+ * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
+ * GNU Affero General Public License for more details.
+ *
+ * You should have received a copy of the GNU Affero General Public License
+ * along with this program.  If not, see <http://www.gnu.org/licenses/>.
+ *
+ */
+
+namespace OCP\Authentication\TwoFactorAuth;
+
+use OCP\AppFramework\Http\ContentSecurityPolicy;
+
+interface IProvidesCustomCSP {
+
+	/**
+	 * @return ContentSecurityPolicy
+	 */
+	public function getCSP();
+}
```
