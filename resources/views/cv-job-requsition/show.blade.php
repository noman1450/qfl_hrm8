<link rel="stylesheet" href="{{asset('bootstrap/css/bootstrap.min.css')}}">

<style>
body {
    margin-top:20px;
    background:#eee;
}
/* PROJECTS */
.project-people,
.project-actions {
text-align: right;
vertical-align: middle;
}
dd.project-people {
text-align: left;
margin-top: 5px;
}
.project-people img {
width: 32px;
height: 32px;
}
.project-title a {
font-size: 14px;
color: #676a6c;
font-weight: 600;
}
.project-list table tr td {
border-top: none;
border-bottom: 1px solid #e7eaec;
padding: 15px 10px;
vertical-align: middle;
}
.project-manager .tag-list li a {
font-size: 10px;
background-color: white;
padding: 5px 12px;
color: inherit;
border-radius: 2px;
border: 1px solid #e7eaec;
margin-right: 5px;
margin-top: 5px;
display: block;
}
.project-files li a {
font-size: 11px;
color: #676a6c;
margin-left: 10px;
line-height: 22px;
}

/* PROFILE */
.profile-content {
border-top: none !important;
}
.profile-stats {
margin-right: 10px;
}
.profile-image {
width: 120px;
float: left;
}
.profile-image img {
width: 96px;
height: 96px;
}
.profile-info {
margin-left: 120px;
}
.feed-activity-list .feed-element {
border-bottom: 1px solid #e7eaec;
}
.feed-element:first-child {
margin-top: 0;
}
.feed-element {
padding-bottom: 15px;
}
.feed-element,
.feed-element .media {
margin-top: 15px;
}
.feed-element,
.media-body {
overflow: hidden;
}
.feed-element > .pull-left {
margin-right: 10px;
}
.feed-element img.img-circle,
.dropdown-messages-box img.img-circle {
width: 38px;
height: 38px;
}
.feed-element .well {
border: 1px solid #e7eaec;
box-shadow: none;
margin-top: 10px;
margin-bottom: 5px;
padding: 10px 20px;
font-size: 11px;
line-height: 16px;
}
.feed-element .actions {
margin-top: 10px;
}
.feed-element .photos {
margin: 10px 0;
}
.feed-photo {
max-height: 180px;
border-radius: 4px;
overflow: hidden;
margin-right: 10px;
margin-bottom: 10px;
}
.file-list li {
padding: 5px 10px;
font-size: 11px;
border-radius: 2px;
border: 1px solid #e7eaec;
margin-bottom: 5px;
}
.file-list li a {
color: inherit;
}
.file-list li a:hover {
color: #1ab394;
}
.user-friends img {
width: 42px;
height: 42px;
margin-bottom: 5px;
margin-right: 5px;
}

.ibox {
clear: both;
margin-bottom: 25px;
margin-top: 0;
padding: 0;
}
.ibox.collapsed .ibox-content {
display: none;
}
.ibox.collapsed .fa.fa-chevron-up:before {
content: "\f078";
}
.ibox.collapsed .fa.fa-chevron-down:before {
content: "\f077";
}
.ibox:after,
.ibox:before {
display: table;
}
.ibox-title {
-moz-border-bottom-colors: none;
-moz-border-left-colors: none;
-moz-border-right-colors: none;
-moz-border-top-colors: none;
background-color: #ffffff;
border-color: #e7eaec;
border-image: none;
border-style: solid solid none;
border-width: 3px 0 0;
color: inherit;
margin-bottom: 0;
padding: 14px 15px 7px;
min-height: 48px;
}
.ibox-content {
background-color: #ffffff;
color: inherit;
padding: 15px 20px 20px 20px;
border-color: #e7eaec;
border-image: none;
border-style: solid solid none;
border-width: 1px 0;
}
.ibox-footer {
color: inherit;
border-top: 1px solid #e7eaec;
font-size: 90%;
background: #ffffff;
padding: 10px 15px;
}
ul.notes li,
ul.tag-list li {
list-style: none;
}
dl.dl-horizontal dt {
margin-bottom: 10px;
}
.table-bordered>tbody>tr>td, 
.table-bordered>tbody>tr>th, 
.table-bordered>tfoot>tr>td, 
.table-bordered>tfoot>tr>th, 
.table-bordered>thead>tr>td, 
.table-bordered>thead>tr>th {
    border: 1px solid #f4f4f4 !important;
}
</style>




    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12">
            <div class="ibox" style="margin-bottom: 0">
                <div class="ibox-content" style="padding: 15px 20px 0px 20px;border:none">                   
                    <div class="row">

                        <input type="hidden" name="req_id" value="{{ $requsition->id }}">

                        <div class="col-lg-4">
                            <div style="margin-bottom: 10px">
                                <label>Job Title :</label> <span>{{ $requsition->job_title }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Published Date :</label> <span>{{ date('d-M-Y', strtotime($requsition->published_date)) }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Ending Date :</label> <span>{{ date('d-M-Y', strtotime($requsition->ending_date)) }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Department :</label> <span>{{ $requsition->deparment->depertment_name }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Designation :</label> <span>{{ $requsition->designation->designation_name }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Employment Status :</label> <span>{{ $requsition->employment_status }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Salary Review :</label> <span>{{ $requsition->salary_review }}</span>
                            </div>
                        </div>

                        <input type="hidden" id="buttonHide" value="{{ $requsition->status }}">

                        <div class="col-lg-4" id="cluster_info">
                            <div style="margin-bottom: 10px">
                                <label>Festival Bonus :</label> <span>{{ $requsition->festival_bonus }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Created By :</label> <span>{{ $requsition->user->name }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Approved By :</label> <span>{{ $requsition->approvedBy->name ?? "N/A" }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Status :</label> @if($requsition->status === 1)
                                                            <span class="badge" style="background-color: #f7913c">Pending..</span>
                                                        @elseif($requsition->status === 2)
                                                            <span class="badge" style="background-color: #708bef">Approved</span>
                                                        @elseif ($requsition->status === 3)
                                                            <span class="badge" style="background-color: #708bef">Published</span>
                                                        @elseif($requsition->status === 0)
                                                            <span class="badge" style="background-color: red">Rejected</span>
                                                        @endif
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Activity :</label> @if($requsition->is_active === 1)
                                                            <span class="badge" style="background-color: #708bef">Active</span>
                                                          @else
                                                            <span class="badge" style="background-color: #f7913c">Inactive</span>
                                                          @endif
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Salary Range :</label> <span>{{ $requsition->salary_range }}</span>
                            </div>
                        </div>


                        <div class="col-lg-4" id="cluster_info">                    
                            <div style="margin-bottom: 10px">
                                <label>Age Range :</label> <span>{{ $requsition->age_range }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Job Level :</label> <span>{{ $requsition->job_level }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Work Place :</label> <span>{{ $requsition->work_place }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Job Location :</label> <span>{{ $requsition->job_location }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Lunch Facilities :</label> <span>{{ $requsition->lunch_facilities }}</span>
                            </div>

                            <div style="margin-bottom: 10px">
                                <label>Gender :</label> <span>{{ $requsition->gender }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                            <div style="margin-bottom: 10px">
                                <label>Context :</label> <span>{{ $requsition->job_context }}</span>
                            </div> 

                            <div>
                                <label>Compensation :</label> 
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
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel blank-panel" style="margin-bottom: 0">
    
                                <div class="panel-body" style="padding: 0">    
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab-1">

                                            <div class="row">
                                                <div class="col-md-12 col-xs-12" style="padding-right:0;padding-left:0">
                                                    <div class="table-responsive">
                                                        <table class="modal-table table table-hover table-bordered" style="margin-bottom: 0">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 80%">Job Description</th>
                                                                    <th style="width: 20%">Point</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($requsitionDetails as $item) 
                                                                    <tr>
                                                                        <td>{{ $item->jobDescription->job_description }}</td>
                                                                        <td>{{ $item->point }}</td>
                                                                    </tr>
                                                                @endforeach                    
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                
                                    </div>        
                                </div>        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script>
        var hideValue = $('#buttonHide').val();

        if (hideValue != 1) {
            $('.buttonHide').hide()
        } else {
            $('.buttonHide').show()
        }
    </script>
