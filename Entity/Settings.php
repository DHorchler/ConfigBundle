<?php
namespace DHorchler\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="dh_settings")
 */
class Settings {

    public function __construct() {
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
     * @ORM\Column(name="type", type="string", options={"comment" = "string, integer, float, date, datetime, choice, multiplechoice"})
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(name="filter", type="string", options={"comment" = "formats: (value: integer, float or string): min:value max:value range:value..value choice:choice1,choice2,choice3 regexp:/regular expresion/"}, nullable=true)
     */
    protected $filter;

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
    public function getId()
    {
        return $this->id;
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
     * Set filter
     *
     * @param string $filter
     * @return Settings
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Get filter
     *
     * @return string 
     */
    public function getFilter()
    {
        return $this->filter;
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
    
    public function __toString()
    {
        return 'Settings:'.$this->name;
    }
}
