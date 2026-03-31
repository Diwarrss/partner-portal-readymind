@extends('emails.layout')

@section('title', 'Credenciales del portal ReadyMind')

@section('heading')
    Acceso al portal
@endsection

@section('content')
    <p style="margin:0 0 16px 0;">Hola <strong>{{ $customer->name }}</strong>,</p>
    <p style="margin:0 0 24px 0;color:#52525b;">Tu cuenta en el <strong>portal de clientes ReadyMind</strong> está lista. Usa los siguientes datos para iniciar sesión en la página de acceso unificado:</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fafafa;border-radius:12px;border:1px solid #e4e4e7;margin:0 0 24px 0;">
        <tr>
            <td style="padding:20px 24px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                    <tr>
                        <td style="padding:8px 0;color:#71717a;width:140px;vertical-align:top;">Correo</td>
                        <td style="padding:8px 0;color:#18181b;font-weight:600;word-break:break-all;">{{ $customer->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#71717a;vertical-align:top;">Contraseña</td>
                        <td style="padding:8px 0;color:#18181b;font-family:ui-monospace,Consolas,monospace;font-size:13px;font-weight:600;letter-spacing:0.02em;">{{ $plainPassword }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#71717a;vertical-align:top;">Código de centro</td>
                        <td style="padding:8px 0;">
                            <span style="display:inline-block;background-color:#eef2ff;color:#4338ca;font-weight:700;font-size:13px;padding:4px 10px;border-radius:6px;letter-spacing:0.05em;">{{ $customer->tenant?->code ?? '—' }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px 0;font-size:14px;color:#52525b;">En el inicio de sesión, elige el <strong>centro (tenant)</strong> que te indicó tu administrador — debe coincidir con el código anterior.</p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 24px 0;">
        <tr>
            <td style="background-color:#fef3c7;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:14px 18px;">
                <p style="margin:0;font-size:13px;line-height:1.5;color:#92400e;"><strong>Seguridad:</strong> te recomendamos cambiar la contraseña temporal en cuanto entres al portal y no compartir estas credenciales.</p>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:14px;color:#71717a;">Si tienes dudas, responde a este correo o contacta al equipo ReadyMind.</p>
@endsection
