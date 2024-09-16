<?php

namespace App\Entity;

use App\Repository\CardAttributeTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;



#[ORM\Entity(repositoryClass: CardAttributeTranslationRepository::class)]
class CardAttributeTranslation implements TranslationInterface

{
    use TranslationTrait, SoftDeletableTrait, TimestampableTrait, SluggableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(name:"title", type: 'string', nullable: true)]
    protected $title;

    #[ORM\Column(name:"product_attribute_title", type: 'string', nullable: true)]
    protected $productAttributeTitle;

    #[ORM\Column(name:"shortContent", type: 'text', nullable: true)]
    protected $shortContent;

    #[ORM\Column(name:"content", type: 'text', nullable: true)]
    protected $content;

    function __construct($title = null, $content = null, $shortContent = null, $productAttributeTitle = null,  $locale = null){
        $this->title = $title;
        $this->productAttributeTitle = $productAttributeTitle;
        $this->content = $content;
        $this->shortContent = $shortContent;
        $this->locale = $locale;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getShortContent()
    {
        return $this->shortContent;
    }

    /**
     * @param mixed $shortContent
     */
    public function setShortContent($shortContent): void
    {
        $this->shortContent = $shortContent;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getProductAttributeTitle()
    {
        return $this->productAttributeTitle;
    }

    /**
     * @param mixed $productAttributeTitle
     */
    public function setProductAttributeTitle($productAttributeTitle): void
    {
        $this->productAttributeTitle = $productAttributeTitle;
    }


}
