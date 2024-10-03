<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Lance extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($lance) {
            $leilao = Leilao::find($lance->leilao_id);
            if (!$leilao) {
                throw new ModelNotFoundException('Leilão não encontrado.');
            }
            if ($lance->valor < $leilao->valor_inicial) {
                throw new \InvalidArgumentException('O valor do lance não pode ser menor que o valor inicial do leilão.');
            }
            $maiorLance = Lance::where('leilao_id', $lance->leilao_id)->max('valor');
            if ($lance->valor <= $maiorLance) {
                throw new \InvalidArgumentException('O valor do lance deve ser maior que o maior lance atual.');
            }
            // Verificar se é uma atualização
            if (!$lance->exists) {
                $lanceAnterior = Lance::where('usuario_id', $lance->usuario_id)->orderBy('created_at', 'desc')->first();
                if ($lanceAnterior && $lanceAnterior->leilao_id == $lance->leilao_id) {
                    throw new \InvalidArgumentException('O usuário não pode dar dois lances seguidos.');
                }
            }
        });
    }

    protected $fillable = ['leilao_id', 'valor', 'usuario_id'];

    public function leilao()
    {
        return $this->belongsTo(Leilao::class); // Relacionamento "Um para um" com Leilão
    }

    public function participante()
    {
        return $this->belongsTo(User::class, 'usuario_id'); // Relacionamento "Um para um" com User
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id'); // Relacionamento "Um para um" com User
    }

}
