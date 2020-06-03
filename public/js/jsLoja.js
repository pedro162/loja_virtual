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

//---------------- Esconde exibe botoes de ver mais na view home ---------------------------

$('.hidBtn').mouseenter(function(){
      $(this).siblings('a').show();
    })
  
  //efito zoom na imagem view de detalhes
  
    

     

// load ajax da view de detables do produto

$('#containerLoja, body').delegate('.link-produto', 'click', function(e){
  e.preventDefault();
  $('#closeModal').trigger('click');//fecha o modal se estiver aberto
  let url = $(this).attr('href');
  
  $.ajax({
    type: 'GET',
    url:url,
    dataType:'HTML',
    success:function(retorno){
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


// ------------------------------ Preview de imagens upload ---------------------
  
  $('#dinamic').delegate('#imgproduto, #upload','change',function(){
    
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
          let a = $('<a/>').addClass('produto-item link-produto').attr('href', `/estoque/detalhes?cd=${parse[i].idProduto}`);
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

//------------------------ Menu de opcoes admin ---------------------
$('#menuAdminHide').on('click', function(){
  let menu = [
  
  '/produto/all',
  '/financeiro',
  '/venda/logistica'
    
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
      lista.append($('<li/>').addClass('nav-item mb-5 mr-3').append($('<a/>').addClass('btn link').css('color', '#9400D3').append(icon[i]).append(texto[i]).attr('href', menu[i])));
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

  $('body').delegate('.link','click', function(event){
    event.preventDefault();
    let rota = $(this).attr('href');
    let xhr = $.ajax({
            url: rota,
            type: 'GET',
            dataType: 'json',
            success: function(retorno){

              switch(rota){
                case '/produto/all':
                  listaTabelaProdutos(retorno);
                break;
                case '/produto/all':
                  listaTabelaProdutos(retorno);
                break;


              }

              



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
})



/*-------------------------------Executa os lincks da tabela de visualização dos produtos --------------------------*/
$("#dinamic").delegate('#tableProdutos tbody a', 'click', function(event){
  event.preventDefault();
  let acao = $(this).attr('href');

  let ajax = $.ajax({
    url: acao,
    type: 'GET',
    dataType: 'json',
    success:function(retorno){

      

      let componetesFormulario =  [
        [
          ['Nome', 'input', 'text',retorno.nomeProduto,'Nome do produto', 'nomeProduto', true],
          ['NF', 'input', 'number','0000000','Nº NF', 'nf', true],
          ['Fornecedor' ,'input', 'text', 'nom padrao fornecedor','Nome do fornecedor', 'codFornecedor',true]
          
        ],
        [
          ['Quantidade' ,'input', 'number',retorno.estoque,'Informe a quantidade', 'estoque', true],
          ['Valor Unit', 'input', 'number', retorno.preco ,'Descricão do produto', 'preco', true],
        ],
        [
          ['Codigo', 'input', 'text', retorno.codigo,'Código do produto','codProduto', true],
          ['Imagem' ,'input', 'file', '','', 'imgProduto', true]
        ]

      ];

      console.log(retorno);

      let formulario = formularioProduto(componetesFormulario, 'Atualizar Produto', 'Atualizar', retorno.textoPromorcional) // cria o formulario e o retorna
      $('#dinamic').html(formulario);

    }



  })
  
})





//---------------------------------------------Ctia o formulario de castro de produtos ---------------------------------------------------


$('#dinamic').delegate('#cadastrar', 'click', function(event){
  event.preventDefault();
  $('#dinamic').html('');

  let componetesFormulario =  [
    [
      ['Nome', 'input', 'text','','Nome do produto', 'nomeProduto', true],
      ['Nº NF', 'input', 'number','','Nº NF', 'nf', true],
      ['Fornecedor' ,'input', 'text','','Nome do fornecedor', 'codFornecedor',true]
      
    ],
    [
      ['Quantidade' ,'input', 'number','','Quantidade', 'estoque', true],
      ['Valor Unit', 'input', 'number', '' ,'Valor unitario', 'preco', true],
    ],
    [
      ['Codigo Produto', 'input', 'text','','Código do produto','codProduto', true],
      ['Imagem' ,'input', 'file','','', 'imgProduto', true]
    ]

  ];


  let formulario = formularioProduto(componetesFormulario) // cria o formulario e o retorna
  $('#dinamic').html(formulario);

  

})



// ---------------------------- cria elementos select ---------------------------------------------

function criaComponentSelect(values, name){

  let select = $('<select/>').addClass('form-control');
  let id = name.toLowerCase();

  select.attr('id', id);
  select.attr('name', id);
  select.attr('required', 'required');

  for (let i = 0; !(i == values.length); i++) {
    select.append($('<option/>').val(values[i][0]).html(values[i][1]));
    
  }

  return select;

}

//------------------------------- cria alguns componetes inputs de formulario---------------------------------------------

function criaComponenteForm(label, nomeElento, type, value='', placeholder='', name = '',required=false){

  let elento = null;

  let formLabel = $('<label/>').css('color', '#000');

  switch(nomeElento){
    case 'button':
      elento = $('<button/>').addClass('form-control').attr('placeholder', placeholder);
    break;

    case 'checkbox':
      elento = $('<checkbox/>').addClass('form-check-input');
      formLabel.addClass('form-check-label');
    break;

    case 'textarea':
      elento = $('<textarea/>').addClass('form-control').attr('placeholder', placeholder);
    break;

    case 'file':
      elento = $('<file/>').addClass('form-control').attr('placeholder', placeholder);
    break;

    default:
      elento = $('<input/>').addClass('form-control').attr('placeholder', placeholder);
    break;

   
  }

   if(required == true){
      elento.attr('required', required);
    }


    let idElento = name.toLowerCase();

    elento.attr('type', type);
    elento.attr('name', name);
    elento.attr('id', idElento);
    elento.val(value);


    formLabel.attr('id', idElento);

    formLabel.html(label);

    let result = $('<div/>').addClass('form-group').append(formLabel).append(elento);

    return result;


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

    //inicas os canvas
    let canvasMeta = $('<div/>').addClass('col-xs-12 mt-3 col-sm-12 col-md-6').
      append($('<div/>').text('Meta').addClass('titleChar h3')).append($('<canvas/>').attr('id', 'metaVenda'))

    let canvasExecAnual = $('<div/>').addClass('col-xs-12 mt-3 col-sm-12 col-md-6').append($('<div/>').
      text('Compartivo do exercicio anual').addClass('titleChar h3')).append($('<canvas/>').attr('id', 'excAnual'))

    let canvasMaisVendidos = $('<div/>').addClass('col-xs-12 mt-3 mb-3 col-sm-12 col-md-6').append($('<div/>').text('Mais vendidos').
      addClass('titleChar h3')).append($('<canvas/>').attr('id', 'maisVendidos'))

    let canvasMargem = $('<div/>').addClass('col-xs-12 col-sm-12 mt-3 mb-3 col-md-6').append($('<div/>').
      text('Composição da margem').addClass('titleChar h3')).append($('<canvas/>').attr('id', 'compMargemLucr'))

    //cria uma div e armazena os canvas
    let rowCanvas = $('<div/>').addClass('row').css('background-color', 'rgba(0, 0, 0, .8)');
    rowCanvas.css('padding', 'padding: 20px 40px')

    rowCanvas.append(canvasMeta);
    rowCanvas.append(canvasExecAnual);
    rowCanvas.append(canvasMaisVendidos);
    rowCanvas.append(canvasMargem);

    //isere a dive no corpo do documetno principal
    $('#dinamic').html('');
    $('#dinamic').append(rowCanvas);

    //exibe a barra vertial direita
    $('#optionPlus').css('display', 'block');

 


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



/* ------------------------------------------------------------------------- Views -------------------------------------------------------------------------------------------*/



//cria a tabela de protos e recebe os dados por parametro
function listaTabelaProdutos(retorno) {

  $('#closeModal').trigger('click');

  //exibe a barra lateral direita
   
  $('#optionPlus').css('display', 'block');
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
                        .append($('<a/>').addClass('btn btn-primary mr-2').attr('href','/produto/visualizar?id='+retorno[0][i].idProduto).html('<i class="fas fa-search-plus"></i>'))
                        .append($('<a/>').addClass('btn btn-danger').attr('href','/estoque/deletar?id='+retorno[0][i].idProduto).html('<i class="fas fa-trash-alt"></i>'))
          )

      )
    }



    //armazena a tabela numa div de coluna 12
    let divTabela = $('<div/>').css('color', '#000').css('padding', '20px 40px 0px 40px').addClass('col-md-12');

    divTabela.append($('<a/>').attr('id', 'cadastrar').attr('href', '/produto/cadastrar').addClass('btn button-modal mb-2').html('<i class="fas fa-plus-circle fa-2x"></i>'));

    divTabela.append($('<table/>').attr('id', 'tableProdutos').addClass('table table-hover').append(thead).append(tbody));

    //retorna um lista de navegacao
    let retult = pagination(retorno[1].pagina, retorno[1].totPaginas);

    $('#dinamic').css('color', '#000').css('background-color', '#fff').html('');
    $('#dinamic').append(divTabela);
    $('#dinamic').append(retult);
}


/*--------------------------------------------------cria o formulario de cadastro de produtos ------------------------------------------*/

function formularioProduto(componetesFormulario, tituloFormulario ='Cadastar Produto', btnAcao = 'Cadastar', textoPromorcional = null, select = null){ //recebe uma array multdimensional 

  let colInputs = $('<div/>').addClass('col-md-9');

  //cria os elemtnos imputs e adicona ao fieldset
  for (let i = 0; !(i == componetesFormulario.length); i++) {

    let row = $('<div/>').addClass('row');

    for (let j = 0; !(j == componetesFormulario[i].length); j++) {
      let nomeElento  = componetesFormulario[i][j][0];
      let input = componetesFormulario[i][j][1];
      let text  = componetesFormulario[i][j][2];
      let value = componetesFormulario[i][j][3]
      let placehoder = componetesFormulario[i][j][4];
      let name = componetesFormulario[i][j][5]
      let requerido = componetesFormulario[i][j][6];


      let elento = criaComponenteForm(nomeElento, input, text, value, placehoder, name, requerido);
      let colElento = $('<div/>').addClass('col-md-'+ ( 12 / componetesFormulario[i].length)).append(elento);
      
      row.append(colElento);

    }

    colInputs.append(row);
    
  }

  //opcoes para select de marcas
  let optionsMarca = [
    [1, 'Sansung'],
    [2, 'Ios'],
    [3, 'Motorola']
  ];


  //opcoes para select de categorias

   let optionsCategoria = [
    [1, 'Esport'],
    [2, 'Infantil'],
    [3, 'Adulto']
  ];

  let selectMarca = criaComponentSelect(optionsMarca, 'Marca'); //craia uma lista de opcoes e a retorna
  let selectCategoria = criaComponentSelect(optionsCategoria, 'Categoria');
  
  //aramazena as listas de opcoes em div para formulario
  let divSelectMarca = $('<div/>').addClass('form-group').append($('<label/>').css('color', '#000').attr('id', 'marca').html('Marca')).append(selectMarca);

  let divSelectCategoria = $('<div/>').addClass('form-group').append($('<label/>').css('color', '#000').attr('id', 'categoria').html('Categoria')).append(selectCategoria);


  let rowSelect = $('<div/>').addClass('row');

  //aramazena cada lista numa coluna
  rowSelect.append($('<div/>').addClass('col-xs-6 col-sm-6 col-md-6 col-lg-6').append(divSelectMarca))
  rowSelect.append($('<div/>').addClass('col-xs-6 col-sm-6 col-md-6 col-lg-6').append(divSelectCategoria))

  colInputs.append(rowSelect);



  //cria a div para fazer o previw da imagme 
  let preview = $('<div/>').addClass('col-md-3').append($('<img/>').attr('id', 'img'))

  let filtdSet = $('<fieldset/>').append($('<legend/>').text(tituloFormulario).addClass('mt-2'));

  let textAreaPromorcional = $('<textarea/>').attr('name', 'texto').attr('id', 'idTexto');
  textAreaPromorcional.attr('rows', '6').addClass('form-control');
  textAreaPromorcional.attr('required', 'required');
  textAreaPromorcional.html(textoPromorcional);

  let divTextarea = $('<div/>').addClass('form-group').append($('<label/>').css('color', '#000').attr('id', 'idTexto').html('Texto promorcional'));
  divTextarea.append(textAreaPromorcional);


  let rowTextArea = $('<div/>').addClass('row');
  rowTextArea.append($('<div/>').addClass('col-xs-12 col-sm-12 col-md-12 col-lg-12').append(divTextarea));
  //armazena a text area na coluna de imputs
  colInputs.append(rowTextArea);

  colInputs.append($('<button/>').html(btnAcao).addClass('btn btn-lg btn-success'))
  colInputs.append($('<a/>').attr('href', '/produto/all').html('Cancelar').addClass('btn btn-lg btn-danger ml-2 link'))

  //adicona as colunas de imput e preview ao fidset
  filtdSet.append($('<div/>').addClass('row').append(colInputs).append(preview));

  let formulario = $('<form/>').css('color', '#000').attr('enctype', 'multipart/form-data').append(filtdSet);
  formulario.css('padding', '40px 20px 0px 20px')

  let divFormulario = $('<div/>').addClass('col-md-12 mt-2 mb-5').append(formulario);
    
  return divFormulario;
  
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
                let buttonMoreDetals = '<a href=/estoque/detalhes?cd='+idProduto+' class="btn btn-primary link-produto button-modal">Mais detalhes</a>';

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







})
