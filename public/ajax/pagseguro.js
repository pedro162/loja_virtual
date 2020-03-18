$(document).ready(function(){
    $('.pagamento').click(function(){
        
        $.ajax({
            url: '/pagar/finesh',
            type: 'POST',
            dataType: 'json',
            success: function(retorno){
                PagSeguroDirectPayment.setSessionId(retorno.id);
            },
            complete: function(){
                listaMeiosPagamento();
            }
        });
    });


    function listaMeiosPagamento(){
        PagSeguroDirectPayment.getPaymentMethods({
            amount: 500.00,
            success: function(response) {
                $.each(response.paymentMethods.CREDIT_CARD.options, function(i, obj){
                    $('.cartao_credito').append("<div><img src=https://stc.pagseguro.uol.com.br/"+obj.images.SMALL.path+"/>"+obj.name+"</div>");
                });

                $('.boleto').append("<div><img src=https://stc.pagseguro.uol.com.br/"+response.paymentMethods.BOLETO.options.BOLETO.images.SMALL.path+"/>"+response.paymentMethods.BOLETO.name+"</div>");

                $.each(response.paymentMethods.ONLINE_DEBIT.options, function(i, obj){
                    $('.debito_online').append("<div><img src=https://stc.pagseguro.uol.com.br/"+obj.images.SMALL.path+"/>"+obj.name+"</div>");
                });
            },
            complete: function(response) {
                
            }
        });

    }


    $('#NumeroCartao').on('keyup', function(){
        let numeroCartao = $(this).val();
        let qtdCaractere = numeroCartao.length;
        
            if(qtdCaractere == 6){
                PagSeguroDirectPayment.getBrand({
                cardBin: numeroCartao,
                success: function(response) {
                  let bandeira = response.brand.name;
                  $('.bandeira_cartao').html("<img src=https://stc.pagseguro.uol.com.br/public/img/payment-methods-flags/42x20/"+bandeira+".png/>");
                },
                error: function (response){
                    alert('Cartão não reconhecido!');
                    $('.bandeira_cartao').empty();
                }
            });
        }

        
    })
    
   
})