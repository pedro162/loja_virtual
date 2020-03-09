$(document).ready(function(){
    $('.pagamento').click(function(){
        
        $.ajax({
            url: '/pagar/finesh',
            type: 'POST',
            dataType: 'json',
            success: function(retorno){
                console.log(retorno)
                
                //PagSeguroDirectPayment.setSessionId(retorno.id);
            }
        });
    })
   
})