<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELASI ---

    // User sebagai Instruktur (pemilik kursus)
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // User sebagai Siswa (tracking progress)
    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    // Riwayat Quiz User
    public function quizResults()
    {
        return $this->hasMany(QuizResult::class);
    }

    // Sertifikat yang dimiliki User
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
