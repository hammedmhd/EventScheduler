<div class='col-xs-3'>

<label id='expenseload' class='loaderbox'>
	<img src='img/pink.gif' class='loader'>
</label>
<div id='notify' class='alert alert-info'></div>
	<div class='external-events'>
		<h4 class='text-center' id='clientlistheader'><i id='clientadd' class='fa fa-plus-circle fa-2x'></i>Active Clients</h4>
		<hr>
		@include('layouts.errors')

		<div id='newclient' class='panel panel-primary'>

	        <p class='panel-heading text-center'>
	            <b>New Client</b>
	        </p>

	        <div class='panel-body'>

	            <form id='addclient' method='post' autocomplete='off' action='newclient'>
	                {{ csrf_field() }}
	                
	                <div class='form-group'>
	                    <label for='#name'>Client's Name</label>
	                    <input type='text' name='name' id='name' class='form-control' value='{{ old("name")}}' required>
	                </div>

	                <div class='form-group'>
	                    <label for='address'>Client's Address</label>
	                    <input type='text' name='address' id='address' class='form-control' value='{{ old("address")}}'>
	                </div>

	                <div class='form-group'>
	                	<label for='color'>Label Colors</label>
	                	<input type='text' name='backgroundcolor' id='color' class='form-control' placeholder="Background color">
	                	<input type='text' name='textcolor' id='color' class='form-control' placeholder="Text color">
	                </div>

	                <p class='text-center'>
	                    <input type='submit' class='btn btn-primary' value='Add Client'>
	                </p>

	            </form>

	        </div>

	    </div>

		<div class='external-events-listing'>
		
			@if(count($clients) > 0)

				@foreach ($clients as $client)
				<form method='post' action='removeClient' onsubmit="return(confirm('Are you sure you\'d like to remove client and any associated lessons?'))">
				{{ method_field('DELETE') }}
				{{ csrf_field() }}
					<input type='hidden' name='name' value='{{ $client->clientName }}'>
					<p class='fc-event text-center' style='background-color:{{$client->color}};color:{{$client->textColor}}' title='{{$client->address}}' onmouseover="this.childNodes[1].style.display = 'inline';" onmouseout="this.childNodes[1].style.display = 'none';" ondblclick="editClient(this.id)" id="{{$client->clientName}}"> 
						{{ $client->clientName }}
						<button type='submit' class='fa fa-times' id='deleteclient' title='Remove Client and lessons'></button>
					</p>
				</form>
				@endforeach

			@else
				 <p class='text-center'>No clients yet :(</p>

			@endif
		</div>

	</div>

	<div class='payment-toggle'>
		<h4 class='text-center' id='togglepayment'><i id='refreshexpense' class='fa fa-refresh' onclick='incomingExpenses()'></i>Payment</h4>
		<hr>
		<table id='payment' class='table table-condensed'>
			<thead>
				<tr>
					<th class='text-center'>Client</th>
					<th class='text-center'>Time</th>
					<th class='text-center'>Status</th>
				</tr>
			</thead>
			<tbody id='payment-body'>
				<td colspan="3"><p>No lessons for this period...</p></td>
			</tbody>
		</table>
	</div>

	<div class='expense-list'>
		<h4 class='text-center' id='expenseheader'>{{--<i id='refreshexpense' class='fa fa-refresh' onclick='incomingExpenses()'></i>--}}Inbound Payments</h4>
		<hr>
		<table id='expense' class='table table-striped'>
			<tr>
				<td>Received:</td><td id='paidamount' class='text-center'></td>
			</tr>
			<tr>
				<td>Pending:</td><td id='unpaidamount' class='text-center'></td>
			</tr>
			<tr>
				<td>Total:</td><td id='totalamount' class='text-center'></td>
			</tr>
		</table>
	</div>

</div>