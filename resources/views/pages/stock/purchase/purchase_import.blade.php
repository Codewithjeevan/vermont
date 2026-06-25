<div id="purchase-import" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Import Items</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <input type="file" class="file" v-on:change="uploadFile()"  v-model="purchase_items_file" id="upload__file"  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
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
        
     </div>
     <div class="modal-footer">
      <button type="button" class="btn btn-default" v-on:click="resetUpload" data-dismiss="modal">Close</button>
    </div>
  </div>

</div>
</div>