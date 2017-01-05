<?php
/**
 * Copyright (c) 2017 Andrew Breksa
 */

namespace AndrewBreksa\Unicorn\Controller;

use AndrewBreksa\Unicorn\App\Application;
use League\Route\Http\Exception\MethodNotAllowedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AbstractRestfulController
 * @package AndrewBreksa\Unicorn\Controller
 * @author Andrew Breksa
 */
abstract class AbstractRestfulController implements RestfulControllerInterface
{

    /**
     * The Application instance
     *
     * @var Application|null
     */
    protected $application = null;

    /**
     * The AbstractRestfulController constructor
     *
     * AbstractRestfulController constructor.
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @inheritdoc
     */
    public function options($id = null)
    {
        $response = $this->getApplication()->getResponse();
        $response = $response->withAddedHeader("Allow", implode(', ', $this->getAllowedMethods($id == null ? false : true)));
        return $response;
    }

    /**
     * @inheritdoc
     */
    public function bootstrap()
    {
        $app = $this->getApplication();
        $controller = get_class($this);
        foreach ($this->getAllowedMethods(false) as $method) {
            $this->application->getRouteCollection()->addRoute($method, $this->getRoute(), function (ServerRequestInterface $request, ResponseInterface $response, $params = []) use ($app, $controller) {
                return $app->getContainer()->get($controller)->execute($request, $response, $params);
            });
        }
        foreach ($this->getAllowedMethods(true) as $method) {
            $this->application->getRouteCollection()->addRoute($method, $this->getRoute() . '/{id:' . ($this->getEntityIdRegex() == null ? '[0-9]+' : $this->getEntityIdRegex()) . '}', function (ServerRequestInterface $request, ResponseInterface $response, $params = []) use ($app, $controller) {
                return $app->getContainer()->get($controller)->execute($request, $response, $params);
            });
        }
    }

    /**
     * @inheritdoc
     */
    public function getEntityIdRegex()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function execute(ServerRequestInterface $request, ResponseInterface $response, array $params = [])
    {
        $methodName = strtolower($request->getMethod());
        if (array_key_exists('id', $params)) {
            $id = $params['id'];
        } else {
            $id = null;
        }
        if (!method_exists($this, $methodName)) {
            throw new \InvalidArgumentException('the method ' . $methodName . ' is not defined in ' . get_class($this) . ', but is being matched as it is present in getAllowedMethods()');
        }

        return $this->$methodName($id);
    }

    /**
     * @inheritdoc
     */
    public function get($id = null)
    {
        $response = $this->getApplication()->getResponse();
        $response = $response->withStatus(405);

        return $response;
    }

    /**
     * @return Application|null
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * @inheritdoc
     */
    public function patch($id = null)
    {
        $response = $this->getApplication()->getResponse();
        $response = $response->withStatus(405);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function delete($id = null)
    {
        $response = $this->getApplication()->getResponse();
        $response = $response->withStatus(405);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function put($id = null)
    {
        $response = $this->getApplication()->getResponse();
        $response = $response->withStatus(405);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function head($id = null)
    {
        $response = $this->getApplication()->getResponse();
        $response = $response->withStatus(405);

        return $response;
    }

    /**
     * Takes the provided application and array of controller class names and adds them to the Container and runs bootsstrap() on each one
     *
     * @param Application $application
     * @param string[] $controllers
     */
    public static function bootstrapControllers(Application $application, array $controllers = [])
    {
        if (empty($controllers)) {
            $controllers = $application->getConfig()['restful-unicorn']['controllers']; //todo: change this hardcoded path?
        }
        /*
		 * $controller should be a fully-qualified class name
		 */
        foreach ($controllers as $controller) {
            /**
             * @var $instance AbstractRestfulController
             */
            $instance = new $controller($application);
            $instance->bootstrap();
            $application->getContainer()->share($controller, $instance);
        }
    }
}
