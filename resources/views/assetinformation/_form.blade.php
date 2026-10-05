{{ csrf_field() }}

  <div class="col-lg-6 col-md-6 col-xs-12">
    <div class="box-body">
      <div class="col-lg-12 col-md-12 col-xs-12 personal-info">

        <div class="form-group has-feedback {{ $errors->has('description') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <label class="col-lg-3 control-label">Short Description</label>
          <div class="col-lg-9">
              <input type="text" class="form-control" name="description" placeholder="Description..." value="{{ old('description',$asset->description??null) }}" required autofocus>
            @if ($errors->has('description'))
                <span class="help-block">
                    <strong>{{ $errors->first('description') }}</strong>
                </span>
            @endif
          </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('asset_type_id') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <label class="col-lg-3 control-label">Asset Type</label>
          <div class="col-lg-9">
            <select id="asset_type_id" name="asset_type_id" class="form-control" required>
                @if($form_type =='edit')
                 <option value="{{ $asset->asset_type_id }}">{{ $asset->asset_type_name }}</option>
                @endif
            </select>
            @if ($errors->has('asset_type_id'))
                <span class="help-block">
                    <strong>{{ $errors->first('asset_type_id') }}</strong>
                </span>
            @endif
          </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('brand_id') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <label class="col-lg-3 control-label">Brand</label>
          <div class="col-lg-9">
            <select id="brand_id" name="brand_id" class="form-control" required>
                @if($form_type =='edit')
                 <option value="{{ $asset->brand_id }}">{{ $asset->brand_name }}</option>
                @endif


            </select>
            @if ($errors->has('brand_id'))
                <span class="help-block">
                    <strong>{{ $errors->first('brand_id') }}</strong>
                </span>
            @endif
          </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('model') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <label class="col-lg-3 control-label">Model</label>
          <div class="col-lg-9">
              <input type="text" class="form-control" name="model" placeholder="Model..." value="{{ old('model',$asset->model??null) }}"  autofocus autocomplete="off">
            @if ($errors->has('model'))
              <span class="help-block">
                  <strong>{{ $errors->first('model') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group has-feedback {{ $errors->has('model') ? ' has-error' : '' }} col-lg-12 col-md-12 col-xs-12">
          <label class="col-lg-3 control-label">Depreciation Rate</label>
          <div class="col-lg-9">
              <input type="number" step="any" min="1" class="form-control" id="depreciation_rate" name="depreciation_rate" placeholder="Depreciation Rate..." value="{{ old('depreciation_rate',$asset->depreciation_rate??null) }}" required>

                @if ($errors->has('depreciation_rate'))
                    <span class="help-block">
                        <strong>{{ $errors->first('depreciation_rate') }}</strong>
                    </span>
                @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  @if ($form_type == 'edit')
{{--     <div class="col-lg-3 col-md-3 col-xs-12">
      <div class="col-lg-4 col-md-4 col-xs-12 text-center">
        <img height="150" style="vertical-align: middle" id="imgup" src="{{asset('asset_images/'.$asset->imgpath)}}" alt="your image" />
        <input  type='file' name="imgpath" value="{{$asset->imgpath}}" onchange="readURL(this);" />
      </div>
    </div> --}}
  @endif

  @if ($form_type == 'create')
{{--     <div class="col-lg-6 col-md-6 col-xs-12">
      <div class="col-lg-4 col-md-4 col-xs-12 text-center">
        <div class="kv-avatar">
          <div class="file-loading">
            <input id="avatar" name="imgpath" type="file">
          </div>
        </div>
        <div class="kv-avatar-hint"><small>Select file < 1500 KB</small></div>
      </div>
    </div> --}}
  @endif

  <div class="form-group" style="padding-left: 50px">
    <label class="col-md-3 control-label"></label>
    <div class="col-md-8">
      <input type="submit" class="btn btn-success block btn-flat pull-left" value="Submit" id="btnSubmit">
      <span></span>
    </div>
  </div>

@section('script')

  <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

  <script type="text/javascript">
      isNegative("#depreciation_rate")
    

    $('#asset_type_id').select2({
      placeholder: 'Enter Asset Type',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/asset_type_list_data',
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });

    $('#brand_id').select2({
      placeholder: 'Enter Brand',
      allowClear: true,
      ajax: {
        dataType: 'json',
        url: '{{URL::to('/')}}/brand_list_data',
        headers:{
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
        delay: 250,
        data: function(params) {
          return {
            term: params.term
          }
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data
          };
        },
        cache: true
      }
    });


   function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
          $('#imgup').attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      }
    }




  </script>



@endsection
