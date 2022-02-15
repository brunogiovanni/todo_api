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

use Cake\Http\Middleware\EncryptedCookieMiddleware;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\DashedRoute;

return static function (RouteBuilder $routes) {
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
    $routes->setRouteClass(DashedRoute::class); 

    $routes->scope('/', function (RouteBuilder $builder) {
        $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
        $builder->connect('/pages/*', ['controller' => 'Pages::display']);

        $builder->fallbacks();
    });

    $routes->scope('/api', function (RouteBuilder $builder) {
        $builder->setExtensions(['json']);
        // $builder->applyMiddleware('cookies');

        //Users
        // $builder->resources('Users');
        $builder->get('/user/*', ['controller' => 'Users', 'action' => 'view', '_ext' => 'json']);
        $builder->post('/user', ['controller' => 'Users', 'action' => 'add', '_ext' => 'json']);
        $builder->put('/user/*', ['controller' => 'Users', 'action' => 'edit', '_ext' => 'json']);
        $builder->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
        $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);
        
        //Tasks
        // $builder->resources('Tasks', ['_ext' => 'json']);
        $builder->get('/tasks', ['controller' => 'Tasks', 'action' => 'index', '_ext' => 'json']);
        $builder->get('/task/*', ['controller' => 'Tasks', 'action' => 'view', '_ext' => 'json']);
        $builder->post('/task', ['controller' => 'Tasks', 'action' => 'add', '_ext' => 'json']);
        $builder->put('/task/*', ['controller' => 'Tasks', 'action' => 'edit', '_ext' => 'json']);
        $builder->delete('/task/*', ['controller' => 'Tasks', 'action' => 'delete', '_ext' => 'json']);

        $builder->fallbacks();
    });
};
