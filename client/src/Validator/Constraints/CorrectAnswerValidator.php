<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\Answer;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CorrectAnswerValidator.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class CorrectAnswerValidator extends ConstraintValidator
{
    /**
     * @param mixed      $answers
     * @param Constraint $constraint
     */
    public function validate($answers, Constraint $constraint): void
    {
        if ($answers instanceof Collection) {
            $filter = function (Answer $answer) {
                return $answer->isCorrect();
            };
            $correctAnswers = $answers->filter($filter);
            if ($correctAnswers->count() === $constraint->num) {
                return;
            }
        }
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ num }}', $constraint->num)
            ->addViolation();
    }
}
