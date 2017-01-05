<?php
/**
 * Copyright (c) 2017 Andrew Breksa
 */
require_once(__DIR__.'/../vendor/autoload.php');
use AndrewBreksa\Unicorn\Controller\AbstractRestfulController;

class TestController extends AbstractRestfulController
{

    /**
     * @param bool $hasId
     */
    public function getAllowedMethods($hasId = false)
    {
        if ($hasId) {
            return ['GET','OPTIONS'];
        }
        return ['GET', 'OPTIONS'];
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return '/tests';
    }

    public function get($id = null)
    {
        $response = $this->getApplication()->getResponse();
        $response = $response->withStatus(200);
        $response = $response->withBody(\AndrewBreksa\Unicorn\App\Application::newTempStream(json_encode(['method'=>$this->getApplication()->getRequest()->getMethod(), 'id' => $id])));
        return $response;
    }
}
