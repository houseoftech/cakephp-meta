<?php
namespace Meta\Config;

use Cake\Routing\Router;

Router::prefix('admin', function (RouteBuilder $routes) {
	$routes->plugin('Meta', function (RouteBuilder $routes) {
		$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => DashedRoute::class]);
		$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);
	});
});

Router::plugin('Meta', function ($routes) {
		$routes->connect('/', ['controller' => 'Meta', 'action' => 'index'], ['routeClass' => 'DashedRoute']);
		$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);
		$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);
});
