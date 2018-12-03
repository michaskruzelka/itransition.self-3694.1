<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Attempt;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Class UserRepository.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class QuizRepository extends EntityRepository
{
    /**
     * SQL:
     *   SELECT q.id, q.name, q.is_active,
     *   SUM(CASE WHEN a.status = 2 THEN 1 ELSE 0 END) AS attempts_count,
     *   (SELECT u.full_name FROM users u
     *      LEFT JOIN attempts a_sub ON a_sub.author_id = u.id
     *      WHERE a_sub.quiz_id = q.id
     *      ORDER BY a_sub.score DESC, a_sub.duration ASC LIMIT 1) AS leading_scorer,
     *   (SELECT my_a_sub.status FROM attempts my_a_sub
     *      WHERE my_a_sub.quiz_id = q.id AND my_a_sub.author_id = {$me->getId()}) AS myAttemptStatus
     *   FROM quizzes q
     *   LEFT JOIN attempts a ON a.quiz_id = q.id
     *   GROUP BY q.id.
     *
     * @return array
     */
    public function getCompleteOverview(User $me): array
    {
        $subDqlLeader = $this->_em->createQueryBuilder()
            ->select('u_sub.fullName')
            ->from(User::class, 'u_sub')
            ->leftJoin(Attempt::class, 'a_sub', Join::WITH, 'a_sub.author = u_sub')
            ->where('a_sub.quiz = q')
            ->orderBy('a_sub.score', 'DESC')
            ->addOrderBy('a_sub.duration', 'ASC')
            ->getDQL();

        $subDqlMe = $this->_em->createQueryBuilder()
            ->select('my_a_sub.status')
            ->from(Attempt::class, 'my_a_sub')
            ->where('my_a_sub.quiz = q')
            ->andWhere($this->_em->getExpressionBuilder()->eq('my_a_sub.author', $me->getId()))
            ->getDQL();

        $completeStatus = Attempt::getStatuses()['COMPLETE'];
        $query = $this->_em->createQueryBuilder()
            ->select('q AS quiz')
            ->addSelect("SUM(CASE WHEN a.status = {$completeStatus} THEN 1 ELSE 0 END) AS attemptsCount")
            ->addSelect("FIRST({$subDqlLeader}) AS leadingScorer")
            ->addSelect("({$subDqlMe}) AS myAttemptStatus")
            ->from($this->_entityName, 'q')
            ->leftJoin(Attempt::class, 'a', Join::WITH, 'a.quiz = q')
            ->groupBy('q.id')
            ->orderBy('q.updatedAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
}
