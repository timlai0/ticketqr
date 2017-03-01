<?php

$login = new Login;
$login->check();

class DB {

	function connect() {
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

		return $dbCon;
	}

		function query($dbq, $debug = 1) {
			$dbCon = DB::connect();

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

			function excape($var) {
				$dbCon = $this->connect();
				return $dbCon->mysqli_real_escape_string($var);
			}
		}



    class Ticket {
      public function generate($comment = '') {
				#Generiet ein Ticket in der Datenbank
        $uid = uniqid();

        $ar_tickets = DB::query("SELECT * FROM `tickets`");

        $nr = count($ar_tickets) + 1;


        DB::query("INSERT INTO `tickets` (`id`, `uid`, `gen`, `comment`, `entry`) VALUES (NULL, '$uid', CURRENT_TIMESTAMP, '$comment', '0');");
        $validate_url = 'pc.timlai.de/ticketqr/validate.php?uid='.$uid.'&nr='.$nr;

				if (!file_exists('tmp')) {
					mkdir('tmp', 0777, true);
				}



				#QR

				require("phpqrcode/qrlib.php");
        QRcode::png($validate_url, 'tmp/'.$nr.'.png', 'L', '4', 0, 3, 0);

        require_once('tfpdf/tfpdf.php');


				#pdf
				if (!file_exists('tickets')) {
					mkdir('tickets', 0777, true);
				}

				$uid_hum = substr($uid,0,4).'-'.substr($uid,4,4).'-'.substr($uid,8);


				$pdf = new tFPDF('P', 'mm', array('148','105'));
        $pdf->SetMargins(5, 0);
				$pdf->SetAutoPageBreak(false);
        $pdf->AddPage();
        $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
        $pdf->SetFont('DejaVu','',12);

				$pdf->SetY('-35');

				$pdf->Cell(30, 30, $pdf->Image('tmp/'.$nr.'.png', $pdf->GetX(), $pdf->GetY(), 30), 1);

				$pdf->Cell(0, 15, $nr.'--'.$uid_hum, 'TR', 1, 'C');
				$pdf->Cell(30, 15);
        $pdf->Cell(0, 15, $comment, 'TRB', 0, 'C');

        $pdf->Output('tickets/Ticket-'.$nr.'.pdf', 'F');
        $pdf->Output('Ticket-'.$nr.'.pdf', 'I');
      }

			public function generate_print($comment = 'Tim Lai') {
				$this->generate($comment);


			}




      public function validate($uid, $nr) {

        if ($ticket = DB::query("SELECT * FROM `tickets` WHERE `id` = $nr")) {;

          if ($uid == $ticket[0]['uid']) {
            echo "Valide";
						#ENTWERTUNG
          } else {
            echo "invalide";
          }
        }
      }
    }

		class Login {
			public function check()	{
				@session_start();
				if (!empty($_SESSION['login'])) {
					// die();
				}
			}

			public function f_login($user, $password) {
				session_start();
				$dbCon = $this->connect();
			}
		}




 ?>
