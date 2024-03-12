<?php

namespace App\Models\Entities;

use App\Exceptions\InvalidResetTokenException;
use Monderka\DoctrineTools\Interfaces\DoctrineEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Exception;
use Nette\Security\Passwords;
use Nette\Utils\Random;
use Stringable;

#[ORM\Table(name: 'reset_tokens')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class ResetTokenEntity implements DoctrineEntityInterface, Stringable
{
    final public const EXPIRATION = "1 Day";
    final public const SECRET_LENGTH = 32;

    #[ORM\Column(type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected UserEntity $user;

    #[ORM\Column(type: 'datetime')]
    protected ?DateTime $created = null;

    #[ORM\Column(type: "boolean")]
    protected bool $registration = false;

    #[ORM\Column(type: "string")]
    protected string $password;

    protected ?string $secret = null;

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function __clone()
    {
        $this->id = null;
    }

    public function getUser(): UserEntity
    {
        return $this->user;
    }

    public function setUser(UserEntity $user): self
    {
        $this->user = $user;
        return $this;
    }

    /** @throws Exception */
    #[ORM\PrePersist]
    public function onCreate(): void
    {
        $this->created = new DateTime();
    }

    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    public function isRegistration(): bool
    {
        return $this->registration;
    }

    public function setRegistration(bool $registration): self
    {
        $this->registration = $registration;
        return $this;
    }

    public function validatePassword(Passwords $passwordService, string $password): bool
    {
        return $passwordService->verify($password, $this->password);
    }

    public function generatePassword(Passwords $passwordService): string
    {
        $this->secret = Random::generate(self::SECRET_LENGTH);
        $this->password = $passwordService->hash((string) $this->secret);
        return $this->secret;
    }

    /**
     * @param string $base64string
     * @return array{ "id": int, "secret": string }
     * @throws InvalidResetTokenException
     */
    public static function parseString(string $base64string): array
    {
        $string = base64_decode($base64string);
        if (!$string) {
            throw new InvalidResetTokenException("Reset password token is not base64 encoded string");
        }
        $arr = explode(".", $string);
        if (count($arr) !== 2 || !is_scalar(is_string($arr[0])) || !is_string($arr[1])) {
            throw new InvalidResetTokenException("Reset password token does not contain id and secret");
        }
        return [
            "id" => (int) $arr[0],
            "secret" => $arr[1]
        ];
    }

    public function __toString(): string
    {
        return base64_encode($this->id . "." . $this->secret);
    }
}
