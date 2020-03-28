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
  

  //--------------------------------------------- Butão modal de produtos -----------------------------------------
  $('.child-card-footer .button-modal').on('click', function(){
    let idProduto = $(this).parents('.card-produto').find('a').attr('href')
    idProduto = idProduto.substring(idProduto.indexOf('=')+1);

    $.ajax({
            url: '/produto/more?id='+idProduto,
            type: 'GET',
            dataType: 'json',
            success: function(retorno){
              let list = '<ul>';
                for (var i = 0; !(i == retorno.length); i++) {
                  list += '<li>'+retorno[i]+'</li>';
                }
                list += '</ul><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>';
                console.log(list);
                $('.modal-body').html(list);

                let buttonAdd = '<button type="button" class="btn btn-primary  button-modal">Adicionar ao carrinho</button>';
                let buttonMoreDetals = '<a href=/produto/detals?cd='+idProduto+' class="btn btn-primary  button-modal">Mais detalhes</a>';
                $('.modal-footer').html(buttonAdd+buttonMoreDetals).find('.btn-success, .button-modal').css('background-color', '#8B008B');
            }
            
        });

  });
  

})
