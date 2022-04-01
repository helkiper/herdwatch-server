<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer")
     * @Groups({
     *     "default",
     *     "user",
     *     "group"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @OA\Property(type="string", maxLength=30)
     * @Assert\NotBlank
     * @Assert\Length(max=30)
     * @Groups({
     *     "default",
     *     "user",
     *     "user.create",
     *     "group"
     * })
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=30)
     * @OA\Property(type="string", maxLength=30)
     * @Assert\NotBlank
     * @Assert\Length(max=30)
     * @Assert\Email
     * @Groups({
     *     "default",
     *     "user",
     *     "user.create",
     *     "group"
     * })
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, inversedBy="users")
     * @Groups({
     *     "default",
     *     "user"
     * })
     */
    private $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        $this->groups->removeElement($group);

        return $this;
    }
}
