<?php
/*
TODO Things that need to be added:

- Option to show only hard or soft states 
- The ability to configure box size, or set it to dynamically fill the screen

- Add a filter and custom label so two dashboards could provide different labeled views.
(you may have two boxy dashboards with two different filters open- one for each dev team/environment/project)

*/


use Icinga\Data\Filter\Filter;
use Icinga\Module\Monitoring\Controller;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Module\Monitoring\Object\Service;
use Icinga\Web\Url;

#FIXME should this be Controller or ModuleActionController? The documentation was unclear.
class BoxyDash_IndexController extends Controller
{
    public function indexAction()
    {
        $this->getTabs()->activate('dashboard');
        $this->setAutorefreshInterval(10);
        $this->getServiceData();
        $this->getHostData();
    }


    # Create the tabs for the dashboard and the configuration tab.
    public function getTabs()
    {
        $tabs = parent::getTabs();
        $tabs->add(
            'dashboard',
            array(
                'title' => $this->translate('Dashboard'),
                'url'   => 'boxydash',
                'tip'  => $this->translate('Overview')
            )
        );
        $tabs->add(
            'config',
            array(
                'title' => $this->translate('Configure'),
                'url'   => 'boxydash/config',
                'tip'  => $this->translate('Configure')
            )
        );

        return $tabs;
    }


    # get the status of each host
    public function getHostData()
    {
        $columns = array(
            'host_name',
            'host_display_name',
            'host_state',
            'host_acknowledged',
            'host_in_downtime',
            'host_output',
            # Most of these may not be needed; included it for future integration/use (yeah, right)
            'host_icon_image',
            'host_handled',
            'host_attempt',
            'host_state_type',
            'host_hard_state',
            'host_last_check',
            'host_notifications_enabled',
            'host_unhandled_services',
            'host_action_url',
            'host_notes_url',
            'host_last_comment',
            'host_last_ack',
            'host_last_downtime',
            'host_active_checks_enabled',
            'host_passive_checks_enabled',
            'host_current_check_attempt',
            'host_max_check_attempts'

        );
        $query = $this->backend->select()->from('hostStatus', $columns);

        # This might be very very bad for very very large environments. I just don't know how well it'll perform.
        $this->view->hosts = $query->getQuery()->fetchAll();

        foreach ($this->view->hosts as $host) {
            #FIXME: using Service function for a host state. that's kinda ugly.
            #Loop through and make sure there's a field that says "OK" so we can grab the right css class
            $host->{'host_state_text'}=Service::getStateText($host->{'host_state'});
        }

    }
    # get the status of each service
    public function getServiceData()
    {
        $columns = array(
            'host_name',
            'host_display_name',
            'host_in_downtime',
            'host_acknowledged',
            'host_state',
            'service_description',
            'service_display_name',
            'service_state',
            'service_in_downtime',
            'service_acknowledged',
            'service_output',
            # Most of these may not be needed; included it for future integration/use (yeah, right)
            'host_last_state_change',
            'service_attempt',
            'service_last_state_change',
            'service_is_flapping',
            'service_state_type',
            'service_last_check',
            'service_last_comment',
            'current_check_attempt' => 'service_current_check_attempt',
            'max_check_attempts'    => 'service_max_check_attempts'
        );
        $query = $this->backend->select()->from('serviceStatus', $columns);


        $this->view->services = $query->getQuery()->fetchAll();

        foreach ($this->view->services as $service) {
            #Loop through and make sure there's a field that says "OK" so we can grab the right css class
            $service->{'service_state_text'}=Service::getStateText($service->{'service_state'});
            $service->{'host_state_text'}=Service::getStateText($service->{'host_state'});
        }
    }

    
/*  This function may be useful if I can figure out an overall way to show it that integrates well.
    public function statusSummary(){
        $this->view->stats = $this->backend->select()->from('statusSummary', array(
            'services_total',
            'services_ok',
            'services_problem',
            'services_problem_handled',
            'services_problem_unhandled',
            'services_critical',
            'services_critical_unhandled',
            'services_critical_handled',
            'services_warning',
            'services_warning_unhandled',
            'services_warning_handled',
            'services_unknown',
            'services_unknown_unhandled',
            'services_unknown_handled',
            'services_pending',
        ))->getQuery()->fetchRow();

    }
*/
}

