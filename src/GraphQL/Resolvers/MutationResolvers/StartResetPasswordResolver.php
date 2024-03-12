<?php

namespace App\GraphQL\Resolvers\MutationResolvers;

use App\Exceptions\UserNotFoundException;
use App\Interfaces\GraphQLResolverInterface;
use App\Models\Entities\ResetTokenEntity;
use App\Services\ResetTokensService;
use App\Services\UsersService;
use Nette\Security\Passwords;

final class StartResetPasswordResolver implements GraphQLResolverInterface
{
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
     * @throws UserNotFoundException
     */
    public function resolve(array $parameters, array $options = []): array
    {
        $this->resetTokensService->clearExpiredTokens();
        // load user
        $user = $this->usersService->findByEmail($parameters["data"]["email"]);

        // create reset token
        $resetToken = (new ResetTokenEntity())
            ->setUser($user);
        $resetToken->generatePassword($this->passwords);
        $this->resetTokensService->save($resetToken);

        // send email
        $this->sendEmail($resetToken);

        // format result
        return $user->toGraphQLOutput();
    }

    private function sendEmail(ResetTokenEntity $resetTokenEntity): void
    {
        $email = $resetTokenEntity->getUser()->getEmail();
        $resetToken = (string) $resetTokenEntity;
        // todo send email processing
    }
}
