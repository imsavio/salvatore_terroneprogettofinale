<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferma ricezione messaggio</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .message-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
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
            min-width: 80px;
            margin-right: 15px;
        }
        .detail-value {
            color: #212529;
            flex: 1;
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
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 20px;
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
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>✅ Messaggio Ricevuto!</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Grazie per averci contattato</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div style="text-align: center;">
                <div class="icon">📧</div>
                <h2 style="color: #28a745; margin: 0 0 10px 0;">Ciao {{ $name }}!</h2>
                <p style="color: #6c757d; margin: 0 0 20px 0;">
                    Abbiamo ricevuto il tuo messaggio e ti risponderemo presto.
                </p>
            </div>

            <!-- Message Details -->
            <div class="message-details">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Dettagli del messaggio:</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Soggetto:</div>
                    <div class="detail-value">{{ $subject }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Inviato il:</div>
                    <div class="detail-value">{{ $timestamp->format('d/m/Y H:i') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Messaggio:</div>
                    <div class="detail-value" style="white-space: pre-wrap;">{{ $message }}</div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="message-box">
                <h3 style="margin: 0 0 15px 0; color: #495057;">Cosa succede ora?</h3>
                <ul style="margin: 0; padding-left: 20px; color: #6c757d;">
                    <li>Il nostro team esaminerà il tuo messaggio</li>
                    <li>Riceverai una risposta entro 24 ore</li>
                    <li>Se hai domande urgenti, contattaci direttamente</li>
                </ul>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}" class="btn">
                    Visita il nostro sito
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>{{ config('app.name', 'Laravel Blog') }}</strong><br>
                Questo messaggio è stato inviato automaticamente, non rispondere a questa email.
            </p>
            <p style="margin-top: 15px; font-size: 12px;">
                © {{ date('Y') }} {{ config('app.name', 'Laravel Blog') }}. Tutti i diritti riservati.
            </p>
        </div>
    </div>
</body>
</html>



















