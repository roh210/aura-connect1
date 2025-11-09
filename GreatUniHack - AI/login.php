<?php
//var_dump($_GET);
//die; // This stops the script
$view = new stdClass();
$view->pageTitle = 'Homepage';
require_once('Views/login.phtml');
