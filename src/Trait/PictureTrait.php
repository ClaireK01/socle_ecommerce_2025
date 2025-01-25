<?php

namespace App\Trait;


use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\HttpKernel\KernelInterface;

trait PictureTrait
{
    private $kernel;

    public function __construct()
    {
//        $this->kernel = $kernel;
    }

    #[ManyToMany(targetEntity: Picture::class, inversedBy: "entity", cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[JoinTable(name: "entity_picture")]
    #[JoinColumn(name: "entity_id", referencedColumnName: "id")]
    #[InverseJoinColumn(name: "picture_id", referencedColumnName: "id")]
    private $pictures;


    public function getPictures(): ?Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
        }

        return $this;
    }

    public function removePicture($picture, $delete = false): self
    {
        if($picture instanceof Picture){
            $this->pictures->removeElement($picture);
        }

        if($delete){
//            $path = $this->kernel->getProjectDir() . "/public/" . $picture->getPath();
//            if(file_exists($path)){
//                unlink($path);
//            }
        }

        return $this;
    }

    public function getFirstPicture(){
        if($this->pictures[0]){
            return $this->pictures[0];
        }else{
            $pic = new Picture();
            $pic->setPath(Picture::SANS_IMG);
            return $pic;
        }
    }

}