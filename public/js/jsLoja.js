// Disable form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Get the forms we want to add validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();


$(document).ready(function(){

  /*-------- HABILITA OS LINKS DO SISTEMA -----------*/
  ConfigController.ativarLinks();


  $('.btn-success, .button-modal').css('background-color', '#8B008B');

  $('.cnpj').hide();
  $('#cadastro #id_cpf').on('click', function(){
    $('.cnpj').hide();
    $('.cpf').show();
  })

  $('#cadastro #id_cnpj').on('click', function(){
    $('.cpf').hide();
    $('.cnpj').show();
  })

//-------------------------- FAX O AJAX E GERA O LOGIN / LOGOUT------------------------------

$('body').delegate('#entrar', 'click', function(event){
  event.preventDefault();
  let url = $(this).attr('href');

  $.ajax({
    type: 'GET',
    url: url,
    dataType:'HTML',
    success: function(retorno){

      getModal('Login',retorno);
    }
  })


})


//------------------------ EFEITOS DA ESTRELA DE NIVEL DO PRODUTO ------------------
$('body').delegate('.star', 'mouseover', function(){
  let indice = $(this).index();
  $('.star').removeClass('full');

  for (let i =0; !(i == indice + 1); i++) {
    $('.star:eq('+i+')').addClass('full')
  }
})

$('body').delegate('.star', 'mouseout', function(){//remove a cor da estrela quando sai de cima
  
  $('.star').removeClass('full');

})

$('body').delegate('.star', 'click', function(){
  let idProd = $('#imgP img.img-produto').attr('id');
  let pontos = $(this).index() +1;
  
  $.ajax({
    type:'GET',
    url:'/produto/voto?cd='+idProd+'&&pt='+pontos,
    dataType:'json',
    success:function(retorno){

      let  percentBar = pontos * 20;
      $('.bg').animate({width:percentBar+'%'}, 500)
      
      $('#barra .bg').css('width',+percentBar);
      $('div#response').html(retorno[1])
    }
  })

});

//----------------- SALVA O COMENTÁRIO DO CLIENTE SOBRE O PRODUTO ------------
$('body').delegate('#formComentario', 'submit', function(event){
  event.preventDefault();

  if($('#textComentario').val().trim() == ''){
    alert("Comentario inválido\n");
    return false;
  }


  let formulario = $(this);

  let url = $(this).attr('action');

  let form = new FormData(formulario [0]);

  $.ajax({
    type:'POST',
    url:url,
    data:form,
    processData:false,
    contentType: false,
    dataType:'json',
    success:function(retorno){
      if(retorno[0] > 0){
        $.ajax({
          type:'POST',
          url:'/produto/load/comentarios',
          data:{'produto':retorno[0]},
          dataType:'HTML',
          success:function(retorno){
            $('#textComentario').val('')
            $('body #comentarios').html(retorno);
          }
        })


      }else{
        alert("Não foi possivel salvar seu comentario\n\r "+retorno[2]);
        return false;
      }
    }

  });

});




//---------------- Esconde exibe botoes de ver mais na view home ---------------------------

$('.hidBtn').mouseenter(function(){
      $(this).siblings('a').show();
    })
     

//----------------- LOAD AJAX DA VIEW DE DETALHES DO PRODUTO ---------------------

$('#containerLoja, body').delegate('.link-produto', 'click', function(e){
  e.preventDefault();
  $('#closeModal').trigger('click');//fecha o modal se estiver aberto
  let url = $(this).attr('href');
  
  $.ajax({
    type: 'GET',
    url:url,
    dataType:'HTML',
    success:function(retorno){
      
      $('#bodyLojaVirtual').css('background', '#fff');//muda a cor de fundo da página
      $('#containerLoja').html(retorno);
      $(window).scrollTop('0')//posiciona o scroll no top

      //gera o efeito zoo da imagem.
      $('.img-produto').elevateZoom({
            //responsive:true,
            //zoomType: 'lens',
            cursor:'crosshair',
            scrollZoom:true,
            gallery:'gali',
            galleryActiveClass: 'active',
            //zoomWindowPosition: 0,
            //lensShape:'round',
            //slensSize:250,
            zoomWindowPosition: 0,
            //zoomWindowOffsetX: 10,
            borderSize: 1,
            zoomWindowHeight: 500,
            zoomWindowWidth: 600,
            tint:true,
            tintColour:'#f90',
            tintOpacity:0.5,
            respond: [
                  {
                      range: '600-799',
                      tintColour: '#F00',
                      zoomWindowHeight: 100,
                      zoomWindowWidth: 100
                  },
                  {
                      range: '800-1199',
                      tintColour: '#00F',
                      zoomWindowHeight: 200,
                      zoomWindowWidth: 200
                  },
                  {
                      range: '100-599',
                      enabled: false,
                      showLens: false
                  }
              ]
        })

      $(".img-produto-opt").bind("click", function(e) {  
        var ez =   $('.img-produto-opt').data('elevateZoom'); 
        $.fancybox(ez.getGalleryList());
          
        return false;
      });
    }
  });
})  


//---------------------- Filtro lateral busa produtos no banco de acordo com o filtro------------------------------//

  $('#filtroLateral').on('click', function(){
    let departamento = new Array();

    let preco = new Array();;

    let condicoes = new Array();;

    $("input[name='produtos[Categoria][]']:checked").each(function(){
       departamento.push($(this).val());
    })

    $("input[name='produtos[Preco][]']:checked").each(function(){
        preco.push($(this).val());
    })

    $("input[name='produtos[Condicoes][]']:checked").each(function(){
        condicoes.push($(this).val());
    })

    let Filtro = new Array();

    if(departamento.length > 0){
      departamento.unshift('Categoria');
      Filtro.push(departamento);
    }

    if(preco.length > 0){
       preco.unshift('Preco');
       Filtro.push(preco);
    }

    if(condicoes.length > 0){
       condicoes.unshift('Condicoes');
       Filtro.push(condicoes);
    }


      

    if(Filtro.length > 0){
        
      let xhr = $.ajax({
        type: "POST",
        url: '/produto/filtro',
        data:{'produtos': Filtro},
        success: function(retorno){
          
        parse = $.parseJSON(retorno);


        if(parse[0] == 'msg')
        {
           getModal($('<div/>').addClass('alert alert-warning').html($('<strong/>').html('Ops !')),
            $('<div/>').attr('align', 'center').addClass('h4 alert-warning').html(parse[1]+'<br/>Tente outro filtro!')
            , '');

          return false;
        }

        $('#closeModal').trigger('click');

        //$('#itens').html($('<div/>').addClass('row').html(divProduto));
        $('#itens').html($('<div/>').addClass('row').attr('id', 'resultFiltro'));

        for (var i = parse.length - 1; i >= 0; i--) {

          //console.log('produto '+parse[i].nomeProduto+' preco '+parse[i].preco);
          //let divProduto = $('<div/>').addClass('col-xs-6 col-md-2 card-produto');
          let divProduto = $('<div/>').addClass('col-xs-6 col-md-2 card-produto');

          let cardItem = $('<div/>').addClass('card produto-item')
          let a = $('<a/>').addClass('produto-item link-produto').attr('href', `/pedido/produto/detalhes?cd=${parse[i].idProduto}`);
          let divImg = $('<div/>').attr('align', 'center').css('padding-top', '10px').append($('<img/>').css('width', '100px').css('height', '100px').attr('src', '../files/imagens/xbox_controller.jpeg'))

          let cardBody = $('<div/>').addClass('card-body').append($('<div/>').
            append($('<h3/>').html(`${parse[i].nomeProduto}`)).
            append($('<p/>').html(`${parse[i].textoPromorcional}`)).
            append($('<p/>').append($('<strong/>').html(`<sup><small>R$</small></sup>0<sup><small></small></sup>`)))
            );

          a.append(divImg)
          a.append(cardBody);

          cardItem.append(a);

          let cardFooter = $('<div/>').addClass('card-footer');

          let divButton = $('<div/>').addClass('child-card-footer');

          let button = $('<button/>').addClass('btn btn-primary  button-modal').
          attr('type', 'button').attr('data-toggle','modal').attr('data-target', '#myModal').
          text('Mais detalhes');

          divButton.append(button);

          cardFooter.append(divButton);
          cardFooter.append(`<ul class="curt-lista">
                          <li class="">
                            <button class="btn btn-xs btn-default" style='font-size:20px;'>&#128077;</button>
                            <span>1</span>
                          </li>
                          <li class="">
                            <button class="btn btn-xs btn-default" style='font-size:20px;'>&#128078;</button>
                            <span>1</span>
                          </li>
                          </ul>`);

          cardItem.append(cardFooter);
          divProduto.append(cardItem);



          $('#resultFiltro').append(divProduto);
         // $('#resultFiltro').append(pagination());

             
            }
            //$('#itens').html($('<div/>').addClass('row').html(divProduto));

          },

          beforeSend: function(){
             getModal('Aguarde...', loadImg('load.gif'), '');
          }
        });

        //cancela a requisicao se clicado
        $('#closeModal, #myModal').on('click', function(){
             xhr.abort();
          });
      }else{
         getModal($('<div/>').addClass('alert alert-warning').html($('<strong/>').html('Ops !')),
          $('<div/>').attr('align', 'center').addClass('h4 alert-warning').html('Escolha um filtro de pesquisa<br/> para continuar!')
          , '');
      }

  });

// Faz o sistema de paginação------------------------------------------------------------------------
  function pagination(pagina, totPaginas) {
    //inicia a cria cao da lista de navegacao
    let preview = (pagina - 1);

    let blockPrev = '';
    if(Number(pagina) == 1){
        blockPrev = 'disabled';
    }

    //cria a ul
    let lista = $('<ul/>').css('color', '#8B008B').addClass('pagination justify-content-end').append($('<li/>').addClass('page-item').append($('<a/>').on('click',function(event){
      //desativa o evento click do link
        event.preventDefault()
      }
    ).attr('herf', '/produto/all?pagina='+preview).attr('id', preview).addClass('page-link '+blockPrev).html('peview')));

    //adiciona um id a lista
    lista.attr('id', 'paginacao')
    //cria os li e adiciona a ul
    for (let i = 0; !(i == totPaginas); i++) {
      let estilo = '';

      if(pagina == (i + 1)){
        estilo = 'active';
      }

      let li = $('<li/>').addClass('page-item '+estilo).append($('<a/>').on('click',function(event){//desativa o evento click do link
            event.preventDefault()
          }
        ).attr('herf', '/produto/all?pagina='+(i+1)).attr('id', (i+1)).addClass('page-link').html((i+1)))
      lista.append(li);
    }

    let blockNext = '';
    if(pagina == totPaginas){
        blockNext = 'disabled';
    }

    let next = (Number(pagina) + 1);
    
    lista.append($('<li/>').addClass('page-item').append($('<a/>').on('click',function(event){ //desativa o evento click do link
       event.preventDefault()
     }
     ).attr('herf', '/produto/visualizar?id='+next).addClass('page-link '+blockNext).attr('id', next).html('next')));
    
    let divLista =$('<div/>').addClass('col-md-12').append($('<nav/>').append(lista));

    return divLista;

  }



//chama o modal e passa alguns parametros

  function getModal(titulo='Aguarde', body='', footer='') {
    $('.modal-header h4').html(titulo)
    $('.modal-body').html(body);
    $('.modal-footer').html(footer);
  }

//--------------------------------------------- Modal de produtos -----------------------------------------
  $('.child-card-footer, div#itens').delegate('.button-modal','click',  function(){
    //cria e adiciona elementos ao carrocel do modal

    let idProduto = $(this).parents('.card-produto').find('a').attr('href')
    idProduto = idProduto.substring(idProduto.indexOf('=')+1);

    let carouselModal = $('<div/>').attr('id', 'slid').addClass('carousel').addClass('slide').
    attr('data-ride', 'carousel').
    prepend(
      $('<div/>').addClass('carousel-inner').
      prepend($('<div/>').attr('align', 'center').addClass('carousel-item').prepend($('<img/>')
        .attr('src','../files/imagens/xbox_controller.jpeg').css('width','100px').css('height','100px'))).

      prepend($('<div/>').attr('align', 'center').addClass('carousel-item').prepend($('<img/>')
        .attr('src','../files/imagens/images.png').css('width','100px').css('height','100px'))).

      prepend($('<div/>').attr('align', 'center').addClass('carousel-item active').prepend($('<img/>')
        .attr('src','../files/imagens/console.jpeg').css('width','100px').css('height','100px')))
          ).
    append($('<a/>').addClass('carousel-control-prev').attr('href', '#slid').attr('data-slide', 'prev')
      .prepend($('<span/>').addClass('carousel-control-prev-icon').css('background-color', '#8B008B'))).
    append($('<a/>').addClass('carousel-control-next').attr('href', '#slid').attr('data-slide', 'next')
      .prepend($('<span/>').addClass('carousel-control-next-icon').css('background-color', '#8B008B')));

    //exibe texto detalhes no head do modal
    $('.modal-header h4').html("Detalhes").css('text-align', 'center');
      
    let img = $(this).parents('.card').find('img').attr('src');

    //faz a requizição e exibe detalhes do produto escolhido
    let xhr = $.ajax({
            url: '/produto/more?id='+idProduto,
            type: 'GET',
            dataType: 'json',
            success: function(retorno){
              let list = '<ul style="list-style:none;">';
                for (var i = 0; !(i == retorno.length); i++) {
                  list += '<li>'+retorno[i]+'</li>';
                }
                list += '</ul>';

                let container = $('<div/>').addClass('container-fluid').append(
                  $('<div/>').addClass('row mb-5').prepend($('<div/>').addClass('col').html(list)).append($('<div/>').addClass('col').append($('<img/>').attr('src', img)))
                  ).append(
                    $('<div/>').addClass('row').prepend($('<div/>').addClass('col').append('<br/><strong>Relacionados:</strong><br/>').append(carouselModal))
                  );
                
                $('.modal-body').html(container);

                let buttonAdd = '<button type="button" class="btn carrinho btn-primary  button-modal">Adicionar ao carrinho</button>';
                let buttonMoreDetals = '<a href=/pedido/produto/detalhes?cd='+idProduto+' class="btn btn-primary link-produto button-modal">Mais detalhes</a>';

                let botoesOpcoes = $('<div/>').addClass('row')
                .append($('<div/>').addClass('col').html(buttonAdd))
                .append($('<div/>').addClass('col').html(buttonMoreDetals))
                $('.modal-footer').html(botoesOpcoes).find('.btn-success, .button-modal').css('background-color', '#8B008B');
                $('.modal-header h4').html($('<strong/>').html("Detalhes do produto"))
            },
            beforeSend: function(){
              $('.modal-header h4').html($('<strong/>').html("Aguarde..."))
              $('.modal-body').html(loadImg('load.gif'));
              $('.modal-footer .btn').hide();
              
            }
            
        });


        //cancela a requisicao se clicad
        $('#closeModal, #myModal').on('click', function(){
           xhr.abort();
        })


  });

  //----------------------- CALCULAR FRETE DO PRODUTO ---------------------------

  $('body').delegate('#formFretQtd', 'submit', function(e){
    e.preventDefault();
    let form = $(this);
    
    let prod = $(this).find('input[name=prod]').val().trim();
    let cep = $(this).find('input[name=cep]').val().trim();

    let errors = new Array();

    if(prod.length == 0){
      errors.push('Produto inválido');
    }

    if(cep.length == 0){
      errors.push('Por favor, informe um cep');
    }

    if(errors.length > 0){
      let msg = '';
      for(let i=0; !(i == errors.length); i++){
        msg+=errors[i]+"\n";
      }
      alert('Atençao: '+msg)
    }else{

      let url = '/pedido/produto/frete?cep='+cep+"&&prod="+prod;

      let xhr = $.ajax({
                type:'GET',
                url: url,
                dataType:'HTML',
                success: function(retorno){
                  form.find('#response').html(retorno);//'Total: R$ '+retorno.valor+'<br/>Dias: '+retorno.entrega
                },
                beforeSend: function(){
                  form.find('#response').html('Aguarde...');
                    
                }
                
            });

      }
  })
  
 //--------------------CAPITURA OS ITENS ADICIONADOS NA VIEW DE DETALHES E MANDA PARA O CARRINHO 
  $('body').delegate('#formFretQtd button#btnAddCarr', 'click', function(){
    let cd = $('#formFretQtd').find('input[name=prod]').val();
    let qtd = $('#formFretQtd').find('select[name=qtd]').val();


    if(cd.length == 0 || qtd.length == 0){
      return false;
    }

    cd = Number(cd);
    qtd = Number(qtd);

    PedidoController.addToCar(cd, qtd);

    
    
  })

   $('body').delegate('#formCarr .controller', 'click', function(){
    
    let action = $(this).text().trim();
    
    let cd = $(this).parent().find('span.form-control').attr('name').split('-')[1];

    let valQtd = $(this).parent().find('span.form-control').text().trim();
    valQtd = Number(valQtd);


    if(cd.length == 0){
      alert('Erro na solicitação');
      return false;
    }
    
    cd = Number(cd);

    if(action == '-'){

      if(valQtd <= 1){
        alert('Quantidade solicitada inválida');
        return false;
      }
      //remove o item ao carrinho
      PedidoController.addToCar(cd, 1, true)
    }else{
      //adicona o item ao carrinho
      PedidoController.addToCar(cd, 1)
    }
    
    $('body #carrinhoLink').trigger('click');
    
  })

   //------------- RETIRA O ITEM DO CARRINHO -------------------
  $('body').delegate('#formCarr .controller-delete', 'click', function(){
    
    let cd = $(this).parent().find('span.form-control').attr('name').split('-')[1];
    
    //adicona o item ao carrinho
    PedidoController.removeToCar(cd)
    
    $('body #carrinhoLink').trigger('click');
    
  })

 
  // --------------EXIBE A VIEW DE ITENS NO CARRINHO ----------
  $('body').delegate('#carrinhoLink', 'click', function(event){
    event.preventDefault();
    let url = $(this).attr('href');
    
    //exibe o carrinho
    PedidoController.showCarrinho(url);
  
    
  })

 //-------------------------- CHAMA A VIEW DE VER MAIS PRODUTOS RELACIONADOS ------------------------

 $('body').delegate('.ver-mais', 'click', function(e){
  e.preventDefault();
  let url = $(this).attr('href');

  $.ajax({
    url: url,
    type: 'GET',
    dataType: 'HTML',
    success: function(retorno){
      $('#containerLoja').html(retorno);
      $('#bodyLojaVirtual').css('background', '#fff');//muda a cor de fundo da página.
      $(window).scrollTop('0')//posiciona o scroll no top
    }
  })

 })

  //-------------------------- CHAMA A VIEW DE PAGAMENTO ------------------------
  $('body').delegate('#formCarr #finalizarPedido', 'click', function(e){
  e.preventDefault();
  let url = $(this).attr('href');

  $.ajax({
    url: url,
    type: 'GET',
    dataType: 'HTML',
    success: function(retorno){
      $('#containerLoja').html(retorno);
      $('#bodyLojaVirtual').css('background', '#fff');//muda a cor de fundo da página.
      $(window).scrollTop('0')//posiciona o scroll no top
    }
  })

 })


  //-------------------------- REGISTRA O PAGAMENTO ------------------------
  $('body').delegate('#container-pgto #pgSeguro', 'click', function(e){
  e.preventDefault();

  let element = $(this);
  let url = $(this).attr('href');
  let entrega = $('select#entrega-pedido').val();

  //exibe a view de finazar pedido
  PedidoController.finalizarPedido(element, url, entrega)
  
 })


  //-------------- FAX BUSCA O PEDIDO PARA VISUALIZAR NO MODAL -----------
  $('body').delegate('#responseUser #tbl-itens-cliente tbody tr', 'click', function(e){
    
    let cd = $(this).find('td:eq(0)').text();

    let pedido = new PedidoController();
    pedido.showPedido(cd)

  })



  //---------------------------------- INICIANDO SESSAO DE PAGAMENTO PGSEGURO -----------------
  //configura o amout das funcoes
  let Amount = 500.00;
  function iniciaSessaoPgSeguro()
  {
    $.ajax({
      url: '/pagar/seguro',
      type:'POST',
      dataType: 'json',
      success: function(retorno){
        PagSeguroDirectPayment.setSessionId(retorno.id);
      },
      complete: function(){
        listaMeiosPagamentoPgSeguro();
      }
    })
  }

  //---------------------------------- LISTA OS MEIOS DE PAGAMENTO DO PGSEGURO -----------------

  function listaMeiosPagamentoPgSeguro()
  {
    PagSeguroDirectPayment.getPaymentMethods({
      amount: Amount,
      success: function(response) {
          $.each(response.paymentMethods.CREDIT_CARD.options, function(i, obj){
            $('.CartaoCredito').append("<div><img src=https://stc.pagseguro.uol.com.br/"+obj.images.SMALL.path+" />"+obj.CREDIT_CARD.name+"</div>")
          })

          $('.Boleto').append("<div><img src=https://stc.pagseguro.uol.com.br/"+response.paymentMethods.BOLETO.options.BOLETO.images.SMALL.path+" />"+response.paymentMethods.BOLETO.name+"</div>");

          $.each(response.paymentMethods.ONLINE_DEBIT.options, function(i, obj){
            $('.CartaoCredito').append("<div><img src=https://stc.pagseguro.uol.com.br/"+obj.images.SMALL.path+" />"+obj.CREDIT_CARD.name+"</div>")
          })
          
      },
      complete: function(response) {
          getTokenCard();
      }
    });

  }

//----------------- IDENTIFICA O CARTAO DE PAGAMENTO PGSEGURO -----------

$('body').delegate('#NumeroCartao', 'keyup', function(){
  let NumeroCartao = $(this).val();
  let QtdCaracteres = NumeroCartao.length;

  if(QtdCaracteres == 6){
    PagSeguroDirectPayment.getBrand({
      cardBin: NumeroCartao,
      success: function(response) {
        let BandeiraImg = response.brand.name;
        $('#BandeiraCartao').html("<img src='https://public/img/payment-methods-flags/42x20/"+BandeiraImg+".png' />")
        
        getParcelas(BandeiraImg);

      },
      error:function(response){
        alert('Cartão não reconhecido');
        $('#BandeiraCartao').empty();
      }
    });
  }

})

// ------------------- AEXIBE A QUANTIDADE DE PARCELAS DISPONIVEIS -----------

function getParcelas(Bandeira){
  PagSeguroDirectPayment.getInstallments({
        amount:Amount,
        maxInstallmentNoInterest: 2,
        brand: Bandeira,
        success: function(response){
           $.each(response.installments,function(i, obj){
              $.each(obj,function(i2, obj2){

                let NumberParcrelas = obj2.installmentAmount.toFixed(2);

                $('#QtdParcelas').show();

                $('#QtdParcelas').append("<option value='"+obj2.quantity+"' label='"+NumberParcrelas+"'>"+obj2.quantity+" parcelas de "+obj2.installmentAmount+"</option>")

              })
           })
       },
        error: function(response) {
            // callback para chamadas que falharam.
       },
        complete: function(response){
            // Callback para todas chamadas.
       }
  });

}

//--------------^^^^^^^^^ pegar o valor da parcdla ^^^^^^^^^ -----------
$('body').delegate('#ValorParcelas', 'change', function(){
  let ValueSelected = document.getElementById('QtdParcelas');
  $('#ValorParcelas').val(ValueSelected.options[ValueSelected.selectedIndex].label);
})

//--------------------------- OBTEM O TOKEN DO CARTAO DE CREDITO ----------------
  function getTokenCard(){

    PagSeguroDirectPayment.createCardToken({
       cardNumber: '4111111111111111', // Número do cartão de crédito
       brand: 'visa', // Bandeira do cartão
       cvv: '013', // CVV do cartão
       expirationMonth: '12', // Mês da expiração do cartão
       expirationYear: '2026', // Ano da expiração do cartão, é necessário os 4 dígitos.
       success: function(response) {
           $('#TokenCard').val(response.card.token);
       },
       error: function(response) {
                // Callback para chamadas que falharam.
       },
       complete: function(response) {
            // Callback para todas chamadas.
       }
    });

  }

  $('body').delegate('form#form-pg-seguro', 'submit', function(ev){
    ev.preventDefault();

        PagSeguroDirectPayment.onSenderHashReady(function(response){
        if(response.status == 'error') {
            console.log(response.message);
            return false;
        }
        $('#HashCard').val(response.senderHash); //Hash estará disponível nesta variável.
    });

  })

  //------------------------------- PAINEL CLIENTE --------------------------------------------

  $('body').delegate('#menu-principal #penelUser', 'click', function(e){
    e.preventDefault();

    const painel = new PessoaController();

    painel.index();
    
  })
      //----------- pedidos----
  $('body').delegate('#navPanelUser #ultCompras, #pagination-compras a', 'click', function(ev){
    ev.preventDefault();
      let url = $(this).attr('href');

      let pedidoPessoa = new PessoaController();
      pedidoPessoa.pedido(url);
  })
    //pedido com filtro
  $('body').delegate('#pedido-status', 'change', function(ev){
    ev.preventDefault();
      let vl = $(this).val();
      
      let url = '/pessoa/compras?filtro='+vl;

      let pedidoPessoa = new PessoaController();
      pedidoPessoa.pedido(url);
  })

      //-------- cadastro ---------
  $('body').delegate('#navPanelUser #cadastro', 'click', function(ev){
    ev.preventDefault();

      const cadastroPessoa = new PessoaController();
      cadastroPessoa.cadastro()
  })

     //-------- endereco ---------
  $('body').delegate('#navPanelUser #endereco', 'click', function(ev){
    ev.preventDefault();

      const enderecoPessoa = new PessoaController();
      enderecoPessoa.endereco()
  })

  //-------- pagamento ---------
  $('body').delegate('#navPanelUser #pagamento', 'click', function(ev){
    ev.preventDefault();

     const pagamentosPessoa = new PessoaController();

     pagamentosPessoa.pagamento();

  })


  /*---------------------------CHAMA A VIEW DO CHATE ------------*/


  $('body').delegate('#navPanelUser #chate', 'click', function(ev){
    ev.preventDefault();

    let url = $(this).attr('href');

    $.ajax({
      url: url,
      type:'GET',
      dataType: 'HTML',
      success: function(retorno){
        $('#msg-response').html('');

        $('#containerLoja #responseUser').html(retorno);
        $('#bodyLojaVirtual').css('background', '#fff');//muda a cor de fundo da página.
        $(window).scrollTop('0')//posiciona o scroll no top
      }

    })
  });

  /*----------------------------- ENVIA A MENSAGEM ---------------*/
  $('body').delegate('#form-chat', 'submit', function(event){
  event.preventDefault();



    let formulario = $(this);

    let url = $(this).attr('action');

    let form = new FormData(formulario [0]);

    if($(this).find('textarea').val().trim() == ''){
      alert("Comentario inválido\n");
      return false;
    }else{

      $.ajax({
        type:'POST',
        url:url,
        data:form,
        processData:false,
        contentType: false,
        dataType:'HTML',
        success:function(retorno){
          if(retorno.length == 3){
            //alert(retorno[2]);
          }else{
            $('body #containerLoja #responseUser').scrollTop('100%')//posiciona o scroll no top
            $('body #navPanelUser #chate').trigger('click'); 
                       
          }
        }

      });
    }


  

  });



  //-------------------- CHAMA A VIEW PARA CADASTRAR ENDERECO -------------
  $('body').delegate('#enderecos-pessoa a#new-enderecdo, #enderecos-pessoa a#other-endereco', 'click', function(ev){
    ev.preventDefault();
    let url = $(this).attr('href');

    $.ajax({
        type:'GET',
        url:url,
        dataType:'HTML',
        success:function(retorno){
          
          $('#containerLoja #responseUser').html(retorno);
          $('#bodyLojaVirtual').css('background', '#fff');//muda a cor de fundo da página.
          $(window).scrollTop('0')//posiciona o scroll no top
        }

      });
  })

  $('body').delegate('#enderecos-pessoa a#endereco-editar', 'click', function(ev){
    ev.preventDefault();
    
    let url  = $(this).attr('href');

    $.ajax({
      url: url,
      type: 'GET',
      dataType: 'HTML',
      success: function(retorno){
         
        $('#containerLoja #responseUser').html(retorno);
        $('#bodyLojaVirtual').css('background', '#fff');//muda a cor de fundo da página.
        $(window).scrollTop('0')//posiciona o scroll no top
      }
    })

  });

  //---------------------------- ENVAIA O FORMULARIO DE SALVAR DO LOGRADOURO  ---------------- 
 
  $('body').delegate('#cadastro-logradouro', 'submit', function(event){
    event.preventDefault();

    let formulario = $(this);
    LogradouroController.salvarLogradouro(formulario);

  });

   //---------------------------- ENVAIA O FORMULARIO DE EDICAO DE LOGRADOURO  ---------------- 
 
  $('body').delegate('#cadastro-logradouro-editar', 'submit', function(event){
    event.preventDefault();

    let formulario = $(this);

    LogradouroController.atualizarLogradouro(formulario)
  

  });

  //---------------------------- CHAMA O FORMULARIO DE EDIÇÃO DO CADASTRO  ---------------- 
 
  $('body').delegate('a#pessoa-editar', 'click', function(event){
    event.preventDefault();

    let url = $(this).attr('href');

     $.ajax({
      url: url,
      type: 'GET',
      dataType: 'HTML',
      success: function(retorno){
         
        $('#containerLoja #responseUser').html(retorno);
        $('#bodyLojaVirtual').css('background', '#fff');//muda a cor de fundo da página.
        $(window).scrollTop('0')//posiciona o scroll no top
      }
    })

  });

  //---------------------------- ENVAIA O FORMULARIO DE EDIÇÃO DO CADASTRO  ---------------- 
 
  $('body').delegate('#cadastro-pessoa-atualizar', 'submit', function(event){
    event.preventDefault();

    let formulario = $(this);
    CadastroController.atualizarCadastro(formulario);

  });


    //------------- ADICONA UMA MENSAGEM ARMAZENADA NO RETORNO AO OBJ  -------
  function message(obj, retorno){

    if((retorno.length == 3) &&  (retorno[0] == 'msg')){
      let msg = $('<div/>').addClass('alert alert-'+retorno[1]+' alert-dismissible fadeshow col-md-12');
      msg.append($('<button/>').addClass('close').attr('data-dismiss', 'alert').html('&times'))
      msg.attr('align', 'center').append('<h3>'+retorno[2]+'</h3>');
      msg.css('box-shadow', '2px 2px 3px #000');

      obj.html(msg);
      return true;
    }
    return false;
  }


})



/*------------------------------------BASE CONTROLLER --------------------------------------*/

class BaseController{

  static requestAjax(url, type='GET', dataType = 'HTML', data= null, objRender=null, clearMsg = true){
    if(type == 'POST'){

    }else{

        $.ajax({
        url: url,
        type: type,
        dataType: dataType,
        success: function(retorno){
          //limpa a div de respostas
          if(clearMsg){
            $('#msg-response').html('')
          }

          if(objRender){
            objRender.html(retorno);
          }
          $('#bodyLojaVirtual').css('background', '#fff');//muda a cor de fundo da página.
        }
      })
    }


  }




}





/*----------------------------------- CLASSE DE CONTROLLER PESSOA  -------------------------------------------------*/
class PessoaController extends BaseController{

  index(){
    BaseController.requestAjax('/loja/painel', 'GET','HTML', null, $('#containerLoja'));   
  }

  endereco(){
       //-------- endereco ---------
    BaseController.requestAjax('/pessoa/endereco', 'GET','HTML', null, $('#containerLoja #responseUser'));
    
  }

  //----------- pedidos----
  pedido(url){

    if((!url) || (url.trim().length == 0)){
      throw new Error('Parâmetro inválido\n');
    }
    BaseController.requestAjax(url, 'GET','HTML', null, $('#containerLoja #responseUser'));

  }


  //-------- cadastro ---------
  cadastro(){
    BaseController.requestAjax('/pessoa/cadastro', 'GET','HTML', null, $('#containerLoja #responseUser'));
    
  }


  //----------- formas de pagamento ------------
  pagamento(){
    BaseController.requestAjax('/pessoa/pagamento', 'GET','HTML', null, $('#containerLoja #responseUser'));

  }



}

/*----------------------------------- CLASSE DE CONTROLLER PEDIDO  -------------------------------------------------*/

class PedidoController extends BaseController{

  index(){

  }

  showPedido(cd){
    if(cd <= 0){
      return false;
    }


    $.ajax({
      url: '/pedido/loja/view?cd='+cd,
      type: 'GET',
      dataType: 'HTML',
      success: function(retorno){
        let util = new Utilitarios();

        util.getModal('<strong>Detalhes:</strong>',retorno);
      }
    })

    return true;
  }

  static addToCar(cd, qtd, remov=false){
    if((cd <= 0) || (qtd <= 0)){
      return false;
    }

    let url = '/pedido/carrinho?qtd='+qtd+'&cd='+cd;
    if(remov !=false){
      url += '&rem=1';
    }

    $.ajax({
      url:url,
      type:'GET',
      dataType:'json',
      success:function(retorno){

        if(retorno.length == 1){
          $('body').find('#qtdItensCarrinho').text(retorno[0]);
          return true;
        }else{
          //console.log(retorno);
          return false;
        }
      }
    })
  }

  static removeToCar(cd){
    if(cd <= 0){
      throw new Error('Parâmetro inválido');
    }

    let url = '/pedido/carrinho/remove?cd='+cd;

    $.ajax({
      url:url,
      type:'GET',
      dataType:'json',
      success:function(retorno){

        if(retorno.length == 1){
          $('body').find('#qtdItensCarrinho').text(retorno[0]);
          return true;
        }else{
          //console.log(retorno);
          return false;
        }
      }
    })
  }


  static showCarrinho(url){
    if(url.trim().length == 0){
      throw new Error('Parâmetro inválido')
    }

    BaseController.requestAjax(url, 'GET','HTML', null, $('#containerLoja'));

  }

  static finalizarPedido(element, url, entrega){

    if(entrega){

      $(element).hide();

      url += '?cd='+entrega;
      $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(retorno){
          if((retorno.length == 3) && (retorno[1] != 'warning')){
            $('body').find('#qtdItensCarrinho').text(0);

            let utilitario = new Utilitarios();
            utilitario.getModal('<strong>Atençao</strong>',retorno[2]);

            element.parents('#form-pgto').remove();
          }else{
            let msg = '<div class="row"><div class="col col-sm alert alert-warning h4" align="center">'+retorno[2]+'</div></div>'
            
            let urilitario = Utilitarios();
            urilitario.getModal('<strong>Atençao</strong>',msg);

            element.show();

            //console.log(retorno);
          }
        }
      })

      return true;

    }

    throw new Error('Algo errado ocorreu, recarrgue a página')
      //let msg = `<div class="row"><div class="col col-sm alert alert-warning h4" align="center">Cadastre um endereço de entrega para finaliazar o pagamento!</div></div>`;
      //getModal('<strong>Atençao</strong>',msg);
    
  }

}

class CadastroController extends BaseController{

  constructor(){

  }

  static atualizarCadastro(formulario){

    if(!formulario){
      throw new Error('Parâmetro inválido\n')
    }

    let url = formulario.attr('action');

    let form = new FormData(formulario[0]);

    let errors  = [];

    let utilitario = new Utilitarios();
    if(utilitario.validaCpf(formulario.find('#documento').val()) == false){
      errors.push('Cpf inválido\n')
    }

    // falta fazer a validação do formulario
    if(errors.length > 0){
      let msg = '';
      for (let i = 0; !(i == errors.length) ; i++) {
         msg += errors[i]
      }

      let utilitario = new Utilitarios();
      utilitario.message($('#msg-response'), ['msg', 'warning', msg]);

      return false;

    }else{

      $.ajax({
        type:'POST',
        url:url,
        data:form,
        processData:false,
        contentType: false,
        dataType:'json',
        success:function(retorno){

          let utilitario = new Utilitarios();
          utilitario.message($('#msg-response'), retorno);

          if(retorno[1] == 'success'){
            BaseController.requestAjax('/pessoa/cadastro', 'GET','HTML', null, $('#containerLoja #responseUser'));
          }
        }

      });
    }


  }


}

class LogradouroController extends BaseController{
  constructor(){

  }

  static salvarLogradouro(formulario){
    let url = formulario.attr('action');

    let form = new FormData(formulario [0]);

    // falta fazer a validação do formulario
    if(false){
      alert("Comentario inválido\n");
      return false;
    }else{

      $.ajax({
        type:'POST',
        url:url,
        data:form,
        processData:false,
        contentType: false,
        dataType:'json',
        success:function(retorno){

          let utilitario = new Utilitarios();
          utilitario.message($('#msg-response'), retorno)

          if(retorno[1] == 'success'){
            BaseController.requestAjax(
              '/pessoa/endereco', 'GET','HTML', null,
               $('#containerLoja #responseUser'), false
              );
            
          }
        }

      });

    }

  }

  static atualizarLogradouro(formulario){
    let url = $(this).attr('action');

    let form = new FormData(formulario [0]);

    // falta fazer a validação do formulario
    if(false){
      alert("Comentario inválido\n");
      return false;
    }else{

      $.ajax({
        type:'POST',
        url:url,
        data:form,
        processData:false,
        contentType: false,
        dataType:'json',
        success:function(retorno){
         
          let utilitario = new Utilitarios();
          utilitario.message($('#msg-response'), retorno)

          if(retorno[1] == 'success'){
            BaseController.requestAjax(
              '/pessoa/endereco', 'GET','HTML', null,
               $('#containerLoja #responseUser'), false
              );
            
          }

        }

      });
    }

  }


  static editar(url){

  }

}

/*------------- CONTROLLER DE CONFIGURAÇÃO DA PÁGINA -------------------------*/
class ConfigController extends BaseController{
  constructor(){

  }

  static ativarLinks(){
    $('body').find('a.desable-link').removeClass('desable-link');
  }



}






/*----------------------------------- CLASSE DE MOLEDO PESSOA  -------------------------------------------------*/
class Pessoa{
  constructor(){
    this.nome;
    this.cpf;
    this.email;
    this.rg;
  }

  setNome(nome){
    if(!nome){

      return false;
    }

    if(nome.length <= 4){
      return false;
    }

    this.nome = nome;
    return true;

  }

  getNome(){
    if(!this.nome){
      return false;
    }

    return this.nome;
  }


  setEmail(email){
    if(!email){
      return false;
    }

    if(email.length <= 4){
      return false;
    }

    this.email = email;
    return true;
  }

  getRg(){
    if(!this.rg){
      return false;
    }

    return this.rg;

  }

  setRg(rg){
    if(!rg){
      return false;
    }

    if(rg.length <= 5){
      return false;
    }

  }

  getCpf(){
    if(!this.cpf){
      return false;
    }

    return this.cpf;
  }


  setCpf(cpf){
    if((!cpf) || (cpf.length != 11)){
      return false;
    }

    this.cpf = cpf;
    return true;

  }


}

/*----------------------------------- CLASSE DE UTILITÁRIOS  -------------------------------------------------*/

class Utilitarios{

  validaCpf(cpf){
    cpf = cpf.replace(/[^\d]+/g, '');

        if(cpf.length != 11){

          return false;
        }
        
        let splitCpf = cpf.split('');

        let digitoUm = 0;
        let digitoDois = 0;

        

        for (let i=0, x=1; !(i == 9 ); i++, x ++) { 
             digitoUm += splitCpf[i] * x;
        }

        

        for (let i=0,  y=0; !(i == 10 ); i++, y ++) { 

            let invaliCpf = '';

            for (let j = 0; !(j == 11); j++) {
              invaliCpf += i;
            }

            if(invaliCpf == cpf){
                return false;
            }


            digitoDois += splitCpf[i] * y;
        }

        let calculoUm = ((digitoUm % 11) == 10) ? 0 : (digitoUm % 11);
        let calculoDois = ((digitoDois % 11) == 10) ? 0 : (digitoDois % 11);

        if((calculoUm != splitCpf[9]) || (calculoDois != splitCpf[10])){

            return false;
        }

        return cpf;
  }

  /**
    Formata valores para calculo
  */
  static foramtCalcCod(number){
  

    number = String(number);
    

    if(number.length == 0){
      return false;
    }

    let arrNumber = number.split('.');

    let newNumber = '';
    for (let i =0; !(i == arrNumber.length); i++) {
      newNumber+=arrNumber[i]
    }


    newNumber = newNumber.replace(/,/g, '.');

    newNumber = parseFloat(newNumber).toFixed(2);

    return newNumber;


  }


  getModal(titulo='Aguarde', body='', footer=''){

    $('.modal-header h4').html(titulo)
    $('.modal-body').html(body);
    $('.modal-footer').html(footer);

  }

  message(obj, retorno){

    if((retorno.length == 3) &&  (retorno[0] == 'msg')){
      let msg = $('<div/>').addClass('alert alert-'+retorno[1]+' alert-dismissible fadeshow col-md-12');
      msg.append($('<button/>').addClass('close').attr('data-dismiss', 'alert').html('&times'))
      msg.attr('align', 'center').append('<h3>'+retorno[2]+'</h3>');
      msg.css('box-shadow', '2px 2px 3px #000');

      obj.html(msg);
      return true;
    }
    throw new Error('Parâmetro inválido')
  }


}
