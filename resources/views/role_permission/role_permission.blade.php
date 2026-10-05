@extends('layouts.main')
@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection
@section('content')

    <section class="content">

        <div class="box box-default">
            <div class="box-header with-border">
                <h2 class="box-title">Role and Permission</h2>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>

            {!! Form::open(array('url' => 'submit_role_permission', 'id' => 'role-form')) !!}
            <div class="box-body">
                <div class="row">

                    <div class="col-xs-12" style="padding-bottom: 10px;">
                        <table id="role_list" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <?php
                                $count = -1;
                                $holder = DB::table('roles')->get();
                                ?>
                                @if( !empty($holder) )
                                    <td style="width:235px;">Permissions Name</td>
                                    <?php
                                    foreach ($holder as $value) {
                                        $count++;
                                        $role_name = $value->name;
                                    ?>
                                    <td style="width:250px;"> <?php echo $role_name; ?> </td>
                                    <?php } ?>
                                @endif
                            </tr>
                            </thead>

                            <tbody>
                            <?php
                            $holder = DB::table('permissions')->where('isActive', 1)->get();
                            $names = array();
                            $rolei_name = DB::table('roles')->get();

                            foreach ($rolei_name as $value) {
                                $names[] = $value->id;
                            }
                            ?>
                            @if( !empty($holder) )
                                <?php
                                foreach ($holder as $value) {
                                $perm_name    = $value->id;
                                $display_name = $value->name;
                                ?>
                                <tr id="row_id-{{ $perm_name }}">
                                    <td style="width:250px;"> <?php echo $display_name; ?> </td>
                                    <?php
                                    for ($x=0 ; $x <= $count; $x++) {
                                        echo '<td> <input type="checkbox" class="chk" id="'.$perm_name.':'.$names[$x].'" name="permission[]" value="'.$perm_name.':'.$names[$x].'"> </td>';
                                    }

                                    ?>
                                </tr>
                                <?php
                                } ?>
                            @endif
                            </tbody>


                        </table>
                    </div>

                    <div class="col-xs-12" style="padding-bottom: 10px;">
                        <button type="submit" class="btn btn-success pull-right btn-flat">Submit</button>
                    </div>


                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </section>

@endsection

@section('script')
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#role_list').DataTable( {
                paging:         false,
                ordering:       true,
                bInfo:          false,
                searching:      false,
            });
        });
    </script>

    <?php
    $pr_table = DB::table('role_has_permissions')->get();
    if(!empty($pr_table)){
    foreach ($pr_table as $value) {
    $perm_id = $value->permission_id;
    $role_id = $value->role_id;
    $cup1 = DB::table('roles')->select('id')->where('id', '=', $role_id)->get();
    $cup2 = DB::table('permissions')->select('id')->where('id', '=', $perm_id)->get();
    $key_elem = (string)$cup2[0]->id.':'.$cup1[0]->id;
    ?>

    <script>
        $(document).ready(function(){
            $('.chk').each(function(){
                var table_val = $(this).attr('id');
                var dbase_val = '<?php echo $key_elem; ?>';
                var temp = "#" + dbase_val;
                if(table_val == dbase_val) {
                    var id = '#'+table_val;
                    var checkbox = document.getElementById(table_val);
                    $(checkbox).prop("checked",true);
                }
            });
        });
    </script>
    <?php
    }
    }
    ?>
@endsection
