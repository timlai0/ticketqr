<?php

class DB {

		function query($dbq, $debug = 1) {
	        $database = "ticketqr";
	        if (getenv('OPENSHIFT_MYSQL_DB_HOST')) {
	            $dbCon = mysqli_connect(getenv('OPENSHIFT_MYSQL_DB_HOST'), getenv('OPENSHIFT_MYSQL_DB_USERNAME'), getenv('OPENSHIFT_MYSQL_DB_PASSWORD'), $database);
	        } else {
	            $dbCon = mysqli_connect('localhost', "root", "", $database);
	        }

	        $dbCon->set_charset('utf8');

	        if(mysqli_connect_errno()) {
	            echo "Fehler 101" . mysqli_connect_error;
	        }

	        if ($db_result = mysqli_query($dbCon, $dbq)) {
	            $ar_result = array();
	            $i = 0;
	            if (!is_bool($db_result)) {
	                while($row = mysqli_fetch_assoc($db_result)) {
	                    $ar_result[$i] = $row;
	                    $i++;
	                }
	                return $ar_result;
	            } else {
	                return $db_result;
	            }
	        } else {
	            if ($debug) {
	            	http_response_code(500);
	                echo "ERROR: \"$dbq\"<br /><br />";
	                die(mysqli_error($dbCon));
	            } else {
	                return false;
	            }
	        }
	    }
    }

    class Ticket {
      public function generate_new($comment = 'Tim Lai') {

        $uid = uniqid();
        $db = new DB;
        $ar_tickets = $db->query("SELECT * FROM `tickets`");

        $nr = count($ar_tickets) + 1;


        $uid_hum = substr($uid,0,4).'-'.substr($uid,4,4).'-'.substr($uid,8);


        $db->query("INSERT INTO `tickets` (`id`, `uid`, `gen`, `comment`, `entry`) VALUES (NULL, '$uid', CURRENT_TIMESTAMP, '$comment', '0');");
        $validate_url = 'pc.timlai.de/ticketqr/validate.php?uid='.$uid.'&nr='.$nr;

        require("phpqrcode/qrlib.php");

        QRcode::png($validate_url, 'tmp/tmp.png', 'L', '4', 2, 3, 0);

        require('tfpdf/tfpdf.php');

        $pdf = new tFPDF('P', 'mm', array('148','105'));
        $pdf->SetMargins(5, 5);
        $pdf->AddPage();
        $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
        $pdf->SetFont('DejaVu','',12);

        $pdf->Ln(60);
        $pdf->Write(3,"Abi Party");
        $pdf->Ln(5);
        $pdf->Write(3,'Burgdorf');
        $pdf->Ln(5);
        $pdf->Write(3,'Ticket Nummer '.$nr);
        $pdf->Ln(5);


        $pdf->Image('tmp/tmp.png');

        $pdf->Write(3,$uid_hum);
        $pdf->Ln(5);
        $pdf->Write(3, $comment);
        $pdf->Output('tickets/Ticket-'.$nr.'.pdf', 'F');
        $pdf->Output('Ticket-'.$nr.'.pdf', 'I');
      }


      public function validate($uid, $nr) {
        $db = new DB;

        if ($ticket = $db->query("SELECT * FROM `tickets` WHERE `id` = $nr")) {;

          if ($uid == $ticket[0]['uid']) {
            echo "Valide";
          } else {
            echo "invalide";
          }

        }


      }
      
    }




 ?>
