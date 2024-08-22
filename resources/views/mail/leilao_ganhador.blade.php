<!-- resources/views/emails/leilao_ganhador.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Parabéns pelo arremate!</title>
</head>
<body>
    <h1>Olá {{ $ganhador->name }},</h1>
    <p>Você foi o ganhador do leilão: {{ $leilao->nome }}</p>
    <p>Valor do lance vencedor: R$ {{ number_format($leilao->lances()->orderBy('valor', 'desc')->first()->valor, 2, ',', '.') }}</p>
    <p>Obrigado por participar do nosso leilão!</p>
</body>
</html>