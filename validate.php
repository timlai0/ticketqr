<?php
require_once("php.php");
$ticket = new Ticket;

$ticket->validate($_GET['uid'], $_GET['nr']);


 ?>
