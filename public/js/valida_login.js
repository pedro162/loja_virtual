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
          $('#img').attr('src', fileReader.result).css('width', '353px').css('height', '332px')
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

      $("input[name='produtos[Departamento][]']:checked").each(function(){
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
        departamento.unshift('Departamento');
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


  /*---------------------- GRAFICOS -------------------------------
  */



  /*------------------------ Disparando funcoes dos gráficos -----------------------*/
  $('#btnInicio').on('click', function(){
    $('#dinamic').css('background-color', 'rgba(0, 0, 0, .8)')
    $('#dinamic').append($('<div/>').addClass('col-md-6').css('margin-rith', '2px').append($('<div/>').text('Meta').addClass('titleChar h3')).append($('<canvas/>').attr('id', 'metaVenda')));
    $('#dinamic').append($('<div/>').addClass('col-md-6').append($('<div/>').text('Compartivo do exercicio anual').addClass('titleChar h3')).append($('<canvas/>').attr('id', 'excAnual')));
    $('#dinamic').append($('<div/>').addClass('col-md-6').append($('<div/>').text('Mais vendidos').addClass('titleChar h3')).append($('<canvas/>').attr('id', 'maisVendidos')));
    $('#dinamic').append($('<div/>').addClass('col-md-6').append($('<div/>').text('Composição da margem').addClass('titleChar h3')).append($('<canvas/>').attr('id', 'compMargemLucr')));
/*
borderColor:'rgba(255, 255, 255)',
                  backgroundColor: 'rgba(255, 255, 255, 0.001)',exercicio
*/

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
