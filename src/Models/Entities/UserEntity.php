<?php

namespace App\Models\Entities;

use Monderka\DoctrineTools\Interfaces\DoctrineEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Monderka\DoctrineTools\Interfaces\GraphQLEntityInterface;
use Nette\Security\Passwords;

#[ORM\Table(name: 'users')]
#[ORM\Index(columns: ['email'], name: 'email_idx')]
#[ORM\Index(columns: ['active'], name: 'active_idx')]
#[ORM\Entity]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'users')]
class UserEntity implements DoctrineEntityInterface, GraphQLEntityInterface
{
    #[ORM\Column(type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    protected string $email;

    #[ORM\Column(type: "string")]
    protected string $password;

    #[ORM\Column(type: "boolean")]
    protected bool $active = true;

    #[ORM\Column(type: "integer")]
    protected int $counter = 0;

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function __clone()
    {
        $this->id = null;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email, bool $validate = true): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPassword(Passwords $passwordService, string $password): self
    {
        $this->password = $passwordService->hash($password);
        return $this;
    }

    public function validatePassword(Passwords $passwordService, string $password): bool
    {
        return $passwordService->verify($password, $this->password);
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function clearCounter(): self
    {
        $this->counter = 0;
        return $this;
    }

    public function increaseCounter(): self
    {
        $this->counter++;
        return $this;
    }

    public function toGraphQLOutput(): array
    {
        return [
            "id" => $this->getId(),
            "email" => $this->getEmail(),
            "active" => $this->isActive()
        ];
    }

    /**
     * @param array{
     *     "email"?: string,
     *     "active"?: boolean
     * } $parameters
     * @param array<string, mixed> $associatedEntities
     * @param array<string|mixed> $options
     * @return GraphQLEntityInterface
     */
    public function updateFromGraphQLInput(
        array $parameters,
        array $associatedEntities = [],
        array $options = []
    ): UserEntity {
        if (!empty($parameters["email"])) {
            $this->setEmail($parameters["email"]);
        }
        if (array_key_exists("active", $parameters) && is_bool($parameters["active"])) {
            $this->setActive($parameters["active"]);
        }
        return $this;
    }

    /**
     * @param array{
     *     "email": string
     * } $parameters
     * @param array $associatedEntities
     * @param array $options
     * @return GraphQLEntityInterface
     */
    public static function createFromGraphQLInput(
        array $parameters,
        array $associatedEntities = [],
        array $options = []
    ): UserEntity {
        return (new self())
            ->setEmail($parameters["email"])
            ->setActive(true)
            ->clearCounter();
    }
}
