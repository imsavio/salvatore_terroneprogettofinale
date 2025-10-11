@extends('layouts.app')

@section('title', 'Errore del server')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-md mx-auto text-center">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-gray-300">500</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Errore del server</h2>
            <p class="text-gray-600 mb-8">
                Si è verificato un errore interno del server. Il nostro team è stato notificato.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="{{ route('home') }}" 
               class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Torna alla Home
            </a>
            
            <div class="text-sm text-gray-500">
                <p>Se il problema persiste, contattaci:</p>
                <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">
                    {{ route('contact') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
