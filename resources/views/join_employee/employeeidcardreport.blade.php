@extends('layouts.main')

@section('styles')
    <style>
        @media print {
            @page {
                margin: 1cm;
            }
            .break_section {
                page-break-after: always !important;
            }
        }
    </style>
@endsection

@section('content')

<div class="box ">
    <a href="{{ URL::to('employeeidcard')}}" class="btn btn-success hidden-print">Back</a>
    <input type="button" onclick="printDiv('printableArea')" value="Print Me"  class="hidden-print btn btn-info"/>


	<div class="row" id="printableArea">

        @foreach ($data as $keys)
            <div class="col-lg-4 col-md-4 col-xs-4 hello" style="border: 1px solid gray; height: 320px;width: 220px; padding-bottom: 18px; border-style: dotted;">
                <div style="box-shadow: 0px 0px 3px black!important; padding-top: 10px;">
                    <div class="header_logo">
                        <div>
                            <img src="{{asset('dist/img/com-logo999.jpg')}}" class="img-circle" alt="User Image" style="height: 40px;width: 80px; display: block;margin-left: auto; margin-right: auto;">
                        </div>
                        <div>
                            <label style="text-align: center; color: #007F3D!important; font-size: 13px;margin-left: 25px;"> {{ config('configaration.company_name')}}</label>
                        </div>
                    </div>
                    <div class="image" style="text-align: center;">
                        <div >
                            <img  id="blah" src="{{asset('employee_image/'.$keys->Images)}}" alt="your image"  style="width: 75px;height: 75px; display: block;margin-left: auto; margin-right: auto; border:1px solid black"  />

                        </div>
                        <div >
                            <label style="color: #007F3D !important;font-size: 12px;">{{$keys->employee_name}}</label>
                        </div>
                    </div>
                    <div class="body" style="text-align: center; font-size: 10px;">
                        <div>
                            Employee Id :	{{$keys->employee_code}}
                        </div>
                        <div>
                            Department : {{$keys->depertment_name}}
                        </div>
                        <div>
                            Designation : {{$keys->designation_name}}
                        </div>
                        <div style="color: red!important;">
                            Blood Group : {{$keys->blood_group}}
                        </div>
                        <div>
                            NID : {{$keys->nid}}
                        </div>
                        <div>
                            Joining/Issue Date : {{$keys->joining_date}}
                        </div>
                        <div>
                            <br>
                            ---------------------------<br>
                            Employee Signature
                        </div>
                    </div>
                </div>
            </div>

		@endforeach
	</div>
</div>
@endsection

@section('script')


<script>

function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     console.log(printContents);
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
</script>
@endsection
