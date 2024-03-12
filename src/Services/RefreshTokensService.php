<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Entities\RefreshTokenEntity;
use Monderka\DoctrineTools\Services\AbstractDoctrineService;
use DateTime;

/** @extends AbstractDoctrineService<RefreshTokenEntity> */
final class RefreshTokensService extends AbstractDoctrineService implements ServiceInterface
{
    public static string $entityName = RefreshTokenEntity::class;
    public static string $entityAlias = "refreshToken";

    public function clearExpiredTokens(): void
    {
        $qb = $this->em->createQueryBuilder()
            ->select("r")
            ->from(RefreshTokenEntity::class, "r")
            ->where("r.created < ?1")
            ->orWhere("r.counter > ?2")
            ->setParameter(
                1,
                (new DateTime())->modify("-" . RefreshTokenEntity::EXPIRATION)
            )->setParameter(
                2,
                RefreshTokenEntity::COUNT_LIMIT
            );
        /** @var RefreshTokenEntity[] $expiredTokens */
        $expiredTokens = $qb->getQuery()->getResult();
        foreach ($expiredTokens as $expiredToken) {
            $this->em->remove($expiredToken);
            $this->em->flush($expiredToken);
        }
    }
}
