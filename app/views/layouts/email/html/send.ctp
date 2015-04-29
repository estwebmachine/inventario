<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
	<title>Alerta de Inexistencia de Funcionario</title>
</head>

<body>
	<p><?php echo $content_for_layout;?></p>
	<p>
		<ul>
			<li>Nombre: <?php echo $data['User']['names'] .' '. $data['User']['first_last_name'].' '.$data['User']['second_last_name'] ?></li>
			<li>Rut: <?php echo $data['User']['rut']; ?></li>
		</ul>
	</p>
	<p>Este email fue enviado desde El Sistema de Inventario del MDS.</p>
</body>
</html>