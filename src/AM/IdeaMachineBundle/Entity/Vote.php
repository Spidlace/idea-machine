<?php
// src/AM/IdeaMachineBundle/Entity/Vote

namespace AM\IdeaMachineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="idea_vote")
 * @ORM\Entity(repositoryClass="AM\IdeaMachineBundle\Repository\VoteRepository")
 */
class Vote
{

  /**
   * @ORM\ManyToOne(targetEntity="AM\IdeaMachineBundle\Entity\Idea")
   * @ORM\JoinColumn(nullable=false)
   */
  private $idea;

  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="choix", type="text")
   */
  private $choix;

  /**
   * @ORM\ManyToOne(targetEntity="AM\UserBundle\Entity\User")
   * @ORM\JoinColumn(nullable=false)
   */
  private $user;

  /**
   * @ORM\Column(name="date", type="datetime")
   * @Assert\DateTime()
   */
  private $date;
  
  public function __construct()
  {
    $this->date = new \Datetime();
  }

  public function getId()
  {
    return $this->id;
  }

  public function setChoix($choix)
  {
    $this->choix = $choix;

    return $this;
  }

  public function getChoix()
  {
    return $this->choix;
  }

  public function setUser(\AM\userBundle\Entity\User $user = null)
  {
    $this->user = $user;
    return $this;
  }

  public function getUser()
  {
    return $this->user;
  }

  public function setDate(\Datetime $date)
  {
    $this->date = $date;

    return $this;
  }

  public function getDate()
  {
    return $this->date;
  }

    /**
     * Set idea
     *
     * @param \AM\IdeaMachineBundle\Entity\Idea $idea
     *
     * @return Vote
     */
    public function setIdea(\AM\IdeaMachineBundle\Entity\Idea $idea)
    {
        $this->idea = $idea;

        return $this;
    }

    /**
     * Get idea
     *
     * @return \AM\IdeaMachineBundle\Entity\Idea
     */
    public function getIdea()
    {
        return $this->idea;
    }
}
