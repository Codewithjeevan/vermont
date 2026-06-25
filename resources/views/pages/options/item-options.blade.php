<div class="col-md-12">
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">@lang('glob.item_options')</h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
				</button>
			</div>
		</div>
		<!-- /.box-header -->
		<div class="box-body " style="display: block;">
			<ul class="list-group">
				<li class="list-group-item">
					<div class="row">
						<div class="col-md-3">
							<label><input type="checkbox" v-model="itemButtons.wholesale"> @lang('item.wholesale')</label>
						</div>
						<div class="col-md-3">
							<label><input type="checkbox" v-model="itemButtons.item_with_boxes"> @lang('item.item_with_boxes')</label>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<div class="box-footer">
		@if(Common::can_update('options') || Common::can_create('options'))
			<a  class="btn btn-primary" v-on:click="saveitem(itemButtons,$event)"><i class="fa fa-refresh"></i> @lang('glob.save')</a>
		@endif	
		</div>
	</div>
</div>

