<?php
/**
 * Copyright (c) 2017 Andrew Breksa
 */

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/TestController.php');

class BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \AndrewBreksa\Unicorn\App\Application
     */
    public static $app = null;

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        //create the App
        $app = new AndrewBreksa\Unicorn\App\Application();
        $app->bootstrap();
        AndrewBreksa\Unicorn\Controller\AbstractRestfulController::bootstrapControllers($app, [
            TestController::class,
        ]);
        $app->getEventEmitter()->addListener(\AndrewBreksa\Unicorn\App\Application::EVENT_ROUTE_EXCEPTION, function (\League\Event\Event $event, \AndrewBreksa\Unicorn\App\Application $application) {
            $response = $application->getResponse();
            $response = $response->withStatus(404);
            $application->setResponse($response);
        });

        $app->getEventEmitter()->addListener(\AndrewBreksa\Unicorn\App\Application::EVENT_DISPATCH_EXCEPTION, function (\League\Event\Event $event, \AndrewBreksa\Unicorn\App\Application $application, \Exception $exception) {
            $response = $application->getResponse();
            if ($exception instanceof \League\Route\Http\Exception\MethodNotAllowedException) {
                $response = $response->withStatus(405);
            } else {
                $response = $response->withStatus(500);
            }
            $application->setResponse($response);
        });
        $app->setEmit(false);
        self::$app = $app;
    }

    public function testOptions()
    {
        $app = self::$app;
        $request = new \Zend\Diactoros\ServerRequest();
        $request = $request->withMethod("OPTIONS")->withUri(new \Zend\Diactoros\Uri("http://www.example.com/tests"));
        $app->setRequest($request);
        $app->run();
        self::assertEquals(200, $app->getResponse()->getStatusCode(), '200 not returned on correct OPTIONS request');
        self::assertArrayHasKey('Allow', $app->getResponse()->getHeaders(), 'allow not in headers');
        self::assertEquals("GET, OPTIONS", $app->getResponse()->getHeaderLine("Allow"), 'allow header is incorrect');
    }

    public function test404()
    {
        $app = self::$app;
        $request = new \Zend\Diactoros\ServerRequest();
        $request = $request->withMethod("GET")->withUri(new \Zend\Diactoros\Uri("http://www.example.com/tests/a"));
        $app->setRequest($request);
        $app->run();
        self::assertEquals(404, $app->getResponse()->getStatusCode(), '404 not returned on bad route request');
    }

    public function testId()
    {
        $app = self::$app;
        $request = new \Zend\Diactoros\ServerRequest();
        $request = $request->withMethod("GET")->withUri(new \Zend\Diactoros\Uri("http://www.example.com/tests/1"));
        $app->setRequest($request);
        $app->run();
        self::assertEquals(200, $app->getResponse()->getStatusCode(), '200 not returned on correct ID request');
        $app->getResponse()->getBody()->rewind();
        $body = json_decode($app->getResponse()->getBody()->getContents(), true);
        self::assertNotEmpty($body, 'empty body on collection request');
        self::assertArrayHasKey('id', $body, 'id key does not exist in response body');
        self::assertEquals(1, $body['id'], 'id not correct in response body');
        self::assertArrayHasKey('method', $body, 'method key does not exist in response body');
        self::assertEquals('GET', $body['method'], 'method not correct in response body');
    }

    public function testCollection()
    {
        $app = self::$app;
        $request = new \Zend\Diactoros\ServerRequest();
        $request = $request->withMethod("GET")->withUri(new \Zend\Diactoros\Uri("http://www.example.com/tests"));
        $app->setRequest($request);
        $app->run();
        self::assertEquals(200, $app->getResponse()->getStatusCode(), '200 not returned on correct collection request');
        $app->getResponse()->getBody()->rewind();
        $body = json_decode($app->getResponse()->getBody()->getContents(), true);
        self::assertNotEmpty($body, 'empty body on collection request');
        self::assertArrayHasKey('id', $body, 'id key does not exist in response body');
        self::assertEquals(null, $body['id'], 'id not correct in response body');
        self::assertArrayHasKey('method', $body, 'method key does not exist in response body');
        self::assertEquals('GET', $body['method'], 'method not correct in response body');
    }

    public function tearDown()
    {
        self::$app = null;
    }
}
