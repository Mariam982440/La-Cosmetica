<?php
namespace App\DAOs;

use App\Models\User;
use App\DTOs\RegisterDTO;
use Illuminate\Support\Facades\Hash;

class UserDAO
{
    public function createUser(RegisterDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'role' => $dto->role,
        ]);
    }
}