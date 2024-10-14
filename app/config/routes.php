<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
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

use Cake\Http\Response;
use Cake\Http\Runner;
use Cake\Http\ServerRequest;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/*
 * This file is loaded in the context of the `Application` class.
 * So you can use `$this` to reference the application class instance
 * if required.
 */
return function (RouteBuilder $routes): void {
    /*
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
     * inconsistently cased URLs when used with `{plugin}`, `{controller}` and
     * `{action}` markers.
     */
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope("/api/applicants", function (RouteBuilder $builder) {

        // Since this is a Dummy, I'll only check the JWT for the body and signature
        // and expect the header to always be { "alg":"HS256", "typ":"JWT" }
        $builder->registerMiddleware("custom_jwt", function (ServerRequest $req, Runner $runner) {

            $o403Response = (new Response())
                ->withStatus(403)
                ->withHeader("content-type", "text/plain");

            $aTokenMatch = $aTokenChunks = [];

            // Parse Bearer Token
            if (
                empty($sAuthHeader = $req->getEnv("HTTP_AUTHORIZATION"))
                ||!preg_match("/^Bearer (?P<token>.+)$/", $sAuthHeader, $aTokenMatch)
                ||empty($sToken = $aTokenMatch['token']??"")
                ||count($aTokenChunks = explode(".", $sToken, 3)) != 3
            ) return $o403Response;

            // check Signature
            if(strcmp(
                hash_hmac('sha256', $aTokenChunks[0].".".$aTokenChunks[1], getenv("JWT_TOKEN_SECRET"), true),
                base64_decode(str_replace(
                    ["_", "-"],
                    ["/", "+"], 
                    $aTokenChunks[2]
                ))
            )) return $o403Response;

            // ... Do further checks on the Token itself. 
            // Since that is just a Demo and I have no specifics in the Task,
            // I'll stop here. Having a correctly signed Token should be enough

            return $runner->handle($req);

        })->applyMiddleware('custom_jwt');

        $aApplicantIDPattern = ['id' => '[0-9]+'];

        $builder->get("/", 'Applicants::getApplicants');
        $builder->post("/", 'Applicants::postApplicants');
        $builder->get("/{id}", 'Applicants::getApplicant')->setPatterns($aApplicantIDPattern);
        $builder->put("/{id}", 'Applicants::putApplicant')->setPatterns($aApplicantIDPattern);
        $builder->delete("/{id}", 'Applicants::deleteApplicant')->setPatterns($aApplicantIDPattern);
    });


    $routes->scope('/', function (RouteBuilder $builder): void {
        /*
         * Here, we are connecting '/' (base path) to a controller called 'Pages',
         * its action called 'display', and we pass a param to select the view file
         * to use (in this case, templates/Pages/home.php)...
         */
        $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

        /* ...and connect the rest of 'Pages' controller's URLs.  */
        $builder->connect('/pages/*', 'Pages::display');


        /*
         * Connect catchall routes for all controllers.
         *
         * The `fallbacks` method is a shortcut for
         *
         * ```
         * $builder->connect('/{controller}', ['action' => 'index']);
         * $builder->connect('/{controller}/{action}/*', []);
         * ```
         *
         * It is NOT recommended to use fallback routes after your initial prototyping phase!
         * See https://book.cakephp.org/5/en/development/routing.html#fallbacks-method for more information
         */
        $builder->fallbacks();
    });

    /*
     * If you need a different set of middleware or none at all,
     * open new scope and define routes there.
     *
     * ```
     * $routes->scope('/api', function (RouteBuilder $builder): void {
     *     // No $builder->applyMiddleware() here.
     *
     *     // Parse specified extensions from URLs
     *     // $builder->setExtensions(['json', 'xml']);
     *
     *     // Connect API actions here.
     * });
     * ```
     */
};
