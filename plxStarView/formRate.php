<?php

if (!defined('PLX_ROOT')) { exit; }
if($this->getParam('formLegend'	)== '1') {$legend = '<legend class="rate-infos">'.$this->getLang('L_RATE_IT').'</legend>';} else {$legend='';}
if($this->getParam('formRating'	)== '1') {$rating = '<sup>'.$jsonDatas[$id]['nbvote'].' '.$this->getLang('L_RATES').'  / '.$this->getLang('L_AVERAGE').' '.$jsonDatas[$id]['average'] / 10 .'</sup>';} else {$rating='';}
if($this->getParam('formShow'	)== '1') {$show = '<sup>'.$this->getLang('L_VIEWS').' '.$jsonDatas[$id]['nbview'].'</sup>';} else {$show='';}
			$form= '
				<form style="--average:'.$jsonDatas[$id]['average'].'%" id="rate-'.intval($id).'" action="../../plugins/'. basename(__DIR__ ).'/rateIt.php" method="post">
				  <fieldset>
					'.$legend.'
					<input type="hidden" name="artId" value="'.intval($id).'">
					<button name="e" value="2"  '.$disabled.'>'.$stars.'</button>
					<button name="d" value="4"  '.$disabled.'>'.$stars.'</button>
					<button name="c" value="6"  '.$disabled.'>'.$stars.'</button>
					<button name="b" value="8"  '.$disabled.'>'.$stars.'</button>
					<button name="a" value="10" '.$disabled.'>'.$stars.'</button>
				  </fieldset>
				  '.$rating.'
				  '.$show.'
				</form>';				
				
				?>