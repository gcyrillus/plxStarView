<?php
# Fichier du plugin plxStarView
# Charge PluXml avant utilisation
# Définition des constantes 
$gu_sub = explode('plugins',$_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF']);
$gu_sub = str_replace($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR,'',$gu_sub[0]);
$plugName =basename( __DIR__ );
define('PLX_ROOT','../../'); 
define('PLX_CORE', PLX_ROOT.'core'.DIRECTORY_SEPARATOR);
define('PLX_PLUGINS', PLX_ROOT.'plugins'.DIRECTORY_SEPARATOR);

include(PLX_ROOT.'config.php');
include(PLX_CORE.'lib'.DIRECTORY_SEPARATOR.'config.php');

# On verifie que PluXml est installé
if(!file_exists(path('XMLFILE_PARAMETERS'))) {
	header('Location: '.PLX_ROOT.'install.php');
	exit;
}
# On continue et on démarre la session
session_start();

# On inclut les class interdépendantes de pluxml
include_once(PLX_CORE.'lib/class.plx.date.php');
include_once(PLX_CORE.'lib/class.plx.glob.php');
include_once(PLX_CORE.'lib/class.plx.utils.php');
include_once(PLX_CORE.'lib/class.plx.msg.php');
include_once(PLX_CORE.'lib/class.plx.record.php');
include_once(PLX_CORE.'lib/class.plx.motor.php');
include_once(PLX_CORE.'lib/class.plx.admin.php');
include_once(PLX_CORE.'lib/class.plx.encrypt.php');
include_once(PLX_CORE.'lib/class.plx.medias.php');
include_once(PLX_CORE.'lib/class.plx.plugins.php');
include_once(PLX_CORE.'lib/class.plx.token.php');
include_once(PLX_CORE.'lib/class.plx.capcha.php');
include_once(PLX_CORE.'lib/class.plx.erreur.php');
include_once(PLX_CORE.'lib/class.plx.feed.php');
include_once(PLX_CORE.'lib/class.plx.show.php');

# Creation de l'objet principal et lancement du traitement
$plxMotor = plxMotor::getInstance();
# on s'occupe de notre plugin

#Infos du plugin
$plxMotor->plxPlugins->plug = array(
			'dir' 			=> PLX_PLUGINS,
			'name' 			=> $plugName,
			'filename'		=> PLX_PLUGINS.$plugName.'/'.$plugName.'.php',
			'parameters.xml'=> PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.$plugName.'.xml',
			'infos.xml'		=> PLX_PLUGINS.$plugName.'/infos.xml'
		);
		
# on declare le plugin concerné
$plxPlugin = $plxMotor->plxPlugins->aPlugins[$plugName];

# enfin on verifie que notre plugin est bien là et actif sinon on sort

if (!isset($plxMotor->plxPlugins->aPlugins[$plugName])) { exit;}

# maintenant on utilise le plugin

# quelques verifications:
#on verifie que la requête correspond à un article ou une page statique
$badMessage = '<!DOCTYPE html>
		<html>
		<meta charset="UTF-8">
		<title>(◠﹏◠✿)</title>
		<head><style>html{min-height:100vh;display:flex;}body{margin:auto;font-size:8vw;color:hotpink}</style></head>
		<body>(◠﹏◠✿) bad robot request</body>
		</html>';
# A est-c une page statique ?
if(isset($_POST['artId'])) {
	$request =str_pad( trim($_POST['artId']),4,"0", STR_PAD_LEFT) ;
	if(strlen($request) == '4' ){
		# c'est un article
		# existe t-il ?
		if(!array_key_exists($request , $plxMotor->plxGlob_arts->aFiles)) {
		echo $badMessage;
		}
	}
	if(strlen($request) == '7' ){
		# c'est une page statique
		# existe t'elle?
		$request = substr($request, -3);
		if(!array_key_exists($request , $plxMotor->aCats)) {
		echo $badMessage;
		exit;
		}
	}
}
if(isset($_GET['art'])) {
$request =str_pad( trim($_GET['art']),4,"0", STR_PAD_LEFT) ;
	if(strlen($request) == '4' ){
		# c'est un article
		# existe t-il ?
		if(!array_key_exists($request , $plxMotor->plxGlob_arts->aFiles)) {
		echo $badMessage;
		exit;
		}
	}
	if(strlen($request) == '7' ){
		# c'est une page statique
		# existe t'elle?
		$request = substr($request, -3);
		if(!array_key_exists($request , $plxMotor->aCats)) {
		echo $badMessage;
		exit;
		}
	}
}
# Traitement des formulaires en POST (ajax ou pas)
if(isset($_POST['artId'])) {
$id = $_POST['artId'];
if(isset($_POST['a'])) {$points = intval($_POST['a']);}
if(isset($_POST['b'])) {$points = intval($_POST['b']);}
if(isset($_POST['c'])) {$points = intval($_POST['c']);}
if(isset($_POST['d'])) {$points = intval($_POST['d']);}
if(isset($_POST['e'])) {$points = intval($_POST['e']);}
if($plxPlugin->getParam('stars')=='0') {$stars='☆';} elseif($plxPlugin->getParam('stars')=='1')  {$stars='★';}elseif($plxPlugin->getParam('stars')=='2')  {$stars='♡';} elseif($plxPlugin->getParam('stars')=='3')  {$stars='♥';}

		//get datas
$jsonDatas = json_decode(file_get_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.$plugName.'/plxStarsDatas.json'), true);

		// update datas
		$jsonDatas[$id]['points'] = $jsonDatas[$id]['points']  + $points;
		$jsonDatas[$id]['nbvote'] = $jsonDatas[$id]['nbvote'] + 1;
		$jsonDatas[$id]['average'] = ceil(( $jsonDatas[$id]['points'] /  $jsonDatas[$id]['nbvote']   ) * 10);
		$jsonDatas[$id]['ips'][]= $_SERVER['REMOTE_ADDR'];
		
		// save datas 
file_put_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.$plugName.'/plxStarsDatas.json', json_encode($jsonDatas,true) );

		// rafraichi le formulaire
		// echo response / innerHTML 
		$disabled=' type="submit" ';
		if(in_array(trim($_SERVER['REMOTE_ADDR']),$jsonDatas[$id]['ips'])) { $disabled= ' type="button" disabled="disabled" title="max: 1 vote"';}
		echo '<style type="text/css">
		/*html{overflow:hidden;}*/';
		include('../../plugins/'.$plugName.'/css/site.css');				
		echo '</style>';
		echo '
		<form style="--average:'.$jsonDatas[$id]['average'].'%" id="rate-'.$id.'" action="../../plugins/'.$plugName.'/rateIt.php" method="post">
		<fieldset>
			<legend class="rate-infos">Votes <sup>('.$jsonDatas[$id]['nbvote'].' / '.$jsonDatas[$id]['average'].'%)</sup><sub>vue(s)'.$jsonDatas[$id]['nbview'].'</sub></legend>
			<input type="hidden" name="artId" value="'.$id.'">
			<button name="e" value="2"  '.$disabled.'>'.$stars.'</button>
			<button name="d" value="4"  '.$disabled.'>'.$stars.'</button>
			<button name="c" value="6"  '.$disabled.'>'.$stars.'</button>
			<button name="b" value="8"  '.$disabled.'>'.$stars.'</button>
			<button name="a" value="10" '.$disabled.'>'.$stars.'</button>
		 </fieldset>
		 </form>
		';		
}

#traitement du formulaire pour affichage en GET (iframe sans ajax)
if(isset($_GET['art'])) {
	echo '<!DOCTYPE html>
		<html lang="fr">
		<meta charset="UTF-8">
		<title>vote</title>
		<meta name="viewport" content="width=device-width,initial-scale=1">';
	echo '<style type="text/css">
	html{overflow:hidden;}';
    include(PLX_ROOT.'plugins/'.$plugName.'/css/site.css'); 			
	echo '</style><body>';	
	$plxPlugin->showData($_GET['art']);
	echo '</body></html>';
	} ?>