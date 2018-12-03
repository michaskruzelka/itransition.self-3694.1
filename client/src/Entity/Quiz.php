<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * A quiz entity.
 *
 * @ApiResource(
 *     attributes={
 *          "access_control"="is_granted('ROLE_USER')",
 *          "order"={"updatedAt": "DESC"}
 *     },
 *     collectionOperations={
 *          "get",
 *          "post"={"access_control"="has_role('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_ADMIN') or object.isActive() == true",
 *              "normalization_context"={"groups"={"user:readItem"}}
 *          },
 *          "put"={"access_control"="is_granted('ROLE_ADMIN') and object.getAuthor() == user"},
 *          "delete"={"access_control"="is_granted('ROLE_ADMIN') and object.getAuthor() == user"}
 *     },
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"admin:write"}}
 * )
 *
 * @ApiFilter(
 *     OrderFilter::class, properties={"name"}, arguments={"orderParameterName"="order"}
 * )
 * @ApiFilter(
 *     SearchFilter::class, properties={"name": "partial"}
 * )
 *
 * @ORM\Entity(repositoryClass="App\Repository\QuizRepository")
 * @ORM\Table(name="quizzes")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class Quiz implements EntityWithAuthorInterface
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @Assert\Uuid
     * @Groups({"user:read","user:readItem","attempt:readItem"})
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=128)
     * @ORM\Column(type="string", length=128)
     * @Groups({"user:read","user:readItem","admin:write"})
     */
    private $name;

    /**
     * @var bool
     *
     * @Assert\NotNull()
     * @ORM\Column(type="boolean", options={"default": "f"})
     * @Groups({"admin:read","admin:readItem","admin:write"})
     */
    private $isActive = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     * @Groups({"user:read","admin:readItem","admin:write"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     * @Groups({"user:read","user:readItem","admin:write"})
     */
    private $updatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn()
     * @Groups({"user:read","user:readItem","admin:write"})
     */
    private $author;

    /**
     * @var Collection
     *
     * @Assert\Count(min=1, max=200, minMessage="You must specify at least one question")
     * @Groups({"user:readItem","admin:write"})
     * @ORM\OneToMany(targetEntity="QuizQuestionReference", mappedBy="quiz", cascade={"persist","remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position"="ASC"})
     */
    private $questions;

    /**
     * @var Attempt[]|Collection
     *
     * @ORM\OneToMany(targetEntity="Attempt", mappedBy="quiz", cascade={"remove"})
     * @ORM\OrderBy({"score"="DESC", "duration"="ASC"})
     */
    private $attempts;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->attempts = new ArrayCollection();
        $this->questions = new ArrayCollection();
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

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
     * @return null|bool
     */
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return $this
     */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return null|User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User $author
     *
     * @return $this
     */
    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return null|Collection
     */
    public function getQuestions(): Collection
    {
        $questions = new ArrayCollection();
        $this->questions->map(function (QuizQuestionReference $questionReference) use ($questions) {
            $questions->add($questionReference->getQuestion());
        });

        return $questions;
    }

    /**
     * @param array Question[]
     *
     * @return $this
     */
    public function setQuestions(array $questions): self
    {
        $this->questions->clear();
        $this->addQuestions(...$questions);

        return $this;
    }

    /**
     * @param Question ...$questions
     *
     * @return $this
     */
    public function addQuestions(Question ...$questions): self
    {
        foreach ($questions as $question) {
            $this->addQuestion($question);
        }

        return $this;
    }

    /**
     * @param Question $question
     *
     * @return Quiz
     */
    public function addQuestion(Question $question): self
    {
        $closure = function ($key, QuizQuestionReference $ref) use ($question) {
            return $ref->getQuestion() === $question;
        };

        if (!$this->questions->exists($closure)) {
            $position = $this->questions->count();
            $ref = new QuizQuestionReference();

            $ref->setQuiz($this);
            $ref->setQuestion($question);
            $ref->setPosition($position);

            $this->questions->add($ref);
        }

        return $this;
    }

    /**
     * @param Question $question
     *
     * @return $this
     */
    public function removeQuestion(Question $question): self
    {
        foreach ($this->questions as $key => $ref) {
            if ($ref->getQuestion() === $question) {
                $this->questions->remove($key);
            }
        }

        return $this;
    }

    /**
     * @param null|callable $closure
     *
     * @return null|Collection
     */
    public function getAttempts(callable $closure = null): ?Collection
    {
        $attempts = $this->attempts;

        if ($closure && $attempts) {
            $attempts = $attempts->filter($closure);
        }

        return $attempts;
    }

    /**
     * @return null|Collection
     */
    public function getCompleteAttempts(): ?Collection
    {
        $closure = function (Attempt $attempt) {
            return $attempt->complete();
        };

        return $this->getAttempts($closure);
    }

    /**
     * @param User $user
     *
     * @return Attempt
     */
    public function getUserAttempt(User $user): Attempt
    {
        $userAttempt = null;

        foreach ($this->attempts as $attempt) {
            if ($attempt->getAuthor() === $user) {
                $userAttempt = $attempt;
            }
        }

        if (!$userAttempt) {
            $userAttempt = new Attempt();
            $userAttempt->setQuiz($this);
            $userAttempt->setAuthor($user);
            $userAttempt->setCurrentQuestion($this->getQuestions()->first());
        }

        return $userAttempt;
    }

    /**
     * @param null|int $attemptStatus
     *
     * @return bool
     */
    public function availableToStart(?int $attemptStatus): bool
    {
        if ($this->getIsActive() && !$attemptStatus) {
            return true;
        }

        return false;
    }

    /**
     * @param null|int $attemptStatus
     *
     * @return bool
     */
    public function availableToResume(?int $attemptStatus): bool
    {
        $attemptStatuses = Attempt::getStatuses();

        if ($this->getIsActive()
            && in_array($attemptStatus, [$attemptStatuses['IN_PROGRESS'], $attemptStatuses['SUSPENDED']])) {
            return true;
        }

        return false;
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
