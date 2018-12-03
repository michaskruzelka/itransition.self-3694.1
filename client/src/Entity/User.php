<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * A user entity.
 *
 * @ApiResource(
 *     attributes={
 *          "access_control"="is_granted('ROLE_USER')"
 *     },
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"user:read"}}
 * )
 * @ApiFilter(
 *     OrderFilter::class, properties={"id", "username", "fullName", "enabled"}, arguments={"orderParameterName"="order"}
 * )
 * @ApiFilter(
 *     SearchFilter::class, properties={"username": "partial", "fullName": "partial"}
 * )
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"admin:read","admin:readItem"})
     */
    protected $id;

    /**
     * @var string
     *
     * @Groups({"admin:read"})
     */
    protected $username;

    /**
     * @var bool
     *
     * @Groups({"admin:read"})
     */
    protected $enabled;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Registration", "Profile"})
     * @Assert\Length(max=255, groups={"Registration", "Profile"})
     * @ORM\Column(type="string", length=128)
     * @Groups({"user:read","user:readItem","attempt:readItem"})
     */
    private $fullName;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     *
     * @return $this
     */
    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }
}
