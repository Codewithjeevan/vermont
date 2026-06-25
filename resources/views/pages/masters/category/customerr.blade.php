<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">    

	<style type="text/css">
	
		table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
			font-size:10px;			
		}		
		.row {
			margin-right: 0px;
			margin-left: 0px;   
		}
		.labelfont{
			font-weight: bold;
			font-size:12px;
			float: left;
		}
		.tablefont{			
			font-size:8px;
			float: left;
		}


	</style>    
	
</head>
<body>
	<div >
		<div class="row">
			<center>
				<img src="{{ public_path() . "\img\central.jpg" }}" height="20" width="30">
				<label style="font-style: italic; font-weight: bold; font-size:20px;">Central Mall</label>
				<br/>
				<label style="font-weight: bold; font-size:10px;">Tel No : 1234567890</label>
			</center>
		</div>
		


		<div class="row">
			<!-- Location Id -->	
			<label class='labelfont'>@lang('billing.bill_no') : 1</label>			
			<span> </span>				
			<!-- End Location Id -->
		</div>
		<br/>


		<div class="table-responsive" style="width: 100%;">
			<table class="table table-bordered" style="width: 100%;">
				<thead>
					<tr>
						<th class="" style="width: 25%;">@lang('billing.item_name')</th>
						<th style="width: 25%;">@lang('billing.qty')
						</th>					
						<th style="width: 25%;">@lang('billing.rate')
						</th>
						<th style="width: 25%;">@lang('billing.amt')(@lang('glob.curr'))
						</th>

					</tr>
				</thead>
				<tbody >
				

					<tr>
						<td>Suger</td>
						<td>2</td>									
						<td>20</td>
						<td>40</td>									
					</tr>

					<tr>
						<td>Milk Powder</td>
						<td>1</td>									
						<td>60</td>
						<td>60</td>									
					</tr>

					<tr>
						<td>Chilli Powder</td>
						<td>1</td>									
						<td>10</td>
						<td>10</td>									
					</tr>
					

				</tbody>
			</table>
		</div>
		<br/>

		<div class='row' style="width: 100%;">

			<div class='labelfont' align="left" style="width: 25%;">
				<label >@lang('glob.item')</label> 
			</div>
			<div class='labelfont' align="left" style="width: 25%;">
				<label >4</label> 
			</div>	

			<div class='labelfont' align="left" style="width: 25%;">
				<label >@lang('glob.total')</label> 
			</div>			
			<div class='labelfont' align="left" style="width: 25%;">
				<label >110.00</label> 
			</div>

		</div>
		<br/>
		<hr>

		<div class='row' style="width: 100%;">
			<div class='labelfont' align="left" style="width: 25%;">
				<label >@lang('glob.received')</label> 
			</div>
			<div class='labelfont' align="left" style="width: 25%;">
				<label >150.00</label> 
			</div>	

			<div class='labelfont' align="left" style="width: 25%;">
				<label >@lang('glob.change')</label> 
			</div>
			<div class='labelfont' align="left" style="width: 25%;">
				<label >40.00</label> 
			</div>
		</div>
		<br/>
		<br/>
		<br/>
		<br/>
		<br/>
		<br/>
		<br/>

		<div class='row' style="width: 100%;">
			<div class='labelfont' align="left" style="width: 100%;">
				<label >@lang('glob.saved_by')</label> 
			</div>						
		</div>
		<br/>
		<hr>		

		<div class='row' style="width: 100%;">
			<div class='labelfont' align="left" style="width: 20%;">
				<label >@lang('glob.date')</label> 
			</div>
			<div class='labelfont' align="left" style="width: 20%;">
				<label >@lang('glob.time')</label> 
			</div>
			<div class='labelfont' align="left" style="width: 20%;">
				<label >@lang('glob.store')</label> 
			</div>
			<div class='labelfont' align="left" style="width: 20%;">
				<label >@lang('glob.pos')</label> 
			</div>
			<div class='labelfont' align="left" style="width: 20%;">
				<label >@lang('glob.receipt')</label> 
			</div>
		</div>
		<br/>		

		<div class='row' style="width: 100%;">
			<div class='labelfont' align="left" style="width: 20%;">
				<label >11/3/2017</label> 
			</div>
			<div class='labelfont' align="left" style="width: 20%;">
				<label >11:11:00</label> 
			</div>
			<div class='labelfont' align="left" style="width: 20%;">
				<label >1254</label> 
			</div>
			<div class='labelfont' align="left" style="width: 20%;">
				<label >12</label> 
			</div>
			<div class='labelfont' align="left" style="width: 20%;">
				<label >15442</label> 
			</div>
		</div>

   
	</div>
</body>
</html>