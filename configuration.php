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

