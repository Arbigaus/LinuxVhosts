<?php
if(!isset($_SESSION)):
  session_start();
endif;

define("PASTA_SERVIDOR", '/var/www/');
define("PASTA_APACHE", '/etc/apache2/sites-available/');
define("ARQUIVO_HOST", '/etc/hosts');

$dados_form = filter_input_array(INPUT_POST,FILTER_SANITIZE_MAGIC_QUOTES);
if(isset($dados_form['virtual_host']) && !empty($dados_form['virtual_host'])):

    $dados_form['virtual_host'] = trim(strtolower(strip_tags(str_replace(' ','_',$dados_form['virtual_host']))));

    $host_criado = str_replace('www.','',$dados_form['virtual_host']);
    $host_criado = str_replace('.com.br','',$host_criado);
    $host_criado = str_replace('.com','',$host_criado);
    $host_criado = str_replace('.br','',$host_criado);
    $host_criado = str_replace('.com.net','',$host_criado);
    $host_criado = str_replace('.net','',$host_criado);
    $host_criado = str_replace('.edu','',$host_criado);
    $host_criado = str_replace('.gov','',$host_criado);
    $host_criado = str_replace('.pc','',$host_criado);

    $virtual_host = '
    <VirtualHost *:80>
      ServerName '.$host_criado.'.com.br
      ServerAlias www.'.$host_criado.'.com.br
      DocumentRoot "'.PASTA_SERVIDOR.$host_criado.'.com.br"
      ErrorLog ${APACHE_LOG_DIR}/error.log
      CustomLog "${APACHE_LOG_DIR}/access.log" common
      <Directory "'.PASTA_SERVIDOR.$host_criado.'.com.br">
        DirectoryIndex index.php index.html index.htm
        AllowOverride All
        Order allow,deny
        Allow from all
      </Directory>
    </VirtualHost>';

    $pula_linha ="\r\n";

    $caminho_vhosts = PASTA_APACHE.$host_criado."-com-br.conf";

    try {
      // CRIA O ARQUIVO DO VIRTUAL HOST NA PASTA DO APACHE
      $file = fopen($caminho_vhosts,'a');
      fwrite($file, $virtual_host);
      fclose($file);
    } catch (Exception $e) {
      $_SESSION['msg'] = "Erro ao Ler/Criar arqivo Virtual Host";
    }

    // CRIA O ENDEREÇO SEM O WWW NO ARQUIVO HOSTS
    $caminho_localhosts = ARQUIVO_HOST;
    try {
      $file = fopen($caminho_localhosts,'a');
      fwrite($file, $pula_linha);

      $local_host_of_www = "127.0.0.1   ".$host_criado.".com.br";

      $file = fopen($caminho_localhosts,'a');
      fwrite($file, $local_host_of_www);
      fclose($file); 

      // CRIA O ENDEREÇO COM O WWW NO ARQUIVO HOSTS
      $file = fopen($caminho_localhosts,'a');
      fwrite($file, $pula_linha);

      $local_host_on_www = "127.0.0.1   www.".$host_criado.".com.br";

      $file = fopen($caminho_localhosts,'a');
      fwrite($file, $local_host_on_www);
      fclose($file);
    } catch (Exception $e) {
      $_SESSION['msg'] = "Erro ao Ler/Criar arqivo Hosts";
    }

    try {
      // CRIA A PASTA DO PROJETO NO DIRETORIO WWW
      mkdir(PASTA_SERVIDOR.$host_criado.".com.br", 0775);
    } catch (Exception $e) {
      $_SESSION['msg'] = "Erro ao criar a pasta do projeto";
    }

    try {
      $index = '
      <!DOCTYPE html>
      <html lang="pt-br">
      <head>
        <meta charset="utf-8">
        <title> Página gerada pelo gerador de Host Virtual.</title>
        <style>
          h1{
            font-size:1.5em;
            text-align:center;
            color: #ccc;
          }
        </style>
      </head>
      <body>
        <h1>Ola, seja bem vindo(a) a sua página gerada de forma dinâmica pelo gerador de Hosts Virtuais!</h1>
      </body>
      </html>';

      $dir_projeto = PASTA_SERVIDOR.$host_criado.".com.br/index.php";
      $file = fopen($dir_projeto,'a');
      fwrite($file, $index);
      fclose($file);

    } catch (Exception $e) {
      $_SESSION['msg'] = "Erro ao criar index.php na pasta do projeto";
    }

    $shell_vhost = 'sudo a2ensite '.$host_criado.'-com-br.conf';

    shell_exec($shell_vhost);
    shell_exec('sudo service apache2 reload');


    $_SESSION['msg'] = "<p>Virtual Host criado com sucesso!</p>
    <p>Já está disponível no link:</p>
    <p><a href='http://".$host_criado.".com.br' title='Meu novo host!' target='_blank'>".$host_criado.".com.br</a></p>";
    header("Location: index.php");
  else:
    $_SESSION['msg'] = "É necessário preencher o campo acima!";
    header("Location: index.php");
  endif;
