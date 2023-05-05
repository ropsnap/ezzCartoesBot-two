<!DOCTYPE html>
  <html lang="pt-br">
  

<!-- Mirrored from / by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 28 Mar 2021 19:07:08 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <meta charset="utf-8" />
    <link rel="shortcut icon" href="img/icon.html">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Checker de Full - PladixOficial</title>
              <link rel="icon" type="image/png" href="foto1.html">
    <link rel="stylesheet" href="use.fontawesome.com/releases/v5.5.0/css/all.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet" />
    <link href="assets/nucleo-icons.css" rel="stylesheet" />
    <link href="assets/stylee209e209.css?v=1.0.0" rel="stylesheet" />
    <link href="assets/demo.css" rel="stylesheet" />
  </head>
  <body>
	<div class="col-md-11 mt-4" style="margin: auto; ">
      <div class="row">
        <div class="col-md-8">
          <div class="card">
            <div class="card-body text-center">
              <h4 class="title mb-2">Add CCs 1.0</h4>
              <textarea style="height: 8.06rem;" class="form-control text-center form-checker mb-2" placeholder="Insira Sua Lista (999)"></textarea>
              <button class="btn btn-success btn-play text-white" style="width: 49%; float: left;"><i class="fa fa-play"></i> INICIAR</button>
              <button class="btn btn-danger btn-stop text-white" style="width: 49%; float: right;" disabled><i class="fa fa-stop"></i> PARAR</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="title"><i class="fa fa-check"></i> Adicionadas:<span class="badge badge-success float-right aprovadas">0</span></h5><hr>

              <h5 class="title"><i class="fa fa-times"></i> Reprovadas:<span class="badge badge-danger float-right reprovadas">0</span></h5><hr>

              <h5 class="title"><i class="fa fa-sync-alt"></i> Testadas:<span class="badge badge-info float-right testadas">0</span></h5><hr>

              <h5 class="title mb-0"><i class="fa fa-share-square"></i> Carregadas:<span class="badge badge-primary float-right carregadas">0</span></h5>
            </div>
          </div>
        </div>
        <div class="col-xl-12">
          <div class="card">
            <div class="card-body">
              <div class="float-right">
                <button type="show" class="btn btn-primary btn-sm show-lives"><i class="fa fa-eye-slash"></i></button>
                <button class="btn btn-success btn-sm btn-copy"><i class="fa fa-copy"></i></button>
              </div>
              <h4 class="title mb-1"><i class="fa fa-check text-success"></i> ADICIONADAS</h4>
              <div id='lista_aprovadas'></div>
            </div>
          </div>
        </div>
        <div class="col-xl-12">
          <div class="card">
            <div class="card-body">
              <div class="float-right">
                <button type='hidden' class="btn btn-primary btn-sm show-dies"><i class="fa fa-eye"></i></button>
                <button class="btn btn-danger btn-sm btn-trash"><i class="fa fa-trash"></i></button>
              </div>
              <h4 class="title mb-1"><i class="fa fa-times text-danger"></i> REPROVADAS</h4>
              <div style='display: none;' id='lista_reprovadas'></div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="assets/jquery.min.js" type="text/javascript"></script>

<script>

  $(document).ready(function() {

   // getSaldo();

    $('.show-lives').click(function() {
      var type = $('.show-lives').attr('type');

      $('#lista_aprovadas').slideToggle();
      if (type == 'show') {
        $('.show-lives').html('<i class="fa fa-eye"></i>');
        $('.show-lives').attr('type', 'hidden');
      } else {
        $('.show-lives').html('<i class="fa fa-eye-slash"></i>');
        $('.show-lives').attr('type', 'show');
      }});

    $('.show-dies').click(function() {
      var type = $('.show-dies').attr('type');
      $('#lista_reprovadas').slideToggle();
      if (type == 'show') {
        $('.show-dies').html('<i class="fa fa-eye"></i>');
        $('.show-dies').attr('type', 'hidden');
      } else {
        $('.show-dies').html('<i class="fa fa-eye-slash"></i>');
        $('.show-dies').attr('type', 'show');
      }});

    $('.btn-trash').click(function() {
      Swal.fire({
        title: 'Dies Limpas.', icon: 'success', showConfirmButton: false, toast: true, position: 'top-end', timer: 3000
      });
      $('#lista_reprovadas').text('');
    });

    $('.btn-copy').click(function() {
      Swal.fire({
        title: 'Lives Copiadas!', icon: 'success', showConfirmButton: false, toast: true, position: 'top-end', timer: 3000
      });
      var lista_lives = document.getElementById('lista_aprovadas').innerText;
      var textarea = document.createElement("textarea");
      textarea.value = lista_lives;
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand('copy'); document.body.removeChild(textarea);
    });


    $('.btn-play').click(function() {
   
      var lista = $('.form-checker').val().trim();
      var array = lista.split('\n');
      var lives = 0,
      dies = 0,
      testadas = 0,
      txt = '';

      if (!lista) {
        Swal.fire({
          title: 'Erro: Lista Vazia!', icon: 'error', showConfirmButton: false, toast: true, position: 'top-end', timer: 3000
        });
        return false;
      }

      Swal.fire({
        title: 'Teste Iniciado!', icon: 'success', showConfirmButton: false, toast: true, position: 'top-end', timer: 3000
      });

      var line = array.filter(function(value) {
        if (value.trim() !== "") {
          txt += value.trim() + '\n';
          return value.trim();
        }
      });

      /*
var line = array.filter(function(value){
return(value.trim() !== "");
});
*/

      var total = line.length;

      /*
line.forEach(function(value){
txt += value + '\n';
});
*/

      $('.form-checker').val(txt.trim());

      if (total > 1000) {
        Swal.fire({
          title: 'Limite de 1K de Linhas Exedido!', icon: 'warning', showConfirmButton: false, toast: true, position: 'top-end', timer: 3000
        });
        return false;
      }

      $('.carregadas').text(total);
      $('.btn-play').attr('disabled', true);
      $('.btn-stop').attr('disabled', false);

      line.forEach(function(data) {
        var callBack = $.ajax({
          url: 'apii.php?ccs=' + data,
          success: function(retorno) {
            if (retorno.indexOf("#Add") >= 0) {
				$('#saldoCount').html($('#saldoCount').html() -1)
              $('#lista_aprovadas').append("<span class='text-success'>" + retorno + "</span><br>"); 
              removelinha();
              
              lives = lives +1;
            } else {
              $('#lista_reprovadas').append("<span class='text-danger'>" + retorno + "</span><br>"); 
              removelinha();
              dies = dies +1;
            }
            testadas = lives + dies;
            $('.aprovadas').text(lives);
            $('.reprovadas').text(dies);
            $('.testadas').text(testadas);

            if (testadas == total) {
              Swal.fire({
                title: 'Teste Finalizado!', icon: 'success', showConfirmButton: false, toast: true, position: 'top-end', timer: 3000
              });
              $('.btn-play').attr('disabled', false);
              $('.btn-stop').attr('disabled', true);
            }
          }
        });
        $('.btn-stop').click(function() {
          Swal.fire({
            title: 'Teste Parado!', icon: 'warning', showConfirmButton: false, toast: true, position: 'top-end', timer: 3000
          });
          $('.btn-play').attr('disabled', false);
          $('.btn-stop').attr('disabled', true);
          callBack.abort();
          return false;
        });
      });
    });
  });

  function removelinha() {
    var lines = $('.form-checker').val().split('\n');
    lines.splice(0, 1);
    $('.form-checker').val(lines.join("\n"));
  }

</script>


</body>
</html>