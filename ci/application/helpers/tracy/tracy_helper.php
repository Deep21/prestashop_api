<?php

require_once 'vendor/autoload.php';
use Tracy\Debugger;

$ci =& get_instance();
$ci->load->helper('url');

Debugger::enable(Debugger::DETECT, 'C:\re\wamp\www\prestashop_test\ci\application\logs');

Debugger::$strictMode = TRUE;
Debugger::DEVELOPMENT;

