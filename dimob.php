<?php
require_once 'init.php';

try
{
    TTransaction::open('permission');
    $empresa = new Empresa($_REQUEST['empresa']);
    TTransaction::close();
    
    $ano = $param['ano'];
    $retificadora = $_REQUEST['retificadora'];
    $numrecibo = str_pad($_REQUEST['numrecibo'],10,'0',STR_PAD_RIGHT);
    $especial = $_REQUEST['especial'];
    $dataevento = str_pad($_REQUEST['dataevento'],8,'0',STR_PAD_RIGHT);
    $evento = $_REQUEST['evento'];
    $cnpj = $empresa->cnpj;
    $cnpj = tirarFormatacao($empresa->cnpj);
    $nome = str_pad(tirarAcentos($empresa->nome),60,' ',STR_PAD_RIGHT);
    $endereco = str_pad(tirarAcentos($empresa->endereco),120,' ',STR_PAD_RIGHT);
    $cpf = tirarFormatacao($empresa->representante_cpf);
    
    $linha = 'R01'.$cnpj.$ano.$retificadora.$numrecibo.$especial.$dataevento.$evento.$nome.$cpf.$endereco.$empresa->uf_id.'8175  ';
    echo $linha;
    
    $file = 'tmp/dimob.txt';
    $fp = fopen($file, "w+");
            
    fwrite($fp, $linha);
    fclose($fp);
}
catch (Exception $e) // in case of exception
{
    new TMessage('error', $e->getMessage());
}

function tirarAcentos($string)
{
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}
    
function tirarFormatacao($string)
{
    $string = str_replace('.','',$string);
    $string = str_replace('-','',$string);
    $string = str_replace('/','',$string);
    return $string;
}

function date2us($date)
{
    if ($date)
    {
        // get the date parts
        $day  = substr($date,0,2);
        $mon  = substr($date,3,2);
        $year = substr($date,6,4);
        return "{$year}-{$mon}-{$day}";
    }
}
    
function date2br($date)
{
    if ($date)
    {
        // get the date parts
        $year = substr($date,0,4);
        $mon  = substr($date,5,2);
        $day  = substr($date,8,2);
        return "{$day}/{$mon}/{$year}";
    }
}
?>