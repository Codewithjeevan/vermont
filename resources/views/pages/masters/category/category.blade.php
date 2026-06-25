@extends('layouts.app')

@section('page-title')@lang('glob.category')@endsection

@section('page-subtitle')@lang('category.add_edit_category')@endsection

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.category')</li> 
</ol>

@endsection

@section('content')
<div class="category">

	@if(Common::can_create('category')) 

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom"> 
			<a  @if(isset($editCategory)) href="/common/add/Categories" @endif class="btn bg-purple new-customer" data-toggle="collapse" data-target="#categoryFormDiv" v-on:click="addForm()">@lang('glob.add_new')</a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="{{ url('master/categorys/xls' ) }}">Excel</a></li>
					<li><a href="{{ url('master/categorys/pdf' ) }}">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->

		<div id="categoryFormDiv" class="collapse @if (session('open') || isset($open) || isset($editCategory) || !$errors->isEmpty() ) in @endif">

			@if (isset($editCategory)) 
			{{ Form::model($editCategory, ['url'=>array('master/category',$editCategory->id),'method'=>'PUT','id'=>'categoryForm']) }}
			@else 
			{{ Form::open(['action'=>'CategoriesController@store','id'=>'categoryForm']) }}<!-- form action for store -->
			@endif

			<div class="row customer-box">

				<div class="col-md-12">

					<div class="box box-primary">

						<div class="box-body">

							<div class="row">

								<!-- type -->
								<div class="col-md-5">
									<div class="form-group {{ $errors->has('categorie') ? ' has-error' : '' }}">
										{!! Form::label('title', trans('glob.sub_category')) !!}<span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-tags" aria-hidden="true"></i></div>
											{!! Form::text('categorie', null, array('required', 'class'=>'form-control input-sm', 'placeholder'=>trans('glob.sub_category'))) !!}
										</div>

										@if ($errors->has('categorie'))
										<span class="help-block">
											<strong>{{ $errors->first('categorie') }}</strong>
										</span>
										@endif

									</div>
								</div>
								<!-- end type -->

							</div>
							<!-- /.row -->
							
							<!-- .row -->
							<div class="row">

								{{-- parent category --}}
								<div class="col-md-5">
									<div class="form-group {{ $errors->has('parent_category') ? ' has-error' : '' }}">
										{!! Form::label('title', trans('glob.category')) !!}<span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-tags" aria-hidden="true"></i></div>
											<select name="parent_category" class="form-control input-sm " required="">
												<option value="0" >Parent Category</option>
												@foreach( $parentCategories as $c)
												@if(isset($editCategory) && $editCategory->id!=$c->id)
												<option value="{{ $c->id }}"  @if(isset($editCategory) && $editCategory->parent_category==$c->id ) selected  @endif>{{ $c->categorie }}</option>
												@elseif(!isset($editCategory) )
												<option value="{{ $c->id }}"  @if(isset($editCategory) && $editCategory->parent_category==$c->id ) selected  @endif>{{ $c->categorie }}</option>
												@endif
												@endforeach
											</select>
										</div>

										@if ($errors->has('parent_category'))
										<span class="help-block">
											<strong>{{ $errors->first('parent_category') }}</strong>
										</span>
										@endif

									</div>
								</div>

							</div>
							{{-- /.row --}}
						</div>

						<!-- .box-body -->
						<div class="box-footer">

							<div class="form-group">

								<button type="submit"  class="btn btn-primary " v-on:click="getLoader($event)"><i class="fa fa-floppy-o"></i>@if (isset($editCategory)) @lang('glob.update') @else @lang('glob.save') @endif</button>
								<a  class="btn btn-cyan" v-on:click="resetForm()"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>
								<a  @if (isset($editCategory)) href="{{ url('master/category') }}" @endif class="btn btn-danger add-customer" data-toggle="collapse" data-target="#categoryFormDiv" v-on:click="cancelForm">@lang('glob.cancel')</a>

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
				<table class="table table-bordered table-hover datatable-order" data-sort-order="desc" data-sort-col="0">
					<thead>
						<tr>
							<th style="width:2%">#</th>
							<th>@lang('glob.category')</th>

							<th>@lang('glob.parentCategory')</th>

							<th>@lang('glob.created_date')</th>

							@if(Common::can_update('category') || Common::can_delete('category'))

							<th style="width:4%">@lang('glob.actions')</th>

							@endif

						</tr>
					</thead>
					<tbody>

						@foreach($category as $row)

						<tr>
							<td>{{$row->id}}</td>
							<td>{{$row->categorie}}</td>
							<td>@if($row->parent_category === 0) Parent Category @elseif (isset($row->parentCategory->categorie)) {{  $row->parentCategory->categorie }}  @endif </td>
							<td >{{ $row->created_at->format('jS F, Y') }}
								<br/>
								<em>
									<small class="font-roboto">
										Last Updated At {{ $row->created_at->diffForHumans() }}
									</small>
								</em>
							</td>

							@if(Common::can_update('category') ||  Common::can_delete('category'))

							<td>

								@if(Common::can_update('category'))

								<a href="{{route('category.edit',$row->id)}}" v-on:click="getLoader($event)" class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								@endif
								@if(Common::can_delete('category'))

								<a id='{{ $row->id }}' v-on:click="deleteCategory({{ $row->id }}, $event)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> 

								@endif

							</td> 

							@endif

						</tr>

						@endforeach
						
					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th style="width:2%">#</th>
							<th class="search">@lang('glob.category')</th>
							<th class="search">@lang('glob.parentCategory')</th>
							<th class="search">@lang('glob.created_date')</th>

							@if(Common::can_update('category') ||  Common::can_delete('category'))

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