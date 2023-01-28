<?php if(!defined('PLX_ROOT')) exit;

	# Control du token du formulaire
	plxToken::validateFormToken($_POST);
	
	#chargement du fichier json
	$jsonDatas = json_decode(file_get_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.basename( __DIR__).'/plxStarsDatas.json'), true);
	    if(!empty($_POST)) {
			$plxPlugin->setParam( 'nbArts', 		$_POST['nbArts'		] , 'numeric') ; 
			$plxPlugin->setParam( 'stars', 			$_POST['stars'		] , 'numeric') ; 	
			$plxPlugin->setParam( 'formLegend', 	$_POST['formLegend'	] , 'numeric') ; 	
			$plxPlugin->setParam( 'formRating', 	$_POST['formRating'	] , 'numeric') ; 	
			$plxPlugin->setParam( 'formShow', 		$_POST['formShow'	] , 'numeric') ; 	
			$plxPlugin->saveParams();
			if(isset($_POST['submitRated'])) {
				echo 'article exclu<pre>
			';
			foreach ($jsonDatas as $key => $value) {
				if(array_key_exists($key, $_POST)  and $_POST[$key]=='on') { $jsonDatas[$key]['rated'] = '0';}	else { $jsonDatas[$key]['rated'] = '1';}		
				
				
			}
			
			file_put_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.basename( __DIR__).'/plxStarsDatas.json', json_encode($jsonDatas,true) );
			}
		header('Location: parametres_plugin.php?p='.$plugin);
	exit;
    }

	$var['nbArts'	  ] = $plxPlugin->getParam('nbArts'		)=='' ?  5 	: $plxPlugin->getParam('nbArts'    );
	$var['stars'	  ] = $plxPlugin->getParam('stars'		)=='' ?  0 	: $plxPlugin->getParam('stars'     );
	$var['formLegend' ] = $plxPlugin->getParam('formLegend'	)=='' ?  0 	: $plxPlugin->getParam('formLegend');
	$var['formRating' ] = $plxPlugin->getParam('formRating'	)=='' ?  0 	: $plxPlugin->getParam('formRating');
	$var['formShow'	  ] = $plxPlugin->getParam('formShow'	)=='' ?  0 	: $plxPlugin->getParam('formShow'  );
?>
<form action="parametres_plugin.php?p=<?php echo $plugin ?>" method="post" id="plxStars">
	<fieldset>
		<legend>Configuration</legend>
			<label><?php echo $plxPlugin->getLang('L_NBARTS_SHOW')  ?>	:</label>
			<input type="text" name="nbArts" size="2" value="<?php  echo $var['nbArts'] ?>" />
			
			<label><?php echo $plxPlugin->getLang('L_LEGEND_SHOW')  ?>	:</label>
			<?php plxUtils::printSelect('formLegend',array('1'=>L_YES,'0'=>L_NO),$var['formLegend']); ?>
			
			<label><?php echo $plxPlugin->getLang('L_RATING_SHOW')  ?>	:</label>
			<?php plxUtils::printSelect('formRating',array('1'=>L_YES,'0'=>L_NO),$var['formRating']); ?>
			
			
			<label><?php echo $plxPlugin->getLang('L_VIEW_SHOW')  ?>	:</label>
			<?php plxUtils::printSelect('formShow',array('1'=>L_YES,'0'=>L_NO),$var['formShow']); ?> 
			
			<label><?php echo $plxPlugin->getLang('L_STARS')?> 			:</label>
			<?php plxUtils::printSelect('stars', array('0'=>'&star;','1'=>'&starf;', '2'=> '&#9728;', '3' => '&hearts;'), $var['stars']);?>
			
			<label><?php echo $plxPlugin->getLang('L_SAVE_TO_UPDATE') ?>:</label>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
			<?php echo plxToken::getTokenPostMethod() ?>
	</fieldset>
	<fieldset class="artToExclude">
		<legend>Articles à exclures</legend>
		<?php 	
		krsort($jsonDatas);
			foreach ($jsonDatas as $key => $value) {
				$checked='';
			if($value['rated'] =='0') $checked='checked="checked"';
			echo '<label for="'.$key.'"><input type="checkbox" name="'.$key.'" 	'.$checked.'> '. $key .' - '.$value['title'].'</label>'. PHP_EOL;
				
			}
		?>
			<p style="grid-column:1/-1;display:flex;gap:1em;align-items:center;background:#efefef;padding:0.5rem;border-radius:3px;">
				<label><?php echo $plxPlugin->getLang('L_SAVE_TO_UPDATE') ?>:</label>
				<input type="submit" name="submitRated" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
			</p>
	</fieldset>
</form>
<style>
form#plxStars {
 width: max-content;
  width: 100%;
  margin: 2em auto;
}
form#plxStars fieldset {
 display:grid;
 grid-template-columns:repeat(2,auto);
 gap: 1em;
  padding: 0.5em 2em;
  box-shadow: 0 0 3px;
  width:max-content;
  max-width:90%;
}
form#plxStars fieldset.artToExclude {    
  margin-top:1em;
  grid-template-columns:repeat(auto-fill,minmax(10em,1fr)); 
  width:auto;
}
form#plxStars fieldset.artToExclude label{   
  margin:0;
}
#plxStars label {
 margin-inline-start: auto;
}
#plxStars input,
#plxStars select {
 margin-inline-end: auto;
}
#plxStars legend {
 background:ivory;
 font-size:1.4em;
  border-radius: 5px;
  box-shadow: 0 0 2px;
  padding-inline: 0.5em;
}
#plxStars code {
 color:blue;
 padding:0.2rem 0.1rem;
 background:cornsilk;
}
#plxStars input[type="submit"],
button {
 margin-inline-end: auto;
 background:tomato;
}
#plxStars button:not(:hover) {
 background:initial;
 color:initial
}
#id_stars{
	color:gold;
}
</style>