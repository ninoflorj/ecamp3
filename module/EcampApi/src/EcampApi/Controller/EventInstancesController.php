<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\EventInstanceSerializer;
use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class EventInstancesController extends AbstractRestfulBaseController
{

    /**
     * @return \EcampCore\Repository\EventInstanceRepository
     */
    private function getEventInstanceRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\EventInstance');
    }

    public function getList()
    {
        $criteria = $this->createCriteriaArray(array(
            'camp'		=> $this->params('camp'),
            'period'	=> $this->params('period'),
            'day'		=> $this->params('day'),
            'event'		=> $this->params('event'),
        ));

        $eventInstances = $this->getEventInstanceRepository()->findForApi($criteria);

        $eventInstanceSerializer = new EventInstanceSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($eventInstanceSerializer($eventInstances));
    }

    public function get($id)
    {
        $eventInstance = $this->getEventInstanceRepository()->find($id);

        $eventInstanceSerializer = new EventInstanceSerializer(
            $this->params('format'), $this->getEvent()->getRouter());

        return new JsonModel($eventInstanceSerializer($eventInstance));
    }

    public function head($id = null)
    {
        $format = $this->params('format');
        die("head." . $format);
    }

    public function create($data)
    {
        $format = $this->params('format');
        die("create." . $format);
    }

    public function update($id, $data)
    {
        $format = $this->params('format');
        die("update." . $format);
    }

    public function delete($id)
    {
        $format = $this->params('format');
        die("delete." . $format);
    }
}