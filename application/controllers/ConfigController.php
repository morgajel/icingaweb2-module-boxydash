<?php

use Icinga\Web\Controller;

class BoxyDash_ConfigController extends Controller
{
    public function indexAction()
    {
        $this->getTabs()->activate('config');
        #TODO set up configurations here.
    }
    public function getTabs()
    {
        $tabs = parent::getTabs();
        $tabs->add(
            'dashboard',
            array(
                'title' => 'Dashboard',
                'url'   => 'boxydash'
            )
        );
        $tabs->add(
            'config',
            array(
                'title' => 'Configure',
                'url'   => 'boxydash/config'
            )
        );

        return $tabs;
    }
}

