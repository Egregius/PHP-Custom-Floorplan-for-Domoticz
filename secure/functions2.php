<?php 
function Schakel($idx,$cmd,$name=NULL){global $domoticzurl,$user,$Usleep,$log,$actions;$reply=json_decode(file_get_contents($domoticzurl.'json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd='.$cmd.'&level=0&passcode='),true);}
function Schakelaar($name,$kind,$size,$boven,$links){global ${'S'.$name},${'SI'.$name},${'ST'.$name};
	if(isset(${'S'.$name})) {
		echo '<div style="position:absolute;top:'.$boven.'px;left:'.$links.'px;width:'.$size*1.7 .'px;text-align:center;z-index:500;" title="'.strftime("%a %e %b %k:%M:%S", ${'ST'.$name}).'">		
		<form method="POST"><input type="hidden" name="Schakel" value="'.${'SI'.$name}.'">';
		echo ${'S'.$name}=='Off'?'<input type="hidden" name="Actie" value="On"><input type="hidden" name="Naam" value="'.$name.'"><input type="image" src="images/'.$kind.'_Off.png" height="'.$size.'px" width="auto">' 
					   :'<input type="hidden" name="Actie" value="Off"><input type="hidden" name="Naam" value="'.$name.'"><input type="image" src="images/'.$kind.'_On.png" height="'.$size.'px" width="auto">';
		echo '<br/>'.$name.'</form></div>';
	} else echo '<div class="red" style="position:absolute;top:'.$boven.'px;left:'.$links.'px;">'.$name.'</div>';
}
function Smokedetector($name,$size,$boven,$links){global ${'S'.$name},${'SI'.$name},${'ST'.$name};
	if(isset(${'S'.$name})) {
		echo '<div style="position:absolute;top:'.$boven.'px;left:'.$links.'px;z-index:10;" title="'.strftime("%a %e %b %k:%M:%S", ${'ST'.$name}).'">
		<form method="POST"><input type="hidden" name="Schakel" value="'.${'SI'.$name}.'"><input type="hidden" name="Naam" value="'.$name.'">
		<input type="hidden" name="Actie" value="Off"><input type="image" src="images/smokeon.png" height="'.$size.'px" width="auto">'.$name.'
		</form></div>';
	} else echo '<div class="red" style="position:absolute;top:'.$boven.'px;left:'.$links.'px;">'.$name.'</div>';

}
function Sunscreen($name,$size,$boven,$links){global ${'S'.$name},${'SI'.$name},${'ST'.$name};
	if(isset(${'S'.$name})) {
		echo '<div style="position:absolute;top:'.$boven.'px;left:'.$links.'px;z-index:500;" title="'.strftime("%a %e %b %k:%M:%S", ${'ST'.$name}).'">		
		<div style="position:absolute;top:0px;left0px;"><form method="POST">
			<input type="hidden" name="Schakel" value="'.${'SI'.$name}.'">
			<input type="hidden" name="Naam" value="'.$name.'">
			<input type="hidden" name="Actie" value="Off">';
		echo ${'S'.$name}=='Open'?'<input type="image" src="images/arrowgreenup.png" height="'.$size.'px" width="auto">':'<input type="image" src="images/arrowup.png" height="'.$size.'px" width="auto">';
		echo '</form></div>
		<div style="position:absolute;top:0px;left:95px;"><form method="POST">
			<input type="hidden" name="Schakel" value="'.${'SI'.$name}.'">
			<input type="hidden" name="Naam" value="'.$name.'">
			<input type="hidden" name="Actie" value="Stop">
			<input type="image" src="images/close.png" height="'.$size.'px" width="auto">
		</form></div>
		<div style="position:absolute;top:0px;left:190px;"><form method="POST">
			<input type="hidden" name="Schakel" value="'.${'SI'.$name}.'">
			<input type="hidden" name="Naam" value="'.$name.'">
			<input type="hidden" name="Actie" value="On">';
		echo ${'S'.$name}=='Closed'?'<input type="image" src="images/arrowgreendown.png" height="'.$size.'px" width="auto">':'<input type="image" src="images/arrowdown.png" height="'.$size.'px" width="auto">';
		echo '</form></div></div>';
	} else echo '<div class="red" style="position:absolute;top:'.$boven.'px;left:'.$links.'px;">'.$name.'</div>';
}
