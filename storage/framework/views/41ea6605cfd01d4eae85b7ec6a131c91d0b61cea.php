

<div class="col-md-12">
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title"><?php echo app('translator')->getFromJson('glob.vat_options'); ?></h3>
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
						<div class="col-md-12"> 
							<label class="radio-inline"><input type="radio" name="vatType" value="0" v-model="vatButtons.vat_type" v-on:click="checkVatRecords($event)"  >No VAT</label>
							<label class="radio-inline"><input type="radio" name="vatType" value="1" v-model="vatButtons.vat_type" v-on:click="checkVatRecords($event)"  >Inclusive VAT</label>
							<label class="radio-inline"><input type="radio" name="vatType" value="2" v-model="vatButtons.vat_type" v-on:click="checkVatRecords($event)" >Exclusive VAT</label>
						</div>
						</div>
				</li>
			</ul>
		</div>
		<div class="box-footer">
		<?php if(Common::can_update('options') || Common::can_create('options')): ?>
			<button class="btn btn-primary" id="vatButton"  v-on:click="saveVatSetting(vatButtons,$event)" :disabled="checkVatFlag == false ? true : false"><i class="fa fa-refresh"></i> <?php echo app('translator')->getFromJson('glob.save'); ?></button>
		<?php endif; ?>	
		</div>
	</div>
</div>



