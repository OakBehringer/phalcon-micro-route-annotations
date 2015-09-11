<?php

namespace PhalconExt;

use Phalcon\Exception;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Annotations\Adapter\Memory as MemoryAnnAdaptor;

/**
 * Class MicroRouteAnnotations
 * Provides annotation based routing for a micro application
 */
class MicroRouteAnnotations {
	/** @var \Phalcon\Mvc\Micro */
	protected $app;
	/** @var \Phalcon\DiInterface */
	protected $di;
	/** @var Micro\Collection[] */
	protected $collections = array();
	protected $namespaces = array();
	/** @var Loader */
	protected $ourLoader;

	public function __construct(Micro $app) {
		$this->app = $app;
	}

	/**
	 * Add a controller directory that does not have namespaced controllers
	 *
	 * @param $dir
	 */
	public function addControllerDirectory($dir) {
		$this->addControllerNamespace('', $dir);
	}

	/**
	 * Add a controller directory for namespaced controllers
	 *
	 * @param $namespace
	 * @param $dir
	 */
	public function addControllerNamespace($namespace, $dir) {
		$this->namespaces[] = array(
			'ns' => $namespace,
			'dir' => $dir,
		);
	}

	/**
	 * If the class isn't already loaded, and an autoloader hasn't been set up
	 * for the class (i.e. class not loaded), we will set up our own autoloader
	 * for all namespaces and dirs registered and attempt to load the class
	 *
	 * @param $className
	 * @throws Exception
	 */
	protected function autoloadClass($className) {
		// has the class be loaded?
		if (class_exists($className)) {
			return;
		}

		if (!$this->ourLoader) {
			$this->ourLoader = new Loader();
			$ourLoaderNamespaces = array();
			$ourLoaderDirs = array();

			foreach ($this->namespaces as $ns) {
				if ($ns['ns']) {
					$ourLoaderNamespaces[$ns['ns']] = $ns['dir'];
				}
				else {
					$ourLoaderDirs[] = $ns['dir'];
				}

			}

			$this->ourLoader->registerNamespaces($ourLoaderNamespaces);
			$this->ourLoader->registerDirs($ourLoaderDirs);
		}

		$loaded = $this->ourLoader->autoLoad($className);

		if (!$loaded) {
			throw new \Exception('Unable to load autoload class ' . $className);
		}
	}

	/**
	 * Scan the added namespaces/directories, create appropriate micro collections
	 *
	 * @throws \Exception
	 */
	protected function scanNamespaces() {
		$reader = new MemoryAnnAdaptor();

		foreach ($this->namespaces as $ns) {
			$scan = scandir($ns['dir']);

			foreach ($scan as $f) {
				$ff = $ns['dir'] . DIRECTORY_SEPARATOR . $f;
				$info = pathinfo($ns['dir'] . PATH_SEPARATOR . $f);

				// make sure we're working with a php file
				if (!is_file($ff) || $info['extension'] != 'php')
					continue;

				// OK, let's get the fully qualified class name
				$className = $ns['ns'] . '\\' . substr($f, 0, -(strlen($info['extension']) + 1));
				$this->autoloadClass($className);

				// Create the reflector to read the annotations
				$reflector = $reader->get($className);
				/** @var Annotation[] $annotations */
				$classAnn = $reflector->getClassAnnotations();
				// will store the prefix for all URLs here
				$curPrefix = '';
				// Will we setup default rest routes?
				$doRest = false;

				// Check if we have a class annotation for this class
				if ($classAnn) {
					// loop all annotations
					foreach ($classAnn as $ann) {
						// check if we have an annotation that we care about
						switch ($ann->getName())  {
							case 'RoutePrefix':
								$args = $ann->getArguments();

								// we need one argument, the prefix!
								if (count($args) >= 1) {
									$curPrefix = $args[0];
								}

								break;

							case 'RouteDefault':
								$args = $ann->getArguments();

								// we need one argument, the default type!
								if (count($args) >= 1 && $args[0] == 'Rest') {
									$doRest = true;
								}
						}
					}
				}

				if ($doRest) {
					$this->setupRestRoutes($className, $curPrefix);
				}

				/** @var Annotation[] $annotations */
				$methAnn = $reflector->getMethodsAnnotations();

				// check if we have any method annotations for this class
				if ($methAnn) {
					// lopp through the functions with annotations
					foreach ($methAnn as $function => $annotations) {
						// loop the annotations for the current function
						foreach ($annotations as $ann) {
							$args = $ann->getArguments();

							// handle the annotations we care about
							switch ($ann->getName()) {
								case 'Get':
								case 'Post':
								case 'Put':
								case 'Delete':
									if (count($args) != 1) {
										throw new \Exception('Invalid argument count for ' .
											$className . '::' . $function . '() / @' . $ann->getName());
									}

									$this->updateCollection($className, $function, $curPrefix . $args[0], $ann->getName());
									break;
								case 'Route':
									if ($ann->getNamedArgument('methods')) {
										$httpMethods = $ann->getNamedArgument('methods');
									}
									else {
										$httpMethods = array('GET', 'PUT', 'POST', 'DELETE');
									}

									if (count($args) < 1) {
										throw new \Exception('Invalid argument count for ' .
											$className . '::' . $function . '() / @' . $ann->getName());
									}

									foreach ($httpMethods as $mtd) {
										$this->updateCollection($className, $function, $curPrefix . $args[0], $mtd);
									}
									break;
							}
						}

					}
				}
			}

		}
	}

	protected function setupRestRoutes($className, $routePrefix) {
		$restRoutes = array(
			array(
				'function' => 'indexAction',
				'verb' => 'get',
				'route' => '/',
			),
			array(
				'function' => 'getAction',
				'verb' => 'get',
				'route' => '/{id}',
			),
			array(
				'function' => 'putAction',
				'verb' => 'put',
				'route' => '/{id}',
			),
			array(
				'function' => 'postAction',
				'verb' => 'post',
				'route' => '/',
			),
			array(
				'function' => 'deleteAction',
				'verb' => 'delete',
				'route' => '/{id}',
			),
		);

		foreach ($restRoutes as $routeInfo) {
			$this->updateCollection(
				$className,
				$routeInfo['function'],
				$routePrefix . $routeInfo['route'],
				$routeInfo['verb']
			);
		}
	}

	/**
	 * Create or append the method, uri, and http method to the className
	 *
	 * @param $className
	 * @param $classMethod
	 * @param $uri
	 * @param $httpMethod
	 */
	protected function updateCollection($className, $classMethod, $uri, $httpMethod) {
		if (!isset($this->collections[$className])) {
			$this->collections[$className] = new Micro\Collection();
			$this->collections[$className]->setHandler($className)->setLazy(true);
		}

		// always remove trailing slash
		while (strlen($uri) > 0 && substr($uri, -1) == '/') {
			$uri = substr($uri, 0, -1);
		}

		$verb = strtolower($httpMethod);
		$this->collections[$className]->$verb($uri, $classMethod);
	}

	/**
	 * Mount all collections
	 */
	public function mount() {
		$this->scanNamespaces();

		foreach ($this->collections as $col) {
			$this->app->mount($col);
		}
	}
}