@component('mail::message')
# Parabéns pelo arremate!

Olá {{ $ganhador->name }},

Você foi o ganhador do leilão: **{{ $leilao->nome }}**

Valor do lance vencedor: **R$ {{ number_format($leilao->lances()->orderBy('valor', 'desc')->first()->valor, 2, ',', '.') }}**

Obrigado por participar do nosso leilão!

@endcomponent