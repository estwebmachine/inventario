<?php
App::import('Vendor','tcpdf/tcpdf');

// Extend the TCPDF class to create custom Header and Footer
class PAGEORDER extends TCPDF {
	
	/**
	* @var subtitle
	* @access protected
	*/
	protected $subtitle = '';
	
		//Setear Subtitulo
	public function SetSubtitle($subtitle) {
	//Subtitulo
		$this->subtitle = $subtitle;
	}
	
		//Page header
		public function Header() {
		// Logo
		$image_file = 'app/webroot/img/logopdf.jpg';
			//$image_file = Router::url('/img/logopdf.jpg', true);
	
		//$image_file = K_PATH_IMAGES.'logo_example.jpg';
		$this->Image($image_file, '', '', 30);
		// Set font
		$this->SetFont('helvetica', 'B', 14);
		// Title
		$this->SetY(10);
			$this->SetX(50);
		$this->Cell(0, 5, 'Orden de pago', 0, 1, '', 0, '', 0);
	
		if($this->subtitle != '') {
				$this->SetX(50);
				$this->Cell(0, 5, $this->subtitle, 0, 1, '', 0, '', 0);
		}
		}
	
		// Page footer
		public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
			// Page number
		$this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 'T', 0, 'R', 0, '', 0, false, 'T', 'M');
		}
		}
		?>