@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('images/DBOOST-logo.png') }}" class="logo" alt="DBOOST Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
