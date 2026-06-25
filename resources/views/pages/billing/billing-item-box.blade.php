
<div class="box" style="margin-bottom:5px;border-top:0;border:2px solid #c8c8c8; ">
	<div class="box-body no-padding" style="border-radius: 0px; height:143px; overflow-y:scroll">
		<div class="col-md-12 col-sm-12 col-xs-12 btn-group btn-group-lg no-padding">
			<div class="no-padding box-container col-sm-3"  v-for="(i, index) in itemBox" v-if="i.item_with_box == '1'">
				<a class="btn no-shadow bg-purple btn-block item-box-button" v-bind:class="{ 'text-wrap' :(i.itemName.length > 52)}" v-on:click="addSingleItem(i)">@{{i.itemNameArabic }} </br> @{{ i.itemName}}</a>
			</div>
		</div>
	</div>
</div>