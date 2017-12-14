<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password'
    ];

    public function fullName()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function setPassword($password)
    {
        return $this->update([
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }
}
