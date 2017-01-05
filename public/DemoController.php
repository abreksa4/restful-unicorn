<?php
require_once(__DIR__.'/../vendor/autoload.php');

class DemoController extends AndrewBreksa\Unicorn\Controller\AbstractRestfulController
{

    /**
     * @param bool $hasId
     */
    public function getAllowedMethods($hasId = false)
    {
        if ($hasId == true) {
            return ['GET','OPTIONS','PATCH'];
        }
        return ['GET', 'OPTIONS'];
    }

    /**
     * @inheritDoc
     */
    public function get($id = null)
    {
        $response = $this->getApplication()->getResponse();
        $response = $response->withStatus(200);
        $response = $response->withBody(\AndrewBreksa\Unicorn\App\Application::newTempStream(json_encode(['method'=>'get','id' => $id])));
        return $response;
    }

    public function patch($id = null)
    {
        $response = $this->getApplication()->getResponse();
        $response = $response->withStatus(200);
        $response = $response->withBody(\AndrewBreksa\Unicorn\App\Application::newTempStream(json_encode(['method'=>'patch','id' => $id])));
        return $response;
    }


    /**
     * @return string
     */
    public function getRoute()
    {
        return '/restful-unicorn';
    }
}
