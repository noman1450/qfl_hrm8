{!! Form::open(['method'=>'POST', 'action'=>['SkillController@store'],'onkeypress'=> "return event.keyCode != 13;", 'id'=>'skill_form']) !!}
  <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      <h4 class="modal-title" id="groupAddLabel">Add/Update Skill</h4>
  </div>
  <div class="modal-body" style="padding: 0px;">
      <div class="col-lg-12 entry_panel_body ">
          <input type="hidden" class="form-control "  id="skill_id" name="skill_id" />
      </div>
  </div>
  <div class="modal-body" style="padding: 0px;">
      <div class="col-lg-12 entry_panel_body ">
          <input type="text" class="form-control "  id="skill_name" name="skill_name" placeholder="Skill Name" style="width: 100%; margin-left: 5px; margin-top: 10px; margin-bottom: 10px;" required/>
      </div>
  </div>
  <div class="modal-footer">
      <button type="button" class="btn btn-default closeId" data-dismiss="modal">Close</button>
      <input type="submit" class="btn btn-success btn-flat pull-right" value="Submit" id="btnSubmit">
  </div>
{!! Form::close() !!}