@extends('layouts.app')

@section('page-title')@lang('glob.user')@endsection

@section('page-subtitle')@lang('user.add_edit_user')@endsection

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.user')</li>
</ol>

@endsection

@section('content')

<div id="users">

	@if(Common::can_create('user')) 

	<!-- Button div -->
	<div class="row">
		<div class="col-md-6 margin-bottom">
			<a @if(isset($editUser)) href="/common/add/Users" @endif class="btn bg-purple new-customer" data-toggle="collapse" data-target="#userFormDiv" v-on:click="addForm">@lang('glob.add_new')</a>
		</div>
		<div class="col-md-6 dropdown margin-bottom ">
			<button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown" >Export
				<span class="caret" ></span></button>
				<ul class="dropdown-menu pull-right">
					<li><a href="{{ url('master/users/xls' ) }}">Excel</a></li>
					<li><a href="{{ url('master/users/pdf' ) }}">PDF</a></li>				
				</ul>				
			</div>	
		</div>
		<!-- Button div end -->

		<!-- Button div -->	{{-- Add user Form --}}
		<div id="userFormDiv" class="collapse @if (session('open') || isset($open) || isset($editUser) || !$errors->isEmpty()) in @endif">

			@if (isset($editUser)) 
			{{ Form::model($editUser, ['url'=>array('master/user',$editUser->id), 'method'=>'PUT','id'=>'userForm']) }}

			@else
			{{ Form::open(['action'=>'UsersController@store','id'=>'userForm']) }}
			<!-- form action for store -->
			@endif
			<div class="row customer-box">
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-body">
							<div class="row">

								<!-- user name -->
								<div class="col-md-4">
									<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
										{!! Form::label('email', trans('user.user_name')) !!}<span style="color:red">*</span>

										<div class="input-group">
											<div class="input-group-addon"> <i class="ion ion-person"></i> </div>
											{!! Form::text('email',null,array('required','class' => 'form-control input-sm', 'placeholder' => trans('user.user_name'))) !!}
										</div>

										@if ($errors->has('email'))
										<span class="help-block">
											<strong>{{ $errors->first('email') }}</strong>
										</span>
										@endif

									</div>
								</div>
								<!--End user name -->

							</div>
							<!-- .row -->


							<div class="row">

								<!--  password -->
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
										{!! Form::label('password', trans('glob.password')) !!}<span style="color:red">*</span>

										<div class="input-group">
											<div class="input-group-addon"> <i class="ion-android-lock"></i> </div>
											{{ Form::password('password', array('required', 'class' => 'form-control input-sm', 'placeholder'=>trans('glob.password'))) }}
										</div>

										@if ($errors->has('password'))
										<span class="help-block">
											<strong>{{ $errors->first('password') }}</strong>
										</span>
										@endif

									</div>
								</div>
								<!--End password -->


								<!--  confirm password -->
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('password-confirm', trans('glob.conf_password')) !!}<span style="color:red">*</span>

										<div class="input-group">
											<div class="input-group-addon"> <i class="ion-android-lock"></i> </div>
											{{ Form::password('password_confirmation', array('required', 'class' => 'form-control input-sm', 'placeholder'=>trans('glob.conf_password'))) }}
										</div>

									</div>
								</div>
								<!--End confirm password -->


							</div>
							<!-- .row --> 


							<div class="row">

								<!-- Assign Role -->
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('title', trans('user.assign_role')) !!}

										<span style="color:red">*</span>
										<div class="input-group">
											<div class="input-group-addon"> <i class="fa fa-user-plus"></i> </div>
											<select name="roleId" class="form-control input-sm select2" required>
												<option value="">Select Role</option>
												@foreach($roleList as $role)
												<option value="{{ $role->id }}" @if(isset($editUser) && $editUser->roleId== $role->id) selected  @endif>{{ $role->role }}</option>
												@endforeach
											</select>
											
										</div>

									</div>
								</div>
								<!--End Assign Role -->

								<!-- Assign floor -->
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('title', trans('user.assign_floor')) !!}
										
										<div class="input-group">
											<div class="input-group-addon"> <i class="fa fa-list-ol"></i></div>
											<select name="floor" class="form-control input-sm select2" >
												@foreach( $floor as $f)
												<option value="{{ $f }}"  @if(isset($editUser) && $editUser->floor==$f ) selected  @endif>{{ $f }}</option>
												@endforeach
											</select>

											
										</div>

									</div>
								</div>
								<!--End Assign floor -->

							</div>
							<!-- .row -->
						</div>
						<!-- .box-body -->


						<div class="box-footer">
							<div class="form-group"> 

								{{-- Save button--}}
								<button type="submit" class="btn btn-primary" v-on:click="checkValidateLoader($event)">
									<i class="fa fa-floppy-o"></i>
									@if (isset($editUser)) @lang('glob.update') @else @lang('glob.save') @endif
								</button>

								{{-- Reset button --}}
								<a  class="btn btn-cyan" v-on:click="resetForm"><i class="fa fa-refresh"></i> @lang('glob.reset')</a>

								{{-- Cancel button --}}
								<a  @if (isset($editUser)) href="{{ url('master/user') }}" @endif class="btn btn-danger add-customer" data-toggle="collapse" data-target="#userFormDiv" v-on:click="cancelForm"> @lang('glob.cancel') </a>

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
			<!-- end form -->

		</div>

		@endif

		<!-- view table data  -->
		<div class="row">
			<div class="col-md-12 table-responsive">
				<table class="table table-bordered table-striped datatable-order" data-sort-order="desc" data-sort-col="0">
					<thead>
						<tr>
							<th style="width:10%">#</th>
							<th>@lang('user.user_name')</th>
							<th>@lang('user.assign_role')</th>
							<th>@lang('user.assign_floor')</th>
							<th>@lang('glob.created_date')</th>

							@if(Common::can_update('user') || Common::can_delete('user'))

							<th style="width:10%">@lang('customer.actions')</th>

							@endif

						</tr>
					</thead>
					<tbody>

						@foreach($users as $user)
						<tr>
							<td>{{ $user->id }}</td>
							<td>{{ $user->email }}</td>
							<td> @if(isset($user->role->role)) {{ $user->role->role }} @endif</td>
							<td>{{ $user->floor }}</td>
							<td>{{ $user->created_at->format('jS F, Y') }} <br> <small><em> @lang("glob.updated_at") {{ $user->updated_at->diffForHumans() }} </em> </small></td>

							@if(Common::can_update('user') || Common::can_delete('user'))

							<td>

								@if(Common::can_update('user'))

								<a  href="{{ url('/master/user/'.$user->id.'/edit') }}" v-on:click="getLoader($event)"  class="btn btn-xs btn-info btn-circle" ><i class="fa fa-pencil" aria-hidden="true"></i></a>

								@endif
								@if(Common::can_delete('user'))

								<a  v-on:click="deleteUser({{ $user->id }}, $event)" class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> 

								@endif

							</td>

							@endif

						</tr>

						@endforeach

					</tbody>
					<tfoot class="showSearch">
						<tr>
							<th style="width:10%">#</th>
							<th class="search">@lang('user.user_name')</th>
							<th class="search">@lang('user.assign_role')</th>
							<th class="search">@lang('user.assign_floor')</th>
							<th>@lang('glob.created_date')</th>

							@if(Common::can_update('user') || Common::can_delete('user'))

							<th style="width:10%">@lang('customer.actions')</th>

							@endif

						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<!-- view table data end -->

		

	</div>

	@endsection