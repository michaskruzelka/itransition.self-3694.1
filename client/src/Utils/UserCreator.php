<?php

declare(strict_types=1);

namespace App\Utils;

use App\Exception\InvalidArgumentException;
use FOS\UserBundle\Model\UserManagerInterface;
use App\Entity\User;

/**
 * Class UserCreator.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class UserCreator
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * UserCreator constructor.
     *
     * @param UserManagerInterface $userManager
     * @param Validator            $validator
     */
    public function __construct(UserManagerInterface $userManager, Validator $validator)
    {
        $this->validator = $validator;
        $this->userManager = $userManager;
    }

    /**
     * @param string $username
     * @param string $plainPassword
     * @param string $email
     * @param string $fullName
     *
     * @return User
     *
     * @throws InvalidArgumentException
     */
    public function createAdmin(?string $username, ?string $plainPassword, ?string $email, ?string $fullName): User
    {
        $this->validateUserData($username, $plainPassword, $email, $fullName);

        /** @var User $user */
        $user = $this->userManager->createUser();
        $user->setEnabled(true);
        $user->setSuperAdmin(true);

        $user->setFullName($fullName);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($plainPassword);

        $this->userManager->updateUser($user);

        return $user;
    }

    /**
     * @param null|string $username
     * @param null|string $plainPassword
     * @param null|string $email
     * @param null|string $fullName
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    private function validateUserData(?string $username, ?string $plainPassword, ?string $email, ?string $fullName): self
    {
        $this->validator->validateUsername($username);
        $existingUser = $this->userManager->findUserByUsername($username);

        if (null !== $existingUser) {
            throw new InvalidArgumentException(sprintf('There is already a user registered with the "%s" username.', $username));
        }

        $this->validator->validatePassword($plainPassword);
        $this->validator->validateEmail($email);
        $this->validator->validateFullName($fullName);

        $existingEmail = $this->userManager->findUserByEmail($email);

        if (null !== $existingEmail) {
            throw new InvalidArgumentException(sprintf('There is already a user registered with the "%s" email.', $email));
        }

        return $this;
    }
}
