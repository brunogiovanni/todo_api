<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

Router::scope('/api', function (RouteBuilder $routes) {
    $routes->addExtensions('json');
    $routes->setExtensions('json');
    
    //Users
    $routes->get('/user/*', ['controller' => 'Users', 'action' => 'view', '_ext' => 'json']);
    $routes->post('/user', ['controller' => 'Users', 'action' => 'add', '_ext' => 'json']);
    $routes->put('/user/*', ['controller' => 'Users', 'action' => 'edit', '_ext' => 'json']);
    $routes->get('/logout', ['controller' => 'Users', 'action' => 'logout', '_ext' => 'json']);
    $routes->connect('/login', ['controller' => 'Users', 'action' => 'login', '_ext' => 'json']);
    
    //Tasks
    $routes->get('/tasks', ['controller' => 'Tasks', 'action' => 'index', '_ext' => 'json']);
    $routes->get('/task/*', ['controller' => 'Tasks', 'action' => 'view', '_ext' => 'json']);
    $routes->post('/task', ['controller' => 'Tasks', 'action' => 'add', '_ext' => 'json']);
    $routes->put('/task/*', ['controller' => 'Tasks', 'action' => 'edit', '_ext' => 'json']);
    $routes->delete('/task/*', ['controller' => 'Tasks', 'action' => 'delete', '_ext' => 'json']);
    
    $routes->fallbacks(DashedRoute::class);
});

/**
 * Load all plugin routes. See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
