<?php
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__.'/DemoController.php');

//create the App
$app = new AndrewBreksa\Unicorn\App\Application();
$app->bootstrap();
AndrewBreksa\Unicorn\Controller\AbstractRestfulController::bootstrapControllers($app, [
    DemoController::class
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

//run the app!
$app->run();
