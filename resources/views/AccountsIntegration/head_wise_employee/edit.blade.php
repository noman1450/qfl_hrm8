@extends('layouts.main')

@section('content')
<div class="box box-default">
    <div class="box-body">
        <h3>Edit Head Wise Employee Setup</h3>

        @include('AccountsIntegration.head_wise_employee._form', ['headWiseEmpSetup' => $headWiseEmpSetup])
    </div>
</div>
@endsection
