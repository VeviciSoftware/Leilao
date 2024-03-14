<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use User;

class Leilao extends Model
{
    use HasFactory;

    protected $table = 'leilao';

    protected $fillable = ['nome', 'descricao', 'valor_inicial', 'data_inicio', 'data_termino', 'status'];

    public function participante()
    {
        return $this->belongsTo(User::class);
    }

    public function lances()
    {
        return $this->hasMany(Lance::class);
    }

    public function getValorAtualAttribute()
    {
        if ($this->lances->isEmpty()) {
            return $this->valor_inicial;
        }

        return $this->lances->max('valor');
    }

    public function isAberto()
    {
        return $this->status === 'ABERTO';
    }

    public function isFinalizado()
    {
        return $this->status === 'FINALIZADO';
    }

    public function isExpirado()
    {
        return $this->status === 'EXPIRADO';
    }

    public function isInativo()
    {
        return $this->status === 'INATIVO';
    }

    public function scopeAtivos($query)
    {
        return $query->where('status', 'ABERTO');
    }

    public function scopeFinalizados($query)
    {
        return $query->where('status', 'FINALIZADO');
    }

    public function scopeExpirados($query)
    {
        return $query->where('status', 'EXPIRADO');
    }

    public function scopeInaivos($query)
    {
        return $query->where('status', 'INATIVO');
    }

}
