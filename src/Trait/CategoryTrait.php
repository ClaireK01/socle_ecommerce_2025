<?php

namespace App\Trait;


use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;

trait CategoryTrait
{

    #[ManyToMany(targetEntity: Category::class, inversedBy: "entity")]
    #[JoinTable(name: "entity_category")]
    #[JoinColumn(name: "entity_id", referencedColumnName: "id")]
    #[InverseJoinColumn(name: "category_id", referencedColumnName: "id")]
    private $categories;

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);
        return $this;
    }

    public function getFirstCategory(){
        if($this->categories[0]){
            return $this->categories[0];
        }

        return null;
    }

}