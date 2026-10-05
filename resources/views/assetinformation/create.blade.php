@extends('layouts.main')

@section('styles')
  <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
  <style>
  </style>
@stop

@section('content')

  <div class="box box-default">
    <div class="box-body">
      <h3>Create New Asset</h3>

      <div class="row">

      <form method="POST" action="{{ route('asset.store') }}" onkeypress="return event.keyCode != 13;" id="asset_form">
        @csrf

        @php $form_type ='create' @endphp
        @include('assetinformation._form')
      </form>

      </div>
    </div>
  </div>

@stop

@section('script')
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
          defaultPreviewContent: '<img src="{{asset('img/asset_image.png')}}" alt="Your Avatar">',
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
                      alert(data.message)
                      window.location.replace('/asset');

                }else{
                      $('#btnSubmit').attr("disabled", false);
                      $("#btnSubmit").val('Submit');
                      alert(data.message)
                }
              }
          });
    }));
  </script>


@endsection
