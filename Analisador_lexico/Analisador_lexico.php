<?php
$simbols = array("#", "$", "%", "!", "@");
$palavras_reservadas = array("Program","Var", "Const", "Begin", "End.", "Write", "Read");
$operacoes = array("+", "-", "*", "/");
$comparacao = array("<>", "==", "<", ">",">=","<=");
$id = '/[a-z][a-z0-9]*/';
$valInt = '/[0-9]+/';
$valReal = '/^[\-+]?[0-9]*\.*\,?[0-9]+$/';

function gera_tokens($lexema, $token)
{
	$token = array
	(
		"lexema"  => $lexema,
		"token" => $token,
	);
	return $token;
}
function gera_log($posicao, $string)
{
	$log = array
	(
		"code"   => 42,
		"message"  => "Lexic-00042: O simbolo ou comando não existe",
		"offset"  => $posicao,
		"text" => $string ,
	);
	return $log;
}
function imprime_info($token)
{
	echo htmlentities($token['lexema'])." | "; 
	echo htmlentities($token['token']); 
	echo("<br>");
}

//$data = file_get_contents("codigo.txt");

$str = 'Program Pgrau; 
Var y , x : real ; 
Const A = 2 ; 1B = 3 ; 
a Begin 
Read ( x ) ; 
Y := a * x + b:; 
Write ( y ) ; 
End.';
$tagsArray = explode(' ', $str);
$error = false;
$str2 = nl2br ($str); 
$pieces = explode("<br />", $str2);
//$ql = substr_count ($str2 , '<br />') + 1;

for($j = 0; $j < sizeof($pieces); $j++) 
{
	for($i = 0; $i < sizeof($simbols) ; $i++) 
	{
		if(strpos($pieces[$j], $simbols[$i]))
		{
			$pos = strpos($pieces[$j], $simbols[$i]);
			$log = gera_log($pos, $pieces[$j]);
			
			echo htmlentities($log['message']); 
			echo "<pre>"; 
			echo htmlentities($log['text']); 
			if ($j == 0)
				printf("\n%".($log['offset']+1)."s", "^"); 
			else 
				printf("\n%".($log['offset']-1)."s", "^"); 
			echo "</pre>";
			$error = true;
			break;
		}
	}
}
if(!$error)
{
	for($j = 0; $j < sizeof($tagsArray); $j++) 
	{
		$tagsArray[$j] = trim($tagsArray[$j]);
		//echo $tagsArray[$j];
		if (in_array($tagsArray[$j], $palavras_reservadas))
		{
			$token = gera_tokens($tagsArray[$j], 'Palavra reservada');
			imprime_info($token);
		}
		else if (in_array($tagsArray[$j], $operacoes))
		{
			$token =  gera_tokens($tagsArray[$j], 'Operador aritmetico');
			imprime_info($token);
		}
		else if (in_array($tagsArray[$j], $comparacao))
		{
			$token =  gera_tokens($tagsArray[$j], 'Operador relacional');
			imprime_info($token);
		}
		else
		{
			if (preg_match($id, $tagsArray[$j]))
			{
				$token =  gera_tokens($tagsArray[$j], 'ID');
				imprime_info($token);
			}
			else if (preg_match($valInt, $tagsArray[$j]))
			{
				$token =  gera_tokens($tagsArray[$j], 'Valor inteiro');
				imprime_info($token);
			}
			else if (preg_match($valReal, $tagsArray[$j]))
			{
				$token =  gera_tokens($tagsArray[$j], 'Valor real');
				imprime_info($token);
			}
			else
			{
				if (!strcmp($tagsArray[$j],':='))
				{
					$token =  gera_tokens($tagsArray[$j], 'simbolo de atribução');
					imprime_info($token);
				}
				else 
				{
					$token =  gera_tokens($tagsArray[$j], 'simbolo');
					imprime_info($token);
				}
			}
			
		}	
	}		
}
