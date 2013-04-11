<?php
namespace RobertLemke\NewRelic;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "RobertLemke.NewRelic"   *
 *                                                                        */

use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Mvc\Controller\ControllerInterface;
use TYPO3\Flow\Mvc\RequestInterface;
use TYPO3\Flow\Mvc\ResponseInterface;
use TYPO3\Flow\Package\Package as BasePackage;
use TYPO3\Flow\Core\Bootstrap;

/**
 * TYPO3 Flow package bootstrap
 */
class Package extends BasePackage {

	/**
	 * Configures New Relic, if it is installed
	 *
	 * @param Bootstrap $bootstrap
	 * @return void
	 */
	public function boot(Bootstrap $bootstrap) {
		if (extension_loaded('newrelic')) {
			$appName = getenv('ROBERTLEMKE_NEWRELIC_APPNAME') ?: NULL;
			if ($appName === NULL) {
				$appName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'TYPO3 Flow Application';
			}
			newrelic_set_appname($appName);
			$dispatcher = $bootstrap->getSignalSlotDispatcher();
			$dispatcher->connect('TYPO3\Flow\Mvc\Dispatcher', 'beforeControllerInvocation',
				function(RequestInterface $request, ResponseInterface $response, ControllerInterface $controller) {
					if ($request instanceof ActionRequest) {
						newrelic_name_transaction ($request->getControllerPackageKey() . ($request->getControllerSubpackageKey() != '' ? '/' . $request->getControllerSubpackageKey() : '') . '/' . $request->getControllerActionName());
					}
				}
			);
		}
	}

}
?>
