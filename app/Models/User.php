<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        "account_type",
        "balance",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function totalWithdrawnThisMonth()
    {
        $now = now();

        $firstDayOfMonth = $now->startOfMonth();

        $lastDayOfMonth = $now->endOfMonth();

        $totalWithdrawn = $this->transactions()
            ->where('type', 'withdrawal')
            ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
            ->sum('amount');

        return $totalWithdrawn;
    }
    public function totalWithdrawn()
    {
        $totalWithdrawn = $this->transactions()
            ->where('type', 'withdrawal')
            ->sum('amount');
        return $totalWithdrawn;
    }

}
