<?php

error_reporting(0);

function getStr($separa, $inicia, $fim, $contador){
  $nada = explode($inicia, $separa);
  $nada = explode($fim, $nada[$contador]);
  return $nada[0];
}

$lista = $_GET['ccs'];
$cc = explode("|", $lista)[0];
$bin = substr($cc, 0, 6);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://bins.su/");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Content-Type: application/x-www-form-urlencoded',
'Host: bins.su'));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'action=searchbins&bins='.$bin.'&bank=&country=');
$dados1 = curl_exec($ch);

$bin = getStr($dados1, 'bins<table><tr><td>BIN</td><td>Country</td><td>Vendor</td><td>Type</td><td>Level</td><td>Bank</td></tr><tr><td>','</td><td>' , 1);
$pais = getStr($dados1, '<tr><td>'.$bin.'</td><td>','</td><td>' , 1);
$bandeira = getStr($dados1, '</td><td>'.$pais.'</td><td>','</td><td>' , 1);
$tipo = getStr($dados1, '</td><td>'.$bandeira.'</td><td>','</td><td>' , 1);
$nivel = getStr($dados1, '</td><td>'.$tipo.'</td><td>','</td><td>' , 1);
$banco = getStr($dados1, '</td><td>'.$nivel.'</td><td>','</td></tr>' , 1);

$bin = substr($cc, 0, 6);
$arq = file_get_contents('../ccs/ccs.json');
$arq = json_decode($arq, true);

if(empty($nivel))
{
  die("#Erro -> Não foi possível checar a bin -> $lista -> @VanModder");
} else {
  
if($arq[$cc])
{
  die("#Erro -> Lista já está no estoque -> $lista -> @VanModder");
} else {
  $arq[$cc] = array(
    "lista" => $lista,
    "bin" => $bin,
    "nivel" => $nivel,
    "bandeira" => $bandeira,
    "tipo" => $tipo,
    "banco" => $banco,
    "pais" => $pais);
   $ccsalvar = json_encode($arq,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT );
 $salvar = file_put_contents('../ccs/ccs.json', $ccsalvar);
 if($salvar)
 {
   die("#Add -> Lista adicionada -> $lista -> @VanModder");
 } else {
   die("#Erro -> Não foi possível salvar esta lista -> $lista ->
   @VanModder");
  }
 }
}
?>