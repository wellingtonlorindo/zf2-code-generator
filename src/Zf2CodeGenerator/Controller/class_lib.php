<?php 

class Lib
{   
    public $module;
    public $ctrlName;
    public $ctrlPlural;
    public $table;
    public $pathGenerator;
    public $pathModule;

    public function rFile($file){
        $data = '';
        $handle = fopen($file, 'r') or die('Cannot open file:  '.$file);
        $data = fread($handle, filesize($file));
        return $data;
    }

    public function isEmptyDirectory($dir){
        $scan = scandir($dir);
        if(count($scan) > 2) {
            return false;
        }

        return true;
    }

    public function existsFile($file){
        return file_exists($file);
    }

    public function wFile($file, $tmpl_data){
       $handle = fopen($file, 'w') or die('Cannot open file:  '.$file);
       fwrite($handle, $tmpl_data);
    }


    public function replaceData($str, $key, $tmpl_data){

        $tmpl_data = str_replace('${' . ucfirst($str) . '}', ucfirst($key), $tmpl_data);
        $tmpl_data = str_replace('${' . lcfirst($str) . '}', lcfirst($key), $tmpl_data);

        
        return $tmpl_data;
    }

    public function populate($data = array())
    {
        $this->module = (!empty($data['module']) ? ucfirst(strtolower($data['module'])) : 'Application');
        $this->ctrlName = (!empty($data['name'])) ? ucfirst(strtolower($data['name'])) : null;
        $this->ctrlPlural = (!empty($data['plural'])) ? ucfirst(strtolower($data['plural'])) : $data['name'] . 's';
        $this->table = !empty($data['tabela']) ? $data['tabela'] : null;
        $this->pathGenerator = dirname(__DIR__);
        $this->pathModule = dirname(__DIR__);
    }

    public function defineValueField($type)
    {
        if (strpos($type, '(')) {
            $type = strtolower(trim(substr($type, 0, strpos($type, '('))));
        } else {
            $type = strtolower($type);
        }
        $string = array('varchar', 'text', 'char', 'tinytext');
        $number = array('int', 'double', 'float', 'real');
        $date = array('date', 'datetime');

        if (in_array($type, $string)) {
           return 'Lorem ipsum'; 
        } else if (in_array($type, $number)) {
            return 123;
        } else if (in_array($type, $date)) {
           return '2014-03-20';
        }
        
    }

    public function getArrayData($campos, $pk = false, $emptyVal = false)
    {
        $dados = "";
        foreach ($campos as $key => $value) {
            if ($value == 'pk') {                
                if (!empty($pk)) {
                    $dados .= "'$key' => 7, ";
                } else {
                    $dados .= "'$key' => '', ";
                }
            } else if ($emptyVal) {
                $dados .= "'$key' => null, "; 
            } else {
                $dados .= "'$key' => '$value', ";                
            }
        }
        return $dados;
    }


    /**
     * Loads the template files
     * @param  Array $dirTemplate [description]
     * @return [type]              [description]
     */
    public function getTemplates($dirTemplate)
    {
        $templates = array();
        $templates['controller'] = $this->rFile($dirTemplate . '/Controller.php');
        $templates['form'] = $this->rFile($dirTemplate . '/Form.php');
        $templates['model'] = $this->rFile($dirTemplate . '/Model.php');        
        $templates['viewAdd'] = $this->rFile($dirTemplate . '/views/add.phtml');        
        $templates['viewEdit'] = $this->rFile($dirTemplate . '/views/edit.phtml');        
        $templates['viewDelete'] = $this->rFile($dirTemplate . '/views/delete.phtml');        
        $templates['viewView'] = $this->rFile($dirTemplate . '/views/view.phtml');        
        $templates['viewIndex'] = $this->rFile($dirTemplate . '/views/index.phtml');        
        $templates['testBootstrap'] = $this->rFile($dirTemplate . '/test/Bootstrap.php');        
        $templates['testPhpunit'] = $this->rFile($dirTemplate . '/test/phpunit.xml');        
        $templates['testCtrl'] = $this->rFile($dirTemplate . '/test/Controller.php');   

        return $templates;
    }

    public function generateEntity() 
    {
        $map = '.././vendor/bin/doctrine-module orm:convert-mapping';
        if (!empty($this->table)) {
            $map .= ' --filter="'.$this->table.'"';        
        }
        $map .= ' --namespace=\''.$this->module.'\\Entity\\\' --force --from-database annotation ./module/'.$this->module.'/src/';
        if (exec($map)) {
            $entities = '.././vendor/bin/doctrine-module orm:generate-entities ./module/'.$this->module.'/src/';
            if (!empty($this->table)) {
                $entities .= ' --filter="'.$this->table.'"';        
            }
            $entities .= ' --generate-annotations=true --update-entities --extend="'.$this->module.'\\Entity\\BaseEntity"';
            if (exec($entities)) {
                return true;
            } 
            return false;
        }         
        return false;        
    }

    public function describeTable()
    {
        $sql = mysql_query('describe '.$this->table);
        if (!$sql) {
           $_SESSION['error'][] = 'Tabela não encontrada';
           header('Location:'.$_SERVER['HTTP_REFERER']);
           exit();
        }
    }

    public function generateFile($type, $template)
    {
        $dir = dirname(__DIR__);
        switch ($type) {
            case 'controller':
                # code...
                break;
            
            default:
                # code...
                break;
        }

    }

    public function generateRoutes($pathModule, $controller)
    {
        if (self::existsFile($pathModule.'/config/module.config.php')) {
            $config = str_replace('__DIR__', '"__DIR__"', self::rFile($pathModule.'/config/module.config.php'));
            self::wFile($pathModule.'/config/module.config.php', $config);
            $config = include $pathModule.'/config/module.config.php';
        } else {
            $config = include dirname(__DIR__).'/generator/templates/module.config.php';
        }
        $modulo = end(explode('/', $pathModule));
        $config['router']['routes'][strtolower($controller)] = array(
            'type' => 'Segment', 
            'options' => array(
                'route'    => '/'.strtolower($controller).'[/][:action][/:id]',
                   'constraints' => array(
                       'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                       'id'     => '[0-9]+',
                       ),
                   'defaults' => array(
                       'controller' => $modulo.'\Controller\\'.ucfirst(strtolower($controller)),
                       'action'     => 'index',
                       ),
                   ),
        );

        $config['controllers']['invokables'][$modulo.'\Controller\\'.$controller.''] = $modulo.'\Controller\\'.$controller.'Controller';
        $config = var_export($config, true);
        file_put_contents($pathModule.'/config/module.config.php', '<?php return '.$config.';');
        $config = str_replace(array("\\\\", "'__DIR__", "0 =>", "1 =>"), array("\\", "__DIR__ . '", "", ""), file_get_contents($pathModule.'/config/module.config.php'));
        file_put_contents($pathModule.'/config/module.config.php', $config);
    }

}