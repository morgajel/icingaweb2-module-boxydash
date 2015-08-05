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
            'include_soft_status',
            array(
                'required'      => true,
                'label'         => $this->translate('Include Soft Status'),
                'description'   => $this->translate('Enable this to have soft status included')
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
