@extends('layouts.main')

@section('styles')
  <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('content')

  <div class="box box-default">
    <div class="box-body">
      <h3>Edit Asset</h3>

      <div class="row">
      <form action="{{ route('asset.update', $asset->id) }}" method="post" onkeypress="return event.keyCode != 13;" files="true" id="asset_form" >
        {{ method_field('put') }}
        @php $form_type ='edit' @endphp
        @include('assetinformation._form')
      </form>
      </div>
    </div>
  </div>

@endsection

@section('script')
  <script scr="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('plugins/input-mask/jquery.inputmask.js')}}"></script>
  <script src="{{asset('plugins/input-mask/jquery.inputmask.date.extensions.js')}}"></script>
  <script src="{{asset('plugins/input-mask/jquery.inputmask.extensions.js')}}"></script>

  <script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
  <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
  <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

  <script src="{{asset('js/fileinput.js')}}"></script>

  <script>
    $(document).ready(function($) {

        // $('#date_of_birth').datepicker({
        //   // startDate: new Date() ,
        //   autoclose: true
        // });


      var btnCust = '<button type="button"  class="btn btn-secondary" title="Add picture tags" ' +
          'onclick="alert(\'Call your custom code here.\')">' +
          '<i class="glyphicon glyphicon-tag"></i>' +
          '</button>';
      $("#avatar").fileinput({
          overwriteInitial: true,
          maxFileSize: 1500,
          showClose: false,
          showCaption: false,
          browseLabel: '',
          removeLabel: '',
          browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
          removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
          removeTitle: 'Cancel or reset changes',
          elErrorContainer: '#kv-avatar-errors-1',
          msgErrorClass: 'alert alert-block alert-danger',
          defaultPreviewContent: '<img src="{{asset('img/default_avatar_male.jpg')}}" alt="Your Avatar">',
          layoutTemplates: {main2: '{preview} ' +  btnCust + ' {remove} {browse}'},
          allowedFileExtensions: ["jpg", "png", "gif"]
      });
    });
  </script>

  <script>
    $('#asset_form').on('submit',(function(e) {
          e.preventDefault();

        $("#btnSubmit").attr("disabled", true);
        $("#btnSubmit").val('Please wait..');

          var formData = new FormData(this);

          $.ajax({
              type:'POST',
              url: $(this).attr('action'),
              data:formData,
              cache:false,
              contentType: false,
              processData: false,
              success:function(data){


                if(data.success == true) {
                      $('#btnSubmit').attr("disabled", false);
                      $("#btnSubmit").val('Submit');
                      window.location.replace('/asset');
                      console.log('f');

                }else{
                      // toastr.error(data.messages);
                      $('#btnSubmit').attr("disabled", false);
                      $("#btnSubmit").val('Submit');
                      console.log('lse');
                }
              }
          });
    }));
  </script>

@endsection
