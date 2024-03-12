<?php

namespace App\Models\Entities;

use App\Exceptions\InvalidRefreshTokenException;
use Monderka\DoctrineTools\Interfaces\DoctrineEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Exception;
use Nette\Security\Passwords;
use Nette\Utils\Random;
use Stringable;

#[ORM\Table(name: 'refresh_tokens')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class RefreshTokenEntity implements DoctrineEntityInterface, Stringable
{
    final public const EXPIRATION = "1 Year";
    final public const COUNT_LIMIT = 1000;
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

    #[ORM\Column(type: "integer")]
    protected int $counter = 0;

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

    public function increaseCounter(): self
    {
        $this->counter++;
        return $this;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function generatePassword(Passwords $passwordService): string
    {
        $this->secret = Random::generate(self::SECRET_LENGTH);
        $this->password = $passwordService->hash((string) $this->secret);
        return $this->secret;
    }

    public function validatePassword(Passwords $passwordService, string $password): bool
    {
        return $passwordService->verify($password, $this->password);
    }

    /**
     * @param string $base64string
     * @return array{ "id": int, "secret": string }
     * @throws InvalidRefreshTokenException
     */
    public static function parseString(string $base64string): array
    {
        $string = base64_decode($base64string);
        if (!$string) {
            throw new InvalidRefreshTokenException("Reset password token is not base64 encoded string");
        }
        $arr = explode(".", $string);
        if (count($arr) !== 2 || !is_scalar(is_string($arr[0])) || !is_string($arr[1])) {
            throw new InvalidRefreshTokenException("Reset password token does not contain id and secret");
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
