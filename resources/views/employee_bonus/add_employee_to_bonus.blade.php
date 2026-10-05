
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

<form action="{{ url('/add_employee_to_bonus') }}" data-table-name="#list_table" class="dynamicFormSubmit" method="post">
    @csrf

    <div class="form-group">
        <label for="bonus_name">Bonus</label>
        <select class="form-control bonus_name_model" name="bonus_name" id="bonus_name"></select>
    </div>

    <div class="form-group">
        <label for="hrm_employee_id">Employee</label>
        <select class="form-control" name="hrm_employee_id" id="hrm_employee_id"></select>
    </div>

    <div class="form-group" style="display: flex; justify-content: end;">
        <button type="submit" class="btn btn-success submit-button">Submit</button>
    </div>
</form>

<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>

<script>
    select2Dropdown(
        "#hrm_employee_id",
        "{{ url('/join_employee_list') }}",
        "Search Employee"
    )

    $bonusData= $('.bonus_name_model').select2({
      placeholder: 'Select a Bonus Name',
      allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/process_bonus_list",
            delay: 250,
          data: function(params) {
              return {
                term: params.term
              }
          },
            processResults: function (data, params) {
              params.page = params.page || 1;
              return {
                results: data,
                pagination: {
                  more: (params.page * 30) < data.total_count
                }
              };
            },
            cache: true
        }
    });
</script>
