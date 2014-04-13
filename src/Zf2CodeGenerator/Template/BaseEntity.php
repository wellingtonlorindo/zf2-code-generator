<?php
namespace Application\Entity;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/** @ORM\MappedSuperclass */
class BaseEntity implements InputFilterAwareInterface
{
    protected $inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                    ),
                ));

            // $inputFilter->add(array(
            //     'name'     => 'nome',
            //     'required' => true,
            //     'filters'  => array(
            //         array('name' => 'StripTags'),
            //         array('name' => 'StringTrim'),
            //         ),
            //     'validators' => array(
            //         array(
            //             'name'    => 'StringLength',
            //             'options' => array(
            //                 'encoding' => 'UTF-8',
            //                 'min'      => 1,
            //                 'max'      => 100,
            //                 ),
            //             ),
            //         ),
            //     ));

            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array()) 
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->nome = (!empty($data['nome'])) ? $data['nome'] : null;
        $this->email  = (!empty($data['email'])) ? $data['email'] : null;
        $this->tipo  = (!empty($data['tipo'])) ? $data['tipo'] : null;
        $this->idade  = (!empty($data['idade'])) ? $data['idade'] : null;
        $this->valor  = (!empty($data['valor'])) ? $data['valor'] : null;
    }

    /** 
     * @ORM\PreUpdate
     */
    public function modified()
    {
        $this->modified = date('Y-m-d H:i:s');
    }

    /** 
     * @ORM\PrePersist
     */
    public function created()
    {
        $this->created = date('Y-m-d H:i:s');
    }

}