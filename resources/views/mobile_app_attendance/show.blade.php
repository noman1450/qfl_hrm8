<style>
.card-5 {
    box-shadow: 0 19px 38px rgba(0,0,0,0.30), 0 15px 12px rgba(0,0,0,0.22);
}
</style>
<div class="row">
    <div class="col-md-12">
            <div  >
                <label class="col-md-2">Name</label>
                <label class="col-md-10">: {{ $employeeAttendance->employee_name }}</label>
            </div>
            <div  >
                <label class="col-md-2">Designation</label>
                <label class="col-md-10">: {{ $employeeAttendance->designation_name }}</label>
            </div>

            <div  >
                <label class="col-md-2">Date Time</label>
                <label class="col-md-10">: {{ date('d-M-Y h:i:s a', strtotime($employeeAttendance->date_time)) }}</label>

            </div>
    </div>
    <div class="col-md-6">
        <div class="card card-5" style="text-align: center;">
            <h4 style="margin-top:0;">{{ $employeeAttendance->status === 1 ? 'In time image' : 'Out time image' }}</h4>
            <img  src="{{asset($employeeAttendance->attachment)}}" alt="" height="350" width="373">
        </div>

    </div>

    <div class="col-md-6">
        <div class="card card-5">
            <iframe src="{{url('leaflet_map/'.$employeeAttendance->id)}}" name="iframe_a" style="height:373px;width:100%; border:none;"></iframe>
        </div>
    </div>

</div>
