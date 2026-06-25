@extends('layouts.app')

@section('page-title')@lang('glob.loyalty')@endsection

@section('page-subtitle')@lang('glob.manage_loyalty')@endsection 

@section ('breadcrumb')

<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> @lang('glob.dashboard')</a></li>
	<li class="active">@lang('glob.loyalty')</li>
</ol>

@endsection 

@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-body">
				<form action="/loyalty/loyalty" method="post">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-md-6">

							<div class="form-group">
								<label for="">Loyalty Activation</label>
								<div class="input-group">
									<div class="input-group-addon">
										Sales
									</div>
									<input name="loyalty_activation" type="text" value="{{ $loyalty_fields["loyalty_activation"] }}" class="form-control validate-decimal" maxlength="15" required="">
									<div class="input-group-addon">
										{{ Common::curr() }}
									</div>
								</div>
							</div>


							<div class="form-group">
								<label for="">Loyalty Points Formula</label>
								<div class="row">
									<div class="col-md-6 no-right-padding no-right-margin">
										<div class="input-group">
											<div class="input-group-addon">Sales</div>
											<input  name="loyalty_points_on_sale" type="text" class="form-control validate-decimal" value="{{ $loyalty_fields["loyalty_points_on_sale"] }}"  maxlength="10" required="">
											<div class="input-group-addon">
												{{ Common::curr() }}
											</div>
										</div>	
									</div>
									<div class="col-md-1 no-margin no-padding">
										<div class="input-group">
											<div class="input-group-addon" style="padding: 6px; font-size: 20px; font-weight: 500"> = </div>
										</div>
									</div>
									<div class="col-md-5 no-left-padding">
										<div class="input-group">
											<div class="input-group-addon">Points</div>
											<input name="loyalty_points" value="{{ $loyalty_fields["loyalty_points"] }}"  type="text" class="form-control validate-decimal" maxlength="10" required="">
										</div>
									</div>

								</div>
							</div>

							<div class="form-group">
								<label for="">Loyalty Redeem Maturity</label>
								<div class="input-group">
									<div class="input-group-addon">
										Points
									</div>
									<input name="loyalty_redeem_maturity" value="{{ $loyalty_fields["loyalty_redeem_maturity"] }}" type="text" class="form-control validate-decimal" maxlength="10" required="">
								</div>
							</div>

							<div class="form-group">
								<label for="">Loyalty Redeem Formula</label>
								<div class="row">
									<div class="col-md-5 no-right-padding no-right-margin">
										<div class="input-group">
											<div class="input-group-addon">Points</div>
											<input name="loyalty_on_redeem_points" value="{{ $loyalty_fields["loyalty_on_redeem_points"] }}"  type="text" class="form-control validate-decimal" maxlength="10"  required="">
										</div>	
									</div>
									<div class="col-md-1 no-margin no-padding">
										<div class="input-group">
											<div class="input-group-addon"  style="padding: 6px; font-size: 20px; font-weight: 500"> = </div>
										</div>
									</div>
									<div class="col-md-6 no-left-padding">
										<div class="input-group">
											<div class="input-group-addon">Sales</div>
											<input name="loyalty_on_redeem_discount" value="{{ $loyalty_fields["loyalty_on_redeem_discount"] }}" type="text" class="form-control validate-number" maxlength="3" required="">
											<div class="input-group-addon">%</div>
										</div>
									</div>

								</div>
							</div>

							<button class="btn btn-primary">Update</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div> 

@endsection