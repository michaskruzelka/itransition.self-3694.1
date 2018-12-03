<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Quiz;
use App\Form\QuestionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Ramsey\Uuid\Uuid;

/**
 * Class QuizController.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class QuizController extends AbstractController
{
    /**
     * Quizzes list.
     *
     * @Route("/quizzes", name="quizzes_list", methods={"GET"})
     *
     * @return Response
     */
    public function main(): Response
    {
        $quizzes = $this->getDoctrine()
            ->getRepository(Quiz::class)
            ->getCompleteOverview($this->getUser());

        return $this->render('quiz/list.html.twig', ['quizzes' => $quizzes]);
    }

    /**
     * Quiz page.
     *
     * @Route("/quizzes/{id}", name="quiz_take", requirements={"id"=Uuid::VALID_PATTERN}, methods={"GET","POST"})
     *
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     */
    public function take(string $id, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Quiz $quiz */
        $quiz = $em->find(Quiz::class, $id);
        if (!$quiz) {
            throw $this->createNotFoundException('The quiz does not exist');
        }

        if ($quiz->getQuestions()->isEmpty()) {
            throw $this->createNotFoundException('The quiz has no questions');
        }

        $myAttempt = $quiz->getUserAttempt($this->getUser());

        if ($myAttempt->getId() && $myAttempt->getUpdatedAt() < $quiz->getUpdatedAt()) {
            $em->remove($myAttempt);
            $em->flush();
            $myAttempt = $quiz->getUserAttempt($this->getUser());
            $this->addFlash('warning', 'The quiz has been updated since you were not here. Please start over.');
        }

        if (!$quiz->availableToResume($myAttempt->getStatus()) && !$quiz->availableToStart($myAttempt->getStatus())) {
            return $this->render('quiz/stats.html.twig', compact('quiz', 'myAttempt'));
        }

        $currentQuestion = $myAttempt->getCurrentQuestion();

        $form = $this->createForm(
            QuestionType::class,
            ['quizId' => $id, 'answers' => $currentQuestion->getAnswers()]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Answer $answer */
            $answer = $form->getData()['answer'];

            if ($answer->isCorrect()) {
                $myAttempt->increaseScore();
                $this->addFlash('info', 'Correct!');
            } else {
                $correctAnswer = $currentQuestion->getCorrectAnswer()->getContent();
                $this->addFlash(
                    'notice',
                    "Wrong! Here is the correct answer: {$correctAnswer}"
                );
            }

            $myAttempt->calculateDuration();

            if ($quiz->getQuestions()->last() === $currentQuestion) {
                $myAttempt->setStatus($myAttempt->getStatuses()['COMPLETE']);
            } else {
                $nextQuestion = $quiz->getQuestions()->get($myAttempt->getCurrentQuestionNumber());
                $myAttempt->setCurrentQuestion($nextQuestion);
            }

            $em->persist($myAttempt);
            $em->flush();

            return $this->redirectToRoute('quiz_take', ['id' => $id]);
        }

        if (!$myAttempt->inProgress()) {
            $myAttempt->setStatus($myAttempt::getStatuses()['IN_PROGRESS']);
            $em->persist($myAttempt);
            $em->flush();
        }

        $formView = $form->createView();

        return $this->render('quiz/take.html.twig', compact('quiz', 'myAttempt', 'formView'));
    }
}
