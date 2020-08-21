<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $author;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $photoName;

    /**
     * @ORM\OneToMany(targetEntity=PostTag::class, mappedBy="post", orphanRemoval=true)
     */
    private $post_tags;

    public function __construct()
    {
        $this->post_tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPhotoName(): ?string
    {
        return $this->photoName;
    }

    public function setPhotoName(string $photoName): self
    {
        $this->photoName = $photoName;

        return $this;
    }

    /**
     * @return Collection|PostTag[]
     */
    public function getPostTags(): Collection
    {
        return $this->post_tags;
    }

    public function addPostTag(PostTag $postTag): self
    {
        if (!$this->post_tags->contains($postTag)) {
            $this->post_tags[] = $postTag;
            $postTag->setPost($this);
        }

        return $this;
    }

    public function removePostTag(PostTag $postTag): self
    {
        if ($this->post_tags->contains($postTag)) {
            $this->post_tags->removeElement($postTag);
            // set the owning side to null (unless already changed)
            if ($postTag->getPost() === $this) {
                $postTag->setPost(null);
            }
        }

        return $this;
    }

}
