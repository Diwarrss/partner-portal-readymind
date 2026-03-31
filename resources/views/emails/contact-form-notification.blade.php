@extends('emails.layout')

@section('title', 'Nueva solicitud desde el portal')

@section('heading')
    Solicitud de productos
@endsection

@section('content')
    <p style="margin:0 0 20px 0;">Se ha recibido un nuevo mensaje desde el <strong>formulario de contacto</strong> del portal de clientes.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fafafa;border-radius:12px;border:1px solid #e4e4e7;margin:0 0 20px 0;">
        <tr>
            <td style="padding:20px 24px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                    <tr>
                        <td style="padding:6px 0;color:#71717a;width:130px;vertical-align:top;">Cliente</td>
                        <td style="padding:6px 0;color:#18181b;font-weight:600;">{{ $productRequest->customer?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;color:#71717a;vertical-align:top;">Correo</td>
                        <td style="padding:6px 0;color:#18181b;word-break:break-all;">{{ $productRequest->customer?->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;color:#71717a;vertical-align:top;">Tenant</td>
                        <td style="padding:6px 0;color:#18181b;">{{ $productRequest->customer?->tenant?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;color:#71717a;vertical-align:top;">Producto</td>
                        <td style="padding:6px 0;color:#18181b;font-weight:600;">{{ $productRequest->product_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;color:#71717a;vertical-align:top;">Cantidad</td>
                        <td style="padding:6px 0;color:#18181b;">{{ $productRequest->quantity ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 8px 0;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#71717a;">Mensaje</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border:1px solid #e4e4e7;border-radius:10px;margin:0;">
        <tr>
            <td style="padding:18px 20px;font-size:14px;line-height:1.65;color:#3f3f46;white-space:pre-wrap;">{{ $productRequest->product_description ?: '—' }}</td>
        </tr>
    </table>

    <p style="margin:24px 0 0 0;font-size:13px;color:#71717a;">ID de solicitud: <span style="font-family:ui-monospace,monospace;color:#52525b;">#{{ $productRequest->id }}</span></p>
@endsection
