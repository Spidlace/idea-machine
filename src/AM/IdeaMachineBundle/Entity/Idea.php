<?php

// src/AM/IdeaMachineBundle/Entity/Idea

namespace AM\IdeaMachineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AM\IdeaMachineBundle\Validator\Antiflood;

/**
 * Idea
 *
 * @ORM\Table(name="idea")
 * @ORM\Entity(repositoryClass="AM\IdeaMachineBundle\Repository\IdeaRepository")
 */
class Idea
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\Length(min=5, minMessage="Le titre doit faire au moins {{ limit }} caractères.")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Antiflood()
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\OneToOne(targetEntity="AM\IdeaMachineBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
    */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="AM\IdeaMachineBundle\Entity\Vote", mappedBy="idea")
    */
    private $votes; // Notez le « s », une annonce est liée à plusieurs candidatures

    /**
        * @Gedmo\Slug(fields={"title"})
        * @ORM\Column(name="slug", type="string", length=255, unique=true)
    */
    private $slug;

    /**
     * @ORM\OneToOne(targetEntity="AM\UserBundle\Entity\User", cascade={"persist"})
     * @Assert\Valid()
    */
    private $user;

    public function __construct()
    {
        // Par défaut, la date de l'annonce est la date d'aujourd'hui
        $this->date = new \Datetime();
    }
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Idea
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Idea
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Idea
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set image
     *
     * @param \AM\IdeaMachineBundle\Entity\Image $image
     *
     * @return Idea
     */
    public function setImage(\AM\IdeaMachineBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AM\IdeaMachineBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add vote
     *
     * @param \AM\IdeaMachineBundle\Entity\Vote $vote
     *
     * @return Idea
     */
    public function addVote(\AM\IdeaMachineBundle\Entity\Vote $vote)
    {
        $this->votes[] = $vote;

        // On lie l'idée au vote
        $vote->setIdea($this);

        return $this;
    }

    /**
     * Remove vote
     *
     * @param \AM\IdeaMachineBundle\Entity\Vote $vote
     */
    public function removeVote(\AM\IdeaMachineBundle\Entity\Vote $vote)
    {
        $this->votes->removeElement($vote);
    }

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Idea
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set user
     *
     * @param \AM\userBundle\Entity\User $user
     *
     * @return Idea
     */
    public function setUser(\AM\userBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AM\userBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}