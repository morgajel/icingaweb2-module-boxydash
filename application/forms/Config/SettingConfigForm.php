<?php
/* Icinga Web 2 | (c) 2013-2015 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Boxydash\Forms\Config;

use Exception;
use Icinga\Data\ConfigObject;
use Icinga\Data\ResourceFactory;
use Icinga\Web\Form;
use InvalidArgumentException;
use Icinga\Application\Config;
use Icinga\Exception\ConfigurationError;
use Icinga\Forms\ConfigForm;
use Icinga\Web\Notification;

/**
 * Form class for creating/modifying monitoring Settings
 */
class SettingConfigForm extends ConfigForm
{
    protected $resources;

    public function init()
    {
        $this->setName('form_config_boxydash_settings');
        $this->setSubmitLabel($this->translate('Save Changes'));
    }

    public function createElements(array $formData)
    {
        $this->addElement(
            'text',
            'setting_refresh',
            array(
                'label'         => $this->translate('Dashboard Refresh'),
                'description'   => $this->translate('How quickly the dashboard should refresh (between 1 second and 20 minutes)'),
                'value'         => '10',
                    'validators'    => array(
                        array(
                            'Between',
                            false,
                            array(
                                'min'  => '1',
                                'max'  => '1200',
                                'inclusive' => true,
                            )
                        )
                    )
            )
        );
        $this->addElement(
            'text',
            'setting_boxsize',
            array(
                'label'         => $this->translate('Box Size'),
                'description'   => $this->translate('The size of displayed boxes in pixels'),
                'value'         => '10',
                    'validators'    => array(
                        array(
                            'Regex',
                            false,
                            array(
                                'pattern'  => '/^[\d]+$/',
                                'messages' => array(
                                    'regexNotMatch' => $this->translate(
                                        'The application prefix must be a positive integer.'
                                    )
                                )
                            )
                        )
                    )
            )
        );
        $this->addElement(
            'checkbox',
            'include_softstate',
            array(
                'required'      => true,
                'value'         => true,
                'label'         => $this->translate('Include Soft Status'),
                'description'   => $this->translate('Enable this to have soft status included')
            )
        );
        $this->addElement(
            'checkbox',
            'requires_authentication',
            array(
                'required'      => true,
                'value'         => true,
                'label'         => $this->translate('Require Authentication?'),
                'description'   => $this->translate('Does Boxydash require Authentication? Warning, this may expose sensitive network information.')
            )
        );
        $this->addElement(
            'checkbox',
            'show_legend',
            array(
                'required'      => true,
                'value'         => true,
                'label'         => $this->translate('Show the Legend'),
                'description'   => $this->translate('Do you want to show the legend?')
            )
        );

    }

    public function onSuccess()
    {
        $this->config->setSection('settings', $this->getValues());

        if ($this->save()) {
            Notification::success($this->translate('New setings have successfully been stored'));
        } else {
            return false;
        }
    }

    /**
     * @see Form::onRequest()
     */
    public function onRequest()
    {
        $this->populate($this->config->getSection('settings')->toArray());
    }



}
