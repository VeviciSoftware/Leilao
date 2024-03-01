<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use User;

class Leilao extends Model
{
    use HasFactory;

    protected $table = 'leilao';

    protected $fillable = ['nome', 'descricao', 'valor_inicial', 'data_inicio', 'data_fim', 'status'];

    protected $casts = [
        'status' => 'enum',
    ];

    public function lances()
    {
        return $this->hasMany(Lance::class);
    }

    public function participantes()
    {
        return $this->belongsToMany(User::class);
    }

}
