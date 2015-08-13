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
    protected $requiresAuthentication = true;
    protected $default_ignore_softstate = false;
    protected $default_boxsize = 20;
    protected $default_refresh = 10;

    public function indexAction()
    {
        $this->getTabs()->activate('dashboard');
        $this->config = $this->Config('config'); #? does this work?

        $this->determine_requires_authentication();
        $this->determine_refresh();
        $this->determine_boxsize();
        $this->determine_ignore_softstate();

        $this->_request->getParams();

        $this->getServiceData();
        $this->getHostData();
    }

    public function determine_refresh()
    {
        if (intval($this->_getParam("refresh")) >=1){
            $this->refresh = intval($this->_getParam("refresh"));

        #Failing that, check to see if it's already in the configuration
        }elseif (is_numeric( $this->config->get('settings','setting_refresh','missing'))) {
            # Note that $default_refresh should never be hit on this line.
            
            $this->refresh = intval($this->config->get('settings','setting_refresh',$this->default_refresh));
        }else{
        #failing THAT, use our default.
            $this->refresh =  $this->default_refresh ;
        }
        $this->setAutorefreshInterval( $this->refresh );
    }
    public function determine_boxsize()
    {
        if (is_numeric($this->_getParam("boxsize"))){
            $this->view->boxsize = $this->_getParam("boxsize");

        #Failing that, check to see if it's already in the configuration
        }elseif (is_numeric( $this->config->get('settings','setting_boxsize','missing'))) {
            # Note that $default_boxsize should never be hit on this line.
            $this->view->boxsize = $this->config->get('settings','setting_boxsize',$this->default_boxsize);
        }else{
        #failing THAT, use our default.
            $this->view->boxsize = $this->default_boxsize;
        }
    }
    public function determine_ignore_softstate()
    {

        # First determine if uri override is being passed
        if (is_numeric($this->_getParam("ignore_softstate"))){
            $this->view->ignore_softstate = $this->_getParam("ignore_softstate");

        #Failing that, check to see if it's already in the configuration
        }elseif ($this->config->get('settings','ignore_softstate','missing') !='missing'  ) {
            # Note that $default_ignore_softstate should never be hit on this line.
            $this->ignore_softstate = $this->config->get('settings','setting_ignore_softstate',$this->default_ignore_softstate);

        #failing THAT, use our default.
        }else{
            $this->ignore_softstate = $this->default_ignore_softstate;
        }

    }
    public function determine_requires_authentication()
    {

        #check to see if it's already in the configuration
        if ($this->config->get('settings','requires_authentication',$this->requiresAuthentication) ) {
            $this->requiresAuthentication = true;
        }else{
            $this->requiresAuthentication = false;


        #failing THAT, the predefined system default is used.
        }
#        $this->view->debug=intval($this->requiresAuthentication);
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
            'host_action_url',
            'host_notes_url',
            'host_active_checks_enabled',
            'host_passive_checks_enabled',
            'host_current_check_attempt',
            'host_max_check_attempts'

        );
        $query = $this->backend->select()->from('hostStatus', $columns);
        $query->order('host_name', 'desc');

        # This might be very very bad for very very large environments. I just don't know how well it'll perform.
        $this->view->hosts = $query->getQuery()->fetchAll();

        foreach ($this->view->hosts as $host) {
            #FIXME: using Service function for a host state. that's kinda ugly.

            # if the host is in a soft state && we're ignoring soft state, say things are OK
            if ( ! $host->{'host_hard_state'} && $this->ignore_softstate ){
                $host->{'host_state_text'}=Service::getStateText(0);

            #otherwise display as normal
            }else{
                $host->{'host_state_text'}=Service::getStateText($host->{'host_state'});
            }
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
            'host_hard_state',
            'service_hard_state',
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
            'current_check_attempt' => 'service_current_check_attempt',
            'max_check_attempts'    => 'service_max_check_attempts'
        );
        $query = $this->backend->select()->from('serviceStatus', $columns);
        $query->order('host_name', 'desc');

        $this->view->services = $query->getQuery()->fetchAll();

        foreach ($this->view->services as $service) {
            #Loop through and make sure there's a field that says "OK" so we can grab the right css class
            $service->{'service_state_text'}=Service::getStateText($service->{'service_state'});
            $service->{'host_state_text'}=Service::getStateText($service->{'host_state'});

            # If we're not in a hard state AND the service state isn't unknown (it never goes past soft) AND we're ignoring softstate, then things are OK.
            if ( !$service->{'service_hard_state'} && $service->{'service_state'} != 3 && $this->ignore_softstate ){
                $service->{'service_state_text'}=Service::getStateText(0);
            }else{
                $service->{'service_state_text'}=Service::getStateText($service->{'service_state'});

            }


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

