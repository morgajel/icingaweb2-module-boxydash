<?php

$this->providePermission(
    'boxydash',
    $this->translate('View Boxy Dashboard')
);

$this->menuSection('Boxy Dashboard', array(
    'url' => 'boxydash',
    'icon'  => 'dashboard',
    'priority'  => 20
));

$this->provideConfigTab('config', array(
    'title' => $this->translate('Configure boxydash'),
    'label' => $this->translate('config'),
    'url' => 'config'
));
