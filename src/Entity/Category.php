<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Trait\PictureTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;



use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;


#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category implements TranslatableInterface, SoftDeletableInterface, TimestampableInterface
{
    use SoftDeletableTrait,TimestampableTrait,TranslatableTrait, PictureTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: "childrens")]

    private $parent;

    #[ORM\OneToMany(mappedBy: "parent", targetEntity: Category::class)]
    private $childrens;

    public function __toString() : string
    {
        return $this->translate()->getTitle();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getChildrens(){
        return $this->childrens;
    }

    public function addChildren(Category $children): self
    {
        if (!$this->childrens->contains($children)) {
            $this->childrens->add($children);
        }

        return $this;
    }

    public function removeChildren(Category $children): self
    {
        $this->childrens->removeElement($children);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

}