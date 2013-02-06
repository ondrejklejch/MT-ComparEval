<?php

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;

class NetteContext extends BehatContext {

	private static $container;

	
	public function getService( $name ) {
		return self::$container->getService( $name );
	}


	/**
	 * @BeforeSuite
	 */
	public static function createContainer() {
		$root = __DIR__ . '/../../';

		$configurator = new Nette\Config\Configurator;
		$configurator->addParameters( array(
			'appDir' => $root . '/app/'
		) );
		$configurator->setTempDirectory( $root . '/temp');
		$configurator->createRobotLoader()
			->addDirectory( $root . '/app' )
			->addDirectory( $root . '/libs')
			->register();

		// Create Dependency Injection container from config.neon file
		$configurator->addConfig( $root . '/app/config/config.neon');
		$configurator->addConfig( $root . '/app/config/config.local.neon', $configurator::NONE); // none section
		
		self::$container = $configurator->createContainer();
	}

}
