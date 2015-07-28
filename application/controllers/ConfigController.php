<?php

use Icinga\Web\Notification;
use Icinga\Data\ResourceFactory;
use Icinga\Forms\ConfirmRemovalForm;
use Icinga\Web\Controller;
use Icinga\Module\Monitoring\Forms\Config\BackendConfigForm;
use Icinga\Module\Monitoring\Forms\Config\InstanceConfigForm;
use Icinga\Module\Monitoring\Forms\Config\SecurityConfigForm;
use Icinga\Module\Boxydash\Forms\Config\SettingConfigForm;

class BoxyDash_ConfigController extends Controller
{
    public function indexAction()
    {
        $this->view->settingsConfig = $this->Config('settings');

        $form = new SettingConfigForm();
        $this->view->form = $form;
        $form->setTitle($this->translate('Edit Existing Instance'));
        $form->setIniConfig($this->Config('config'));
        $form->setRedirectUrl('boxydash');
        $form->handleRequest();

        $this->view->form = $form;

        $this->getTabs()->activate('config');
    }

    public function editSettingsAction()
    {
        $form = new SettingConfigForm();

        $form->setTitle($this->translate('Edit Settings'));
        $form->setIniConfig($this->Config('settings'));
        $form->setRedirectUrl('boxydash');
        $form->handleRequest();


        $this->view->form = $form;
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

