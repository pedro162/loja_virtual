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

  viewDeGraficos();//carrega os graficos

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

  //-------------- EXIBE OU ESCONDE A NAVEGAÇÃO DO PAINEL VENDA CLIENTE --------------
  $('#dinamic').delegate('#controlNavPanel', 'click', function(){
    let i = $(this).find('#ic');
    if(i.hasClass('fa-plus-circle')){ 
      i.removeClass('as fa-plus-circle fa-1x').addClass('fas fa-minus')
     
    }else{
       i.addClass('as fa-plus-circle fa-1x').removeClass('fas fa-minus')
    }
    $('#navPanelCliente').toggle();
  })

// ------------------------------ Preview de imagens upload ---------------------
  
  $('#dinamic').delegate('#imgproduto, .upload','change',function(){
   
      let inputFile = $(this);

      if(($(this)[0].files[0].type != 'image/jpeg') && ($(this)[0].files[0].type != 'image/png') && ($(this)[0].files[0].type != 'image/jpg')){

        alert("Imgem com formato inváldo");
        $(this).val('');
        $('.preview[name='+$(this).attr('name')+']').removeAttr('src');
        return false;
      }

      const file = $(this)[0].files[0]

      const fileReader = new FileReader();

      fileReader.onloadend = function(){
          $('.preview[name='+inputFile.attr('name')+']').attr('src', fileReader.result).css('width', '153px').css('height', '132px')
      }
      fileReader.readAsDataURL(file);
  })





//------------------------------CHAMA O PAINEL VENDA CLIENTE APOS O PREENCHIMENTO DO FORMULARIO -------------------------------
  $('#dinamic').delegate('form#vendaPainel', 'submit', function(event){
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
      type:'POST',
      url: url,
      data: {'cliente': submitArray},
      dataType:'HTML',
      success: function(retorno){
        $('#optionPlus').hide();//esconde o menu lateral direito do painel principal
        $('#dinamic').removeClass('col-md-9').addClass('col-md-12');//ajusta o painel principal para 12 colunas
        $('#dinamic').html(retorno);
      }
    })
  });



//-------------------------------------- LOAD DINAMICO DE PRODUTOS PAINEL VENDA------------------------------

$('#dinamic').delegate('form#serchProd input[name=loadProduto], form#loadCodProd input#loadCodProd', 'keyup', function(){
  
  let produto = $(this);
  $('form#serchProd #loadCodProd').val('');

  produto.autocomplete({
  minLength: 3,
  source: function (request, response) {
      loadProdutos(produto ,request, response);
    },
    select:function(event, ui){
      $('form#serchProd #loadCodProd').val(ui.item.cod);

      if((Number(ui.item.qtd) == 0)){

        if($('#tableSearch').hasClass('table-light') == true){

          $('#tableSearch').removeClass('table-light');
        }

        if($('#tableSearch').hasClass('table-danger') == false){

          $('#tableSearch').addClass('table-danger');
        }

        
      }else{

        if($('#tableSearch').hasClass('table-danger') == true){

          $('#tableSearch').removeClass('table-danger');
        }

        if($('#tableSearch').hasClass('table-light') == false){

          $('#tableSearch').addClass('table-light');
        }
        
      }

      let tr = $('<tr/>');
      tr.append($('<td/>').css('text-align', 'center').text(ui.item.cod))
      tr.append($('<td/>').text(ui.item.label))
      tr.append($('<td/>').css('text-align', 'right').text(formatMoney(ui.item.vlVen)))
      tr.append($('<td/>').css('text-align', 'center').text(ui.item.qtd));

      $('#tableSearch tbody').html(tr);
    },
    autoFocus: true,
    change:function(request, response){
      loadProdutos(produto ,request, response);
    },
    delay: 1

  });
});

//---------------------------  FAZ A REQUISIÇÃO AJAX E GERA O AUTO COMPLETE DE PRODUTOS -----------------

function loadProdutos(obj, request, func)
{

  let dados = new Array();
  if(obj.attr('name')=='loadCodProd'){
    dados.push('cod');
    dados.push(request.term);
  }else{
    dados.push(request.term);
  }

  $.ajax({
    type:'POST',
    url: '/pedido/load/estoque',
    data: {'loadEstoque': dados},
    dataType:'json',
    success: function(retorno){

      let arrObj = new Array();

      $.each(retorno, function(key, item){
        arrObj.push({
          label: item[1],
          value: item[1],
          cod: item[0],
          qtd: item[2],
          vlVen: item[3]//crie para teste mas funcionou
        });

        });
        func(arrObj);
      }
    });
}



//---------------------------------- ADICIONA O ITEM NA TABELA DE PEDIDOS DO PAINEL DE VENDAS -------------------------------
let i = 1;
$('#dinamic').delegate('form#serchProd', 'submit', function(event){
  event.preventDefault();

  let qtd = $(this).find('input[name=qtdProduct]').val();
  let desconto = $(this).find('input[name=descPercent]').val();
  desconto = desconto.trim();

  if(qtd <= 0){
    message(['msg', 'warning', 'Atenção: Preenha os campos corretamente!']);

    $('input[name=qtdProduct]').focus().css('border', '1px solid red').css('box-shadow', '2px 2px 3px red').keyup(function(){
    $(this).css('box-shadow', '0px 0px 0px green').css('border', '1px solid green');});
    return false;

  }

  if(desconto.length == 0){
    desconto = 0;

  }else if(desconto == null){
    alert('nulo')

  }else if(parseFloat(desconto) < 0){
    message(['msg', 'warning', 'Atenção: Preenha os campos corretamente!']);

    $('input[name=descPercent]').focus().css('border', '1px solid red').css('box-shadow', '2px 2px 3px red').keyup(function(){
    $(this).css('box-shadow', '0px 0px 0px green').css('border', '1px solid green');});

    return false;
  }

  
  let codigo = String($(this).find('table tbody tr:first td:eq(0)').text());
  let prod = String($(this).find('table tbody tr:first td:eq(1)').text());
  let preco = foramtCalcCod($(this).find('table tbody tr:first td:eq(2)').text());
  let estoque = Number($(this).find('table tbody tr:first td:eq(3)').text());

  if((prod.length == 0) || (preco.length == 0)){

    message(['msg', 'warning', 'Atenção: Adicione os itens corretamente!']);
    return false;
  }

  if(estoque == 0){

    message(['msg', 'warning', 'Atenção: Estoque insuficiente para o produto "'+prod+'" !']);
    return false;
  }


  desconto = parseFloat(desconto);
  preco = parseFloat(preco);
  

  let valDesc = ((desconto / 100) * preco);
  let precoComDesc = (preco - valDesc);
  let parsQtdd = Number(qtd);

  let subTotal = (precoComDesc * parsQtdd);
  let totDesconto = (valDesc * parsQtdd);

  let tr = $('<tr/>');
  tr.append($('<td/>').css('text-align', 'center').html(prod))
  tr.append($('<td/>').css('text-align', 'center').html(parsQtdd))
  tr.append($('<td/>').css('text-align', 'center').html(formatMoney(preco)))
  tr.append($('<td/>').css('text-align', 'center').html(formatMoney(valDesc, 3)))
  tr.append($('<td/>').css('text-align', 'center').html(formatMoney(totDesconto)))
  tr.append($('<td/>').css('text-align', 'center').html(formatMoney(precoComDesc)))
  tr.append($('<td/>').css('text-align', 'center').html(formatMoney(subTotal)))

  let edit = $('<a/>').attr('data-target', '#myModal').attr('href', '/produto/editar?id='+codigo).addClass('btn btn-success mr-2').append($('<i/>').addClass('fas fa-pencil-alt'))
  edit.attr('data-toggle', 'modal');

  let delet = $('<a/>').attr('href', '/produto/excluir?id='+codigo).addClass('btn btn-danger').append($('<i/>').addClass('fas fa-trash-alt'))

  tr.append($('<td/>').append(edit).append(delet))

  $("#trMsg").remove();//remove a mensagem inicial da table de peidito

  $('#vendaPainelTable #itensTable').find('tbody').prepend(tr); //adiciona a tr com os dados

  //calcula o total geral
   calculaTotalVenda();

  //limpa os imputs e a tabela de busca
  $(this).find('input').val('');

  $(this).find('table tbody tr').remove();

  $('form#serchProd #loadProduto').focus();

  i++;

});



  //------------------ reove item da tabela de pedito------
$('#dinamic').delegate('form#vendaPainelTable #itensTable tbody a.btn-danger', 'click', function(event){
  event.preventDefault();

  let produto = $(this).parents('tr').find('td:eq(0)').text()

  let comfirm = confirm('Deseja realmete remover o item "'+produto+'" ?');
  if(comfirm == true){
    $(this).parents('tr').remove();

    calculaTotalVenda();

  }
});

function calculaTotalVenda(){

  let totalGeral = 0;
    $('#vendaPainelTable #itensTable').find('tbody tr').each(function(){
      let val =  $(this).find('td:eq(6)').text(); 
      totalGeral+=parseFloat(foramtCalcCod(val));

    })


    totalGeral = formatMoney(totalGeral)
    $('form#vendaPainelTable #totGeralVenda').text(totalGeral);
}




//------------------ edita o item da tabela de pedito------
$('#dinamic').delegate('form#vendaPainelTable #itensTable tbody a.btn-success', 'click', function(event){
  event.preventDefault();

//recupera a quantidade o desconto e o preco
  let produto = $(this).parents('tr').find('td:eq(0)').text() 
  let qtd = $(this).parents('tr').find('td:eq(1)').text()
  let desconto = parseFloat(foramtCalcCod($(this).parents('tr').find('td:eq(3)').text()))
  let preco = parseFloat(foramtCalcCod($(this).parents('tr').find('td:eq(2)').text()))

  let percentDesc = parseFloat(((desconto * 100) / preco)).toFixed(2)

//cria o imput de quantidade e sua lable
  let inputQtd = $('<input/>').attr('type', 'number').attr('name', 'qtdEdit').attr('id', 'qtdEdit')
  inputQtd.addClass('form-control').val(qtd);

  let labelQtd = $('<label/>').attr('for', 'qtdEdit').html('<strong>Quantidade: </strong>')
  //forma o grupo input lable
  let formGroupQtdLabel = $('<div/>').addClass('form-group').append(labelQtd)
  .append(inputQtd)

//cria o imput de desconto e sua lable
  let inputDesc = $('<input/>').attr('type', 'text').attr('name', 'qtdDesc').attr('id', 'qtdDesc');
  inputDesc.addClass('form-control').val(percentDesc);

  let labelDesc = $('<label/>').attr('for', 'qtdDesc').html('<strong>Desconto %: </strong>');
  //forma o grupo input lable
  let formGroupDescLabel = $('<div/>').addClass('form-group').append(labelDesc)
  .append(inputDesc)

  let aplicar = $('<button/>').attr('id', 'aplyModIten').addClass('btn btn-primary').html('<strong>Applicar</strong>')
  let cancelar = $('<button/>').addClass('btn btn-danger ml-2').html('<strong>Cancelar</strong>');
  cancelar.attr('data-dismiss', 'modal');

  let divRow = $('<div/>').addClass('row').append($('<div/>').addClass('col-md-6').html(formGroupQtdLabel));
  divRow.append($('<div/>').addClass('col-md-6').html(formGroupDescLabel));
  divRow.append($('<div/>').addClass('col-md-12').append(aplicar).append(cancelar));

  //chama o modal e adiciona os elemtnos
  getModal('<strong>Editar: </strong><span>'+produto+'</span>', divRow);

  
});




//--------------- recalcula o total da compra se aplicado a edicao da quantidade ou desconto --------------
$('#dinamic').delegate('#aplyModIten', 'click', function(){
  //falta continar implemtação
  calculaTotalVenda();
});

//------------------ CHAMA O MODAL APRA ADD COBRANÇA AO PEDIDO DE VENDA -------------
$('#dinamic').delegate('#vendaPainelTable .cob', 'click', function(){
  $('#mgCob').remove();//remove a msg da tabela cobranca
  let idCob = Number($(this).attr('id'));

  let contentor = $('<div/>').addClass('row');
  let divDif = $('<div/>').addClass('col-md-4 col-sm-6');
  let divCob = $('<div/>').addClass('col-md-8 col-sm-6');

  divDif.text('R$ '+$('#totGeralVenda').text());//pega o total da compra

  //cria o label do da cobranca
  divCob.addClass('row').append(
        $('<div/>').addClass('col').append($('<label/>').attr('for', 'vlImput').text('R$'))
        .append($('<input/>').attr('type', 'text').attr('id', 'vlImput').attr('name', 'vlParcela').addClass('form-control'))
      );

  if((idCob == 1) || (idCob == 3)){
    //cria os inputs de cobranca
    divCob.append(
        $('<div/>').addClass('col').append($('<label/>').attr('for','cobInput').text('Qtd parcelas'))
        .append(
                $('<select/>').attr('id','cobInput').addClass('form-control').append($('<option/>').val('1').text('1'))
                .append($('<option/>').val('2').text('2'))
                .append($('<option/>').val('3').text('3'))
              )
      )
  }
  contentor.append(divDif)
  contentor.append(divCob)

  //cria os botoes de ação
  let add = $('<button/>').addClass('btn btn-primary mr-2').attr('type', 'button').html($('<strong>').text('Add'));
  let cancel = $('<button/>').addClass('btn btn-danger').attr('type', 'button').html($('<strong>').text('Cancel'));
  contentor.append(
      $('<div/>').addClass('col').append(add).append(cancel)
    )

  getModal('Add Cobrança: '+$(this).text(), contentor);


});

$('#dinamic').delegate('#addCob', 'click', function(){

    //cria os botões de ação.
    let actionEdit = $('<a/>').attr('data-target', '#myModal').append($('<i/>').addClass('fas fa-pencil-alt'));
    actionEdit.attr('data-target', '#myModal');

    let actionDelet = $('<a/>').attr('data-target', '#myModal').append($('<i/>').addClass('fas fa-trash-alt'));
    actionDelet.attr('data-target', '#myModal');

    $('<tr/>').append()
})







//-------------------- ENVIA O PEDIDO PARA SALVAR -----------------------------
$('#dinamic').delegate('form#vendaPainelTable', 'submit', function(event){
  event.preventDefault();

  let dados = new Array();

  $(this).find('table#itensTable tbody tr').each(function(){

    let cod = $(this).find('td:eq(7) a').attr('href');
    cod = cod.split('=')[1];

    let qtd = foramtCalcCod($(this).find('td:eq(1)').text());
    let preco = foramtCalcCod($(this).find('td:eq(2)').text());
    let precDesc = foramtCalcCod($(this).find('td:eq(3)').text());
    let totDesc = foramtCalcCod($(this).find('td:eq(4)').text());
    let precoComDesc = foramtCalcCod($(this).find('td:eq(5)').text());
    let subtTot = foramtCalcCod($(this).find('td:eq(6)').text());

    dados.push([cod, qtd, preco, precDesc, totDesc, precoComDesc, subtTot]);
  
  })

  let form = new FormData($(this)[0]);

  let sentinela = false;
  for (let i = 0; !(i == dados.length); i++) {

    for (let j = 0; !(j == dados[i].length); j++) {
      
      if((dados[i][j].length == 0) || (dados[i][j] === false)){
        sentinela = true;
        break
      }
    }

    form.append('pedidoPanelVenda[]', dados[i]);

  }  

  if(sentinela == true){
    message(['msg', 'warning', 'Atenção: Algo errado aconteceu, recarrege a página novamente!']);
    return false;
  }


  $.ajax({
    type:'POST',
    url: '/pedido/save/pedido',
    data: form,
    processData:false,
    contentType: false,
    dataType:'HTML',
    success: function(retorno){
        getModal('', '');
        
        $('#dinamic').html('').removeClass('col-md-12').addClass('col-md-9');//ajusta o painel
        $('#optionPlus').show();//exibe o menu lateral direito se estiver oculto

        $('#idFilanizar').trigger('click');
        getModal('', retorno);
      }
    });



});



//--------------------------------------------- LOAD DINAMICO DE CLIENTE PARA VENDA --------------------

$('form#vendaPainel #idCod').off('keyup');
$('#dinamic').delegate('form#vendaPainel input[name=loadCliente], form#vendaPainel input#idCod', 'keyup', function(){

  let cliente = $(this);
  $('form#vendaPainel #idCod').val('');

  cliente.autocomplete({
  minLength: 3,
  source: function (request, response) {
      loadPessoa(cliente ,request, response);
    },
    select:function(event, ui){
      $('form#vendaPainel #idCod').val(ui.item.teste);
    },
    autoFocus: true,
    change:function(request, response){
      loadPessoa(cliente ,request, response);
    },
    delay: 1

  });
});

//------------------------------------ FAZ A REQUISIÇÃO AJAX E RETORNA OS DADOS DA PESSOA----------------------------
function loadPessoa(obj, request, func)
{

  let dados = new Array();
  if(obj.attr('name')=='loadCod'){
    dados.push('cod');
    dados.push(request.term);
  }else{
    dados.push(request.term);
  }

  $.ajax({
    type:'POST',
    url: '/pedido/load/pessoa',
    data: {'loadPessoa': dados},
    dataType:'json',
    success: function(retorno){
      let arrObj = new Array();

      $.each(retorno, function(key, item){
        arrObj.push({
          label: item[1],
          value: item[1],
          teste: item[0]//crie para teste mas funcionou
        });

        });
        func(arrObj);

      }
    });
}


// ------------------------ FAZ A REQUISIÇÃO DE CATEGRORIAS PARA CADASTRO PRODUTO --------------

$('#dinamic').delegate(
  'form input#idCategoria,form input#idSubCategoria',
   'keyup', function(){

  let categ = $(this);
  $('form#vendaPainel #idCod').val('');

  categ.autocomplete({
  minLength: 2,
  source: function (request, response) {
      loadCategoria(categ ,request, response);
    },
    select:function(event, ui){

      categ.val(ui.item.label);
      categ.next().val(ui.item.cod);
    },
    autoFocus: true,
    change:function(request, response){
      loadCategoria(categ ,request, response);
    },
    delay: 1

  });
});

//------------------------------------ FAZ A REQUISIÇÃO AJAX E RETORNA OS DADOS DA CATEGORIA----------------------------
function loadCategoria(obj, request, func)
{

  $.ajax({
    type:'POST',
    url: '/categoria/load',
    data: {'loadCategoria': request.term},
    dataType:'json',
    success: function(retorno){
      console.log(retorno);
      let arrObj = new Array();

      $.each(retorno, function(key, item){
        arrObj.push({
          label: item[1],
          value: item[1],
          cod: item[0]
          //teste: item[0]//crie para teste mas funcionou
        });

        });
        func(arrObj);

      }
    });
}


  //------------------------- LANÇAMENTO DE ESTOQUE --------------------------------------

  $('#dinamic').delegate('form#estoque, form#editEstoque', 'submit', function(event){
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
//--------------------------- EDITAR ESTOQUE ------------------------------------

$('#dinamic').delegate('#tableProdutos tbody a', 'click', function(event){
  event.preventDefault();
  let url = $(this).attr('href');

  $.ajax({
    type: 'GET',
    url:url,
    dataType: 'HTML',
    success: function(retorno){
      $('#dinamic').html(retorno);
    }
  });
})



  //---------------------------- CADASTRO DE PRODUTOS -------------------------------

  $('#dinamic').delegate( '#cadastrarProduto, #editarProduto', 'submit', function(event){
    event.preventDefault();

    let url = $(this).attr('action');

    let form = new FormData($(this)[0]);

    $.ajax({
      type: 'POST',
      url: url,
      data: form,
      enctype: 'multipart/form-data',
      dataType: 'HTML',
      processData: false,
      contentType: false,
      success: function(retorno){
        console.log(retorno);return false;
        message(retorno);
      }

    })

  })

//---------------------------- CADASTRO DE MARCAS -------------------------------

  $('#dinamic').delegate( '#cadastrarMarca', 'submit', function(event){
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
      data: {'marca': submitArray},
      dataType: 'json',
      success: function(retorno){
        message(retorno);
      }

    })

  })
  

  //---------------------------- CADASTRO DE CATEGORIAS -------------------------------

  $('#dinamic').delegate( '#cadastrarCategoria', 'submit', function(event){
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
      data: {'categoria': submitArray},
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

    $('#dinamic').find('div #msg').remove();
    $('#cadastrarProduto, #estoque, #editEstoque, #editarProduto, #cadastrarMarca, #cadastrarCategoria, #vendaPainelTable').
    parent().prepend($('<div/>').attr('id', 'msg').addClass('row mb-5').html(msg));
  }
}
 


  // -------------------------------- Imagem de load ---------------------
  function loadImg(nome){
    let img = $('<div/>').addClass('load').attr('align', 'center').html($('<img/>').attr('src','../files/imagens/'+nome ));
    return img;
  }


//-------------- Faz o sistema de paginação------------------------------------------------------------------------
  function pagination(pagina, totPaginas, rota='/produto/all') {
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
    ).attr('herf', rota+'?pagina='+preview).attr('id', preview).addClass('page-link '+blockPrev).html('peview')));

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
        ).attr('herf', rota+'?pagina='+(i+1)).attr('id', (i+1)).addClass('page-link').html((i+1)))
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
            url: '/pedido/carrinho?id='+idProduto,
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
                  $('#closeModal').trigger('click');
                  $('#dinamic').html(retorno);
                break;
                case '/categoria/cadastrar':
                  $('#closeModal').trigger('click');
                  $('#dinamic').html(retorno);
                break;

                case '/pedido/novo':
                  $('#closeModal').trigger('click');
                  $('#optionPlus').show();//exibe o menu lateral direito se estiver oculto
                  $('#dinamic').html(retorno).removeClass('col-md-12').addClass('col-md-9');//ajusta o painel
                break;

                case '/estoque/lancar':
                  $('#closeModal').trigger('click');
                  $('#dinamic').html(retorno);
                break;

                case '/estoque/all':
                  $('#closeModal').trigger('click');
                  $('#dinamic').html(retorno);
                break;

                case '/produto/cadastrar':
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


//-------------- FAZ A FORMATAÇÃO PARA DINHEIRO ------------
function formatMoney(amount, decimalCount = 2, decimal = ',', thousands = '.'){
  try{

    decimalCount = Math.abs(decimalCount);
    decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

    const negativeSing = amount < 0 ? '-' : '';

    let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
    let j = (i.length > 3) ? i.length % 3 : 0;

    let fomartted = negativeSing;
    fomartted += (j ? i.substr(0, j) + thousands : '');
    fomartted += i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands);
    fomartted += (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : '');

    return fomartted;


  }catch(e){

    console.log(e);
  }


}


function foramtCalcCod(number)
{
  try{

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

  }catch(e){

    console.log(e);
  }
}



  $('#btnInicio').on('click', function(){

      $.ajax({
      type: 'GET',
      url: '/home/admin/inicio',
      dataType: 'HTML',
      success: function(retorno){
        $('#dinamic').html(retorno);
        viewDeGraficos()
      }

    })

      
     $('#dinamic').html('')

  })

  $('#btnEntretas').on('click', function(){

     $('#dinamic').html('')
     
  })

  $('#btnConfig').on('click', function(){

     $('#dinamic').html('')
     
  })

  //--------------------- EXIBE O MENU LATERAL DIREITO DO MENU PRINCIPAL ------------------------
  function menuLateralDireito()
  {
    $('#optionPlus').toggle();//exibe o menu lateral direito se estiver oculto

  }


//---------------------- GRAFICOS -------------------------------


  /*------------------------ Disparando funcoes dos gráficos -----------------------*/
  $('#btnDesemprenho').on('click', function(){
      viewDeGraficos();

      $.ajax({
      type: 'GET',
      url: '/home/admin/inicio',
      dataType: 'HTML',
      success: function(retorno){
        $('#dinamic').html(retorno);
        //viewDeGraficos()
      }

    })
   
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
                left: 0,
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
                    fontColor: '#000'
                }
            }],
            xAxes:[{
              ticks: {
                    barPercentage: 0.2,
                    fontColor: '#000'
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
                left: 0,
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
                    fontColor: '#000'
                }
            }],
            xAxes:[{
              ticks: {
                    barPercentage: 0.2,
                    fontColor: '#000'
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
                left: 0,
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
                    fontColor: '#000'
                }
            }],
            xAxes:[{
              ticks: {
                    barPercentage: 0.2,
                    fontColor: '#000'
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
                left: 0,
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
                    fontColor: '#000'
                }
            }],
            xAxes:[{
              ticks:{
                barPercentage: 1,
                fontColor: '#000'
              }
              
            }]
        }
      }
  });




}






})
