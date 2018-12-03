<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CorrectAnswer.
 *
 * @Annotation
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class CorrectAnswer extends Constraint
{
    /**
     * @var int
     */
    public $num = 1;

    /**
     * @var string
     */
    public $message = 'The question contains more or fewer than {{ num }} correct answers.';

    /**
     * @return string
     */
    public function getDefaultOption(): string
    {
        return 'num';
    }
}
