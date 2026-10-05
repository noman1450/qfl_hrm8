@extends('layouts.main')

@section('styles')
  <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('content')
@if(Session::has('message'))
    <p class="alert alert-success mt-2 mb-2">{{ Session::get('message') }}</p>
@endif

  <div class="box box-default">
    <div class="box-body">
      <div class="nav-tabs-custom">

        <ul class="nav nav-tabs">
          <li class="active"><a href="#Asset_List" data-toggle="tab" aria-expanded="true">Asset List</a></li>
          <li class=""><a href="#Asset_Type" data-toggle="tab" aria-expanded="false">Asset Type</a></li>
          <li class=""><a href="#Brand" data-toggle="tab" aria-expanded="false">Brand</a></li>
          <li class=""><a href="#Asset_Return_Type" data-toggle="tab" aria-expanded="false">Return Type</a></li>
        </ul>

        <div class="tab-content" id="myTab">         

        <!--Asset Information List Start ========================================================  -->
          <div class="tab-pane active" id="Asset_List">
            <div class="box box-default">
              <div class="box-body">
                <div class="row">
                  <div class="box-header with-border">
                    <a href="{{ route('asset.create') }}" button type="button" class="btn-success btn btn-sm button pull-left" style="font-size: 12px; font-weight: bold">Add Asset</a>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow: auto;">
                      <table id="asset-table" class="table table-bordered table-hover" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th style="width: 10%">Picture</th>
                            <th style="width: 30%">Description</th>
                            <th style="width: 10%">Asset Type</th>
                            <th style="width: 10%">Brand</th>
                            <th style="width: 10%">Model</th>
                            <th style="width: 10%">Depreciation Rate</th>
                            <th style="width: 10%">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <!--Asset Information List End ========================================================  -->





        <!--Asset Type Start ========================================================  -->
          <div class="tab-pane" id="Asset_Type">
            <div class="box-body">
              <div class="row">
                <div class="box-header with-border">
                  <button button type="button" class="btn-success btn btn-sm button pull-left" data-toggle="modal" data-target="#modal_asset_type" data-whatever="@mdo" style="font-size: 12px; font-weight: bold">Add Asset Type</button>
                </div>

                <div class="modal fade" id="modal_asset_type"  role="dialog" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      @include('assettype.create')
                    </div>
                  </div>
                </div>


              </div>
            </div>

            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  @include('assettype.index')
                </div>
              </div>
            </div>
          </div>
        <!--Asset Type End ========================================================  -->




        <!--Asset Brand Start ========================================================  -->
          <div class="tab-pane" id="Brand">
            <div class="box-body">
              <div class="row">
                <div class="box-header with-border">
                  <button type="button" class="btn-success btn btn-sm button pull-left" data-toggle="modal" data-target="#modal_brand" data-whatever="@mdo" style="font-size: 12px; font-weight: bold">Add Brand</button>
                </div>

                <div class="modal fade" id="modal_brand" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      @include('assetbrand.create')
                    </div>
                  </div>
                </div>

                <div class="box-body" id="BrandBody">
                  <div class="row">
                    @include('assetbrand.index')
                  </div>
                </div>
              </div>
            </div>
          </div>
        <!--End Asset Brand ========================================================  -->





        <!--Asset Return Type Start ========================================================  -->

          <div class="tab-pane" id="Asset_Return_Type">
            <div class="box-body">
              <div class="row">
                <div class="box-header with-border">
                  <button button type="button" class="btn-success btn btn-sm button pull-left" data-toggle="modal" data-target="#modal_asset_return_type" data-whatever="@mdo" style="font-size: 12px; font-weight: bold">Add Asset Type</button>
                </div>

                <div class="modal fade" id="modal_asset_return_type" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      @include('assetreturntype.create')
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  @include('assetreturntype.index')
                </div>
              </div>
            </div>
          </div>

        <!--End Asset Return Type ========================================================  -->



        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')

  <script scr="{{asset('plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
  <script src="{{asset('plugins/input-mask/jquery.inputmask.js')}}"></script>
  <script src="{{asset('plugins/input-mask/jquery.inputmask.date.extensions.js')}}"></script>
  <script src="{{asset('plugins/input-mask/jquery.inputmask.extensions.js')}}"></script>

  <script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
  <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
  <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

  <script src="{{asset('js/fileinput.js')}}"></script>

  <script>

      $(document).ready(function($) {


          // Start Asset Information's Script =============================================================================
        table = $('#asset-table').DataTable({
            destroy:    true,
            paging:     true,
            searching:  true,
            ordering:   true,
            bInfo:      true,
            ajax: {
                type: 'POST',
                url : "{{URL::to('/')}}/asset_list",
                headers:{ 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                dataType: 'json'
            },

            "columns": [

                { "data": "Link",
                    "mRender": function(data, type, full) {
                        return '<img width="70" src="/uploads/'+full.imgpath+'" </img>';
                    }
                },
                { "data": "description" },
                { "data": "asset_type" },
                { "data": "brand" },
                { "data": "model" },
                { "data": "depreciation_rate" },
                { "data": "Link" }
            ],
            "order": [[0,'asc']]
        });

        // End Asset Information's Script  =============================================================================





        // Asset type Start ===========================================================================================

            function  LoadAssetType(){

                  $.ajax({
                    type:   'POST',
                    url :   "{{URL::to('/')}}/asset_type_list",
                    headers:{
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                    dataType: 'json',
                        success: function(data) {
                            var dataSet = data.data;
                            table = $('#assetType-table').DataTable( {
                                destroy:    true,
                                paging:     true,
                                searching:  true,
                                ordering:   true,
                                bInfo:      true,
                                "data":     dataSet,

                            "columns": [

                                { "data": "id" },
                                { "data": "asset_type_name" },
                                { "data": "Link",
                                    "mRender": function(data, type, full) {
                                        return '<a  data-asset_type_id="' + full.id + '" data-asset_type_name="' + full.asset_type_name + '"  class="btn btn-primary btn-flat btn-sm showme"> <span class="glyphicon glyphicon-edit">Edit</a>';
                                    }
                                },
                                { "data": "Link",
                                    "mRender": function (data, type, full) {
                                    return '<form action="{{URL::to('/')}}/asset_type/'+full.id+'"  method="POST"> {{ csrf_field() }} {{ method_field('DELETE') }} <button type="submit" class="btn btn-danger" title="delete" onclick="return confirm(&#39;Are you sure you want to delete this item?&#39;);">Delete</button></form>';
                                    }
                                },
                            ],
                            "order": [[0,'asc']]
                            });
                        }
                  });

                  $('#assetType-table').on('click', '.showme', function() {

                    $('#asset_type_id').val($(this).data('asset_type_id'));
                    $('#asset_type_name').val($(this).data('asset_type_name'));
                    $('#modal_asset_type').modal('show');
                    // console.log($('#asset_type_id').val());
                  });

            };

            LoadAssetType();

            $('#modal_asset_type').on('hidden.bs.modal', function () {
                $('#asset_type_id').val('');
                $('#asset_type_name').val('');
            })

            $('#asset_type_form').on('submit',(function(e) {
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
                  success: function(data) {

                    if(data.success == true) {

                          $('#btnSubmit').attr("disabled", false);
                          $("#btnSubmit").val('Submit');
                          alert(data.message);
                          LoadAssetType();

                          $("#asset_type_id").val('');
                          $("#asset_type_name").val('');
                          $('#modal_asset_type').modal('hide');
                    }else{
                        $('#btnSubmit').attr("disabled", false);
                        $("#btnSubmit").val('Submit');
                        $("#asset_type_name_error").text(data.message);
                    }
                  }
              });
            }));


          // End Asset type ===========================================================================================





          // Brand Start ===========================================================================================

              function dataloadBrand (){

                    $.ajax({

                        type:   'POST',
                        url :   "{{URL::to('/')}}/brand_list",
                        headers:{
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                        dataType: 'json',
                            success: function(data) {
                                var dataSet = data.data;
                                table = $('#brand-table').DataTable( {
                                    destroy:    true,
                                    paging:     true,
                                    searching:  true,
                                    ordering:   true,
                                    bInfo:      true,
                                    "data":     dataSet,

                                "columns": [

                                    { "data": "id" },
                                    { "data": "brand_name" },
                                    { "data": "Link",
                                        "mRender": function(data, type, full) {
                                            return '<a  data-brand_id="' + full.id + '" data-brand_name="' + full.brand_name + '"  class="btn btn-primary btn-flat btn-sm showme"> <span class="glyphicon glyphicon-edit">Edit</a>';
                                        }
                                    },
                                    { "data": "Link",
                                        "mRender": function (data, type, full) {
                                        return '<form action="{{URL::to('/')}}/brand/'+full.id+'"  method="POST"> {{ csrf_field() }} {{ method_field('DELETE') }} <button type="submit" class="btn btn-danger" title="delete" onclick="return confirm(&#39;Are you sure you want to delete this item?&#39;);">Delete</button></form>';
                                        }
                                    },
                                ],
                                "order": [[0,'asc']]
                                });
                            }
                    });

                    $('#brand-table').on('click', '.showme', function() {
                      $('#brand_id').val($(this).data('brand_id'));
                      $('#brand_name').val($(this).data('brand_name'));
                      $('#modal_brand').modal('show');
                      // console.log($('#brand_id').val());
                    });

              };

              dataloadBrand();

            $('#modal_brand').on('hidden.bs.modal', function () {
                $('#brand_id').val('');
                $('#brand_name').val('');
            })

            $('#brand_form').on('submit',(function(e) {
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
                    success:function(data) {
                        if(data.success == true) {
                            $('#btnSubmit').attr("disabled", false);
                            $("#btnSubmit").val('Submit');
                            alert(data.message);
                            // window.location.replace('/asset');
                            // $("#BrandBody").load(location.href + " #BrandBody");

                            dataloadBrand();

                            $("#brand_name").val('');
                            $("#brand_id").val('');
                            $('#modal_brand').modal('hide');


                      }else{
                            $('#btnSubmit').attr("disabled", false);
                            $("#btnSubmit").val('Submit');
                            $("#brand_name_error").text(data.message);
                      }
                    }
                });
              }));

          // End Asset Brand===========================================================================================





          // Asset Return Type Start ===========================================================================================

            function dataloadReturnType (){

               $.ajax({
                  type:   'POST',
                  url :   "{{URL::to('/')}}/return_type_list",
                  headers:{
                              'X-CSRF-TOKEN': '{{ csrf_token() }}'
                          },
                  dataType: 'json',
                      success: function(data) {
                          var dataSet = data.data;
                          table = $('#assetReturnType-table').DataTable( {
                              destroy:    true,
                              paging:     true,
                              searching:  true,
                              ordering:   true,
                              bInfo:      true,
                              "data":     dataSet,

                          "columns": [

                              { "data": "id" },
                              { "data": "asset_return_type_name" },
                              { "data": "return_status_des" },
                              { "data": "Link",
                                  "mRender": function(data, type, full) {
                                      return '<a  data-asset_return_type_id="' + full.id + '" data-asset_return_type_name="' + full.asset_return_type_name + '" data-return_status="'+full.return_status+'" class="btn btn-primary btn-flat btn-sm showme"> <span class="glyphicon glyphicon-edit">Edit</a>';
                                  }
                              },
                              { "data": "Link",
                                  "mRender": function (data, type, full) {
                                  return '<form action="{{URL::to('/')}}/assetreturntype/'+full.id+'"  method="POST"> {{ csrf_field() }} {{ method_field('DELETE') }} <button type="submit" class="btn btn-danger" title="delete" onclick="return confirm(&#39;Are you sure you want to delete this item?&#39;);">Delete</button></form>';
                                  }
                              },
                          ],
                          "order": [[0,'asc']]
                          });
                      }
                  });

                $('#assetReturnType-table').on('click', '.showme', function() {
                    $("#asset_return_type_id").val($(this).data('asset_return_type_id'));
                    $('#asset_return_type_name').val($(this).data('asset_return_type_name'));
                    $('#return_status').val($(this).data('return_status'));
                    $('#modal_asset_return_type').modal('show');
                    console.log($('#asset_return_type_id').val());
                });
            };

            $('#modal_asset_return_type').on('hidden.bs.modal', function () {
                $("#asset_return_type_id").val('');
                $('#asset_return_type_name').val('');
                $('#return_status').val(1);
            })

            dataloadReturnType();

            $('#asset_return_type_form').on('submit',(function(e) {
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
                          alert(data.message);
                          dataloadReturnType();
                          $("#asset_return_type_id").val('');
                          $("#asset_return_type_name").val('');
                          $('#modal_asset_return_type').modal('hide');


                    }else{
                          $('#btnSubmit').attr("disabled", false);
                          $("#btnSubmit").val('Submit');
                          $("#asset_return_type_name_error").text(data.message);
                    }
                  }
              });
            }));

          //End Asset Return Type ===========================================================================================

      });

  </script>

@endsection
