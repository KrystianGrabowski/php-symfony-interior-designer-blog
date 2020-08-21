<?php

namespace App\Entity;

use App\Repository\PostTagRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostTagRepository::class)
 */
class PostTag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $post_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $tag_id;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="post_tags")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity=Tag::class, inversedBy="post_tags")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tag;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostId(): ?int
    {
        return $this->post_id;
    }

    public function setPostId(int $post_id): self
    {
        $this->post_id = $post_id;

        return $this;
    }

    public function getTagId(): ?int
    {
        return $this->tag_id;
    }

    public function setTagId(int $tag_id): self
    {
        $this->tag_id = $tag_id;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function setTag(?Tag $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}
