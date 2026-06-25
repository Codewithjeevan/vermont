<div id="purchase-import" class="modal fade" role="dialog">
  <div class="modal-dialog"  style="width:60%" >

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" v-on:click="resetImported">&times;</button>
        <h4 class="modal-title">Import Opening Stock</h4>
      </div>
      <div class="modal-body">
        <div class="col-md-12"  v-show="boolIsUnitCreate==false">
          <span style="color: red;">Can't create new unit, You don't have privileges to create Unit</span>
        </div>
        <div class="col-md-12" v-show="boolIsCatCreate==false">
         <span  style="color: red;">Can't create new category, You don't have privileges to create Category</span>
       </div>
       <div class="col-md-12"  v-show="boolIsItemCreate==false">
         <span  style="color: red;">Can't create item, You don't have privileges to create item</span>
       </div>
       <div class="col-md-12"  v-show="boolIsItemUpdtae==false">
         <span  style="color: red;">Can't update item, You don't have privileges to update item</span>
       </div>
       <div class="form-group">
        <input type="file" class="file" v-on:change="uploadFile()"  v-model="items_file" id="upload__file"  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
        <div class="input-group col-md-12">
          <span class="input-group-addon"><i class="fa fa-file-excel-o" aria-hidden="true"></i></span>
          <input type="text" class="form-control input-sm" id="file-alt" disabled placeholder="Upload Excel sheet" >
          <span class="input-group-btn" >
            <button class="browse btn btn-primary input-sm"  type="button"><span class="replace"><i class="glyphicon glyphicon-search"></i> Browse </span></button>
          </span>
          
        </div>
      </div>

      <div class="progress progress-sm active" style="width:95%; float: left">
        <div class="progress-bar progress-bar-success  progress-bar-striped active" role="progressbar" :aria-valuenow="fileUploadProgress" aria-valuemin="0" aria-valuemax="100" v-bind:style="{ width: fileUploadCent }">
        </div>
      </div>
      <span  style="font-size: 11px;float: right" v-show="fileUploadProgress!=0">@{{ fileUploadCent }}</span>
      <div class="row" v-show="importedItems.length > 0">
       <div class="col-md-12">
         <table class="table table-bordered"> 
           <thead>
             <tr>
               <th>Sub-Category</th>
               <th>Item Name</th>
               <th>Barcode</th>
               <th>Refrence Number</th>
               <th>Qty</th>
               <th>unit</th>
               <th>Sale Price</th>
               <th>VAT</th>
               <th>Cost Price</th>
               <th>CP VAT</th>
               <th>Action</th>
             </tr>
           </thead>
           <tbody>
             <tr v-bind:class="{ 'bg-green': (ii.type=='new'),'bg-orange': (ii.conflict && ii.type!='duplicate'), 'bg-red': (ii.type=='duplicate') }" v-for="(ii, index) in importedItems">
               <td><input v-show="ii.type=='new' || ii.type=='duplicate' || ii.conflict" class="form-control input-sm" type="text" v-model="ii.category"> <span v-show="ii.type!='new' &&  ii.type!='duplicate' && !ii.conflict ">@{{ ii.category }} </span> </td>
               <td><input v-show="ii.type=='new' || ii.type=='duplicate' || ii.conflict" class="form-control input-sm" type="text" v-model="ii.itemName"> <span v-show="ii.type!='new' &&  ii.type!='duplicate' && !ii.conflict ">@{{ ii.itemName }} </span> </td>
               <td><input v-show="ii.type=='new' || ii.type=='duplicate' || ii.conflict" class="form-control input-sm" type="text" v-model="ii.barcode"> <span v-show="ii.type!='new' && ii.type!='duplicate' && !ii.conflict ">@{{ ii.barcode }} </span> </td>
               <td><input v-show="ii.type=='new' || ii.type=='duplicate' || ii.conflict" class="form-control input-sm" type="text" v-model="ii.serial_number"> <span v-show="ii.type!='new' && ii.type!='duplicate' && !ii.conflict ">@{{ ii.serial_number }} </span> </td>
               <td><input  class="form-control input-sm" type="text" v-model="ii.qty"></td>
               <td><input  class="form-control input-sm" type="text" v-model="ii.unit"> </td>
               <td><input  class="form-control input-sm" type="text" v-model="ii.salePrice"> </td>
               <td  v-bind:class="{'bg-red': (ii.vat < 0 || ii.vat > 100) }"><input  class="form-control input-sm" type="text" v-model="ii.vat"> </td>
               <td><input  class="form-control input-sm" type="text" v-model="ii.costPrice"> </td>
               <td  v-bind:class="{'bg-red': (ii.cp_vat < 0 || ii.cp_vat > 100) }"><input  class="form-control input-sm" type="text" v-model="ii.cp_vat"> </td>
               <td><a v-on:click="deleteIitem(index)" class="btn btn-xs btn-danger btn-circle "><i class="fa fa-trash" aria-hidden="true"></i></a> </td>
             </tr>
           </tbody>
         </table>
       </div>
     </div>
   </div>
   <div class="modal-footer">
    <button class="btn btn-primary pull-left"  v-bind:class="{ 'disabled': importedItems.length < 1 }"  v-on:click="saveImportedItems(importedItems, $event)">Save</button>
    <button class="btn bg-cyan pull-left"   v-on:click="resetImported()">Reset</button>
    <button type="button" class="btn btn-default" v-on:click="resetImported" data-dismiss="modal">Close</button>
  </div>
</div>

</div>
</div>