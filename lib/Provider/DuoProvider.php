<?php

/**
 * @author El-ad Blech <elie@theinfamousblix.com>
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\TwoFactorDuo\Provider;

use OCA\TwoFactorDuo\Web;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\Authentication\TwoFactorAuth\IProvidesCustomCSP;
use OCP\IConfig;
use OCP\IUser;
use OCP\Template;

class DuoProvider implements IProvider, IProvidesCustomCSP {

	/** @var IConfig */
	private $config;

	private function getConfig() {
		return $this->config->getSystemValue('twofactor_duo', null);
	}

	public function __construct(IConfig $config) {
		$this->config = $config;
	}

	/**
	 * Get unique identifier of this 2FA provider
	 *
	 * @return string
	 */
	public function getId() {
		return 'duo';
	}

	/**
	 * Get the display name for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDisplayName() {
		return 'Duo';
	}

	/**
	 * Get the description for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Duo';
	}

	/**
	 * Get the Content Security Policy for the template (required for showing external content, otherwise optional)
	 *
	 * @return ContentSecurityPolicy
	 */
	public function getCSP() {
		$csp = new ContentSecurityPolicy();
		$csp->addAllowedChildSrcDomain('https://*.duosecurity.com');
		$csp->addAllowedStyleDomain('https://*.duosecurity.com');
		$csp->addAllowedFrameDomain('https://*.duosecurity.com');
		return $csp;
	}

	/**
	 * Get the template for rending the 2FA provider view
	 *
	 * @param IUser $user
	 * @return Template
	 */
	public function getTemplate(IUser $user) {
		$config = $this->getConfig();
		$tmpl = new Template('twofactor_duo', 'challenge');
		$tmpl->assign('user', $user->getUID());
		$tmpl->assign('IKEY', $config['IKEY']);
		$tmpl->assign('SKEY', $config['SKEY']);
		$tmpl->assign('AKEY', $config['AKEY']);
		$tmpl->assign('HOST', $config['HOST']);
		return $tmpl;
	}

	/**
	 * Verify the given challenge
	 *
	 * @param IUser $user
	 * @param string $challenge
	 */
	public function verifyChallenge(IUser $user, $challenge) {
		$config = $this->getConfig();

		$IKEY = $config['IKEY'];
		$SKEY = $config['SKEY'];
		$AKEY = $config['AKEY'];

		$resp = Web::verifyResponse($IKEY, $SKEY, $AKEY, $challenge);
		if ($resp) {
			return true;
		}
		return false;
	}

	/**
	 * Decides whether 2FA is enabled for the given user
	 *
	 * @param IUser $user
	 * @return boolean
	 */
	public function isTwoFactorAuthEnabledForUser(IUser $user) {
		return true;
	}

}
