{{ csrf_field() }}
<div class="box-body">
  <div class="row">
    <div class="col-md-6">
      <div class="col-md-5">
        <div class="form-group">
          <label>Purchase Date</label>
        </div>
      </div>
      <div class="col-md-7">
        <div class="form-group">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            @if ($form_type == 'create')
              <input type="date" id="start_date" name="po_date" class="form-control pull-right" value="<?php echo date('Y-m-d'); ?>" required>
            @endif
            @if ($form_type == 'edit')
              <input type="date" name="po_date" value="{{ old('po_date', $hrmAssetPurchaseMaster->purchase_date??null) }}" class="form-control pull-right" required>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="col-md-2">
        <div class="form-group">
          <label>Note</label>
        </div>
      </div>
      <div class="col-md-10">
        <div class="form-group">
          <input type="text" id="note" name="notes" class="form-control" placeholder="Note" value="{{ old('notes', $hrmAssetPurchaseMaster->note??null) }}" required>
        </div>
      </div>
    </div>
  </div>

  <div class="row" style="overflow: auto;" >
    <div class="col-lg-12">
      <table id="asset_store_table" class="table table-bordered table-hover">
        <thead>
          <tr style="background-color: #199ddb;">
            <th style="text-align: center; width: 25%;max-width: 400px; overflow: hidden;">Item Description</th>
            <th style="text-align: center; width:15%;">Dep. Rate</th>
            <th style="text-align: center; width:15%;">Serial</th>
            <th style="text-align: center; width:15%;">Price</th>
            <th style="text-align: center; width:15%;">Quantity</th>
            <th style="text-align: center; width:15%;">Total</th>
            <th style="text-align: center; width:15%;">Action</th>
          </tr>
          <tr>
            <th style="padding-right: 5px; overflow: hidden;">
              <select class="form-control select2" id="asset_list" style="width: 100%;max-width: 400px; overflow: hidden;">
              </select>
            </th>

            <th style="padding-right: 5px">
                <input type="number" step="any" id="depreciation_rate" class="form-control" placeholder="Depreciation Rate" style="width: 100%">
            </th>

            <th style="padding-right: 5px;">
              <select class="form-control serial" name="serial" id="serial" style="width: 100%;" >
                <option value="1">Yes</option>
                <option value="0">No</option>
              </select>
            </th>

            <th style="padding-right: 5px;">
              <input type="number"  class="form-control" id="price" value="" step="any" placeholder="item Unit Price" style="width: 100%; margin-top: 5px;text-align:center;" onfocus="this.select();">
            </th>


            <th style="padding-right: 5px;">
              <input type="number"  class="form-control" id="qty" value="" placeholder="Quantity" style="width: 100%; margin-top: 5px;text-align:center;" onfocus="this.select();">
            </th>

            <th style="padding-right: 5px;">
              <input type="number"  class="form-control" id="total" value="0" step="any" style="width: 100%; margin-top: 5px;text-align:center;" onfocus="this.select();" readonly>
            </th>

            <th style="padding-right: 5px;">
              <button type="button" id="add" class="btn btn-info btn-block btn-flat"  ><i class="fa fa-plus"></i><span  style="color: white;">Add</span> </button>
            </th>
          </tr>
        </thead>

        <tbody>
          @if($form_type == 'edit')
            @foreach ($hrmAssetStoreLedger as $item)
              <tr>

                <td>
                  <input name="ledger_id[]" class="hidden" value="{{$item->id}}">
                  <select class="form-control select2" name="product_id[]" style="width: 100%;">
                    <option value='{{$item->product_id}}' selected>{{$item->asset_type_name.' | '.$item->brand_name.' | '.$item->model}}</option>
                  </select>
                </td>
                <td>
                    <input type="number" step="any" name="depreciation_rate[]" class="form-control text-center" placeholder="Depreciation Rate" style="width:100%" value="{{ $item->depreciation_rate }}">
                </td>
                @if ($item->serial_no == 'None')
                  <td><input  type="text" name="serial_no[]"   class="form-control"     style="width: 100%;text-align:center;"  value="{{$item->serial_no}}" focusin="this.select();" onclick="this.select();" readonly></td>
                @else
                    <td>
                        <input  type="text" name="serial_no[]"   class="form-control check_serial_no"     style="width: 100%;text-align:center;"  value="{{$item->serial_no}}" focusin="this.select();" onclick="this.select();" required>
                        <input class="hidden ledger_id" value="{{$item->id}}">
                        <p style="margin-bottom:0;color:#af4242;" class="check_serial_no_error"></p>
                    </td>
                @endif
                <td><input  type="text"  id="price2"  name="price_info[]"  class="form-control price2"    style="width: 100%;text-align:center;" data-order="{{$item->price}}" value="{{$item->price}}" focusin="this.select();" onclick="this.select();"></td>
                @if ($item->serial_no != 'None')
                  <td data="{{$item->qty_stockin}}"><input  type="text"  id="qty2"  name="qty_info[]"   class="form-control qty2"     style="width: 100%;text-align:center;"  value="{{$item->qty_stockin}}" focusin="this.select();" onclick="this.select();" readonly></td>
                @else
                  <td data="{{$item->qty_stockin}}"><input  type="text"  id="qty2"  name="qty_info[]"   class="form-control qty2"     style="width: 100%;text-align:center;"  value="{{$item->qty_stockin}}" focusin="this.select();" onclick="this.select();"></td>
                @endif

                <td data="{{$item->qty_stockin*$item->price}}">
                  <input  type="text"   class="form-control tot"  id="tot"    style="width: 100%;text-align:center;"  value="{{$item->qty_stockin*$item->price}}" focusin="this.select();" onclick="this.select();" readonly>
                </td>

                <td>
                  <button type="button" class="btn btn-info btn-block btn-flat btn-danger" id="row_delete">Delete</button>
                </td>
              </tr>
            @endforeach
          @endif
        </tbody>

        <tfoot>
          @if ($form_type == 'edit')
            <tr>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th style="text-align: center;">
                <input id="quantityf" type="text"   class="form-control"     style="width: 100%;text-align:center;" focusin="this.select();" onclick="this.select();" readonly>
              </th>
              <th style="text-align: center;">
                <input id="totalf" type="text"   class="form-control"     style="width: 100%;text-align:center;" focusin="this.select();" onclick="this.select();" readonly>
              </th>
            </tr>
          @else
            <tr>
              <th></th>
              <th></th>
              <th></th>
              <th style="text-align: center;"></th>
              <th></th>
              <th style="text-align: center;"></th>
              <th></th>
            </tr>
          @endif
        </tfoot>
      </table>
    </div>
  </div>
  <button type="submit" class="btn btn-success block btn-flat btn pull-right" >Submit</button>
</div>
