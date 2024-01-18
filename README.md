 //relacionado a pagamentos
//Essas são as duas rotas, uma faz o pagamento e a outra é de retorno
 
    Route::get('enquete/pagamento/pix', [PagamentoController::class, 'pagamentoPix'])->name('pagamentoPix');
    Route::post('enquete/retorno/pix', [PagamentoController::class, 'retornoPix'])->name('retornoPix');
