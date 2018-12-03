<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class MenuBuilder.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var Security
     */
    private $security;

    /**
     * @param FactoryInterface $factory
     * @param Security         $security
     */
    public function __construct(FactoryInterface $factory, Security $security)
    {
        $this->factory = $factory;
        $this->security = $security;
    }

    /**
     * @return ItemInterface
     */
    public function createMainMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'navbar-nav',
            ],
        ]);
        $menu->addChild('Quizzes', ['route' => 'quizzes_list']);

        if ($this->security->getToken()) {
            if ($this->security->isGranted('ROLE_USER')) {
                $menu->addChild('Log Out', ['route' => 'fos_user_security_logout']);
            } else {
                $menu->addChild('Log In', ['route' => 'fos_user_security_login']);
                $menu->addChild('Register', ['route' => 'fos_user_registration_register']);
            }
        }

        foreach ($menu->getChildren() as $child) {
            $child->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');
        }

        return $menu;
    }
}
