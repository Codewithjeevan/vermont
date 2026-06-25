@extends('layouts.app')

@section('page-title')@lang('glob.audit_report')@endsection


@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.audit_report')</li>
</ol>

@endsection

@section('content')


<!-- view table data  -->
<div class="row">
	<div class="col-md-12 table-responsive no-search">
		<div class="row">
			<div class="col-md-offset-6 col-md-6 pull-right">
				<form id="form-search-item" class="form-inline" methode="GET">
					<div class="form-group pull-right margin" style="margin-bottom: 10px;">
						<label for="search">Search :</label> 
						<input type="text" name="searchkey" value="{{ $searchkey }}" class="form-control input-sm " placeholder="search" >
					</div>
				</form>
			</div>
		</div>
		<table class="table table-bordered table-striped datatable-paging-false ">

			<thead>
				<tr>
					<th style="width:2%">#</th>
					<th>@lang('glob.record_type')</th>
					<th>@lang('glob.record_id')</th>
					<th>@lang('glob.user')</th>
					<th>@lang('glob.remark')</th>
					<th>@lang('glob.datetime')</th>
				</tr>
			</thead>

			<tbody>

				@foreach($arrAudits as $index=>$audit)
				<tr>
					<td>{{ $audit->id }}</td> 
					@if($audit->type == 1)
					<td>Item</td>
					@elseif($audit->type == 2)
					<td>Purchase Order</td>
					@elseif($audit->type == 3)
					<td>Purchase</td>
					@elseif($audit->type == 4)
					<td>Purchase Return</td>
					@elseif($audit->type == 5)
					<td>Expiry</td>
					@elseif($audit->type == 6)
					<td>Damage</td>
					@endif					
					<td>{{ $audit->record_id }}</td>					
					<td>{{ (isset($audit->_user->email) ? $audit->_user->email : '')  }}</td>
					<td>{{ $audit->remark}}</td>					
					<td>{{ $audit->created_at->format($date_format) }}</td>
				</tr>
				@endforeach
			</tbody>

			<tfoot class="showSearch">
				<tr>
					<th style="width:2%">#</th>
					<th>@lang('glob.record_type')</th>
					<th>@lang('glob.record_id')</th>
					<th>@lang('glob.user')</th>
					<th>@lang('glob.remark')</th>
					<th>@lang('glob.datetime')</th>
				</tr>
			</tfoot>

		</table>
		<div class="row">
			<div class="col-md-12">
				{{  $arrAudits->links() }}
			</div>
		</div>

	</div>
	<!--Table End-->

</div>

<!--Export Buttons-->
<div class="row export-btn">

	@if(sizeOf($arrAudits) > 0)
	
	<div class="col-md-2 col-sm-4 ">
		<a  class="btn btn-success btn-block " href="{{ url('/reports/audit_report/export?xls=1' ) }}" ><i class="fa fa-file-excel-o" aria-hidden="true" ></i> @lang('glob.export_to_xls')</a>		
	</div>
	<div class="col-md-2 col-sm-4 ">			
		<a  class="btn btn-danger btn-block" href="{{ url('/reports/audit_report/export?pdf=1' ) }}" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> @lang('glob.export_to_pdf') </a>	
	</div>
	<div class="col-md-8"></div>

	@else

	<div class="col-md-2 col-sm-4 ">
		<a  class="btn btn-success btn-block "  disabled ><i class="fa fa-file-excel-o" aria-hidden="true"></i> @lang('glob.export_to_xls')</a>		
	</div>
	<div class="col-md-2 col-sm-4 ">			
		<a  class="btn btn-danger btn-block"  disabled><i class="fa fa-file-pdf-o" aria-hidden="true"></i> @lang('glob.export_to_pdf') </a>	
	</div>
	<div class="col-md-8"></div>

	@endif

</div>	
<!--End Export Buttons-->

@endsection