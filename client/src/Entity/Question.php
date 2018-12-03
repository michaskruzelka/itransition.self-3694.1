<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Validator\Constraints as AppAssert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * Class Question.
 *
 * @ApiResource(
 *     attributes={
 *          "access_control"="is_granted('ROLE_USER')",
 *          "order"={"updatedAt": "DESC"}
 *     },
 *     collectionOperations={
 *          "get",
 *          "post"={"access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"question:readItem"}}
 *          },
 *          "put"={"access_control"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     normalizationContext={"groups"={"user:read","question:read"}},
 *     denormalizationContext={"groups"={"admin:write"}}
 * )
 *
 * @ApiFilter(
 *     OrderFilter::class, properties={"content","updatedAt"}, arguments={"orderParameterName"="order"}
 * )
 * @ApiFilter(
 *     SearchFilter::class, properties={"content": "partial"}
 * )
 *
 * @ORM\Entity
 * @ORM\Table(name="questions")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class Question
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @Groups({"attempt:readItem","admin:readItem"})
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=2056)
     * @ORM\Column(type="string", length=2056)
     * @Groups({"user:readItem","question:readItem","question:read","admin:write"})
     */
    private $content;

    /**
     * @var Answer[]|ArrayCollection
     *
     * @Assert\Valid()
     * @Assert\Count(min=1, max=20, minMessage="You must specify at least one answer")
     * @AppAssert\CorrectAnswer(num=1)
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question", cascade={"persist","remove"}, orphanRemoval=true)
     * @Groups({"admin:write","question:readItem"})
     */
    private $answers;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     * @Groups({"user:read","user:readItem","admin:write"})
     */
    private $updatedAt;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param array Answer[] $answers
     *
     * @return $this
     */
    public function setAnswers(array $answers): self
    {
        $this->answers->clear();
        $this->addAnswers(...$answers);

        return $this;
    }

    /**
     * @param Answer ...$answers
     *
     * @return $this
     */
    public function addAnswers(Answer ...$answers): self
    {
        foreach ($answers as $answer) {
            $this->addAnswer($answer);
        }

        return $this;
    }

    /**
     * @param Answer $answer
     *
     * @return Question
     */
    public function addAnswer(Answer $answer): self
    {
        $answer->setQuestion($this);

        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
        }

        return $this;
    }

    /**
     * @return null|Answer
     */
    public function getCorrectAnswer(): ?Answer
    {
        foreach ($this->answers as $answer) {
            if ($answer->isCorrect()) {
                return $answer;
            }
        }

        return null;
    }

    /**
     * @param Answer $answer
     *
     * @return Quiz
     */
    public function removeAnswer(Answer $answer): self
    {
        $this->answers->removeElement($answer);

        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateUpdateAt(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
