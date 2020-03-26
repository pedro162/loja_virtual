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
  $('.cnpj').hide();
  $('#cadastro #id_cpf').on('click', function(){
    $('.cnpj').hide();
    $('.cpf').show();
  })

  $('#cadastro #id_cnpj').on('click', function(){
    $('.cpf').hide();
    $('.cnpj').show();
  })



  
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
  

  

})
