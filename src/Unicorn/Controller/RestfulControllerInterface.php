<?php
/**
 * Copyright (c) 2017 Andrew Breksa
 */

namespace AndrewBreksa\Unicorn\Controller;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface RestfulControllerInterface
 * @package AndrewBreksa\Unicorn\Controller
 * @author Andrew Breksa
 */
interface RestfulControllerInterface
{

    /**
     * Get the allowed methods for collections and entity
     *
     * @param bool $hasId
     */
    public function getAllowedMethods($hasId = false);

    /**
     * Get the base route to work with
     *
     * @return string
     */
    public function getRoute();

    /**
     * Get the regex applied to the route matching for the entity route, defaults to [0-9]+
     *
     * @return string|null
     */
    public function getEntityIdRegex();

    /**
     * Runs on a GET request
     *
     * @param mixed $id
     * @return ResponseInterface
     */
    public function get($id = null);

    /**
     * Runs on a PATCH request
     *
     * @param mixed $id
     * @return ResponseInterface
     */
    public function patch($id = null);

    /**
     * Runs on a DELETE request
     *
     * @param mixed $id
     * @return ResponseInterface
     */
    public function delete($id = null);

    /**
     * Runs on a PUT request
     *
     * @param mixed $id
     * @return ResponseInterface
     */
    public function put($id = null);

    /**
     * Runs on a HEAD request
     * @param null $id
     * @return ResponseInterface
     */
    public function head($id = null);

    /**
     * Runs on an OPTION request
     *
     * @param null $id
     * @return mixed
     */
    public function options($id = null);

    /**
     * Auto-wire each method to it's corresponding route
     */
    public function bootstrap();

    /**
     * Internal method to run the method associated with the HTTP verb
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $params
     * @return ResponseInterface|null|false
     * @throws InvalidArgumentException
     */
    public function execute(ServerRequestInterface $request, ResponseInterface $response, array $params = []);

	/**
	 * Returns as array of all the routes this controller supports in the form of an associative array of 'type', 'method' & 'route' pairs
	 */
	public function getRoutes();
}
