<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lance extends Model
{
    use HasFactory;

    protected $fillable = ['leilao_id', 'valor', 'usuario_id'];

    public function leilao()
    {
        return $this->belongsTo(Leilao::class); // Relacionamento "Um para um" com Leilão
    }

    public function usuario()
    {
        return $this->belongsTo(User::class); // Relacionamento "Um para muitos" com Usuário
    }

}
