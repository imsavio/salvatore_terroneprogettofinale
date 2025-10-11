@extends('layouts.app')

@section('title', 'Pagina non trovata')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-md mx-auto text-center">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-gray-300">404</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Pagina non trovata</h2>
            <p class="text-gray-600 mb-8">
                La pagina che stai cercando non esiste o è stata spostata.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="{{ route('home') }}" 
               class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Torna alla Home
            </a>
            
            <div class="text-sm text-gray-500">
                <p>Se pensi che questo sia un errore, contattaci:</p>
                <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">
                    {{ route('contact') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
