<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ← add this
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // ← add HasFactory

    protected $fillable = ['name', 'email', 'password', 'phone', 'role'];
    protected $hidden   = ['password', 'remember_token'];
}
