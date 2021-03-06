<?php
App::import('Vendor','tcpdf/tcpdf');

// Extend the TCPDF class to create custom Header and Footer
class ORDERPDF extends TCPDF {
	
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
		$image_file = IN_PRODUCTION ? '../webroot/img/logopdf.jpg' : 'app/webroot/img/logopdf.jpg';
	
		//$image_file = K_PATH_IMAGES.'logo_example.jpg';
		$this->Image($image_file, '', '', 30);
		// Set font
		$this->SetFont('helvetica', 'B', 12);
		// Title
		$this->SetY(10);
			$this->SetX(60);
		$this->Cell(0, 0, 'GOBIERNO DE CHILE',0,1,'R');
		$this->Cell(0,0,"MINISTERIO DE JUSTICIA",0,1,'R');
		$this->Cell(0,0,"DEPTO. ADMINISTRATIVO",0,1,'R');
		$this->Cell(0,0,"RECURSOS FISICOS",0,1,'R');
	
			if($this->subtitle != '') {
					$this->SetX(60);
					$this->Cell(0,0,$this->subtitle,0,1,'C');
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