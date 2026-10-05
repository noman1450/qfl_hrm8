@extends('layouts.main')

@section('content')
<div class="box box-danger" style="margin-bottom:0">
  <div class="box-body">
    <div class="nav-tabs-custom" style="margin-bottom:0;box-shadow:none">
      <ul class="nav nav-tabs">
        <li class="active">
		  	  <a href="#jobDescription" data-toggle="tab" aria-expanded="true">
			      Backup Database
			    </a>
		    </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane active" id="jobDescription">			
          <div class="row">
            <div class="box-header with-border">
                <a href="{{ route('database.backup.create') }}" class="btn-success btn btn-sm button pull-left" style="font-size: 12px; font-weight: bold">
                  Manual Backup
                </a>
            </div>

            <div class="form-group col-lg-12 col-md-12 col-xs-12" style="overflow:auto;margin-bottom:0">
              <table id="jobDescriptionDatatable" class="table text-center table-bordered table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Backup File</th>
                    <th>File Size</th>
                    <th>Created</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                  <tbody>
                    @foreach ($files as $file)
                        @if ($file == '.' || $file == '..')
                          @continue
                        @endif

                        {{-- @if (pathinfo($filePath.$file, PATHINFO_EXTENSION) === '.zip') --}}
                          <tr>
                            <td>{{ $loop->index-1 }}</td>                        
                            <td>{{ $file }}</td>
                            <td>{{ readable_file_size(filesize($filePath.$file)) }}</td>
                            <td>{{ file_creation_time($filePath.$file) }}</td>
                            <td>
                                <a title="Download" href="{{ route('database.download', $file) }}" class="btn btn-info btn-sm">
                                    Download
                                </a>
                                <a title="Delete" href="{{ route('database.delete', $file) }}" class="btn btn-danger btn-sm">
                                    Delete                            
                                </a>
                            </td>
                          </tr>
                        {{-- @endif --}}
                      
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
@stop