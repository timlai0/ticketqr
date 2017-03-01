<?php
require_once("php.php");
$ticket = new Ticket;

if (!empty($_GET['c'])) {
  $ticket->generate_print($_GET['c']);
} else {
  $ticket->generate_print();
}

 ?>
