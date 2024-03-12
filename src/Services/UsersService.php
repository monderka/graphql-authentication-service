<?php

namespace App\Services;

use App\Exceptions\UserNotFoundException;
use App\Interfaces\ServiceInterface;
use App\Models\Entities\UserEntity;
use Monderka\DoctrineTools\Services\AbstractDoctrineService;

/** @extends AbstractDoctrineService<UserEntity> */
final class UsersService extends AbstractDoctrineService implements ServiceInterface
{
    public static string $entityName = UserEntity::class;
    public static string $entityAlias = "user";

    /**
     * @param string $email
     * @return UserEntity
     * @throws UserNotFoundException
     */
    public function findByEmail(string $email): UserEntity
    {
        $user = $this->getRepository()->findOneBy([
            "email" => $email
        ]);
        if (empty($user)) {
            throw new UserNotFoundException();
        } else {
            return $user;
        }
    }
}
