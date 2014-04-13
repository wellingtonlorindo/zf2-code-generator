<?php
namespace Application\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="${ctrlName}")
 *
 */

class ${CtrlName} extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Pedido")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", columnDefinition="VARCHAR(100) NOT NULL")
     */    
    protected $nome;

    /**
     * @ORM\Column(type="string", columnDefinition="VARCHAR(100) NOT NULL")
     */ 
    protected $email;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('J', 'F')")
     */ 
    protected $tipo;

    /**
     * @ORM\Column(type="integer", columnDefinition="TINYINT")
     */
    protected $idade;

    /**
     * @ORM\Column(type="string", columnDefinition="DATETIME")
     */
    protected $created;

    /**
     * @ORM\Column(type="string", columnDefinition="DATETIME");
     */
    protected $modified;


    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property) 
    {
        return $this->$property;
    }

    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value) 
    {
        $this->$property = $value;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy() 
    {
        return get_object_vars($this);
    }    

}