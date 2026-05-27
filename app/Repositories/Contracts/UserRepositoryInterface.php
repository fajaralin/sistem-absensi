<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Temukan user berdasarkan email.
     */
    public function findByEmail(string $email);
}
