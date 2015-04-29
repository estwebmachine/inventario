<?php
	ini_set('memory_limit', '128M');
	App::import('Vendor','mypdf');
	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetSubtitle('Listado de Usuarios');

	//set zoom, layout
	$pdf->SetDisplayMode('real', 'OneColumn');

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Ministerio de Justicia');
	$pdf->SetTitle('Sistema Bodega');
	$pdf->SetSubject('Sistema Bodega');
	$pdf->SetKeywords('PDF');

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


	// ---------------------------------------------------------

	// set font
	$pdf->SetFont('times', 'BI', 10);

	// add a page
	$pdf->AddPage();

	// output the HTML content
	// construyo tabla
	
	$res .= '<table id="items" border="1" cellpadding="2">';
	$res .= '<thead><tr><td>Nombre</td><td>Nombre de Usuario</td><td>Email</td><td>Perfil</td><td>Estado</td><td>Cargo</td><td>Centro de Costo</td><td>Adm. Bodegas</td></tr></thead>';
	foreach($users as $user) {
		$res .= '<tr nobr="true">';
		$res .= '<td>' . $user['User']['name']. '</td>';
		$res .= '<td>' . $user['User']['username'] . '</td>';
		$res .= '<td>' . $user['User']['email'] . '</td>';
		$res .= '<td>' . Configure::read('User.roles.' . $user['User']['role']) . '</td>';
		$res .= '<td>' . Configure::read('User.status.' . $user['User']['status']) . '</td>';
		$res .= '<td>' . $user['Position']['name'] . '</td>';
		$res .= '<td>' . $user['CostCenter']['name'] . '</td>';
		$res .= '<td>';
		$wh_names = array();
		foreach($user['Warehouse'] as $warehouse) $wh_names[] = $warehouse['name'];
		$res .= implode(', ', $wh_names);
		$res .= '</td>';
		$res .= '</tr>';
	}

	$res .= '</table>';
	$html = $res;
	//$this->log($request, 'debug');
	//$html = 'ok';
	$pdf->writeHTML($html, true, false, false, false, '');

	// ---------------------------------------------------------

	//Close and output PDF document
	$pdf->Output('imprime.pdf', 'I');

	//============================================================+
	// END OF FILE                                                
	//============================================================+
?>