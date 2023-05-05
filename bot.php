<?php

 $input = file_get_contents('php://input');

 $update = json_decode($input);

 $message = $update->message;
 
 $chat_id = $message->chat->id;
 
 $message_id = $update->message->message_id;
 
 $tipo = $message->chat->type;
 
 $texto = $message->text;
 
 $id = $message->from->id;
 
 $isbot = $message->from->is_bot;
 
 if($message->from->is_premium){
   
     $ispremium = "sim";
     
 }else{
   
     $ispremium = "não";
     
 }
 $nome = $message->from->first_name;
 
 $usuario = $message->chat->username;
 
 $data = $update->callback_query->data;
 
 $query_message_id = $update->callback_query->message->message_id;
 
 $query_chat_id = $update->callback_query->message->chat->id;
 
 $query_usuario = $update->callback_query->message->chat->username;
 
 $query_nome = $update->callback_query->message->chat->first_name;
 
 $query_id = $update->callback_query->id;

function bot($method, $parameters) {
     $token = "6123283180:AAEZr0Ky764D5dGGlYOdzAfZk6OqnRbiP_Q";
 $options = array(
			 'http' => array(
			 'method'  => 'POST',
			 'content' => json_encode($parameters),
			 'header'=>  "Content-Type: application/json\r\n" .
	            "Accept: application/json\r\n"
			 )
			);

$context  = stream_context_create( $options );
		return file_get_contents('https://api.telegram.org/bot'.$token.'/'.$method, false, $context );
  
}

function menu($dados){
  
  $chat_id = $dados["chat_id"];
  $message_id = $dados["query_message_id"];
  
  $txt = "_Escolha uma das opções abaixo:_";

  $button[] = ['text'=>"💳 Unitárias",'callback_data'=>"infoCcs"];
  
  $button[] = ['text'=>"🛍️ Mix",'callback_data'=>"mix"];

  $button[] = ['text'=>"🔙 Voltar", "callback_data" => "start"];

  $menu['inline_keyboard'] = array_chunk($button, 2);

  bot("sendChatAction", 
    array(
    "chat_id" => $chat_id,
    "action" => "typing"));
  
  bot("editMessageText",
    array(
    "chat_id"=> $chat_id,
    "text" => $txt,
    "message_id" => $message_id,
    "reply_to_message_id"=> $message_id,
    "reply_markup" => $menu,
    "parse_mode" => 'Markdown'));
}

function start($dados){

  $chat_id = $dados["chat_id"];
  $message_id = $dados["query_message_id"];
  $nome = $dados["nome"];
  
  $txt = "🚀 Bem Vindo(a) *$nome*, essa é a *Melhor Store de CC's*!
  
🤖 *Sobre a store:*
 ├💳 `".countCcs()."` *CC's* no estoque
 └💰 Saldo em Dobro: *OFF*

_Escolha uma de minhas opções abaixo:_";
  
  $button[] = ['text'=>"💳 CCs",'callback_data'=>"menu"];
  
  $button[] = ['text'=>"",'callback_data'=>"NULL"];
  
  $button[] = ['text'=>"🏦 Carteira",'callback_data'=>"infoUser"];
  
  $button[] = ['text'=>"💰 Adicione Saldo",'callback_data'=>"adicioneSaldo"];
  
  $button[] = ['text'=>"",'callback_data'=>"NULL"];
  
  $button[] = ['text'=>"🎰 Troque pontos",'callback_data'=>"trocarPontos"];
 
 $menu['inline_keyboard'] = array_chunk($button, 2);

  bot("sendChatAction", 
    array(
    "chat_id" => $chat_id,
    "action" => "typing"));

bot("editMessageText",
    array(
    "chat_id"=> $chat_id ,
    "text" => $txt,
    "reply_markup" => $menu,
    "reply_to_message_id"=> $message_id,
    "message_id" => $message_id,
    "parse_mode" => 'Markdown'));
}

function manager($id, $nome, $usuario, $chat_id, $message_id, $txt)
{
  $referal = explode(" ", $txt)[1];
  $pontos = 0;
  
  if($referal)
  {
    
    $receipt = "✅ | *Você ganhou 1 ponto por entrar pelo link de alguém.*";
    
    $sent = "✅ | *Você ganhou 1 ponto por entrarem pelo seu link.*";
    
    $pontos = 1;
    
    bot("sendMessage",array(
           "chat_id"=> $chat_id ,
           "text" => $receipt,
           "parse_mode" => 'Markdown'));
    
    bot("sendMessage",array(
           "chat_id"=> $referal ,
           "text" => $sent,
           "parse_mode" => 'Markdown'));
    
    setPontos($pontos, $referal);
    
  }
  
  $dir = "usuarios/";
  $dirUser = $dir.$id.".json";
  $user = json_decode(file_get_contents($dirUser), true);

  if(!file_exists($dirUser))
  {
    if(empty($usuario))  {$usuario="indefinido";}
  $cadastrado = date("d/m/Y")." ".date("H:i:s");
  $user = array(
      "nome" => $nome,
      "usuario" => "@".$usuario,
      "saldo" => 0.00,
      "referal" => $referal,
      "adm" => false,
      "ccs" => 0,
      "mix" => 0,
      "recarga_manual" => 0,
      "recarga_pix" => 0,
      "pontos" => $pontos,
      "id" => $id,
      "banido" => false,
      "cadastrado" => $cadastrado);
    $json = json_encode($user,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT );
	$salva = file_put_contents($dirUser, $json);
} else if($user["banido"])
{
  die(bot("sendMessage",
    array(
    "chat_id"=> $chat_id ,
    "text" => "*⚠️ | Você foi banido!*",
    "reply_to_message_id"=> $message_id,
    "message_id" => $message_id,
    "parse_mode" => 'Markdown')));
}
}

function enviar($id, $msg){
  
  $dir = "usuarios/";
  $dirUser = $dir.$id.".json";
  $envios = 0;
  $erros = -2;
  $dados = json_encode(array("chat_id" => $id), 1);

  $getContents = json_decode(file_get_contents($dirUser), true);

  $button[] = ['text'=>"Menu",'callback_data'=>"start|$dados"];
 
  $menu['inline_keyboard'] = array_chunk($button, 1);
  
  if($getContents["adm"]){
    $scandir = scandir($dir);

    foreach($scandir as $key){
      $key = explode(".json", $key)[0];

      $sendMessage = bot("sendMessage", 
      array(
        "chat_id" => $key,
        "text" => $msg,
        "reply_markup" => $menu
      ));

      if($sendMessage){
        $envios ++;
      } else {
        $erros ++;
      }
      
    }

    $txt = "Envio Terminado!\nSucesso: $envios\nErros: $erros";

     bot("sendMessage", 
      array(
        "chat_id" => $id,
        "text" => $txt,
      ));

  } else {
    
    bot("sendMessage", 
      array(
        "chat_id" => $id,
        "text" => "Você não tem permissão!"
      ));
    
  }
}

function getDadosUser($id){

  $dir = "usuarios/";
  $dirUser = $dir.$id.".json";

  $data = json_decode(file_get_contents($dirUser), 1);

  return $data;
  
}

function infoUser($dados){

  $chat_id = $dados["chat_id"];
  $message_id = $dados["query_message_id"];
  $data = getDadosUser($chat_id);
  
  $admin = $data["adm"];

  if($admin)
  {
    
    $admin = "Sim";
  
  } else {
    
    $admin = "Não";
    
  }

  $cadastro = $data["cadastrado"];

  $id = $data["id"];

  $usuario = $data["usuario"];

  $saldo = $data["saldo"];

  $pontos = $data["pontos"];

  $ccs = $data["ccs"];

  $mix = $data["mix"];

  $recPix = $data["recarga_pix"];

  $recManu = $data["recarga_manual"];
  

  $txt = "*✨Suas Informações*

📛 *Nome:* `$cnome`
🌐 *User:* `$usuario`
👮‍♀️ *Admin:* $admin
📅 *Data de cadastro:* `$cadastro`

🆔 *ID da carteira:* `$id`
💰 *Saldo:* $saldo
💎 *Pontos:* $pontos

💳 *Cartões comprados:* $ccs
🎲 *Mix comprados:* $mix
💠 *Recargas com pix:* $recPix
💵 *Recargas manuais:* $recManu

_Para ver seu historico completo de recargas , cartões comprados , mixs comprados clica em um dos botões abaixo!_";

  $button[] = ['text'=>"💠 Recargas Pix",'callback_data'=>"recargas_pix"];

  $button[] = ['text'=>"💵 Recargas Manuais",'callback_data'=>"recargas_manuais"];

  $button[] = ['text'=>"💳 Histórico de CC's",'callback_data'=>"historico_ccs"];

  $button[] = ['text'=>"🎲 Histórico de Mix",'callback_data'=>"historico_mix"];

  $button[] = ['text'=>"⚙️ Desenvolvedor",'url'=>"t.me/vanmodder"];

  $button[] = ['text'=>"",'callback_data'=>"nulo"];

  $button[] = ['text'=>"voltar",'callback_data'=>"start"];
 
  $menu['inline_keyboard'] = array_chunk($button, 2);

bot("sendChatAction", 
    array(
    "chat_id" => $chat_id,
    "action" => "typing"));

bot("editMessageText",
    array(
    "chat_id"=> $chat_id,
    "text" => $txt,
    "reply_markup" => $menu, 
    "reply_to_message_id"=> $cmid,
    "message_id" => $cmid,
    "parse_mode" => 'Markdown'));
  
}

function debitarSaldo($preco, $id)
{
  $saldo = json_decode(file_get_contents("usuarios/".$id.".json"), true);
  $saldo["saldo"] = $saldo["saldo"]-$preco;
  
  $json = json_encode($saldo, JSON_PRETTY_PRINT);
  $save = file_put_contents("usuarios/".$id.".json", $json);
  
  if(!$save){
    
    return false;
    
  } else {
    
    return true;
    
  }
}

function setPontos($pontos, $id)
{
  $user = json_decode(file_get_contents("usuarios/".$id.".json"), true);
  $user["pontos"] = $user["pontos"] + $pontos;
  
  $json = json_encode($user, JSON_PRETTY_PRINT);
  $save = file_put_contents("usuarios/".$id.".json", $json);
  
  if(!$save){
    return false;
  } else {
    return true;
  }
}

function chk($dados)
{
   $data = $dados["optional"];
   $cc = explode(' ', $data)[0];
   $preco = explode(' ', $data)[1];
   $chat_id = $dados["chat_id"];
   $message_id = $dados["query_message_id"];
   $query_id = $dados["query_id"];
   
   $ccs = json_decode(file_get_contents('ccs/ccs.json'), 1);
   $user = json_decode(file_get_contents('usuarios/'.$chat_id.'.json'), 1);
   $saldo = $user['saldo'];
   $newSaldo = $saldo - $preco;
   $pontos = ($preco / 100) * 20;
   $userPontos = $user["pontos"] + $pontos;
   
   if($preco > $saldo || 0 > $newSaldo)
   {
     $txt = "💰 | Saldo Insuficiente";
     
     die(bot("answerCallbackQuery",
        array("callback_query_id" => $query_id,
        "text" => $txt,
        "show_alert"=> true,
        "cache_time" => 10)));
   }
   
   $lista = $ccs[$cc]['lista'];
   
   /* Comprou - Vamos ver se pode debitar o saldo*/
   if(!debitarSaldo($preco, $chat_id)){
     /* Vamos finalizar a compra pois o saldo não pode ser debitado */
     die(bot("answerCallbackQuery",
        array("callback_query_id" => $query_id,
        "text" => "🚨 | Compra não realizada, seu saldo não foi debitado!",
        "show_alert"=> true,
        "cache_time" => 10)));
   }
   /* Fim */
   /* Vamos tentar setar os pontos do cliente*/
   if(setPontos($pontos, $chat_id)){
     bot("sendMessage", array(
       "chat_id" => $chat_id,
       "text" => "*Obrigado pela compra!*\n✅ Você ganhou *".$pontos." pontos*, você já tem acumulado *".$userPontos." pontos* 😚",
       "parse_mode" => "Markdown"));
   }
   /* Fim */
   $cc = explode("|", $lista)[0];
   $mes = explode("|", $lista)[1];
   $ano = explode("|", $lista)[2];
   $cvv = explode("|", $lista)[3];
   
   $bandeira = $ccs[$cc]['bandeira'];
   $tipo = $ccs[$cc]['tipo'];
   $banco = $ccs[$cc]['banco'];
   $pais = $ccs[$cc]['pais'];
   $nivel = $ccs[$cc]['nivel'];
   
  /* Removendo cc do banco de dados */
   unset($ccs[$cc]);
   $save = file_put_contents("ccs/ccs.json", json_encode($ccs, JSON_PRETTY_PRINT));
   /* End */
   
   $i = date("i") + 10;
   $function = gerarPessoa();
   $nome = $function["name"];
   $cpf = $function["cpf"];
   
   $txt = "*🛒 COMPRA EFETUADA!*\n\n*✨Detalhes do cartão*

💳 *Cartão*: `$cc`
📆 *Validade*: `$mes/$ano`
🔐 *Cvv*: $cvv

🏳 *Bandeira*: `$bandeira`
💠 *Nível*: `$nivel`
⚜ *Tipo*: `$tipo`
🏛 *Banco*: `$banco`
🌍 *Pais*: `$pais `

👤* DADOS AUXILIARES*
├*CPF:* `$cpf`
└*NOME:* `$nome`

💸 *Valor: R$ $preco*
💰 *Novo Saldo: R$ $newSaldo*

⏰ *Tempo para Reembolso*: `". date("d/m/Y")." ". date("H:$i:s")."`";

   $buttons[] = ['text'=>"🔗 Grupo de Clientes",'url'=>"t.me/vanmodder"];

   $buttons[] = ['text'=>"VOLTAR",'callback_data'=>"start"];
   
   $menu['inline_keyboard'] = array_chunk($buttons, 1);

    bot("editMessageText",array(
           "chat_id"=> $chat_id ,
           "text" => $txt,
           "reply_markup" => $menu,
           "reply_to_message_id"=> $message_id,
           "message_id" => $message_id,
           "parse_mode" => 'Markdown'));
}

function getCountCcs($cc){
  $ccs = json_decode(file_get_contents("ccs/ccs.json"), true);
  $a = 0;
  
  foreach($ccs as $key => $value){
    if($value["nivel"] == $cc){
      $a++;
    }
  }
  
  return $a;
  
}

function infoCcs($dados)
{
  $chat_id = $dados["chat_id"];
  $message_id = $dados["query_message_id"];
  $data = $dados["dataUser"];
  $query_id = $dados["query_id"];
  $ccs = json_decode(file_get_contents("ccs/ccs.json"), true);
  
  if(!$ccs)
  {
    die(bot("answerCallbackQuery",
    array(
    "callback_query_id" => $query_id,
    "text" => "⚠️ | Base está sem Unitárias!",
    "show_alert"=> true,
    "cache_time" => 10)));
  }
  
  foreach($ccs as $key => $values){
       
       $rows[] = $key;

       $niveis[] = $values['nivel'];
      
   }
   
   
   $level = array_unique($niveis);
   
   $prices = json_decode(file_get_contents('prices.json'), 1);
   
   foreach($level as $key1){
       
       if($prices[$key1]){
           
           $price = $prices[$key1];
           
       } else {
           
           $price = 10;
           
       }
       
       $n = getCountCcs($key1);
       
       $lvl .= "\n-* ". $key1." - *` R$ $price`";
       
       $buttons[] = ['text'=>"$key1 | $n",'callback_data'=>"adquirir|$key1"];
       
   }
   
   
   $numeroCCs = sizeof($rows);
       
$txt = "✨* Escolha um nivel/level para prosseguir*
$lvl

✅ `$numeroCCs` *Cartões disponiveis*";

   
   $buttons[] = ['text'=>"",'callback_data'=>"."];

   $buttons[] = ['text'=>"↩️ VOLTAR",'callback_data'=>"menu"];
   
   $menu['inline_keyboard'] = array_chunk($buttons, 2);
   
   bot("editMessageText",array(
           "chat_id"=> $chat_id ,
           "text" => $txt,
           "reply_markup" => $menu,
           "reply_to_message_id"=> $message_id,
           "message_id" => $message_id,
           "parse_mode" => 'Markdown'));
}

function adquirir($dados)
{
   $levelData = $dados["optional"];
   $ccs = json_decode(file_get_contents('ccs/ccs.json'), 1);
   $chat_id = $dados["chat_id"];
   $message_id = $dados["query_message_id"];
   $user = getDadosUser($chat_id);
   $saldo = $user['saldo'];
   
   foreach($ccs as $key => $value){
       if($value['nivel'] == $levelData){
           $listas = $value['lista'];
           $arr[] = $listas;
       }
   } 
   
 
   $position = 0;
   
   $lista = $arr[$position];
   
   $cc = explode("|", $lista)[0];
   $mes = explode("|", $lista)[1];
   $ano = explode("|", $lista)[2];
   
   $ccSubstr = $ccs[$cc]['bin']."xxxxxxxxxx";
   
   $bandeira = $ccs[$cc]['bandeira'];
   $tipo = $ccs[$cc]['tipo'];
   $banco = $ccs[$cc]['banco'];
   $pais = $ccs[$cc]['pais'];
   
   $valor = 10;
   
   $txt = "*✨Detalhes do cartão*

💳 *Cartão*: `$ccSubstr`
📆 *Validade*: `$mes/$ano`
🔐 *Cvv*: xxx

🏳 *Bandeira*: `$bandeira`
💠 *Nível*: `$levelData`
⚜ *Tipo*: `$tipo`
🏛 *Banco*: `$banco`
🌍 *Pais*: `$pais `

💸 *Valor: R$ $valor*
💰 *Seu saldo atual: R$ $saldo*";


  $buttons[] = ['text'=>"COMPRAR",'callback_data'=>"chk|$cc $valor"];

  $buttons[] = ['text'=>"🔁",'callback_data'=>"alterarcc|$levelData $position $arr"];

  $buttons[] = ['text'=>"Voltar",'callback_data'=>"infoCcs"];
   
  $menu['inline_keyboard'] = array_chunk($buttons, 1);
   
   bot("editMessageText",array(
           "chat_id"=> $chat_id ,
           "text" => $txt,
           "reply_markup" => $menu,
           "reply_to_message_id"=> $message_id,
           "message_id" => $message_id,
           "parse_mode" => 'Markdown'));
}

function verifyCeo($id)
{
  //Adicione seu id
  $ceo = 5870697244;
  
  if($ceo != $id){
    return false;
  } else {
    return true;
  }
}
 
//Vamos Verificar qual se um texto específico foi enviado pelo usuário

if (strpos($texto, "/start") === 0){
  
 manager($id, $nome, $usuario);
  
//O * tá falando que a mensagem deverá ser enviada em bold/negrito
  
 $txt = "🚀 Bem Vindo(a) *$nome*, essa é a *Melhor Store de CC's*!
  
🤖 *Sobre a store:*
 ├💳 `".countCcs()."` *CC's* no estoque
 └💰 Saldo em Dobro: *OFF*

_Escolha uma de minhas opções abaixo:_";
  
  $button[] = ['text'=>"💳 CCs",'callback_data'=>"menu"];
  
  $button[] = ['text'=>"",'callback_data'=>"NULL"];
  
  $button[] = ['text'=>"🏦 Carteira",'callback_data'=>"infoUser"];
  
  $button[] = ['text'=>"💰 Adicione Saldo",'callback_data'=>"adicioneSaldo"];
  
  $button[] = ['text'=>"",'callback_data'=>"NULL"];
  
  $button[] = ['text'=>"🎰 Troque pontos",'callback_data'=>"trocarPontos"];
 
 $menu['inline_keyboard'] = array_chunk($button, 2);

  bot("sendChatAction", 
    array(
    "chat_id" => $chat_id,
    "action" => "typing"));

bot("send",
    array(
    "chat_id"=> $chat_id ,
    "text" => $txt,
    "reply_markup" => $menu,
    "reply_to_message_id"=> $message_id,
    "message_id" => $message_id,
    "parse_mode" => 'Markdown'));
}

//Mensagem enviada se o comando /start foi chamado


if(strpos($texto, "/enviar") === 0){

  $msg = substr($texto, 8);

  enviar($id, $msg);
  
}

if(strpos($texto, "/send") === 0){

  $msg = substr($texto, 6);

  enviar($id, $msg);
  
}

if(strpos($texto, "/gift") === 0){
  $user = json_decode(file_get_contents("usuarios/".$id.".json"), true);
  
  if($user["adm"]){
     $valor = substr($texto, 6);
       
       if(!$valor || 0 > $valor){
         $valor = 1;
       }
       
       $cod1 = rand(99999, 10000);
       $cod2 = rand(99999, 10000);
       $cod3 = rand(99999, 10000);
       
       $gift = "$cod1-$cod2-$cod3";
       
       $gifts = json_decode(file_get_contents('gifts.json'), true);
       
       $gifts[$gift] = array(
           "gift" => $gift,
           "valor" => $valor,
           "resgate" => true,
           "usuario_resgate" => NULL,
           "data_resgate" => NULL,
           "hora_resgate" => NULL);
           
        $json = json_encode($gifts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
        
        $save = file_put_contents('gifts.json', $json);
        
        if($save)
        {
            
            $txt .= "*🎁 GIFT GERADO*\n";
            $txt .= "💰* Valor:* `R$$valor,00`\n";
            $txt .= "🪪 *Gift*: `/resgatar $gift`";
            
            bot("sendMessage",
            array(
            "chat_id"=> $chat_id ,
            "text" => $txt,
            "reply_to_message_id"=> $message_id,
            "parse_mode" => 'Markdown'));
            
        } else {
            
          bot("sendMessage",
            array(
            "chat_id"=> $chat_id ,
            "text" => "*Falha ao gerar gift, contate o dev*",
            "reply_to_message_id"=> $message_id,
            "parse_mode" => 'Markdown'));
            
        }
  }
}

if(strpos($texto, "/resgatar") === 0)
   {
       
       $gift = substr($texto, 10);
       
       $gifts = json_decode(file_get_contents('gifts.json'), 1);
       
       if($gifts[$gift] && $gifts[$gift]['resgate'] == true)
       {
           
           $valor = $gifts[$gift]['valor'];
           
           $gifts[$gift] = array(
           "gift" => $gift,
           "valor" => $valor,
           "resgate" => false,
           "usuario_resgate" => $nome,
           "data_resgate" => date("d/m/Y"),
           "hora_resgate" => date("h:i:s"));
           
        $json = json_encode($gifts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
        
        $save = file_put_contents('gifts.json', $json);
        
        if($save)
        {
            
  $usuario = json_decode(file_get_contents('usuarios/'.$id.'.json'), 1);
            
  $usuario['gifts']++;
  $usuario['saldo'] = $valor + $usuario['saldo'];
  $saldoAtual = $usuario['saldo'];
  
  $json = json_encode($usuario, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
        
  $save = file_put_contents('usuarios/'.$id.'.json', $json);
  
         $buttons[] = ['text'=>"MENU",'callback_data'=>"start"];
   
         $menu['inline_keyboard'] = array_chunk($buttons, 2);
  
          bot("sendMessage",
            array(
            "chat_id"=> $chat_id ,
            "text" => "*🎁 Gift foi resgatado!*\n- Você ganhou *R$ $valor*\n- Saldo Novo: *R$ $saldoAtual*",
            "reply_markup" => $menu,
            "reply_to_message_id"=> $message_id,
            "parse_mode" => 'Markdown'));
        
        }
           
       } else if(!$gifts[$gift]) {
           
           bot("sendMessage",
            array(
            "chat_id"=> $chat_id ,
            "text" => "*Gift não existe!*",
            "reply_to_message_id"=> $message_id,
            "parse_mode" => 'Markdown'));
           
       } else {
           
           bot("sendMessage",
            array(
            "chat_id"=> $chat_id ,
            "text" => "*Gift já foi resgatado!*",
            "reply_to_message_id"=> $message_id,
            "parse_mode" => 'Markdown'));
           
       }
}

if(strpos($texto, "/admin") === 0){
    
    if(verifyCeo($id)){
    
    $admin = substr($texto, 7);
    
    if(admin($admin)){
      bot("sendMessage",
            array(
            "chat_id"=> $chat_id ,
            "text" => "*Usuário acabou de ser notificado sobre a contratação*",
            "reply_to_message_id"=> $message_id,
            "parse_mode" => 'Markdown'));
            
      bot("sendMessage",
            array(
            "chat_id"=> $admin ,
            "text" => "*Olá, você acaba de ser contratado pra ser adminstrador do bot.*",
            "parse_mode" => 'Markdown'));
    } else {
      bot("sendMessage",
            array(
            "chat_id"=> $chat_id ,
            "text" => "*Falha ao dar cargo admin ao usuário!*",
            "parse_mode" => 'Markdown'));
     }
    }
  }
  
  if(strpos($texto, "/unadmin") === 0){
    
    if(verifyCeo($id)){
    
    $admin = substr($texto, 9);
    
    if(unadmin($admin)){
      bot("sendMessage",
            array(
            "chat_id"=> $chat_id ,
            "text" => "*Usuário removido de admin. Já foi notificado*",
            "reply_to_message_id"=> $message_id,
            "parse_mode" => 'Markdown'));
            
      bot("sendMessage",
            array(
            "chat_id"=> $admin ,
            "text" => "*Olá, você acaba de ser perder o cargo de administrador do bot.*",
            "parse_mode" => 'Markdown'));
    } else {
      bot("sendMessage",
            array(
            "chat_id"=> $chat_id ,
            "text" => "*Falha ao remover admin usuário!*",
            "parse_mode" => 'Markdown'));
     }
    }
  }



if($data){
  $callback = explode("|", $data)[0];
  $dados = array(
   "chat_id" => $query_chat_id,
   "id" => $query_chat_id,
   "nome" => $query_nome,
   "usuario" => $query_usuario,
   "message_id" => $query_message_id,
   "query_message_id" => $query_message_id,
   "query_nome" => $query_nome,
   "query_id" => $query_id,
   "optional" => explode("|", $data)[1],
   "query_usuario" => $query_usuario,
   "dataUser" => array(getDadosUser($query_chat_id))
   );
    
  if(function_exists($callback)){
  
  $callback($dados);
  
 } else {
    bot("answerCallbackQuery",
    array(
    "callback_query_id" => $query_id,
    "text" => "⚠️ | Função em desenvolvimento!",
    "show_alert"=> false,
    "cache_time" => 10));
 }
}