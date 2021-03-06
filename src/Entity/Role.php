<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="role")
 * @ORM\Entity
 */
class Role implements \JsonSerializable
{
    use Traits\TruncateStrings;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=100)
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     * @var Collection
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="RolePermission", mappedBy="role")
     * @var Collection
     */
    protected $permissions;

    /**
     * Role constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection;
        $this->permissions = new ArrayCollection;
    }

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $this->_truncateString($name, 100);
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return Collection
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function jsonSerialize()
    {
        $return = [
            'id'      => $this->id,
            'name'    => $this->name,
            'permissions' => [
                'global' => [],
                'station' => [],
            ],
        ];

        foreach($this->permissions as $permission) {
            /** @var RolePermission $permission */

            if ($permission->hasStation()) {
                $return['permissions']['station'][$permission->getStationId()][] = $permission->getActionName();
            } else {
                $return['permissions']['global'][] = $permission->getActionName();
            }
        }

        return $return;
    }
}
