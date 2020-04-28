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



// ------------------------------ Preview de imagens upload ---------------------
  
  $('#upload').change(function(){
    
      if(($(this)[0].files[0].type != 'image/jpeg') && ($(this)[0].files[0].type != 'image/png') && ($(this)[0].files[0].type != 'image/jpg')){

        alert("Imgem com formato inváldo");
        $(this).val('');
        $('#img').removeAttr('src');
        return false;
      }

      const file = $(this)[0].files[0]

      const fileReader = new FileReader();

      fileReader.onloadend = function(){
          $('#img').attr('src', fileReader.result).css('width', '253px').css('height', '232px')
      }
      fileReader.readAsDataURL(file);
  })
  


 
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
                let buttonMoreDetals = '<a href=/produto/detals?cd='+idProduto+' class="btn btn-primary  button-modal">Mais detalhes</a>';

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


  // -------------------------------- Imagem de load ---------------------
  function loadImg(nome){
    let img = $('<div/>').addClass('load').attr('align', 'center').html($('<img/>').attr('src','../files/imagens/'+nome ));
    return img;
  }


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
              let a = $('<a/>').addClass('produto-item').attr('href', `/produto/detals?cd=${parse[i].idProduto}`);
              let divImg = $('<div/>').attr('align', 'center').css('padding-top', '10px').append($('<img/>').css('width', '100px').css('height', '100px').attr('src', '../files/imagens/xbox_controller.jpeg'))

              let cardBody = $('<div/>').addClass('card-body').append($('<div/>').
                append($('<h3/>').html(`${parse[i].nomeProduto}`)).
                append($('<p/>').html(`${parse[i].textoPromorcional}`)).
                append($('<p/>').append($('<strong/>').html(`<sup><small>R$</small></sup>${parse[i].preco}<sup><small></small></sup>`)))
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


//------------------------ Menu de opcoes admin ---------------------
$('#menuAdminHide').on('click', function(){
  let menu = [
  /*
  '/produto/all',
  '/financeiro',
  '/venda/logistica'*/
    'produto',
    'financeiro',
    'venda'
  ];

  let icon = [
      '<i class="fas fa-cubes fa-2x" ></i>',
      '<i class="fas fa-coins fa-2x"></i>',
      '<i class="fas fa-shipping-fast fa-2x"></i>'
   ];

  let texto =['<br/>Produto', '<br/>Financeiro', '<br/>Logistica'];


  let rowOptions = $('<div/>').addClass('row').css('color', '#9400D3');

  let lista = $('<ul/>').addClass('nav');
  for (let i=0; !(i == menu.length); i++) {
      lista.append($('<li/>').addClass('nav-item mb-5 mr-3').append($('<span/>').addClass('btn').css('color', '#9400D3').append(icon[i]).append(texto[i]).attr('id', menu[i])/*'.attr('href', menu[i])'*/));
  }
  let nav = $('<nav/>').addClass('navbar').append(lista);
  rowOptions.append(nav).addClass('col-md-12');
  getModal('<strong>Menu de opções</strong>', rowOptions);
})

/* ---------------- Teste -----------*/



//chama o modal e passa alguns parametros

  function getModal(titulo='Aguarde', body='', footer='') {
    $('.modal-header h4').html(titulo)
    $('.modal-body').html(body);
    $('.modal-footer').html(footer);
  }

  //---------------------------------------- Carrinho de Compras --------------------------------------------------------
  $('.modal-footer').delegate('.carrinho', "click", function(){
    let idProduto = $('.modal-footer div.row div.col').find('a').attr('href');
    
    idProduto = idProduto.substring(idProduto.indexOf('=')+1);

   let xhr = $.ajax({
            url: '/venda/carrinho?id='+idProduto,
            type: 'GET',
            dataType: 'json',
            success: function(retorno){
              $('#qtdItensCarrinho').html(retorno)
            }
            
        });

   
  })


/*------------------------------- Faz uma requisizao ajax e lista na tabela de produtos ---------------------------------------------*/

  $('.modal-body').delegate('#produto','click', function(){

   let xhr = $.ajax({
            url: '/produto/all?rq=ajax',
            type: 'GET',
            dataType: 'json',
            success: function(retorno){
              listaTabelaProdutos(retorno);



            }
            
        });


  })

//-----------------------------Botoes da paginaçao da tabela paginacao da tabela de produtos ---------------------------/
$('#dinamic').delegate('ul li a', 'click', function(){

  let id = $(this).attr('id');

  let xhr = $.ajax({
            url: '/produto/all?rq=ajax&pagina='+id,
            type: 'GET',
            dataType: 'json',
            success: function(retorno){
              listaTabelaProdutos(retorno);
            }


          });
              //fecha o modal com menu
  //
})


//cria a tabela de protos e recebe os dados por parametro
function listaTabelaProdutos(retorno) {
  $('#closeModal').trigger('click');

              //Cabecalho da tabela
              let thead = $('<thead/>').css('color', '#000').append($('<tr/>').append($('<th/>').html('Nome'))
                                        .append($('<th/>').html('Estoque'))
                                        .append($('<th/>').html('Preço'))
                                        .append($('<th/>').html('Código'))
                                        .append($('<th/>').html('Ação'))
                )
              

              //Corpo da tabela
              let tbody = $('<tbody/>');
              for (let i = 0; !(i == retorno[0].length); i++) {

                tbody.append($('<tr/>').append($('<td/>').html(retorno[0][i].nomeProduto))
                    .append($('<td/>').html(retorno[0][i].estoque))
                    .append($('<td/>').html(retorno[0][i].preco))
                    .append($('<td/>').html(retorno[0][i].codigo))
                    .append($('<td/>').append($('<a/>').addClass('btn button-modal mr-2').attr('href','/produto/editar?id='+retorno[0][i].idProduto).html('<i class="fas fa-pencil-alt"></i>'))
                                      .append($('<a/>').addClass('btn btn-primary mr-2').attr('href','/produto/editar?id='+retorno[0][i].idProduto).html('<i class="fas fa-search-plus"></i>'))
                                      .append($('<a/>').addClass('btn btn-danger').attr('href','/produto/editar?id='+retorno[0][i].idProduto).html('<i class="fas fa-trash-alt"></i>'))
                        )

                  )
              }



              //armazena a tabela numa div de coluna 12
              let divTabela = $('<div/>').css('color', '#000').css('padding', '20px 40px 0px 40px').addClass('col-md-12');
              
              divTabela.append($('<a/>').attr('href', '/produto/cadastrar').addClass('btn button-modal mb-2').html('<i class="fas fa-plus-circle fa-2x"></i>'));

              divTabela.append($('<table/>').addClass('table table-hover').append(thead).append(tbody));
              


              //inicia a cria cao da lista de navegacao
              let preview = (retorno[1].pagina - 1);

              let blockPrev = '';
              if(Number(retorno[1].pagina) == 1){
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
              for (let i = 0; !(i == retorno[1].totPaginas); i++) {
                let estilo = '';

                if(retorno[1].pagina == (i + 1)){
                  estilo = 'active';
                }

                let li = $('<li/>').addClass('page-item '+estilo).append($('<a/>').on('click',function(event){//desativa o evento click do link
                      event.preventDefault()
                    }
                  ).attr('herf', '/produto/all?pagina='+(i+1)).attr('id', (i+1)).addClass('page-link').html((i+1)))
                lista.append(li);
              }

              let blockNext = '';
              if(retorno[1].pagina == retorno[1].totPaginas){
                  blockNext = 'disabled';
              }

              let next = (Number(retorno[1].pagina) + 1);
              
              lista.append($('<li/>').addClass('page-item').append($('<a/>').on('click',function(event){ //desativa o evento click do link
                 event.preventDefault()
               }
               ).attr('herf', '/produto/visualizar?id='+next).addClass('page-link '+blockNext).attr('id', next).html('next')));
              
              let divLista =$('<div/>').addClass('col-md-12').append($('<nav/>').append(lista));

              $('#dinamic').css('color', '#000').css('background-color', '#fff').html('');
              $('#dinamic').append(divTabela);
              $('#dinamic').append(divLista);
}


  $('#btnInicio').on('click', function(){

     $('#dinamic').html('')

  })

  $('#btnEntretas').on('click', function(){

     $('#dinamic').html('')
     
  })

  $('#btnConfig').on('click', function(){

     $('#dinamic').html('')
     
  })


//---------------------- GRAFICOS -------------------------------


  /*------------------------ Disparando funcoes dos gráficos -----------------------*/
  $('#btnDesemprenho').on('click', function(){
    $('#dinamic').html('');
    $('#dinamic').css('background-color', 'rgba(0, 0, 0, .8)')
    $('#dinamic').append($('<div/>').addClass('col-xs-12 col-sm-12 col-md-6').css('margin-rith', '2px').append($('<div/>').text('Meta').addClass('titleChar h3')).append($('<canvas/>').attr('id', 'metaVenda')));
    $('#dinamic').append($('<div/>').addClass('col-xs-12 col-sm-12 col-md-6').append($('<div/>').text('Compartivo do exercicio anual').addClass('titleChar h3')).append($('<canvas/>').attr('id', 'excAnual')));
    $('#dinamic').append($('<div/>').addClass('col-xs-12 col-sm-12 col-md-6').append($('<div/>').text('Mais vendidos').addClass('titleChar h3')).append($('<canvas/>').attr('id', 'maisVendidos')));
    $('#dinamic').append($('<div/>').addClass('col-xs-12 col-sm-12 col-md-6').append($('<div/>').text('Composição da margem').addClass('titleChar h3')).append($('<canvas/>').attr('id', 'compMargemLucr')));


 


    let ctx = $('#maisVendidos');
    let myChart = new Chart(ctx, {
      type: 'pie',//pie
      data: {
          labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho'],
          datasets: [{
              label: 'My First dataset',
              data: [12, 19, 3, 5, 2, 3],
              backgroundColor: [
                  'rgb(255, 99, 132)',
                  'rgb(54, 162, 235)',
                  'rgb(255, 206, 86)',
                  'rgb(75, 192, 192)',
                  'rgb(153, 102, 255)',
                  'rgb(255, 159, 64)'
              ],
              borderColor: [
                  'rgb(255, 99, 132)',
                  'rgb(54, 162, 235)',
                  'rgb(255, 206, 86)',
                  'rgb(75, 192, 192)',
                  'rgb(153, 102, 255)',
                  'rgb(255, 159, 64)'
              ],
              borderWidth: 1
          }]
      },
      options: {
          layout:{
             padding: {
                  left: 50,
                  right: 0,
                  top: 0,
                  bottom: 0
              },
              width:'10px'
          }
          ,scales: {
              yAxes: [{
                  ticks: {
                      beginAtZero: true,
                      fontColor: '#fff'
                  }
              }],
              xAxes:[{
                ticks: {
                      barPercentage: 0.2,
                      fontColor: '#fff'
                  }
              }]
          }
      }
  });

    /*--------------- Composicao da margem de lucro ---------------*/
    let compMarg = $('#compMargemLucr');
    let margemChar = new Chart(compMarg, {
      type: 'pie',//pie
      data: {
          labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho'],
          datasets: [{
              label: 'My First dataset',
              data: [12, 19, 3, 5, 2, 3],
              backgroundColor: [
                  'rgb(0, 99, 132)',
                  'rgb(54, 162, 0)',
                  'rgb(255, 0, 86)',
                  'rgb(75, 0, 192)',
                  'rgb(70, 102, 20)',
                  'rgb(0, 159, 64)'
              ],
              borderColor: [
                  'rgb(0, 99, 132)',
                  'rgb(54, 162, 0)',
                  'rgb(255, 0, 86)',
                  'rgb(75, 0, 192)',
                  'rgb(70, 102, 20)',
                  'rgb(0, 159, 64)'
              ],
              borderWidth: 1
          }]
      },
      options: {
          layout:{
             padding: {
                  left: 50,
                  right: 0,
                  top: 0,
                  bottom: 0
              },
              width:'10px'
          }
          ,scales: {
              yAxes: [{
                  ticks: {
                      beginAtZero: true,
                      fontColor: '#fff'
                  }
              }],
              xAxes:[{
                ticks: {
                      barPercentage: 0.2,
                      fontColor: '#fff'
                  }
              }]
          }
      }
  });

    /*-------------- Exercicio anual ---------*/
    let excAnual = $('#excAnual');
    let cahrExAnual = new Chart(excAnual, {
      type: 'line',//pie
      data: {
          labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho'],
          datasets: [{
              label: '2020',
              data: [12, 19, 3, 5, 2, 3],
              backgroundColor: 'rgba(255, 255, 255, 0.001)',
              borderColor:'rgba(0,255,0)',
              borderWidth: 3
          },
          {
              label: '2019',
              data: [10, 5, 8, 7, 10, 15],
              backgroundColor: 'rgba(255, 255, 255, 0.001)',
              borderColor:'rgba(0,255,255)',
              borderWidth: 3
          }]
      },
      options: {
          layout:{
             padding: {
                  left: 50,
                  right: 0,
                  top: 0,
                  bottom: 0
              },
              width:'10px'
          }
          ,scales: {
              yAxes: [{
                  ticks: {
                      beginAtZero: true,
                      fontColor: '#fff'
                  }
              }],
              xAxes:[{
                ticks: {
                      barPercentage: 0.2,
                      fontColor: '#fff'
                  }
              }]
          }
      }
  });



    // ------------------ Meta --------
    let ctxs = $('#metaVenda');
    let myCharts = new Chart(ctxs, {
      type: 'bar',//pie
      data: {
          labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho'],
          datasets: [
              {
                  label: 'Meta',
                  data: [15, 20.6, 19.85, 13.9, 25.45, 17.87],
                  borderColor:'rgba(148,0,211)',
                  backgroundColor: 'rgba(255, 255, 255, 0.001)',
                  borderWidth: 3,
                  type: 'line'
              },
              {
              label: 'My First dataset',
              data: [12, 19, 3, 5, 2, 3],
              backgroundColor: [
                  'rgb(255, 99, 132)',
                  'rgb(54, 162, 235)',
                  'rgb(255, 206, 86)',
                  'rgb(75, 192, 192)',
                  'rgb(153, 102, 255)',
                  'rgb(255, 159, 64)'
              ],
              borderColor: [
                  'rgb(255, 99, 132)',
                  'rgb(54, 162, 235)',
                  'rgb(255, 206, 86)',
                  'rgb(75, 192, 192)',
                  'rgb(153, 102, 255)',
                  'rgb(255, 159, 64)'
              ],
              borderWidth: 3
              },
              {
              label: 'My Next dataset',
              data: [10, 19, 5, 9, 8, 11],
              backgroundColor:[
                  'rgb(0, 99, 132)',
                  'rgb(54, 162, 0)',
                  'rgb(255, 0, 86)',
                  'rgb(0, 0, 192)',
                  'rgb(153, 102, 0)',
                  'rgb(255, 0, 64)'

              ],
              borderColor:[
                  'rgb(0, 99, 132)',
                  'rgb(54, 162, 0)',
                  'rgb(255, 0, 86)',
                  'rgb(0, 0, 192)',
                  'rgb(153, 102, 0)',
                  'rgb(255, 0, 64)'
              ],
              borderWidth: 3
          }
          ]
      },
      options: {
          layout:{
             padding: {
                  left: 50,
                  right: 0,
                  top: 0,
                  bottom: 0
              },
              width:'10px'
          }
          ,scales: {
              yAxes: [{
                  ticks: {
                      beginAtZero: true,
                      fontColor: '#fff'
                  }
              }],
              xAxes:[{
                ticks:{
                  barPercentage: 1,
                  fontColor: '#fff'
                }
                
              }]
          }
        }
    });

  })




})
