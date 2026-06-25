<div id="history-modal" class="modal fade" role="dialog">
	<div class="modal-dialog history-modal col-md-3" >
		
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-purple">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">@lang('billing.client_buying_history')&emsp;</h4>
			</div>
			{{-- /.modal-header --}}

			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="well">
							<div class="row">
								<div class="col-md-4">
									<span style="font-size: 14px;font-weight: 800">
										Mobile Number :  
									</span>
								</div>
								<div class="col-md-8 numpad-input">
									<input type="text" class="form-control input-sm mobile mobi validate-number" v-model="history.customer"  placeholder="Enter Mobile Number">
									
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12" style="margin-bottom:15px;">
								<span class="pull-left" :class="history.historyClass">
									<b>@{{ history.custName  }}</b>
								</span>
								<span class="pull-right">
									<label for="">Filter by</label> 
									<button class="btn btn-sm" v-bind:class="{ 'btn-danger' : history.filter=='all' }"   v-on:click="filterHistory('all')"> All Time </button> 
									<button class="btn btn-sm" v-bind:class="{ 'btn-danger' : history.filter=='yearly' }" v-on:click="filterHistory('yearly')"> Yearly </button>
									<button class="btn btn-sm" v-bind:class="{ 'btn-danger' : history.filter=='monthly' }" v-on:click="filterHistory('monthly')"> Monthly </button>
									<button class="btn btn-sm" v-bind:class="{ 'btn-danger' : history.filter=='weekly' }" v-on:click="filterHistory('weekly')"> Weekly </button>
								</span>
							</div>
						</div>

						<i v-if="loading" class="history-loader fa fa-spinner fa-pulse fa-3x fa-fw"></i>
						<span class="sr-only">Loading...</span>
						<div style="overflow-y: scroll;height: 200px;">
							<table class="table table-bordered table-striped history-table">
								<thead>
									<tr>
										
										<th>@lang('billing.item_name')</th>
										<th>@lang('billing.quantity')</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(ch,index) in history.customerHistory" v-on:dblclick="addItemFromHistory(ch)" v-if="ch.offer_type == 0 && ch.qty > 0" style="cursor: pointer;">
										
										<td>@{{ ch.itemName  }}</td>
										<td>@{{  fixQty(ch.qty)  }}</td>
									</tr>
								</tbody>
							</table>
						</div>

					</div>
					{{-- /.col-md-12 --}}
				</div>
				{{-- /.row --}}
			</div>
			{{-- /.Modal body --}}

			<div class="modal-footer">
				<button type="button" class="btn bg-black" data-dismiss="modal">@lang('glob.close')</button>
			</div>
			{{-- /.modal-footer --}}
		</div>
		{{-- /.modal-content --}}

	</div>
	{{-- /.modal-dialog --}}

</div>
{{-- /#history-modal --}}