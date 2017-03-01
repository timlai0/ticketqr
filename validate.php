<?php

  if (!empty($_GET['uid']) and !empty($_GET['nr']) ) {
    require_once("php.php");
    Ticket::validate($_GET['uid'], $_GET['nr']);
  }

?>
