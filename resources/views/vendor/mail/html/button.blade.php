@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center" style="border-radius:8px;background-color:{{ $color === 'primary' ? '#f97316' : ($color === 'success' ? '#16a34a' : '#dc2626') }};padding:12px 32px;">
<a href="{{ $url }}" target="_blank" rel="noopener" style="color:#ffffff;font-size:15px;font-weight:600;text-decoration:none;display:inline-block;">{!! $slot !!}</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
