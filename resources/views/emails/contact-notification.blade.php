<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo messaggio di contatto</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .message-box {
            background-color: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .message-details {
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            min-width: 100px;
            margin-right: 15px;
        }
        .detail-value {
            color: #212529;
            flex: 1;
            word-break: break-word;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
        }
        .btn:hover {
            background-color: #c82333;
        }
        .icon {
            font-size: 48px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .priority {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 6px;
            margin: 15px 0;
            font-weight: 500;
        }
        @media (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            .content {
                padding: 20px 15px;
            }
            .header {
                padding: 20px 15px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                margin-bottom: 5px;
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔔 Nuovo Messaggio di Contatto</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Richiede la tua attenzione</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div style="text-align: center;">
                <div class="icon">📬</div>
                <h2 style="color: #dc3545; margin: 0 0 10px 0;">Nuovo messaggio ricevuto!</h2>
                <p style="color: #6c757d; margin: 0 0 20px 0;">
                    Un utente ha inviato un messaggio attraverso il form di contatto.
                </p>
            </div>

            <!-- Priority Alert -->
            <div class="priority">
                ⚠️ <strong>Attenzione:</strong> Rispondi al messaggio entro 24 ore per mantenere un buon servizio clienti.
            </div>

            <!-- Message Details -->
            <div class="message-details">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Informazioni del messaggio:</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Nome:</div>
                    <div class="detail-value">{{ $name }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value">
                        <a href="mailto:{{ $email }}" style="color: #007bff; text-decoration: none;">
                            {{ $email }}
                        </a>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Soggetto:</div>
                    <div class="detail-value"><strong>{{ $subject }}</strong></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Inviato il:</div>
                    <div class="detail-value">{{ $timestamp->format('d/m/Y H:i') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">IP:</div>
                    <div class="detail-value">{{ $ip }}</div>
                </div>
            </div>

            <!-- Message Content -->
            <div class="message-box">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Contenuto del messaggio:</h3>
                <div style="background-color: #ffffff; padding: 15px; border-radius: 4px; border: 1px solid #e9ecef; white-space: pre-wrap; font-family: inherit;">{{ $message }}</div>
            </div>

            <!-- Technical Details -->
            <div class="alert">
                <h4 style="margin: 0 0 10px 0; color: #856404;">Dettagli tecnici:</h4>
                <div class="detail-row">
                    <div class="detail-label">User Agent:</div>
                    <div class="detail-value" style="font-size: 12px; color: #6c757d;">{{ $user_agent }}</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="text-align: center;">
                <a href="mailto:{{ $email }}?subject=Re: {{ $subject }}" class="btn">
                    📧 Rispondi via Email
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>{{ config('app.name', 'Laravel Blog') }} - Sistema di Contatti</strong><br>
                Questo messaggio è stato generato automaticamente dal sistema.
            </p>
            <p style="margin-top: 15px; font-size: 12px;">
                © {{ date('Y') }} {{ config('app.name', 'Laravel Blog') }}. Tutti i diritti riservati.
            </p>
        </div>
    </div>
</body>
</html>








