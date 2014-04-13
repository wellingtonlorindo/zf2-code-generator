<?php 
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.ico">

    <title>Gerador de Código</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
  </head>

  <body role="document">

    <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">          
          <a class="navbar-brand" href="#">Gerador de Código</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div class="container theme-showcase" role="main">
    <br /> <br />
      <div class="page-header">
        <h1>Preencha os dados abaixo</h1>
      </div>
      <form class="form-horizontal" role="form" action="generator.php" method="post">
        
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Nome do Controller</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="name" placeholder="Exemplo: Cliente" required>
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-2 control-label">Plural</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="plural" placeholder="Exemplo: Clientes" required>
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-2 control-label">Nome da Tabela</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="tabela" placeholder="Exemplo: pedido">
          </div>
        </div>

        <div class="form-group">
          <label for="inputPassword3" class="col-sm-2 control-label">Módulo</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="modulo" placeholder="Exemplo: Application">
          </div>
        </div> 
        
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary btn-lg">Gerar &raquo;</button>
          </div>
        </div>
      </form>
      <br /><br />
        <?php
            if (!empty($_SESSION['error'])) {
                echo '<div class="alert alert-danger">'.implode('<br />', $_SESSION['error']).'</div>';
                $_SESSION['error'] = null;
            } 

            if (!empty($_SESSION['success'])) {
                echo '<div class="alert alert-success">'.implode('<br />', $_SESSION['success']).'</div>';
                $_SESSION['success'] = null;
   
            }
        ?>  
                <div class="alert alert-warning">        
                  <h4 class="list-group-item-heading">Ajustes Manuais</h4>
                  <p class="list-group-item-text">- O Doctrine não suporta o tipo <strong>ENUM</strong>, caso esteja usando altere o campo da tabela para VARCHAR, caso contrário a entidade não será gerada corretamente</p>      
                  <p class="list-group-item-text">- Coloque todas as entidades para extender a entidade BaseEntity</p>
                  <p class="list-group-item-text">- Verifique se o relacionamento entre as entidades foi gerado corretamente</p>      
                  <p class="list-group-item-text">- Mude os atributos da entidade de private para protected</p>      
                  <p class="list-group-item-text">- Adicione os métodos mágicos __get() e __set() e os métodos populate() e getInputFilter() a entidade.</p>      
                  <p class="list-group-item-text">- Adicione o Zend\InputFilter\InputFilter a entidade também.</p>      
                  <p class="list-group-item-text">- Você precisa instalar o PHPUnit para rodar os testes.</p>      
                </div>          
      
    </div> <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>