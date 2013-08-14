<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th>IP</th>
			<th>Page</th>
			<th>User</th>
			<th>Date</th>
			<th>Browser</th>
		</tr>
	</thead>

	<tbody>
		@foreach(Visitor::all() as $visitor)
			<tr>
				<td>{{ $visitor->ip }}</td>
				<td>{{ $visitor->page }}</td>
				@if($visitor->isUser())
					<td>{{ Sentry::getUserProvider()->findById($visitor->user)->username }}</td>
				@else
					<td>Guest</td>
				@endif
				<td>{{ $visitor->updated_at }}</td>
				<td>{{ $visitor->platform }} -> {{ $visitor->agents }}</td>
			</tr>
		@endforeach
	</tbody>

</table>