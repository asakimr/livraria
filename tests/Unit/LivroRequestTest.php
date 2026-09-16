<?php

use App\Http\Requests\LivroRequest;

it('normaliza somente a cópia de validação da moeda brasileira', function (string $entrada, ?string $esperado) {
    $request = LivroRequest::create('/livro', 'POST', ['Valor' => $entrada]);
    expect($request->validationData()['Valor'])->toBe($esperado);
    expect($request->input('Valor'))->toBe($entrada);
})->with([['59,90', '59.90'], ['1.234,56', '1234.56'], ['0,00', '0.00'], ['999.999,99', '999999.99'], ['59.90', null], ['inválido', null], ['12.34,56', null]]);

it('mantém o contrato decimal da API sem converter ponto em milhar', function () {
    $request = LivroRequest::create('/api/livros', 'POST', ['Valor' => '59.90']);
    expect($request->validationData()['Valor'])->toBe('59.90');
});
