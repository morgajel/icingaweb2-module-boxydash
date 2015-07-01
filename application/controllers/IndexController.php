<?php

use Icinga\Web\Controller\ModuleActionController;
use Icinga\Web\Controller\ActionController;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Module\Monitoring\Object\Service;
use Icinga\Module\Monitoring\Object\ServiceList;

class BoxyDash_IndexController extends ModuleActionController
{
    public function indexAction()
    {
        $serviceList = new ServiceList($this->backend);
        $serviceList->setFilter(Filter::fromQueryString((string) $this->params->without('service_problem', 'service_handled')));
        $this->serviceList = $serviceList;

    }
}


class BoxyDash_IndexController extends ActionController
{
    public function indexAction()
    {
        $baseUrl = rtrim($this->Config()->get('boxydash', 'base_url', '/boxydash'), '/');

        $this->view->url = sprintf(
             '%s/graph?host=%s&srv=%s&view=%d',
             $baseUrl,
             urlencode($this->getParam('host')),
             urlencode($this->getParam('srv')),
             $this->getParam('view')
        );
    }
}

