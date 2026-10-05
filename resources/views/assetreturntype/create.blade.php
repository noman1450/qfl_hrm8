{!! Form::open(['method'=>'POST', 'action'=>['AssetReturnTypeController@store'],'onkeypress'=> "return event.keyCode != 13;", 'id'=>'asset_return_type_form']) !!}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="groupAddLabel">Add/Update Asset Type</h4>
</div>
<div class="modal-body" style="padding: 0px;">
    <div class="col-lg-12 entry_panel_body ">
      <input type="hidden" class="form-control "  id="asset_return_type_id" name="asset_return_type_id" />
    </div>
</div>
<div class="modal-body" style="padding: 0px;">
    <div class="col-lg-12 entry_panel_body ">
      <input type="text" class="form-control "  id="asset_return_type_name" name="asset_return_type_name" placeholder="Asset Return Type Name" style="width: 100%; margin-left: 5px; margin-top: 10px; margin-bottom: 10px;" required/>
    </div>

    <div class="col-lg-12 entry_panel_body text-danger" id="asset_return_type_name_error"></div>
</div>


<div class="modal-body" style="padding: 0px;">
    <div class="col-lg-12 entry_panel_body ">
       <select class="form-control" name="return_status" id="return_status">
           <option value="1">Reuse</option>
           <option value="2">Destroy</option>
       </select>
    </div>
</div>



<div class="modal-footer">
    <button type="button" class="btn btn-default closeId" data-dismiss="modal">Close</button>
    <input type="submit" class="btn btn-success btn-flat pull-right" value="Submit" id="btnSubmit">
</div>
{!! Form::close() !!}
