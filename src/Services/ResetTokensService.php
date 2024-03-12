<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Entities\ResetTokenEntity;
use Monderka\DoctrineTools\Services\AbstractDoctrineService;
use DateTime;

/** @extends AbstractDoctrineService<ResetTokenEntity> */
final class ResetTokensService extends AbstractDoctrineService implements ServiceInterface
{
    public static string $entityName = ResetTokenEntity::class;
    public static string $entityAlias = "resetToken";

    public function clearExpiredTokens(): void
    {
        $qb = $this->em->createQueryBuilder()
            ->select("r")
            ->from(ResetTokenEntity::class, "r")
            ->where("r.created < ?1")
            ->setParameter(
                1,
                (new DateTime())->modify("-" . ResetTokenEntity::EXPIRATION)
            );
        /** @var ResetTokenEntity[] $expiredTokens */
        $expiredTokens = $qb->getQuery()->getResult();
        foreach ($expiredTokens as $expiredToken) {
            $this->em->remove($expiredToken);
            $this->em->flush($expiredToken);
        }
    }
}
