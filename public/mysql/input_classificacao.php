<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include('inc_conexao.php');

//ini_set('display_errors', 1);
//ini_set('html_errors', 1);
?>

<table cellpadding="3" cellspacing="3" border="1">
	<colgroup>
		<col class="con0" />
		<col class="con1" />
		<col class="con0" />
		<col class="con1" />
		<col class="con0" />
		<col class="con1" />
		<col class="con0" />
		<col class="con1" />
		<col class="con0" />
		<col class="con1" />
		<col class="con0" />
		<col class="con0" />
	</colgroup>
	<thead>
		<tr>
			<th>ID</th>
			<th>Data</th>
			<th>Marca ID</th>
			<th>Categoria ID</th>
			<th>Titulo</th>
			<th>Produto</th>
			<th>PRODUTO DETECTADO</th>
			<th>Target</th>
			<th>URL</th>
			<!-- th>MARCA</th -->
			<th>MARCA DETECTADA</th>
			<!-- th>CATEGORIA</th -->
			<th>CATEGORIA DETECTADA</th>
		</tr>
	</thead>
	<tbody>
		<?php
		//ARRAY COM MARCAS - ISSO DEVE VIR DO BANCO DEPOIS
		$marcas=array("Samsung","Galaxy","Gear VR","Gear S3","Gear Fit2","Apple","iPhone","Motorola","LGv","Sony","Huawei","Asus","Alcatel","HTC","Xiaomi","Consul","Brastemp","Eletrolux","Panasonic","Mabe","Fensa","Mademsa","Whirlpool","Bosch","GE ","Philco","Bosch");

		/* ZAIRA -  UPDATE 22/12 - adicionei array_map e strtolower */
		$marcas = array_map('strtolower', $marcas);
		
		//ARRAY COM CATEGORIAS - ISSO DEVE VIR DO BANCO DEPOIS
		$categorias=array("Tv","Televisão","Televisor","Television","Pantalla","SmarTV","SmartTV","Televisión","Televisore","Smart TV","LED","Full HD","Oled","OLED","Qled","QLED","Smartphone","Celular","iPhone","Teléfono","Celular Libre","Móvile","Móvil","Refrigerator","Refrigerador","Geladeira","Heladera","Congelador","Frigorífico","Nevera","Refrigeración","Refrigerado","Nevecón","Washing Machine","Lava roupas","Lava roupa","Lavadora de Roupa","Lavadora de Roupas","Lavadora","Lava-roupas","Lava-roupa","Lavarropa","Lavasecarropa","Lavado y Secado","Lavado","Lavasecadora","Lavadora/secadora","Lavadoras","Lavadora-Secadora","Lavadoras-Secadoras","Lavadoras y Secadoras","Lava e Seca","Centro de Lavado","Torre de Lavado");
		
		/* ZAIRA -  UPDATE 22/12 - adicionei array_map e strtolower */
		$categorias = array_map('strtolower', $categorias);
		
		$query = mysqli_query($conexao, "SELECT scraps.*, scraps.id as scraps_id, scraps.created_at as data_scrap, marcas.*, marcas.descricao AS descricao_marcas, categorias.*, categorias.descricao AS descricao_categorias FROM (scraps INNER JOIN marcas ON scraps.marca_id = marcas.id) INNER JOIN categorias ON scraps.categoria_id = categorias.id WHERE scraps.id > 646000 ORDER BY scraps.id ASC") or die(mysqli_error($conexao));		
		while ($resultado = mysqli_fetch_array($query)) {
		?>
		<tr>
			<td><?=$resultado['scraps_id']?></td>
			<td><?=$resultado['data_scrap']?></td>
			<td><?=$resultado['marca_id']?></td>
			<td><?=$resultado['categoria_id']?></td>
			<td><?=$resultado['titulo']?></td>
			<td><?=$resultado['produto']?></td>
			<td><small>[ EM BREVE ]</small></td>
			<td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?=$resultado['target']?></td>
			<td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?=$resultado['url']?></td>
			<?php /* <td><?=$resultado['descricao_marcas']?></td> */?>
			<td style="color:red;">
			<?php
			
			/* ZAIRA -  UPDATE 22/12 - adicionei strtolower */			
			$string = strtolower($resultado['titulo']).strtolower($resultado['produto']).strtolower($resultado['target']).strtolower($resultado['url']);
			
			foreach ($marcas as $marca){
				$buscaMarca = strstr($string, $marca);
				if($buscaMarca){
					
					echo $marca."<br />";

					//ATUALIZA REGISTRO
					$query2 = mysqli_query($conexao, "SELECT * FROM marcas WHERE descricao LIKE '%$marca%' OR descricao_outros LIKE '%$marca%' ") or die(mysqli_error($conexao));
					$resultado2 = mysqli_fetch_assoc($query2);
					if (empty($resultado2)) {
						$marca_id = 0;
					} else {
						$marca_id = $resultado2['id'];
					}
					$query3 = mysqli_query($conexao, "UPDATE scraps SET marca_id='".$marca_id."' WHERE id='".$resultado['scraps_id']."' ") or die(mysqli_error($conexao));
					//echo "UPDATE scraps SET marca_id='".$marca_id."' WHERE id='".$resultado['scraps_id']."' ";
					
				} else {
					//echo 'não encontrou';
				}
			}
						
			?>
			</td>
			<?php /* <td><?=$resultado['descricao_categorias']?></td> */?>
			<td style="color:red;">
			<?php
			
			/* ZAIRA -   UPDATE 22/12 - adicionei strtolower */			
			$string = strtolower($resultado['titulo']).strtolower($resultado['produto']).strtolower($resultado['target']).strtolower($resultado['url']);
			
			foreach ($categorias as $categoria){
				$buscaCategoria = strstr($string, $categoria);
				if($buscaCategoria){
					
					echo $categoria."<br />";

					//ATUALIZA REGISTRO
					$query4 = mysqli_query($conexao, "SELECT * FROM categorias WHERE descricao LIKE '%$categoria%' OR descricao_outros LIKE '%$categoria%' ") or die(mysqli_error($conexao));
					$resultado4 = mysqli_fetch_assoc($query4);
					if (empty($resultado4)) {
						$categoria_id = 0;
					} else {
						$categoria_id = $resultado4['id'];
					}
					$query5 = mysqli_query($conexao, "UPDATE scraps SET categoria_id='".$categoria_id."' WHERE id='".$resultado['scraps_id']."' ") or die(mysqli_error($conexao));
					//echo "UPDATE scraps SET categoria_id='".$categoria_id."' WHERE id='".$resultado['scraps_id']."' ";
					
				} else {
					//echo 'não encontrou';
				}
			}
						
			?>
			</td>
		</tr>
		<?php
		}
		?>									
	</tbody>
</table>
