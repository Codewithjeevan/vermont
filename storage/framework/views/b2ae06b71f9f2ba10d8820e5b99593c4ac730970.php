<div id="searchModal" class="modal fade prevent-enter" role="dialog">
	<div class="modal-dialog" style="width:80%">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-purple">
				<div class="col-md-3 col-sm-4 ">
					<h4 class="modal-title"><?php echo app('translator')->getFromJson('billing.search_items'); ?>&emsp;</h4>
				</div>
				<div class="col-md-6 col-sm-8 " >
					<div class="form-group"  :class=" (bill_return || !isKeyboardEnabled) ? 'display-none-input':'display-block-input'">
						<input id="search-item-id" type="text" name="search" placeholder="Search Item" v-model="search_item" v-on:change="searchItem(search_item)" class="bg-purple search-item-input form-control keyboard-styled" > 
					</div>
					<div class="form-group"  :class=" (bill_return || isKeyboardEnabled) ? 'display-none-input':'display-block-input'">
						<input id="search-item-id1" type="text" name="search" placeholder="Search Item" v-model="search_item"  v-on:keyup.enter="searchItem(search_item)" class="bg-purple search-item-input form-control" > 
					</div>
				</div>
			</div>
			<div class="modal-body search-item-modal-body">
				<div class="table-responsive">
					<div class="table-search-spinner" style="display: none;">
						<i class="fa fa-spinner fa-pulse fa-spin  fa-4x fa-fw"></i>
						<span class="sr-only">Loading...</span>
					</div>

					<table class="table table-bordered item-search-table" v-if="bill_return==false">
						<thead>
							<tr>
								<th><?php echo app('translator')->getFromJson('billing.barcode'); ?></th>
								<th><?php echo app('translator')->getFromJson('glob.category'); ?></th>
								<th><?php echo app('translator')->getFromJson('glob.item'); ?>/<?php echo app('translator')->getFromJson('glob.product'); ?></th>
								<th v-if=" billing.invoice_type == 1"><?php echo app('translator')->getFromJson('billing.sp'); ?></th>
								<th v-if=" billing.invoice_type == 2"><?php echo app('translator')->getFromJson('item.s_p_w'); ?></th>
								<th><?php echo app('translator')->getFromJson('billing.unit'); ?></th>
								<th><?php echo app('translator')->getFromJson('billing.stock'); ?></th>
								<th><?php echo app('translator')->getFromJson('billing.min_level_stock'); ?></th>
								<th><?php echo app('translator')->getFromJson('billing.max_level_stock'); ?></th>
								
							</tr>
						</thead>
						<tbody>
							<tr v-for="i in search_item_table" class="table-select-row" v-on:click="addSingleItem(i)" v-bind:class="{ 'min-stock' : parseFloat(i.min) >= parseFloat(i.stock)  }">
								<td>{{ i.barcode  }}</td>
								<td>{{ i.categories.categorie }}</td>
								<td>{{ i.itemName }}</td>
								<td v-if=" billing.invoice_type == 1">{{ fixtIt(i.salePrise) }}</td>
								<td v-if=" billing.invoice_type == 2">{{ fixtIt(i.wholesale_salePrise)  }}</td>
								<td>{{ i.units.unit }}</td>
								<td>{{ fixQty(i.stock)  }}</td>
								<td>{{ fixQty(i.min) }}</td>
								<td>{{ fixQty(i.max) }}</td>
								
							</tr>
						</tbody>

					</table>

					<table class="table table-bordered item-return-table " v-if="bill_return==true">
						<thead>
							<tr>
								<th><?php echo app('translator')->getFromJson('billing.barcode'); ?></th>
								<th><?php echo app('translator')->getFromJson('glob.category'); ?></th>
								<th><?php echo app('translator')->getFromJson('glob.item'); ?>/<?php echo app('translator')->getFromJson('glob.product'); ?></th>
								<th><?php echo app('translator')->getFromJson('billing.sp'); ?></th>
								<th><?php echo app('translator')->getFromJson('billing.unit'); ?></th>
								<th><?php echo app('translator')->getFromJson('billing.qty'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="i in itemsReturn.item_list" v-if="i.offer_type == 0 && i.qty > 0" class="table-select-row" v-on:click="addSingleItem(i)" >
								<td>{{ i.barcode  }}</td>
								<td>{{ i.categories.categorie }}</td>
								<td>{{ i.itemName }}</td>
								<td v-if=" itemsReturn.invoice_type == 1">{{ fixtIt(i.salePrise) }}</td>
								<td v-if=" itemsReturn.invoice_type == 2">{{ fixtIt(i.wholesale_salePrise) }}</td>
								<td>{{ i.units.unit }}</td>
								<td>{{ fixQty(i.qty)  }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn bg-black prevent-enter" data-dismiss="modal"><?php echo app('translator')->getFromJson('glob.close'); ?></button>
			</div>
		</div>

	</div>
</div>
