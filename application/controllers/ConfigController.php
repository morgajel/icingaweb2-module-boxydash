<?php

use Icinga\Web\Controller\ModuleActionController;

class BoxyDash_ConfigController extends ModuleActionController
{
    public function indexAction()
    {
        $this->view->tabs = $this->Module()->getConfigTabs()->activate('config');
        $hintHtml = $this->view->escape($this->translate(
            'Configuration form is still missing in Boxy Dashboard.'
          . ' in %s following this example:'
        ));  
    }
}

