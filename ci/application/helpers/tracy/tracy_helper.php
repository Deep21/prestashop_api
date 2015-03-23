<?php
require_once 'vendor/autoload.php';
use Tracy\Debugger;


Debugger::enable();
Debugger::enable(Debugger::DETECT,'C:\re\wamp\www\prestashop\ci\application\logs');
Debugger::$strictMode = TRUE;
Debugger::DEVELOPMENT;

