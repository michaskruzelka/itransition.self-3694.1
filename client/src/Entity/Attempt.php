<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Attempt.
 *
 * @ORM\Entity
 * @ORM\Table(name="attempts", uniqueConstraints={@ORM\UniqueConstraint(name="unique_author_quiz", columns={"author_id", "quiz_id"})})
 * @ORM\HasLifecycleCallbacks
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class Attempt implements EntityWithAuthorInterface
{
    const STATUSES = [
        'IN_PROGRESS' => 1,
        'SUSPENDED' => 2,
        'COMPLETE' => 3,
        'EXPIRED' => 4,
    ];

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * Practically, the number of questions answered correctly.
     *
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="smallint", options={"unsigned": true})
     */
    private $score = 0;

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="bigint")
     */
    private $duration = 0;

    /**
     * @var int
     *
     * @Assert\Choice(callback="getStatuses")
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $startedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn()
     * @Groups({"user:read","attempt:readItem","user:readItem"})
     */
    private $author;

    /**
     * @var Quiz
     *
     * @ORM\ManyToOne(targetEntity="Quiz", inversedBy="attempts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quiz;

    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="Question")
     * @ORM\JoinColumn(nullable=false)
     */
    private $currentQuestion;

    public function __construct()
    {
        $this->startedAt = new \DateTime();
    }

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return null|int
     */
    public function getScore(): ?int
    {
        return $this->score;
    }

    /**
     * @param int $score
     *
     * @return $this
     */
    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return null|float
     */
    public function getDuration(): ?float
    {
        return (float) $this->duration;
    }

    /**
     * @return string
     */
    public function getFormattedDuration(): string
    {
        $seconds = $this->getDuration();
        $minutes = floor($seconds / 60);
        $remaining = $seconds % 60;

        return "{$minutes} minute(s) {$remaining} second(s)";
    }

    /**
     * @param int $duration
     *
     * @return $this
     */
    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return null|Quiz
     */
    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    /**
     * @param Quiz $quiz
     *
     * @return $this
     */
    public function setQuiz(Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartedAt(): \DateTime
    {
        return $this->startedAt;
    }

    /**
     * @param null|\DateTime $startedAt
     *
     * @return $this
     */
    public function setStartedAt(\DateTime $startedAt): self
    {
        $this->startedAt = $startedAt;

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
     * @return null|Question
     */
    public function getCurrentQuestion(): ?Question
    {
        return $this->currentQuestion;
    }

    /**
     * @return int
     */
    public function getCurrentQuestionNumber(): int
    {
        return $this->getQuiz()->getQuestions()->indexOf($this->getCurrentQuestion()) + 1;
    }

    /**
     * @param Question $currentQuestion
     *
     * @return $this
     */
    public function setCurrentQuestion(Question $currentQuestion): self
    {
        $this->currentQuestion = $currentQuestion;

        return $this;
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return self::STATUSES;
    }

    /**
     * @return bool
     */
    public function complete(): bool
    {
        return $this->status === self::STATUSES['COMPLETE'];
    }

    /**
     * @return bool
     */
    public function inProgress(): bool
    {
        return $this->status === self::STATUSES['IN_PROGRESS'];
    }

    /**
     * @return $this
     */
    public function calculateDuration(): self
    {
        $dtEnd = new \DateTime();
        $duration = $dtEnd->diff($this->getUpdatedAt())->s;
        $this->duration += $duration;

        return $this;
    }

    /**
     * @param int $score
     *
     * @return $this
     */
    public function increaseScore(int $score = 1): self
    {
        $this->score += $score;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate
     */
    public function updateUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }
}
