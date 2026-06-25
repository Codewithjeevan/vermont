@extends('layouts.app')

@section('page-title')@lang('glob.bank')@endsection

@section('page-subtitle')@lang('glob.add_edit_bank')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.bank')</li>
</ol>

@endsection

@section('content') 
<div class="bank">

	@if(Common::can_create('bank'))

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a  class="btn bg-purple new-customer" @if(isset($editBank)) href="/common/add/Banks" @endif data-toggle="collapse" data-target="#bankFormDiv" v-on:click="addForm">@lang('glob.add_new')</a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="{{ url('master/banks/xls' ) }}">Excel</a></li>
					<li><a href="{{ url('master/banks/pdf' ) }}">PDF</a></li>				
				</ul>				
		</div>		
	</div>
	<!-- Button div end -->
	
	
	<!-- add/update customer form div -->
	<div id="bankFormDiv" class="collapse @if (session('open') || isset($open) || isset($editBank) || !$errors->isEmpty() ) in @endif">
		@if (isset($editBank)) 
		{{ Form::model($editBank, ['url'=>array('master/bank',$editBank->id), 'method'=>'PUT','id'=>'bankForm']) }}
		@else 
		{{ Form::open(['action'=>'BanksController@store','id'=>'bankForm']) }}<!-- form action for store -->
		@endif
		<div class="row customer-box">  
			<div class="col-md-12">
				<div class="box box-primary">
					<div class="box-body">
						<div class="row">

							<!-- bank name -->
							<div class="col-md-5">
								<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
									{!! Form::label('title', trans('glob.bank')) !!}<span style="color:red">*</span>
									<div class="input-group">
										<div class="input-group-addon"> <i class="fa fa-bank" aria-hidden="true"></i> </div>
										{!! Form::text('name', null, 
										array('required',  'class'=>'form-control input-sm',  'placeholder'=>trans('glob.bank'))) !!}
									</div>

									@if ($errors->has('name'))
									<span class="help-block">
										<strong>{{ $errors->first('name') }}</strong>
									</span>
									@endif

								</div>
							</div>
							<!-- End bank name -->

						</div>
						<!-- .row -->
					</div>
					<!-- .box-body -->
					<div class="box-footer">

						<div class="form-group">
							<button type="submit" class="btn btn-primary " v-on:click="getLoader($event)" >
								<i class="fa fa-floppy-o"></i>
								@if (isset($editBank)) @lang('glob.update') @else @lang('glob.save') @endif
							</button>
							<a  class="btn btn-cyan" v-on:click="resetForm()"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
							<a @if (isset($editBank)) href="{{ url('master/bank') }}" @endif class="btn btn-danger add-bank" data-toggle="collapse" data-target="#bankFormDiv" v-on:click="cancelForm">@lang('glob.cancel')</a>
						</div>

					</div>
					<!-- box-footer -->

				</div>
				<!-- .box .box-primary -->

			</div>
			<!-- .col-md-12 -->

		</div>
		<!--end .row .customer-box -->
		{{ Form::close() }}

	</div>


	@endif

	<!-- view table data  -->
	<div class="row">
		<div class="col-md-12 table-responsive">

			<table class="table table-bordered table-striped datatable1 ">
				
				<thead>
					<tr>
						<th style="width:2%">#</th>
						<th>@lang('glob.bank')</th>
						<th>@lang('glob.created_date')</th>

						@if(Common::can_update('bank') || Common::can_delete('bank'))

						<th style="width:4%">@lang('glob.actions')</th>

						@endif

					</tr>
				</thead>
				
				<tbody>

					@foreach($bank as $row)
					<tr>
						<td>{{$row->id}}</td>
						<td>{{$row->name}}</td>
						<td>
							{{ $row->created_at->format('jS F, Y') }} <br>
							<small>
								<em> @lang("glob.updated_at") {{ $row->updated_at->diffForHumans() }}</em>
							</small>
						</td>

						@if(Common::can_update('bank') || Common::can_delete('bank'))

						<td>

							@if(Common::can_update('bank'))

							<a href="{{route('bank.edit',$row->id)}}" v-on:click="getLoader($event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>
							
							@endif
							@if(Common::can_delete('bank'))

							<a id='{{ $row->id }}' v-on:click="deleteBank({{ $row->id }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 
							
							@endif
						</td>

						@endif

					</tr>

					@endforeach
				</tbody>

				<tfoot class="showSearch">
					<tr>
						<th style="width:2%">#</th>
						<th class="search">@lang('glob.bank')</th>
						<th>@lang('glob.created_date')</th>

						@if(Common::can_update('bank') || Common::can_delete('bank'))
						
						<th style="width:4%">@lang('glob.actions')</th>

						@endif

					</tr>
				</tfoot>

			</table>

		</div>
		{{-- .col-md-12 --}}
	</div>
	<!-- view table data end -->

</div>

@endsection