<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * Interface EntityWithAuthor.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
interface EntityWithAuthorInterface
{
    /**
     * @return null|User
     */
    public function getAuthor(): ?User;

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function setAuthor(User $user);
}
