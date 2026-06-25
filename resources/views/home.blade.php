@extends('layouts.app')
@section('page-title')
Home
@endsection

@section('content')
<div class="row">
	<div class="col-md-12">
	<table class="table table-bordered table-striped datatable2">
			<thead>
				<tr>
					<th style="width:2%">ID</th>
					<th>Name</th>
					<th>Phone</th>
					<th>City</th>
					<th style="width:4%">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$names=["Alexander pierce","Alex Parish", "Ajay Patel", "Vijay Roy", "Akshay Singh"];
				$phone=["9988552233","9988552211","9988663322","8888888888","8866556666","9865321145"];
				$city=["New York", "WDC","Mumbai", "Delhi", "Pune"];
				?>
				<?php for ($i=0;$i<100;$i++) {
					$r = rand(0, 4); ?>
					<tr>
						<td><?php echo $i+1; ?></td>
						<td><?php echo $names[$r]; ?></td>
						<td><?php echo $phone[$r]; ?></td>
						<td><?php echo $city[$r]; ?></td>
						<td><a  href="" class="btn btn-xs btn-info btn-circle"><i class="fa fa-pencil" aria-hidden="true"></i></a>
							<a  href="" class="btn btn-xs btn-danger btn-circle"><i class="fa fa-trash" aria-hidden="true"></i></a> </td>
						</tr>
						<?php

					} ?>
				</tbody>
				<tfoot class="showSearch">
					<tr>
						<th style="width:2%">ID</th>
						<th class="search">Name</th>
						<th class="search">Phone</th>
						<th class="search">City</th>
						<th style="width:4%">Actions</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
@endsection
