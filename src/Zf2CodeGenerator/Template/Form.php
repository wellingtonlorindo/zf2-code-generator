<?php 
namespace Application\Form;

use Zend\Form\Form;

/**             
 * Caminho module/Modulo/src/Modulo/Form/${CtrlName}Form.php
 */
class ${CtrlName}Form extends Form
{
	public function __construct($name = null)
	{
         // we want to ignore the name passed
		parent::__construct('${ctrlName}');

		$this->add(array(
			'name' => '${id}',
			'type' => 'Hidden',
			));

		${cpsForm}

		$this->add(array(
           'name' => 'submit',
           'type' => 'Submit',
          'attributes' => array(
               'value' => 'Enviar',
               'id' => 'submitbutton',
               ),
           ));
	}
}