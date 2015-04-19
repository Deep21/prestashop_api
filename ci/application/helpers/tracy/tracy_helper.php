<?php
require_once 'vendor/autoload.php';
use Tracy\Debugger;

$ci =& get_instance();
$ci->load->helper('url');

Debugger::enable();
Debugger::enable(Debugger::DETECT);
Debugger::$strictMode = TRUE;
Debugger::DEVELOPMENT;

