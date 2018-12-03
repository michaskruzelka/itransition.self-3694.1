<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class QuestionType.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class QuestionType extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (isset($options['data']['quizId']) && isset($options['data']['answers'])) {
            $builder->setAction($this->router->generate('quiz_take', ['id' => $options['data']['quizId']]))
                ->add('answer', ChoiceType::class, [
                    'label' => 'Answers',
                    'choices' => $options['data']['answers'],
                    'choice_name' => 'id',
                    'choice_value' => 'id',
                    'choice_label' => 'content',
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true, ])
                ->add('save', SubmitType::class);
        }
    }
}
