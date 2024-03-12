<?php

namespace App\GraphQL\Resolvers\MutationResolvers;

use App\Exceptions\EmailNotUniqueException;
use App\Interfaces\GraphQLResolverInterface;
use App\Models\Entities\ResetTokenEntity;
use App\Models\Entities\UserEntity;
use App\Services\ResetTokensService;
use App\Services\UsersService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Security\Passwords;
use Nette\Utils\Random;

final class RegisterResolver implements GraphQLResolverInterface
{
    public const RANDOM_PASSWORD_LENGTH = 15;

    public function __construct(
        private readonly UsersService $usersService,
        private readonly ResetTokensService $resetTokensService,
        private readonly Passwords $passwords
    ) {
    }

    /**
     * @param array{ "data": array{ "email": string } } $parameters
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     * @throws EmailNotUniqueException
     */
    public function resolve(array $parameters, array $options = []): array
    {
        $this->resetTokensService->clearExpiredTokens();
        try {
            // create new user
            $data = $parameters["data"];
            $user = UserEntity::createFromGraphQLInput($data);

            // generate random password
            $randomPassword = Random::generate(self::RANDOM_PASSWORD_LENGTH);
            $user->setPassword($this->passwords, $randomPassword);
            $this->usersService->save($user);

            // create reset token
            $resetToken = (new ResetTokenEntity())
                ->setUser($user)
                ->setRegistration(true);
            $resetToken->generatePassword($this->passwords);
            $this->resetTokensService->save($resetToken);

            // send email
            $this->sendEmail($resetToken);

            // format result
            return $user->toGraphQLOutput();
        } catch (UniqueConstraintViolationException) {
            throw new EmailNotUniqueException();
        }
    }

    private function sendEmail(ResetTokenEntity $resetTokenEntity): void
    {
        $email = $resetTokenEntity->getUser()->getEmail();
        $resetToken = (string) $resetTokenEntity;
        // todo send email processing
    }
}
