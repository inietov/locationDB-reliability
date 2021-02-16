<?php
/*
 * This file has been automatically generated by TDBM.
 * You can edit this file as it will not be overwritten.
 */

declare(strict_types=1);

namespace App\Domain\Dao;

use App\Domain\Dao\Generated\BaseUserDao;
use App\Domain\Enum\Filter\SortOrder;
use App\Domain\Enum\Filter\UsersSortBy;
use App\Domain\Enum\Role;
use App\Domain\Model\Proxy\PasswordProxy;
use App\Domain\Model\User;
use App\Domain\Throwable\InvalidModel;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TheCodingMachine\GraphQLite\Annotations\Factory;
use TheCodingMachine\TDBM\ResultIterator;
use TheCodingMachine\TDBM\TDBMService;

/**
 * The UserDao class will maintain the persistence of User class into the users table.
 */
class UserDao extends BaseUserDao
{
    private ValidatorInterface $validator;

    public function __construct(TDBMService $tdbmService, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        parent::__construct($tdbmService);
    }

    /**
     * @Factory
     */
    public function getById(string $id, bool $lazyLoading = false): User
    {
        return parent::getById($id, $lazyLoading);
    }

    /**
     * @throws InvalidModel
     */
    public function validate(User $user): void
    {
        $violations = $this->validator->validate($user);
        InvalidModel::throwException($violations);
    }

    /**
     * @throws InvalidModel
     */
    public function save(User $user): void
    {
        $this->validate($user);
        parent::save($user);
    }

    /**
     * @throws InvalidModel
     */
    public function updatePassword(User $user, PasswordProxy $passwordProxy): void
    {
        $violations = $this->validator->validate($passwordProxy);
        InvalidModel::throwException($violations);

        $user->setPassword($passwordProxy->getNewPassword());
        $this->save($user);
    }

    /**
     * @return User[]|ResultIterator
     */
    public function search(
        ?string $search = null,
        ?Role $role = null,
        ?UsersSortBy $sortBy = null,
        ?SortOrder $sortOrder = null
    ): ResultIterator {
        $sortBy    = $sortBy ?: UsersSortBy::FIRST_NAME();
        $sortOrder = $sortOrder ?: SortOrder::ASC();

        return $this->find(
            [
                'first_name LIKE :search OR last_name LIKE :search OR email LIKE :search',
                'role = :role',
            ],
            [
                'search' => '%' . $search . '%',
                'role' => $role,
            ],
            $sortBy . ' ' . $sortOrder
        );
    }
}
