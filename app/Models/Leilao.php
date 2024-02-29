<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leilao extends Model
{
    use HasFactory;

    protected $table = 'leilao';

    protected $fillable = ['nome', 'descricao'];
}
