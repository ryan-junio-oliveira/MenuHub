<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
{!! $head ?? '' !!}
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#f4f4f5;">
<tr>
<td align="center" style="padding:32px 16px;">

<table width="570" cellpadding="0" cellspacing="0" role="presentation" style="max-width:570px;width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px -2px rgba(0,0,0,0.06),0 2px 4px -2px rgba(0,0,0,0.04);">

<!-- Header -->
<tr>
<td align="center" style="background:linear-gradient(135deg,#f97316,#ea580c);padding:36px 32px 28px;">
<div style="display:inline-block;background-color:rgba(255,255,255,0.2);border-radius:10px;padding:8px 20px;">
<span style="color:#ffffff;font-size:24px;font-weight:800;letter-spacing:-0.02em;">MenuHub</span>
</div>
<p style="color:rgba(255,255,255,0.85);font-size:14px;margin:10px 0 0;text-align:center;">Sistema de Gestao para Restaurantes</p>
</td>
</tr>

<!-- Body -->
<tr>
<td style="padding:32px 32px;background-color:#ffffff;">
{!! Illuminate\Mail\Markdown::parse($slot) !!}
{{ $subcopy ?? '' }}
</td>
</tr>

<!-- Footer -->
<tr>
<td align="center" style="padding:20px 32px;background-color:#fafafa;border-top:1px solid #e4e4e7;">
<p style="color:#a1a1aa;font-size:12px;margin:0;">&copy; {{ date('Y') }} MenuHub. Todos os direitos reservados.</p>
<p style="margin:4px 0 0;">
<a href="{{ config('app.url') }}" style="color:#a1a1aa;font-size:12px;text-decoration:underline;">{{ config('app.url') }}</a>
</p>
</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
