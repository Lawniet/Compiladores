<?php
$simbols = array("#", "$", "%", "!", "@");
$palavras_reservadas = array("Program","Var", "Const", "Begin", "End.", "Write", "Read");
$tipos = array("integer","real", "boolean", "char", "string");
$operacoes = array("+", "-", "*", "/");
$comparacao = array("<>", "==", "<", ">",">=","<=");
$id = '/^[a-zA-Z]/';
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
        "message"  => "Lexic-00042: O simbolo ou comando inexistente",
        "offset"  => $posicao,
        "text" => $string ,
    );
    return $log;
}
function imprime_info($token)
{
    echo "[ ".htmlentities($token['lexema'])." ] => ";
    echo htmlentities($token['token']);
    echo("<br>");
}
function imprime_log($log, $j)
{
    echo htmlentities($log['message']);
    echo "<pre>";
    echo htmlentities($log['text']);
    if ($j == 0)
        printf("\n%".($log['offset']+1)."s", "^");
        else
            printf("\n%".($log['offset']-1)."s", "^");
            echo "</pre>";
            return true;
}

$str = file_get_contents("codigo.txt");

$error = false;
$str2 = nl2br ($str);
$pieces = explode("<br />", $str2);
//$ql = substr_count ($str2 , '<br />') + 1;
$tagsArray = array();

for($j = 0; $j < sizeof($pieces); $j++)
{
    for($i = 0; $i < sizeof($simbols) ; $i++)
    {
        if(strpos($pieces[$j], $simbols[$i]))
        {
            $pos = strpos($pieces[$j], $simbols[$i]);
            $log = gera_log($pos, $pieces[$j]);
            $error = imprime_log($log, $j);
        }
    }
    $parcial = explode(' ', $pieces[$j]);
    array_push( $tagsArray ,$parcial);
}

if(!$error)
{
    echo "Resultado da analise lexica para o codigo:<br><br>";
    for($j = 0; $j < sizeof($tagsArray); $j++)
    { 
        for($i = 0; $i < sizeof($tagsArray[$j]); $i++)
        {
            $tagsArray[$j][$i] = trim($tagsArray[$j][$i]);
            if (!strcmp($tagsArray[$j][$i],':='))
            {
                $token =  gera_tokens($tagsArray[$j][$i], 'Simbolo de atribucao');
                imprime_info($token);
            }
            else if (in_array($tagsArray[$j][$i], $palavras_reservadas))
            {
                $token = gera_tokens($tagsArray[$j][$i], 'Palavra reservada');
                imprime_info($token);
            }
            else if (in_array($tagsArray[$j][$i], $operacoes))
            {
                $token =  gera_tokens($tagsArray[$j][$i], 'Operador aritmetico');
                imprime_info($token);
            }
            else if (in_array($tagsArray[$j][$i], $comparacao))
            {
                $token =  gera_tokens($tagsArray[$j][$i], 'Operador relacional');
                imprime_info($token);
            }
            else if (in_array($tagsArray[$j][$i], $tipos))
            {
                $token =  gera_tokens($tagsArray[$j][$i], 'Tipo de variavel');
                imprime_info($token);
            }
            else
            {
                if (preg_match($id, $tagsArray[$j][$i]))
                {
                    if (preg_match('/\"/',$tagsArray[$j][$i]))
                    {
                        $token =  gera_tokens($tagsArray[$j][$i], 'Literal');
                        imprime_info($token);
                    }
                    else
                    {
                        $token =  gera_tokens($tagsArray[$j][$i], 'Identificador');
                        imprime_info($token);
                    }
                }
                else if (preg_match($valInt, $tagsArray[$j][$i], $inteiro) and !strcmp($tagsArray[$j][$i], $inteiro[0]))
                {
                    $token =  gera_tokens($tagsArray[$j][$i], 'Valor inteiro');
                    imprime_info($token);
                }
                else if (preg_match($valReal, $tagsArray[$j][$i], $real) and !strcmp($tagsArray[$j][$i],$real[0]))
                {
                    $token =  gera_tokens($tagsArray[$j][$i], 'Valor real');
                    imprime_info($token);
                }
                else if (strlen($tagsArray[$j][$i])==1)
                {
                    $token =  gera_tokens($tagsArray[$j][$i], 'Simbolo');
                    imprime_info($token);
                }
                else
                {
                    $token =  gera_tokens($tagsArray[$j][$i], 'Identificador ou valor invalido');
                    imprime_info($token);
                }
            }
        }
    }
}
?>
