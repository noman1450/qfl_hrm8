<style>
td[disableBg] {
    background-color: #eee;
    cursor: not-allowed;
}
</style>




<div class="row">

    <div class="col-md-12"> 

        <h5 style="color:red; text-align: center;font-weight: 700;border-style: dotted; padding: 10px;"> Your application will be considered canceled, if any ticked mark does not match with you. </h5> 

    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="applicant_name">Applicant Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" value="" id="applicant_name" name="applicant_name" required placeholder="Your full name here..">


            @if ($errors->has('applicant_name'))
                <span class="help-block">
                    <p class="text-danger">{{ $errors->first('applicant_name') }}</p>
                </span>
            @endif
        </div>
    </div>

    <input type="hidden" name="hrm_cv_job_requsition_id" value="{{ $requsitionDetails[0]->hrm_cv_job_requsition_id }}">

    <div class="col-md-6">
        <div class="form-group">
            <label for="applicant_contact_no">Contact Number <span class="text-danger">*</span></label>
            <input type="text" class="form-control" value="" id="applicant_contact_no" name="applicant_contact_no" required placeholder="Contact Number..">
            
            @if ($errors->has('applicant_contact_no'))
                <span class="help-block">
                    <p class="text-danger">{{ $errors->first('applicant_contact_no') }}</p>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="applicant_email">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" value="" id="applicant_email" name="applicant_email" required placeholder="Enter your email address..">
            
            @if ($errors->has('applicant_email'))
                <span class="help-block">
                    <p class="text-danger">{{ $errors->first('applicant_email') }}</p>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="present_address">Present Addess <span class="text-danger">*</span></label>
            <input type="text" class="form-control" value="" id="present_address" name="present_address" required placeholder="Present address..">
            
            @if ($errors->has('present_address'))
                <span class="help-block">
                    <p class="text-danger">{{ $errors->first('present_address') }}</p>
                </span>
            @endif
        </div>
    </div>
</div>


@foreach ($requsitionDetails as $requsitionDetail)

<div class="row">
    <div class="col-xs-12 col-md-12 col-lg-12">
        <h4 style="font-weight: 700;color:#18a218d1">

             @php $rj = $requsitionDetail->job_description_group_name @endphp
             
             @if($rj!==$reserve)
                
                {{ $requsitionDetail->job_description_group_name }}
                <span style="font-size:14px;color:red">(Select only which is match with you)</span>
                
                @php $reserve = $requsitionDetail->job_description_group_name @endphp
             @endif
        </h4> 

        <table class="table table-bordered" style="margin-bottom: 0">
            <tbody>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="hrm_cv_job_requsition_details_id[]" class="visitcheck" value="{{ $requsitionDetail->id }}">
                        </td>
                        <td class="text-center disable-bg" disableBg>{{ $loop->index+1 }}</td>
                        <td style="width: 60%;font-size: 14px;" class="disable-bg" disableBg>
                            {{ $requsitionDetail->job_description }}
                        </td>
                        <td class="text-center disable-bg" disableBg>
                            <input name="note[]" class=" visitNotes" placeholder="Note.." disabled>
                        </td>
                    </tr>                    

            </tbody>
        </table>                            
    </div>
</div>
@endforeach



<div class="row">
    <div class="col-md-6">
        <div class="form-group" style="margin-top:20px;margin-bottom:0">
            <label for="attachment">Attachment your CV <span class="text-danger">*</span> <span style="font-size:12px;color:darkred">(only .pdf format accepted)</span></label>
            <input type="file" class="form-control" name="attachment" accept=".pdf" required>
        </div>
    </div>

        
    <div class="col-lg-12 col-md-12 col-xs-12">
           <div id="alert-message"></div>
    </div>



</div>

