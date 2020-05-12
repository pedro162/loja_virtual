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


//funcao para atualizar notificacoes
  $(window).mousemove(function(){
    let date = new Date();
    let min = date.getMinutes();

    if((min % 5) == 0){

      // aqui vou criar uma funcao ajax para buscar no banco de dados 
      //as novas vendas realizadas, sempre que ocorrer uma venada, uma mensagem será gerada.
      //o admin vai poder clicar nas notificacoes e abrir as mensagens com informaçoes sobre as vendas
      console.log(`Executa a cada 5 minutos`)
    }
    
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





  //------------------------- LANÇAMENTO DE ESTOQUE --------------------------------------

  $('#dinamic').delegate('form#estoque', 'submit', function(event){
    event.preventDefault();

    let submitArray = new Array(); //define um super array para armazenar os valores dos campos

    $(this).find('input, select, textarea').each(function(){

      let key = $(this).attr('name')

      let value = String($(this).val()); // transforama para string para retirar os espacoes em branco do inicio e final

      value = value.trim();

      //verifica se o campo foi preenchido e 
      if(value.length == 0){

        //casso algo não esteja peenchido, exibem uma mensagem
        message(['msg', 'warning', 'Atenção: Preenha os campos corretamente!']);

        $('#dinamic').find('[name='+key+']').focus().css('border', '1px solid red').css('box-shadow', '2px 2px 3px red').keyup(function(){
        $(this).css('box-shadow', '0px 0px 0px green').css('border', '1px solid green');

          
        });
        return false;
      }

       submitArray.push(key+'='+value);


    })

    let url = $(this).attr('action');
    
    $.ajax({
      type: 'POST',
      url: url,
      data: {'estoque': submitArray},
      dataType: 'json',
      success: function(retorno){

        message(retorno);
      }

    })


  })




  //---------------------------- CADASTRO DE PRODUTOS -------------------------------

  $('#dinamic').delegate( '#cadastrarProduto', 'submit', function(event){
    event.preventDefault();


    let submitArray = new Array(); //define um super array para armazenar os valores dos campos

    $(this).find('input, select, textarea').each(function(){

      let key = $(this).attr('name')

      let value = String($(this).val()); // transforama para string para retirar os espacoes em branco do inicio e final

      value = value.trim();

      //verifica se o campo foi preenchido e 
      if(value.length == 0){

        //casso algo não esteja peenchido, exibem uma mensagem
        message(['msg', 'warning', 'Atenção: Preenha os campos corretamente!']);

        $('#dinamic').find('[name='+key+']').focus().css('border', '1px solid red').css('box-shadow', '2px 2px 3px red').keyup(function(){
        $(this).css('box-shadow', '0px 0px 0px green').css('border', '1px solid green');

          
        });
        return false;
      }

       submitArray.push(key+'='+value);


    })

    let url = $(this).attr('action');
    
    $.ajax({
      type: 'POST',
      url: url,
      data: {'produto': submitArray},
      dataType: 'json',
      success: function(retorno){
        message(retorno);
      }

    })

  })


  
//------------------------------- FUNCAO PARA APRESENTACAO DE MENSGENS---------
function message(retorno){

  if(retorno[0] == 'msg'){
    let msg = $('<div/>').addClass('alert alert-'+retorno[1]+' alert-dismissible fadeshow col-md-12');
    msg.append($('<button/>').addClass('close').attr('data-dismiss', 'alert').html('&times'))
    msg.attr('align', 'center').append('<h3>'+retorno[2]+'</h3>');
    msg.css('box-shadow', '2px 2px 3px #000');

    $('#msg').detach();
    $('#cadastrarProduto, #estoque').parent().prepend($('<div/>').attr('id', 'msg').addClass('row mb-5').html(msg));
  }
}
 


  // -------------------------------- Imagem de load ---------------------
  function loadImg(nome){
    let img = $('<div/>').addClass('load').attr('align', 'center').html($('<img/>').attr('src','../files/imagens/'+nome ));
    return img;
  }


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
  $.ajax({
    url:'/home/menu',
    type: 'GET',
    dataType: 'HTML',
    success:function(retorno){
      getModal('<strong>Menu de opções</strong>', retorno);
    }
  })

  /*let menu = [
  
  '/produto/all',
  '/financeiro',
  '/venda/logistica',
  '/marca/cadastrar'
    
  ];

  let icon = [
      '<i class="fas fa-cubes fa-2x" ></i>',
      '<i class="fas fa-coins fa-2x"></i>',
      '<i class="fas fa-shipping-fast fa-2x"></i>',
      '<i class="fas fa-box fa-2x"></i>'
   ];

  let texto =['<br/>Produto', '<br/>Financeiro', '<br/>Logistica'];


  let rowOptions = $('<div/>').addClass('row').css('color', '#9400D3');

  let lista = $('<ul/>').addClass('nav');
  for (let i=0; !(i == menu.length); i++) {
      lista.append($('<li/>').addClass('nav-item mb-5 mr-3').append($('<a/>').addClass('btn link').css('color', '#9400D3').append(icon[i]).append(texto[i]).attr('href', menu[i])));
  }
  let nav = $('<nav/>').addClass('navbar').append(lista);
  rowOptions.append(nav).addClass('col-md-12');
  getModal('<strong>Menu de opções</strong>', rowOptions);*/
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
            dataType: 'HTML',
            success: function(retorno){

              switch(rota){
                case '/produto/all':
                  listaTabelaProdutos(retorno);
                break;
                
                case '/marca/cadastrar':
                  $('#dinamic').html(retorno);
                break;

                case '/venda/nova':
                  $('#closeModal').trigger('click');
                  $('#dinamic').html(retorno);
                break;

                case '/estoque/lancar':
                  $('#closeModal').trigger('click');
                  $('#dinamic').html(retorno);
                break;

                case '/estoque/all':
                  $('#closeModal').trigger('click');
                  $('#dinamic').html(retorno);
                break;


              }

              



            }
            
        });


  })

//-----------------------------Botoes da paginaçao da tabela paginacao da tabela de produtos ---------------------------/
$('#dinamic').delegate('ul li a', 'click', function(event){
  event.preventDefault();
  let url = $(this).attr('href');

  let xhr = $.ajax({
            url: url,
            type: 'GET',
            dataType: 'HTML',
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
    dataType: 'HTML',
    success:function(retorno){
      $('#dinamic').html('');
      $('#dinamic').html(retorno)

    }



  })
  
})





//---------------------------------------------Ctia o formulario de castro de produtos ---------------------------------------------------


$('#dinamic').delegate('#cadastrar', 'click', function(event){
  event.preventDefault();
  let url = $(this).attr('href');
  $.ajax({
    url: url,
    type: 'GET',
    dataType: 'HTML',
    success: function(retorno){


    //limpa o painel principal
    $('#dinamic').html('');
    $('#dinamic').html(retorno)

    }


  })
  

 // componetesFormulario, tituloFormulario ='Cadastar Produto', btnAcao = 'Cadastar',
 //textoPromorcional = null, optionsMarca = null, optionsCategoria = null
  

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
      viewDeGraficos();
   
  })



/* ------------------------------------------------------------------------- VIEWS DO SISTEMA -------------------------------------------------------------------------------------------*/



//cria a tabela de protos e recebe os dados por parametro
function listaTabelaProdutos(retorno) {
  $('#dinamic').css('color', '#000').css('background-color', '#fff').html('');
  $('#dinamic').append(retorno);

  $('#closeModal').trigger('click');


}



 //------------------------ View de graficos ------------------------------ 
function viewDeGraficos(){
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




}






})
