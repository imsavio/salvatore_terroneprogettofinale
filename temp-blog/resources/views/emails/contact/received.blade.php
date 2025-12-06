<x-mail::message>
# Nuovo messaggio da {{ $messageData->name }}

<x-mail::panel>
**Email:** {{ $messageData->email }}  
**Oggetto:** {{ $messageData->subject ?? '—' }}
</x-mail::panel>

{{ $messageData->message }}

<x-mail::button :url="url('/')">
Apri NovaBlog
</x-mail::button>

Grazie,<br>
{{ config('app.name') }}
</x-mail::message>
