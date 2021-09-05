<?php

namespace App\Entity;
use App\TestServices\Spam;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\TestServices\SaveUser;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     * @Assert\NotBlank(message="Le nom de la marque est obligatoire")
     * @Assert\NotEqualTo(propertyPath="content")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\Type("string")
     * @Assert\NotEqualTo(propertyPath="title", message="Le titre ne doit pas être identique au contenu")
     *
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     */
    private $author;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type("int")
     * @Assert\NotBlank(message="Le nombre de vues est obligatoire")
     * @Assert\GreaterThan("1")
     */

    private $nb_views = 1;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type("bool")
     */
    private $published;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("DateTime")
     * @var string A "Y-m-d H:i:s" formatted value
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Type("DateTime")
     * @var string A "Y-m-d H:i:s" formatted value
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="comment", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="articles")
     * @ORM\JoinTable(name = "asso_article_category")
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     *
     */
    private $user;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->categories = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

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

    public function getNbViews(): ?int
    {
        return $this->nb_views;
    }

    public function setNbViews(int $nb_views): self
    {
        $this->nb_views = $nb_views;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setComment($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getComment() === $this) {
                $comment->setComment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addArticle($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeArticle($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @ORM\PrePersist
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

//    /**
//     * @Assert\Callback()
//     */
//    public function isDescriptionValid(ExecutionContextInterface $context)
//    {
//
//        if($this->nb_views == -3) {
//            $context->buildViolation("Je ne veux pas de -trois")
//                    ->atPath('nb_views')
//                    ->addViolation();
//        }
//    }




}
