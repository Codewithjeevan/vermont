@extends('layouts.app')

@section('page-title')@lang('glob.location')@endsection

@section('page-subtitle')@lang('glob.add_edit_location')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-home"></i> @lang('glob.home')</a></li>
	<li><a href="#"><i class="fa fa-user"></i> @lang('menu.masters')</a></li>
	<li class="active">@lang('glob.location')</li>
</ol>

@endsection

@section('content')
<div class="location"> 

	<!-- view table data  -->
	<div class="row">

		<div class="col-md-12 table-responsive">

			<table class="table table-bordered table-striped datatable1">
				<thead>
					<tr>
						<th style="width:10%">#</th>						
						<th>@lang('glob.location')</th> 
						<th>@lang('glob.name')</th>
						<th>@lang('supplier.tin_no')</th>						

						
					</tr>
				</thead>
				<tbody>

					

					<tr>
						<td>aaaa</td>						
						<td>aaaaaaaa</td>
						<td>aaaaaaaa</td>						
						<td>aa</td>			

					</tr>
					<tr>
						<td>aaaa</td>						
						<td>aaaaaaaa</td>
						<td>aaaaaaaa</td>						
						<td>aa</td>			

					</tr>

					<tr>
						<td>aaaa</td>						
						<td>aaaaaaaa</td>
						<td>aaaaaaaa</td>						
						<td>aa</td>			

					</tr>

					<tr>
						<td>aaaa</td>						
						<td>aaaaaaaa</td>
						<td>aaaaaaaa</td>						
						<td>aa</td>			

					</tr>


					

					
				</tbody>
				<tfoot class="showSearch">
					<tr>

						<th style="width:10%">#</th>					
						<th>@lang('glob.location')</th> 
						<th>@lang('glob.name')</th>
						<th>@lang('supplier.tin_no')</th>					

					</tr>
				</tfoot>
			</table>

		</div>
	</div>
	<!-- view table data end -->
</div>

@endsection