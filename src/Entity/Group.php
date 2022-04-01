<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`group`")
 */
class Group
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer")
     * @Groups({
     *     "default",
     *     "group",
     *     "user"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotNull
     * @Assert\Length(max=50)
     * @OA\Property(type="string", maxLength=50)
     * @Groups({
     *     "default",
     *     "group",
     *     "group.create",
     *     "user"
     * })
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="groups")
     * @OA\Property(type="array", @OA\Items(ref=@Model(type=User::class)))
     * @Groups({
     *     "default",
     *     "group"
     * })
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeGroup($this);
        }

        return $this;
    }
}
