<?php
// src/DHorchler/ConfigBundle/Entity/Settings.php
namespace DHorchler\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dh_settings")
 */
class Settings
{
    public function __construct()
    {
        $this->updated = new \DateTime;
        $this->name = '';
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false, unique=true)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="defaultvalue", type="string", nullable=true)
     */
    protected $defaultValue;
    /**
     * @var string
     * @ORM\Column(name="currentvalue", type="string", length=1024, nullable=true)
     */
    protected $currentValue;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=1024, nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(name="min", type="string", nullable=true)
     */
    protected $min;

    /**
     * @var string
     * @ORM\Column(name="max", type="string", nullable=true)
     */
    protected $max;

    /**
     * @var string
     * @ORM\Column(name="choices", type="string", nullable=true)
     */
    protected $choices;

    /**
     * @var string
     * @ORM\Column(name="section", type="string", nullable=true)
     */
    protected $section;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    

    /**
     * Get id
     *
     * @return integer 
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set defaultValue
     *
     * @param string $defaultValue
     * @return Settings
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    
        return $this;
    }

    /**
     * Get defaultValue
     *
     * @return string 
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set currentValue
     *
     * @param string $currentValue
     * @return Settings
     */
    public function setCurrentValue($currentValue)
    {
        $this->currentValue = $currentValue;
    
        return $this;
    }

    /**
     * Get currentValue
     *
     * @return string 
     */
    public function getCurrentValue()
    {
        return $this->currentValue;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Settings
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    public function __toString()
    {
        return 'Settings:'.$this->name;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Settings
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Settings
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Settings
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set min
     *
     * @param string $min
     * @return Settings
     */
    public function setMin($min)
    {
        $this->min = $min;
    
        return $this;
    }

    /**
     * Get min
     *
     * @return string 
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set max
     *
     * @param string $max
     * @return Settings
     */
    public function setMax($max)
    {
        $this->max = $max;
    
        return $this;
    }

    /**
     * Get max
     *
     * @return string 
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set choices
     *
     * @param string $choices
     * @return Settings
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
    
        return $this;
    }

    /**
     * Get choices
     *
     * @return string 
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * Set section
     *
     * @param string $section
     * @return Settings
     */
    public function setSection($section)
    {
        $this->section = $section;
    
        return $this;
    }

    /**
     * Get section
     *
     * @return string 
     */
    public function getSection()
    {
        return $this->section;
    }
}