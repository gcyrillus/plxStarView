<?php

if (!defined('PLX_ROOT')) { exit; }

class plxStarView extends plxPlugin {	 

/**
 * plxStarView
 *
 * Ajoute un formulaire à étoile dans les articles sans Hook.
 * Compte le nombre de votes et de vues
 *
 * Peut-être inserer via une iframe dans une page statique ou un endroit de de votre théme
 * 4 hooks permettent d'aficher:  .
 *		-les mieux noté
 * 		-les plus vus
 * 		-les moins bien notés
 *		-les moins vus
 *  
 * @Auteur 	GCyrillus aka gc-nomade 
 * @date	2023-01-08
 * */

        const BEGIN_CODE = '<?php' . PHP_EOL;
        const END_CODE = PHP_EOL . '?>';
		
		
		
	public function __construct($default_lang) {

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		
		# déclaration des hooks
		$this->addHook('showData', 'showData');
		$this->addHook('makeJson', 'makeJson');
		$this->addHook('getData', 'getData');
		$this->addHook('mostRated', 'mostRated');
		$this->addHook('mostViewed', 'mostViewed');
		$this->addHook('worstRated', 'worstRated');
		$this->addHook('lessViewed', 'lessViewed');
		$this->addHook('IndexBegin', 'IndexBegin');
		$this->addHook('plxMotorParseArticle', 'plxMotorParseArticle');
		$this->addHook('plxShowLastArtList', 'plxShowLastArtList');
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
		$this->addHook('ThemeEndBody', 'ThemeEndBody');


		
		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);
		


	}
	# désactive de force la compression gzip 
	public function  IndexBegin() {
	echo '<?php ';
		?>
		$plxMotor->aConf['gzip'] ='0';
	?>
		<?php 

	}	
	
	#code à exécuter à l’activation du plugin
	/* config par defaug  */		
	public function OnActivate() { 
		if($this->getParam('set')=='') {
			$this->setParam( 'mostRated', 	5 , 'numeric') ; 
			$this->setParam( 'mostViewed', 	5 , 'numeric') ; 
			$this->saveParams();	
		}
		# verifie si le repertoire de destination existe, sinon le crée
		if (!is_dir(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.__CLASS__)) {
				mkdir(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.__CLASS__);
			}
		# verifie si le fichier json existe, sinon le crée
		if(!file_exists(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'. __CLASS__ .'/plxStarsDatas.json')) { $this->makeJson();	}
	}	

	# création du fichier json à partir des articles
	# appeler à la premiere activation
	# réinitialise toutes les entrée si fichier déjà existant

	public function makeJson(){
		$plxMotor = plxMotor::getInstance();
		foreach($plxMotor->plxGlob_arts->aFiles as $id =>$name) {
			$id=intval(substr($id, 0, 4));
			$title =explode(".",$name) ;
			end($title);// on recupere le titre
			$datasArt[$id]= array('nbvote'=>'0' , 'points'=>'0', 'average'=>'0', 'nbview' =>'0', 'ips'=> array('0.0.0.0') , 'title' => str_replace('-',' ',prev($title)) , 'rated' => '1'	);				
		}
		file_put_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.__CLASS__.'/plxStarsDatas.json', json_encode($datasArt,true) );
		
	}
	
	# affiche le formulaire
	# crée l'entrée si celle-ci est manquante, puis affiche le formulaire
	
	public function showData($id) {
	if(!file_exists(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'. __CLASS__ .'/plxStarsDatas.json')) { $this->makeJson();	}
	if($this->getParam('stars')=='0') {$stars='☆';} elseif($this->getParam('stars')=='1')  {$stars='★';}elseif($this->getParam('stars')=='2')  {$stars='☀';} elseif($this->getParam('stars')=='3')  {$stars='♥';}
		$jsonDatas = json_decode(file_get_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.__CLASS__.'/plxStarsDatas.json'), true);
		$disabled=' type="submit" ';
		if(!array_key_exists('ipsView', $jsonDatas[$id]))$jsonDatas[$id]['ipsView'][]='0.0.0.0';		
		// si pas de données sur cette article, on le crée
		if (!array_key_exists($id,$jsonDatas)) {
			$plxMotor = plxMotor::getInstance();
			$title= $plxMotor->plxGlob_arts->aFiles[str_pad($id, 4, "0", STR_PAD_LEFT)];
			echo $title;
			$title =explode(".",$title) ;
			end($title);
			$jsonDatas[$id]= array('nbvote'=>'0' , 'points'=>'0', 'average'=>'0', 'nbview' =>'0', 'ips'=> array('0.0.0.0'), 'title' => str_replace('-',' ',prev($title)) , 'rated' => '1' , 'ipsView' => array('0.0.0.0') 		);	
			file_put_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'. __CLASS__ .'/plxStarsDatas.json', json_encode($jsonDatas,true) );	
			$this->showData($id);
		}
        else {
			if(in_array(trim($_SERVER['REMOTE_ADDR']),$jsonDatas[$id]['ips'])) { $disabled= ' type="submit" disabled="disabled" title="max: 1 vote"';}
			else{
						$plxMotor = plxMotor::getInstance();
				if ($plxMotor->mode =='article' &&  @!in_array(trim($_SERVER['REMOTE_ADDR']),$jsonDatas[$id]['ipsView'])   ) {
					$jsonDatas[$id]['nbview']=$jsonDatas[$id]['nbview']+1;
					$jsonDatas[$id]['ipsView'][] = trim($_SERVER['REMOTE_ADDR']);
					file_put_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.__CLASS__.'/plxStarsDatas.json', json_encode($jsonDatas,true) );
					}
				}
			if($jsonDatas[$id]['rated'] =='1') {
			include('formRate.php');
			echo $form;
			}				
		}
	}	

	# stock le formulaire dans une variable
	# crée l'entrée si celle-ci est manquante, puis stocke le formulaire
	public function getData($id) {
	if(!file_exists(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'. __CLASS__ .'/plxStarsDatas.json')) { $this->makeJson();	}
	if($this->getParam('stars')=='0') {$stars='☆';} elseif($this->getParam('stars')=='1')  {$stars='★';}elseif($this->getParam('stars')=='2')  {$stars='☀';} elseif($this->getParam('stars')=='3')  {$stars='♥';}
		$jsonDatas = json_decode(file_get_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'. __CLASS__ .'/plxStarsDatas.json'), true);
		$disabled=' type="submit" ';
		$id=intval($id);
		// si pas de données sur cette article, on le crée
		if (!array_key_exists($id,$jsonDatas)) {
			$plxMotor = plxMotor::getInstance();
			$title= $plxMotor->plxGlob_arts->aFiles[str_pad($id, 4, "0", STR_PAD_LEFT)];
			$title =explode(".",$title) ;
			end($title);
			$jsonDatas[$id]= array('nbvote'=>'0' , 'points'=>'0', 'average'=>'0', 'nbview' =>'0', 'ips'=> array('0.0.0.0'), 'title' => str_replace('-',' ',prev($title)) , 'rated' => '1' , 'ipsView' => array('0.0.0.0') 	);
			file_put_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'. __CLASS__ .'/plxStarsDatas.json', json_encode($jsonDatas,true) );	
			$this->getData($id);
		}
        else {
			if(!array_key_exists('ipsView', $jsonDatas[$id])){$jsonDatas[$id]['ipsView'][]='0.0.0.0';}
			if(in_array(trim($_SERVER['REMOTE_ADDR']),$jsonDatas[$id]['ips'])) { $disabled= ' type="submit" disabled="disabled" title="max: 1 vote"';}
			else{
				$plxMotor = plxMotor::getInstance();
				if ($plxMotor->mode =='article' &&  !in_array(trim($_SERVER['REMOTE_ADDR']),$jsonDatas[$id]['ipsView'])   ) {
					$jsonDatas[$id]['nbview']=$jsonDatas[$id]['nbview']+1;
					$jsonDatas[$id]['ipsView'][] = trim($_SERVER['REMOTE_ADDR']);
					file_put_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.__CLASS__.'/plxStarsDatas.json', json_encode($jsonDatas,true) );
				}
			}
			if($jsonDatas[$id]['rated'] =='1') {
			include('formRate.php');
				return $form;
			}
		}
	}	

	# sans utilité actuellement
	
	public function updateData($vars) { // let's see 
		// traitement , passage $vars ?
		include(rateIt.php);//? to test
	}	

	# affiche un titre et liens vers les mieux notés
	# possibilité de choisir la structure: liste ou div + p par exemple
	
	public function mostRated($vars) { // let's see 	
		$jsonDatas = json_decode(file_get_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'. __CLASS__ .'/plxStarsDatas.json'), true);		
		uasort($jsonDatas, function ($item1, $item2) {
		return $item2['average'] <=> $item1['average'];
	});
		$plxShow = plxShow::getInstance();
		$plxMotor = plxMotor::getInstance();
		$initialAfiles= $plxShow->plxMotor->plxGlob_arts->aFiles;
		
		$serie =  $plxMotor->plxPlugins->aPlugins[ __CLASS__ ]->getParam('nbArts');
		
		$i = 0;
        foreach ($jsonDatas as $key => $value) {
			if(!isset($plxMotor->plxGlob_arts->aFiles[ str_pad( $key,4,"0", STR_PAD_LEFT)]))  {$i--; continue;}
			$i++;            
			$mostRated[ str_pad( $key,4,"0", STR_PAD_LEFT)]=  $plxMotor->plxGlob_arts->aFiles[ str_pad( $key,4,"0", STR_PAD_LEFT)] ;// format expected
			if($i >= $serie) break;
        }

		$plxShow->plxMotor->plxGlob_arts->aFiles = $mostRated;
		echo '<h3> Most Rated </h3>';
		echo'<'.$vars[0].'>';
		$plxShow->lastArtList($vars[1]);
		echo '</'.$vars[0].'>';
		$plxShow->plxMotor->plxGlob_arts->aFiles = $initialAfiles;		
	
	}	

	# affiche un titre et liens vers les plus vus
	# possibilité de choisir la structure: liste ou div + p par exemple	
	public function mostViewed($vars) { // let's see 	
		$jsonDatas = json_decode(file_get_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'. __CLASS__ .'/plxStarsDatas.json'), true);		
		uasort($jsonDatas, function ($item1, $item2) {
		return $item2['nbview'] <=> $item1['nbview'];
	});
		
		$plxShow = plxShow::getInstance();
		$plxMotor = plxMotor::getInstance();
		$initialAfiles= $plxShow->plxMotor->plxGlob_arts->aFiles;
		
		$serie =  $plxMotor->plxPlugins->aPlugins[ __CLASS__ ]->getParam('nbArts');
		
		$i = 0;
        foreach ($jsonDatas as $key => $value) {
			if(!isset($plxMotor->plxGlob_arts->aFiles[ str_pad( $key,4,"0", STR_PAD_LEFT)]) )  {$i--; continue;}
			//$art= $plxMotor->parseArticle(PLX_ROOT . $plxMotor->aConf['racine_articles'] . $plxMotor->plxGlob_arts->aFiles[ str_pad( $key,4,"0", STR_PAD_LEFT)]); // extraction données 
			$mostviewed[ str_pad( $key,4,"0", STR_PAD_LEFT)]=  $plxMotor->plxGlob_arts->aFiles[ str_pad( $key,4,"0", STR_PAD_LEFT)] ;// format expected
			$i++;            
			if($i >= $serie) break;
        }
		$plxShow->plxMotor->plxGlob_arts->aFiles = $mostviewed;

		echo '<h3> Most viewed </h3>';
		echo'<'.$vars[0].'>';
		$plxShow->lastArtList($vars[1],$serie,'','','random');
		echo '</'.$vars[0].'>';
		$plxShow->plxMotor->plxGlob_arts->aFiles = $initialAfiles;
		
	
	}

	# affiche un titre et liens vers les moins notés
	# possibilité de choisir la structure: liste ou div + p par exemple	
	public function worstRated($vars) { // let's see 	
		$jsonDatas = json_decode(file_get_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'. __CLASS__ .'/plxStarsDatas.json'), true);		
		uasort($jsonDatas, function ($item1, $item2) {
		return $item1['average'] <=> $item2['average'];
	});
		
		$plxShow = plxShow::getInstance();
		$plxMotor = plxMotor::getInstance();
		$initialAfiles= $plxShow->plxMotor->plxGlob_arts->aFiles;
		
		$serie =  $plxMotor->plxPlugins->aPlugins[ __CLASS__ ]->getParam('nbArts');
		
		$i = 0;
        foreach ($jsonDatas as $key => $value) {
			if(!isset($plxMotor->plxGlob_arts->aFiles[ str_pad( $key,4,"0", STR_PAD_LEFT)]))  {$i--; continue;}
			$worstRated[ str_pad( $key,4,"0", STR_PAD_LEFT)]=  $plxMotor->plxGlob_arts->aFiles[ str_pad( $key,4,"0", STR_PAD_LEFT)] ;// format expected
			$i++;            
			if($i >= $serie) break;
        }
		$plxShow->plxMotor->plxGlob_arts->aFiles = $worstRated;
		echo '<h3> worst rated </h3>';
		echo'<'.$vars[0].'>';
		$plxShow->lastArtList($vars[1]);
		echo '</'.$vars[0].'>';
		$plxShow->plxMotor->plxGlob_arts->aFiles = $initialAfiles;
		
	}	

	# affiche un titre et liens vers les moins vus
	# possibilité de choisir la structure: liste ou div + p par exemple	
	public function lessViewed($vars) { // let's see 	
		$jsonDatas = json_decode(file_get_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'. __CLASS__ .'/plxStarsDatas.json'), true);		
		uasort($jsonDatas, function ($item1, $item2) {
		return $item1['nbview'] <=> $item2['nbview'];
	});
		$plxShow = plxShow::getInstance();
		$plxMotor = plxMotor::getInstance();
		$initialAfiles= $plxShow->plxMotor->plxGlob_arts->aFiles;
		
		$serie =  $plxMotor->plxPlugins->aPlugins[__CLASS__]->getParam('nbArts');
		
		$i = 0;
        foreach ($jsonDatas as $key => $value) {
			if(!isset($plxMotor->plxGlob_arts->aFiles[ str_pad( $key,4,"0", STR_PAD_LEFT)])) {$i--; continue;}
			
			$lessViewed[ str_pad( $key,4,"0", STR_PAD_LEFT)]=  $plxMotor->plxGlob_arts->aFiles[ str_pad( $key,4,"0", STR_PAD_LEFT)] ;// format expected
			$i++;  
            			
			if($i >= $serie) break;
        }
		$plxShow->plxMotor->plxGlob_arts->aFiles = $lessViewed;
		echo '<h3> less viewed </h3>';
		echo'<'.$vars[0].'>';
		$plxShow->lastArtList($vars[1]);
		echo '</'.$vars[0].'>';
		$plxShow->plxMotor->plxGlob_arts->aFiles = $initialAfiles;
	
	}
	
	

	# insere une la feuille de style
	public function ThemeEndHead() {
		
	?>
			<!-- <?= __CLASS__ ?> plugin -->
			<style type="text/css">
			<?php include('plugins/'. __CLASS__ .'/css/site.css'); ?>
			</style>
				<?php
	}
	
	# insere le javascript pour un vote en Ajax
	public function ThemeEndBody() {
		
	?>
			<!-- <?= __CLASS__ ?> plugin -->
			<script>				
					for (let e of  document.querySelectorAll('form[id^="rate-"]')) {	
						let formId=e.getAttribute('id');
						let id = formId.slice(5); 
						let form_data = new FormData(e);						
						for (let b of  document.querySelectorAll('form[id='+formId+'] button')){
							b.addEventListener("click", function () {
							let name = b.getAttribute('name');
							let value = b.getAttribute('value');						
							form_data.append(name, value);
							});
				
						};					
									
						e.addEventListener("submit", function () {
								event.preventDefault(); 
							let out ='';
							for(let [name, value] of form_data) {
								out = out +  ` ${value}` ;
							}
							let xhttp = new XMLHttpRequest();
							let upUrl= '../../plugins/<?= __CLASS__ ?>/rateIt.php';
							xhttp.open("POST", upUrl , true);
							xhttp.onload = function(event) {
								output = document.querySelector('#rate-'+id);
								if (xhttp.status == 200) {
									output.outerHTML = this.responseText;
								}

								};
						xhttp.send(form_data);
						}); 	
						
					}
			</script>
				<?php
	}

	#insere le formulaire dans l'article en fin de contenu.
	public function plxMotorParseArticle() { 
	echo self::BEGIN_CODE;
?>
        	# pour accéder au plugin	
			$plxMotor = plxMotor::getInstance();
			$plugin = $plxMotor->plxPlugins->aPlugins['<?= __CLASS__ ?>']; 
			if($plxMotor->mode =='article') $art['content'] .=  $plugin->getData($art['numero']);
<?php
            echo self::END_CODE;
	}

	# inutilisé
	public function plxShowLastArtList	() { 
	echo self::BEGIN_CODE;
?>

			
<?php
            echo self::END_CODE;
	}
	
	#inutilisé
	/* traduction du mois si langue autre que anglais et disponible */
	public function checkMonthLangDate($stringDate) {
		if($this->default_lang !='en' && file_exists(PLX_PLUGINS.'plx_artViews/lang/'.$this->default_lang.'.php')) {
			$MonthToTranslate = array('Jan','Feb','Mar','Apr','May','Jun','July','Aug','Sept','Oct',' Nov','Dec') ;
			$index=0;
			foreach($this->getLang('L_DATE_LANG') as $month){
				$stringDate = str_replace(trim($MonthToTranslate[$index]), $this->getLang('L_DATE_LANG')[$index], $stringDate);  	
				$index++;				
			}
		return $stringDate;
		}
		
	}


}
?>