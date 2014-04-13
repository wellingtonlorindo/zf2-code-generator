<?php 
    session_start();
    // disabilita o opcache para não manter cache de arquivos de configuração .php
    ini_set('opcache.enable', 0);
    include("db.php");
    include("class_lib.php");
    
    // RECUPERANDO DADOS E COLOCANDO PRIMEIRA LETRA MAIÚSCULA
    $modulo = (!empty($_POST['modulo']) ? ucfirst(strtolower($_POST['modulo'])) : 'Application');
    $name = ucfirst(strtolower($_POST['name']));
    $plural = (isset($_POST['plural']) ? ucfirst(strtolower($_POST['plural'])) : $name . 's');
    $tabela = !empty($_POST['tabela']) ? $_POST['tabela'] : $name;

    $_SESSION['success'] = array();
    $_SESSION['error'] = array();

    $dir = dirname(__DIR__);
    $dir_tmpl = $dir . '/generator/templates';
    $dir_app = $dir . '/module/'.$modulo.'/src/'.$modulo;
    $dir_view = $dir . '/module/'.$modulo.'/view/'.strtolower($modulo).'/'.strtolower($name);
    $dir_test = $dir . '/module/'.$modulo.'/test';
    
    // PEGA A DESCRIÇÃO DA TABELA NO BANCO
    $sql = mysql_query('describe '.$tabela);
    if (!$sql) {
       $_SESSION['error'][] = 'Tabela não encontrada';
       header('Location:'.$_SERVER['HTTP_REFERER']);
       exit();
    }

    // CRIA O DIRETÓRIO DAS VIEWS
    if (!file_exists($dir_view)) {
        mkdir($dir_view);
    }

    $lib = new Lib();

    // LENDO ARQUIVOS
    $tmpl_controller = $lib->rFile($dir_tmpl . '/Controller.php');
    $tmpl_form = $lib->rFile($dir_tmpl . '/Form.php');
    $tmpl_model = $lib->rFile($dir_tmpl . '/Model.php');        
    $tmpl_view_add = $lib->rFile($dir_tmpl . '/views/add.phtml');        
    $tmpl_view_edit = $lib->rFile($dir_tmpl . '/views/edit.phtml');        
    $tmpl_view_delete = $lib->rFile($dir_tmpl . '/views/delete.phtml');        
    $tmpl_view_view = $lib->rFile($dir_tmpl . '/views/view.phtml');        
    $tmpl_view_index = $lib->rFile($dir_tmpl . '/views/index.phtml');        
    $tmpl_test_boot = $lib->rFile($dir_tmpl . '/test/Bootstrap.php');        
    $tmpl_test_phpunit = $lib->rFile($dir_tmpl . '/test/phpunit.xml');        
    $tmpl_test_ctrl = $lib->rFile($dir_tmpl . '/test/Controller.php');        


    // ESCREVENDO ARQUIVOS 

    # Verifica se o controller já foi criado
    if(!$lib->existsFile($dir_app . '/Controller/' . $name . 'Controller.php')) { 
        $tmpl_controller = $lib->replaceData('CtrlName', $name, $tmpl_controller);
        $tmpl_controller = $lib->replaceData('CtrlNames', $plural, $tmpl_controller);
        $lib->wFile($dir_app . '/Controller/' . $name . 'Controller.php', $tmpl_controller);
        $_SESSION['success'][] = 'Controller: '.$dir_app . '/Controller/' . $name . 'Controller.php';

    } else {
        $_SESSION['error'][] = 'O Controller já está criado e não pode ser substituido';           
    }
            
    # Verifica se o model já foi criado
    if(!$lib->existsFile($dir_app . '/Model/' . $name . '.php')) { 
        $tmpl_model = $lib->replaceData('CtrlName', $name, $tmpl_model);
        $tmpl_model = $lib->replaceData('CtrlNames', $plural, $tmpl_model);
        $lib->wFile($dir_app . '/Model/' . $name . '.php', $tmpl_model);
        $_SESSION['success'][] = 'Model: '.$dir_app . '/Model/' . $name . '.php';
    } else {
         $_SESSION['error'][] = 'O Model já está criado e não pode ser substituido';  
    }

    // GERANDO ENTIDADES
    $map = '.././vendor/bin/doctrine-module orm:convert-mapping --namespace=\''.$modulo.'\\Entity\\\' --force --from-database annotation ./module/'.$modulo.'/src/';
    exec($map);
    $entities = '.././vendor/bin/doctrine-module orm:generate-entities ./module/'.$modulo.'/src/ --generate-annotations=true --update-entities --extend="Application\\Entity\\BaseEntity"';
    exec($entities);

    // GERANDO AS VIEWS
    $form = array();
    $campos = array();
    $pk = null;
    while ($row = mysql_fetch_array($sql, MYSQL_BOTH)) {
        if ($row['Key'] == 'PRI') {
            $pk = $row['Field'];
            $form[] =  'echo $this->formHidden($form->get("'.$row['Field'].'"));'."\n";
        } else if ($row['Field'] != 'created' && $row['Field'] != 'modified') {
            
            $campos[$row['Field']] = $lib->defineValueField($row['Type']);
            $form[] =  'echo $this->formRow($form->get("'.$row['Field'].'"));'."\n";
        }
    }


    # Gera view Add
    if(!$lib->existsFile($dir_view . '/add.phtml')) { 
        $tmpl_view_add = $lib->replaceData('CtrlName', $name, $tmpl_view_add);
        $tmpl_view_add = $lib->replaceData('CtrlNames', $plural, $tmpl_view_add);
        $tmpl_view_add = $lib->replaceData('cpsNames', implode(' ', $form), $tmpl_view_add);
        $tmpl_view_add = $lib->replaceData('id', $pk, $tmpl_view_add);
        $lib->wFile($dir_view . '/add.phtml', $tmpl_view_add);
        $_SESSION['success'][] = 'View: '.$dir_view . '/add.phtml';
    } else {
         $_SESSION['error'][] = 'A view add.phtml já está criada e não pode ser substituida';  
    }

    # Gera view edit
    if(!$lib->existsFile($dir_view . '/edit.phtml')) { 
        $tmpl_view_edit = $lib->replaceData('CtrlName', $name, $tmpl_view_edit);
        $tmpl_view_edit = $lib->replaceData('CtrlNames', $plural, $tmpl_view_edit);
        $tmpl_view_edit = $lib->replaceData('cpsNames', implode(' ', $form), $tmpl_view_edit);
        $tmpl_view_edit = $lib->replaceData('id', $pk, $tmpl_view_edit);
        $lib->wFile($dir_view . '/edit.phtml', $tmpl_view_edit);
        $_SESSION['success'][] = 'View: '.$dir_view . '/edit.phtml';
    } else {
         $_SESSION['error'][] = 'A view edit.phtml já está criada e não pode ser substituida';  
    }

    # Gera view delete
    if(!$lib->existsFile($dir_view . '/delete.phtml')) { 
        $tmpl_view_delete = $lib->replaceData('CtrlName', $name, $tmpl_view_delete);
        $tmpl_view_delete = $lib->replaceData('CtrlNames', $plural, $tmpl_view_delete);
        $tmpl_view_delete = $lib->replaceData('cpsNames', implode(' ', $form), $tmpl_view_delete);
        $tmpl_view_delete = $lib->replaceData('id', $pk, $tmpl_view_delete);
        $lib->wFile($dir_view . '/delete.phtml', $tmpl_view_delete);
        $_SESSION['success'][] = 'View: '.$dir_view . '/delete.phtml';
    } else {
         $_SESSION['error'][] = 'A view delete.phtml já está criada e não pode ser substituida';  
    }

    # Organiza html das views
    $html = '';
    $cabecalho = '';
    $linha = '';
    $cps_form = array();        
    foreach ($campos as $key => $valor) {
        if ($key != 'created' && $key != 'modified') {
            $html .= '<p>'."\n";
            $html .= '  <strong>'.ucfirst($key).'</strong>';
            $html .= '  <?php echo $this->escapeHtml($${ctrlName}->'.$key.');?>'."\n";
            $html .= '</p>'."\n";
            $cabecalho .= '<th>'.ucfirst($key).'</th> '."\n";
            $linha .= '<td><?php echo $this->escapeHtml($${ctrlName}->'.$key.');?></td>   '."\n";
            $cps_form[] = '$this->add(array("name" => "'.$key.'", "type" => "Text", "options" => array("label" => "'.ucfirst($key).'", )));'."\n";
        }
    }

    # Gera view view
    if(!$lib->existsFile($dir_view . '/view.phtml')) { 
        $tmpl_view_view = $lib->replaceData('cpsNames', $html, $tmpl_view_view);
        $tmpl_view_view = $lib->replaceData('CtrlName', $name, $tmpl_view_view);
        $tmpl_view_view = $lib->replaceData('CtrlNames', $plural, $tmpl_view_view);
        $tmpl_view_view = $lib->replaceData('id', $pk, $tmpl_view_view);
        $lib->wFile($dir_view . '/view.phtml', $tmpl_view_view);
        $_SESSION['success'][] = 'View: '.$dir_view . '/view.phtml';
    } else {
         $_SESSION['error'][] = 'A view view.phtml já está criada e não pode ser substituida';  
    }

    # Gera view index
    if(!$lib->existsFile($dir_view . '/index.phtml')) { 
        $tmpl_view_index = $lib->replaceData('tbCabecalho', $cabecalho, $tmpl_view_index);
        $tmpl_view_index = $lib->replaceData('tbLinhas', $linha, $tmpl_view_index);
        $tmpl_view_index = $lib->replaceData('CtrlName', $name, $tmpl_view_index);
        $tmpl_view_index = $lib->replaceData('CtrlNames', $plural, $tmpl_view_index);
        $tmpl_view_index = $lib->replaceData('id', $pk, $tmpl_view_index);
        $lib->wFile($dir_view . '/index.phtml', $tmpl_view_index);
        $_SESSION['success'][] = 'View: '.$dir_view . '/index.phtml';
    } else {
         $_SESSION['error'][] = 'A view index.phtml já está criada e não pode ser substituida';  
    }

    # Verifica se o form já foi criado
    if(!$lib->existsFile($dir_app . '/Form/' . $name . 'Form.php')) { 
        $tmpl_form = $lib->replaceData('CtrlName', $name, $tmpl_form);
        $tmpl_form = $lib->replaceData('CtrlNames', $plural, $tmpl_form);
        $tmpl_form = $lib->replaceData('id', $pk, $tmpl_form);
        $tmpl_form = $lib->replaceData('cpsForm', implode(' ', $cps_form), $tmpl_form);
        $lib->wFile($dir_app . '/Form/' . $name . 'Form.php', $tmpl_form);
        $_SESSION['success'][] = 'Form: '.$dir_app . '/Form/' . $name . 'Form.php';
    } else {
        $_SESSION['error'][] = 'O Form já está criado e não pode ser substituido';  
    }

     # Gerando Rotas
    $lib->generateRoutes($dir . '/module/'.$modulo, $name);

    // GERANDO TESTES
    if (!file_exists($dir_test)) {
        mkdir($dir_test);
    }

    if (!$lib->existsFile($dir_test.'/'.$modulo.'Test')) {
        mkdir($dir_test.'/'.$modulo.'Test');
        mkdir($dir_test.'/'.$modulo.'Test/Controller');
        mkdir($dir_test.'/'.$modulo.'Test/Entity');
        mkdir($dir_test.'/'.$modulo.'Test/Form');
        mkdir($dir_test.'/'.$modulo.'Test/Model');
    }

    if (!$lib->existsFile($dir_test . '/phpunit.xml')) {
        $tmpl_test_phpunit = $lib->replaceData('ModuleTest', $modulo.'Test', $tmpl_test_phpunit);
        $lib->wFile($dir_test . '/phpunit.xml', $tmpl_test_phpunit);
        # $lib->existsFile($dir_tmpl . '/test/' . $name . 'Form.php')
    }

    if (!$lib->existsFile($dir_test . '/Bootstrap.php')) {
        $tmpl_test_boot =  $lib->replaceData('ModuleName', $modulo, $tmpl_test_boot);
        $tmpl_test_boot =  $lib->replaceData("ModulesNames", "'".$modulo."'", $tmpl_test_boot);
        $lib->wFile($dir_test . '/Bootstrap.php', $tmpl_test_boot);

    }

    if (!$lib->existsFile($dir_test . '/'. $modulo .'Test/Controller/'.$name.'ControllerTest.php')) {
        $tmpl_test_ctrl =  $lib->replaceData('ModuleName', $modulo, $tmpl_test_ctrl);
        $tmpl_test_ctrl =  $lib->replaceData('CtrlName', $name, $tmpl_test_ctrl);
        $tmpl_test_ctrl =  $lib->replaceData('CtrlNames', $plural, $tmpl_test_ctrl);
        $campos[$pk] = 'pk';
        $tmpl_test_ctrl =  $lib->replaceData('arrayData', $lib->getArrayData($campos), $tmpl_test_ctrl);
        $tmpl_test_ctrl =  $lib->replaceData('arrayData1', $lib->getArrayData($campos, false, true), $tmpl_test_ctrl);
        $tmpl_test_ctrl =  $lib->replaceData('arrayData2', $lib->getArrayData($campos, true), $tmpl_test_ctrl);
        $lib->wFile($dir_test . '/'. $modulo .'Test/Controller/'.$name.'ControllerTest.php', $tmpl_test_ctrl);

    }
 
    // SAÍDA
    if (!empty($_SESSION['success'])) {
        array_unshift($_SESSION['success'], '<strong>Arquivos gerados para '.$name.':</strong>');
    }
    if (!empty($_SESSION['error'])) {
        array_unshift($_SESSION['error'], '<strong>Erros gerados para '.$name.':</strong>');
    }

    header('Location:'.$_SERVER['HTTP_REFERER']);


?>