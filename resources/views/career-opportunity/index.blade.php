<title>Career Opportunity</title>

<link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">

<style>
@import url(https://fonts.googleapis.com/css?family=Ubuntu);

body {
    font-family: "Ubuntu", sans-serif;
    background-color: #e2e2e2a9;
}
.container {
   padding-top: 30px;
}
article {
  margin-bottom: 10px;
}
article h4.heading {
    padding-left: 15px
}
.img-responsive {
    width: 150px;
    height: 150px;
}
figcaption {
    margin-top: 0;
    margin-left: 15px;
    background: #8480801c;
    padding: 0px 25px;
    border-radius: 10px;
}

figcaption > h3 {
    font-weight: bolder;
    font-size: xx-large;
}
.table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
    border-bottom-width: 0;
}

/* accordion table css */
.table tr {
    cursor: pointer;
}
.table{
    background-color: #fff !important;
}
.hedding h1{
    color:#fff;
    font-size:25px;
}
.main-section{
    margin-top: 120px;
}
.hiddenRow {
    padding: 0 4px !important;
    /* background-color: #eeeeee; */
    font-size: 13px;
}
/* /accordion table css */
</style>




<div class="container">
    <div class="row">
      <div class="col-lg-offset-2 col-lg-8">
        <section class="panel panel-default">
            <div class="panel-body">
                <article class="panel-body">
                    <figure style="display: flex">
                      <img src="dist/img/com-logo.jpg" class="img-thumbnail img-circle img-responsive" alt="me">
                      <figcaption>
                        <h3>{{$companyInfo[0]->company_name}}</h3> 
                          <p style="font-weight: 600; color:gray"> 
                                {{$companyInfo[0]->address}}
                              <br> Tel. {{$companyInfo[0]->contact_number}} <br> E-mail: {{$companyInfo[0]->email}} <br>  Website: {{$companyInfo[0]->website}} 
                          </p>
                      </figcaption>
                    </figure>
                </article>
                <br>
                <article class="panel-body">
                    <h4 class="heading">
                        <strong>Career Opprtunity</strong>
                    </h4>
                    <hr>
    
                    <div class="table-responsive">

                        <table class="table table-bordered table-hover" style="border-collapse:collapse;margin-bottom:0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%">#</th>
                                    <th class="text-center" style="width: 20%">No. of Vacancy</th>
                                    <th class="text-center" style="width: 40%">Position</th>
                                    <th class="text-center" style="width: 20%">Deadline</th>
                                    <th class="text-center" style="width: 15%">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requsitions as $requsition)
                                    <tr colspan="6" data-toggle="collapse" data-target="#requsition-{{ $requsition->id }}" class="accordion-toggle">
                                        <td class="text-center">{{ $loop->index+1 }}</td>
                                        <td class="text-center">{{ $requsition->vacancy }}</td>
                                        <td class="text-center">
                                            <p>{{ $requsition->designation->designation_name }} ({{ $requsition->deparment->depertment_name }})</p>
                                        </td>
                                        <td class="text-center">
                                            {{ date('F d, Y', strtotime($requsition->ending_date)) }}
                                        </td>

                                        <td class="text-center">
                                            <button type="button" class="btn btn-info">Show</button>
                                        </td>
                                    </tr>
                                    <tr class="p">
                                        <td colspan="6" class="hiddenRow">
                                            <section id="requsition-{{ $requsition->id }}" class="accordian-body collapse p-3">
                                                <article class="panel-body">
                                                    <div class="row">
                                                        <div style="width: 100%">
                                                            <div class="col-lg-12 col-md-12 col-xs-12">
                                                                <h3 style="margin-top: 0;color: red;">{{ $requsition->job_title }}</h3>
                                                            
                                                                <div style="display: flex; align-items: center">
                                                                    <h4 style="font-weight: 700;color:#18a218d1">Vacancy :</h4> 
                                                                    <span style="margin-left: 10px;font-weight: 700;color:red">{{ $requsition->vacancy }}</span>
                                                                </div>
                                                
                                                                <div style="display: flex; flex-direction: column;">
                                                                    <h4 style="font-weight: 700;color:#18a218d1">Job Context</h4> 
                                                                    <p style="margin-left: 30px">
                                                                        {{ $requsition->job_context }}
                                                                    </p>
                                                                </div>

                                                                @php

                                                                    $requsitionDetails = DB::SELECT("SELECT 
                                                                                                            c.job_description_group_name,b.job_description
                                                                                                        FROM
                                                                                                            hrm_cv_job_requsition_details a
                                                                                                                JOIN
                                                                                                            hrm_cv_job_description b ON a.hrm_cv_job_description_id = b.id
                                                                                                                AND a.is_active = 1 AND a.hrm_cv_job_requsition_id = $requsition->id
                                                                                                                JOIN
                                                                                                            hrm_cv_job_description_group c ON b.hrm_cv_job_description_group_id = c.id
                                                                                                        Order By c.job_description_group_name");

                                                                    $reserve = 'blank';

                                                                @endphp

                                                
                                                                @foreach ($requsitionDetails as $requsitionDetail)
                                                                
                                                                    @php $rj = $requsitionDetail->job_description_group_name @endphp
                                                                    
                                                                    <div style="display: flex; flex-direction: column;">
                                                                        @if($rj!==$reserve)
                                                                            <h4 style="font-weight: 700;color:#18a218d1">{{ $requsitionDetail->job_description_group_name }}</h4>
                                                                            @php $reserve = $requsitionDetail->job_description_group_name @endphp
                                                                        @endif
                                                                        <ul>
                                                                            <li>{{ $requsitionDetail->job_description }}</li>                                
                                                                        </ul>
                                                                    </div>

                                                                @endforeach

                                                
                                                                <div class="row">                                                                   

                                                                    <div class="col-md-6">
                                                                        <div style="display:flex;align-items:center;margin-bottom:10px">
                                                                            <label style="font-weight:700;color:#18a218d1;font-size:16px">Employment Status :</label> 

                                                                            <span style="font-weight:700;margin-left:5px">{{ $requsition->employment_status }}</span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div style="display:flex;align-items:center;margin-bottom:10px">
                                                                            <label style="font-weight:700;color:#18a218d1;font-size:16px">Festival Bonus :</label> 

                                                                            <span style="font-weight:700;margin-left:5px">{{ $requsition->festival_bonus }}</span>
                                                                        </div>
                                                                    </div>

                                                                    @if ($requsition->salary_review !== "N/A")                                                                        
                                                                        <div class="col-md-6">
                                                                            <div style="display:flex;align-items:center;margin-bottom:10px">
                                                                                <label style="font-weight:700;color:#18a218d1;font-size:16px">Salary Review :</label> 
    
                                                                                <span style="font-weight:700;margin-left:5px">{{ $requsition->salary_review }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif                                                                    
                                                                    
                                                                    <div class="col-md-6">
                                                                        <div style="display:flex;align-items:center;margin-bottom:10px">
                                                                            <label style="font-weight:700;color:#18a218d1;font-size:16px">Job Level :</label> 

                                                                            <span style="font-weight:700;margin-left:5px">{{ $requsition->job_level }}</span>
                                                                        </div>
                                                                    </div>

                                                                    @if ($requsition->work_place !== "N/A")
                                                                        <div class="col-md-6">
                                                                            <div style="display:flex;align-items:center;margin-bottom:10px">
                                                                                <label style="font-weight:700;color:#18a218d1;font-size:16px">Work Place :</label> 
    
                                                                                <span style="font-weight:700;margin-left:5px">{{ $requsition->work_place }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    <div class="col-md-6">
                                                                        <div style="display:flex;align-items:center;margin-bottom:10px">
                                                                            <label style="font-weight:700;color:#18a218d1;font-size:16px">Job Location :</label> 

                                                                            <span style="font-weight:700;margin-left:5px">{{ $requsition->job_location }}</span>
                                                                        </div>
                                                                    </div>

                                                                    @if ($requsition->lunch_facilities !== "N/A")
                                                                        <div class="col-md-6">
                                                                            <div style="display:flex;align-items:center;margin-bottom:10px">
                                                                                <label style="font-weight:700;color:#18a218d1;font-size:16px">Lunch Facilities :</label> 
    
                                                                                <span style="font-weight:700;margin-left:5px">{{ $requsition->lunch_facilities }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    <div class="col-md-6">
                                                                        <div style="display:flex;align-items:center;margin-bottom:10px">
                                                                            <label style="font-weight:700;color:#18a218d1;font-size:16px">Gender :</label> 

                                                                            <span style="font-weight:700;margin-left:5px">{{ $requsition->gender }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div style="display: flex;flex-direction:column;">
                                                                            <h4 style="font-weight:700;color:#18a218d1;font-size:16px">Salary Range</h4> 
                                                                            <p>
                                                                                <span style="font-weight:700;">{{ $requsition->salary_range }}</span> (Based on your experiance and skill)                              
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div style="display:flex;align-items:center">
                                                                            <label style="font-weight:700;color:#18a218d1;font-size:16px">Age Range :</label> 
                                                                           
                                                                            <span style="font-weight:700;margin-left:5px;margin-right:5px">{{ $requsition->age_range }}</span> years
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">

                                                                    @php
                                                                        $compensations = DB::table('hrm_cv_job_requsition_compensation as a')
                                                                                            ->join('hrm_cv_compensation as b', 'a.hrm_cv_compensation_id', '=', 'b.id')
                                                                                            ->where('a.hrm_cv_job_requsition_id', $requsition->id)
                                                                                            ->orderBy('b.id')
                                                                                            ->select('b.id', 'b.compensation_name')
                                                                                            ->get();
                                                                    @endphp

                                                                    <div class="col-xs-12 col-md-12" style="margin-top: 20px;">
                                                                        <h4 style="font-weight:700;color:#18a218d1">Compensation</h4>
                                                                        <span style="margin-left: 7px">
                                                                            @forelse ($compensations as $compensation)
                                                                                <span>
                                                                                    {{ $compensation->compensation_name }}{{ !$loop->last ? ',' : '' }}
                                                                                </span>                    
                                                                            @empty
                                                                                N/A
                                                                            @endforelse
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </article>
                                                
                                                <section class="panel-body">
                                                    <div style="display:flex;align-items:center;justify-content:center">
                                                        <div style="font-weight:600;font-size:16px;color:gray;text-align:center;line-height:1.8">
                                                            Carefully read before apply <br>
                                                
                                                            <span style="font-size:12px;color:red">*photograph</span> must be enclosed with the resume <br>
                                                
                                                            <!-- <span style="color:#000">Send Your CV to : <a href="#">forjob@mail.com</a></span> <br> -->
                                                            <span style="font-size:12px;">Application Deadline : <span style="font-size:14px;color:#000;font-weight:600">{{ $requsition->ending_date }}</span></span>
                                                            <br><br>
                                                            <a href="{{ route('apply-for-job', $requsition->id) }}" title="{{ $requsition->designation->designation_name }} ({{ $requsition->deparment->depertment_name }})"  class="btn btn-primary applyModal">Apply Now</a>
                                                        </div>
                                                    </div>
                                                </section>
                                            </section>                                             
                                        </td> 
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>               
        </section>
      </div>
    </div>
  </div>


<!-- apply modal -->
<div class="modal fade" id="showDetaildModal" data-backdrop="static">
    <div id="modalSize" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <span style="font-size:18px;font-weight:700;margin-left:5px">Apply position for</span>
                <span class="modal-title" style="font-size:18px;font-weight:700;margin-left:5px" id="showDetaildModalTile"></span>
            </div>

            <form action="{{ route('submit-application') }}" id="CompensationForm" method="post" enctype="multipart/form-data">

                {{ csrf_field() }}

                <div class="modal-body" id="showDetaildModalBody"></div>

                <div class="modal-footer">
                    <a data-dismiss="modal" class="btn btn-default" href="#">Close</a>
                    <button id="btnSubmit" type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- /apply modal -->




  <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
  <script src="{{asset('bootstrap/js/bootstrap.min.js')}}"></script>

<script>
$('.accordion-toggle').click(function(){
    var hide = $('.hiddenRow').hide();
    var show = $(this).next('tr').find('.hiddenRow').show();
});




$(document).on("click", ".open-details", function (e) {
        e.preventDefault();
        $("#jobDetailsSection").html("");

        $.ajax({
            type: "GET",
            url: $(this).attr('href'),

            success: function (data) {
                $("#jobDetailsSection").html(data);
                $("#jobDetailsSection").removeClass('hidden');
            }
    });
});

$(document).on("click", ".applyModal", function (e) {
    e.preventDefault();

    var title = $(this).attr('title');
    $("#showDetaildModalTile").text(title);

    // $("#showDetaildModal").modal('show');

    $.ajax({
        type: "GET",
        url: $(this).attr('href'),
        
        success: function (data) {

          $(".loadingImg").html("");
          $("#showDetaildModalBody").html(data);
          $("#showDetaildModal").modal('show');
        }
    });
});

$(document).on("click", ".visitcheck", function() {
    var thisRow = $(this).closest('tr');
    var checked = $(this).is(":checked");
    if (checked) {
        thisRow.find('.visitNotes').removeAttr("disabled")
        thisRow.find('.disable-bg').removeAttr("disableBg")
    } else {
        thisRow.find('.visitNotes').attr('disabled', 'true');
        thisRow.find('.disable-bg').attr("disableBg", "true");
    }
});



    $('#CompensationForm').on('submit', function (e) {
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

                    console.log(data.success);

                    if(data.success==true){
                        var erreurs ='<div class="alert alert-success"><ul>';
                        erreurs += '<li>'+data.messages+'</li>';
                        erreurs += '</ul></div>';
                        $('#alert-message').html(erreurs);   
                        $('#alert-message').show(0).delay(4000).hide(0); 


                        $('#showDetaildModalBody').modal('hide');

                    }else{
                        var erreurs ='<div class="alert alert-danger"><ul>';
                        erreurs += '<li>'+data.messages+'</li>';
                        erreurs += '</ul></div>';
                        $('#alert-message').html(erreurs);   
                        $('#alert-message').show(0).delay(6000).hide(0); 

                    }



                        $('#btnSubmit').attr("disabled", false);
                        $("#btnSubmit").val('Submit');

                }

        });
    });

















</script>