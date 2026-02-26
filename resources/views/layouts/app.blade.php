<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @if(config('settings.app_favicon'))
            <link rel="icon" href="{{ Storage::url(config('settings.app_favicon')) }}">
        @endif
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50">
        <div x-data="{ contactModalOpen: false }" class="flex h-screen">
            <!-- Sidebar -->
            <aside class="w-64 bg-white p-6 flex flex-col h-full shadow-lg">
                <div class="flex items-center space-x-3 mb-10">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        @if(config('settings.app_logo'))
                            <img src="{{ Storage::url(config('settings.app_logo')) }}" alt="{{ config('app.name') }}" class="h-10 w-auto">
                        @else
                            <div class="bg-blue-600 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3.25a3.25 3.25 0 00-3.25-3.25H6.25A3.25 3.25 0 003 16.75V20h6v-3.25a3.25 3.25 0 00-3.25-3.25H6.25A3.25 3.25 0 003 16.75V20h6m-6-10h6m6 0h.01M18 20h.01M18 10h.01M18 4h.01M12 4h.01M6 4h.01M3 4h.01M3 10h.01M3 16h.01M21 10h.01M21 16h.01M21 4h.01"></path></svg>
                            </div>
                        @endif
                        <h1 class="text-xl font-bold text-slate-800">{{ config('app.name') }}</h1>
                    </a>
                </div>
                <nav class="flex-grow">
                    <ul>
                        <li>
                            <a href="{{ route('dashboard') }}" class="w-full flex items-center space-x-3 p-3 rounded-lg text-sm font-medium mb-2 transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        @can('manage fees')
                        <li>
                            <a href="{{ route('invoices.index') }}" class="w-full flex items-center space-x-3 p-3 rounded-lg text-sm font-medium mb-2 transition-colors {{ request()->routeIs('invoices.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span>Fee Management</span>
                            </a>
                        </li>
                        @endcan
                        @can('manage donations')
                        <li>
                            <a href="{{ route('donations.index') }}" class="w-full flex items-center space-x-3 p-3 rounded-lg text-sm font-medium mb-2 transition-colors {{ request()->routeIs('donations.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.104 0 2.08.448 2.828 1.172M12 8a3 3 0 100 6 3 3 0 000-6zm0 0V6m0 6v2m0-8a8.962 8.962 0 016.364 2.636M12 2a8.962 8.962 0 016.364 15.364L21 21M3 21l2.636-2.636A8.962 8.962 0 0112 2zm0 0A8.962 8.962 0 013 21"></path></svg>
                                <span>Donations</span>
                            </a>
                        </li>
                        @endcan
                        @can('manage expenses')
                        <li>
                            <a href="{{ route('expenses.index') }}" class="w-full flex items-center space-x-3 p-3 rounded-lg text-sm font-medium mb-2 transition-colors {{ request()->routeIs('expenses.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                <span>Expenses</span>
                            </a>
                        </li>
                        @endcan
                        @can('manage employees')
                        <li>
                            <a href="{{ route('employees.index') }}" class="w-full flex items-center space-x-3 p-3 rounded-lg text-sm font-medium mb-2 transition-colors {{ request()->routeIs('employees.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span>Employees</span>
                            </a>
                        </li>
                        @endcan
                        @can('manage expenses') {{-- Or a new 'manage payroll' permission --}}
                        <li>
                            <a href="{{ route('payroll.index') }}" class="w-full flex items-center space-x-3 p-3 rounded-lg text-sm font-medium mb-2 transition-colors {{ request()->routeIs('payroll.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span>Payroll</span>
                            </a>
                        </li>
                        @endcan
                        @can('manage students')
                        <li>
                            <a href="{{ route('students.index') }}" class="w-full flex items-center space-x-3 p-3 rounded-lg text-sm font-medium mb-2 transition-colors {{ request()->routeIs('students.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197" /></svg>
                                <span>Students</span>
                            </a>
                        </li>
                        @endcan
                        @can('generate reports')
                        <li>
                            <a href="{{ route('reports.index') }}" class="w-full flex items-center space-x-3 p-3 rounded-lg text-sm font-medium mb-2 transition-colors {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Reports</span>
                            </a>
                        </li>
                        @endcan
                        {{-- Donations and Reports can be added here later --}}
                        @can('manage settings')
                        <li>
                            <a href="{{ route('settings.index') }}" class="w-full flex items-center space-x-3 p-3 rounded-lg text-sm font-medium mb-2 transition-colors {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <span>Settings</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </nav>
                <div class="mt-auto">
                    <div class="bg-slate-100 p-4 rounded-lg text-center">
                        <p class="text-sm font-semibold text-slate-800">Need Help?</p>
                        <p class="text-xs text-slate-500 mt-1 mb-3">Check our documentation or contact support.</p>
                        <a href="{{ route('help.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Documentation</a>
                        <button @click="contactModalOpen = true" class="mt-2 w-full bg-blue-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-blue-700 transition-colors">Contact Us</button>
                    </div>
                </div>
            </aside>

            <!-- Main content -->
            <main class="flex-1 flex flex-col overflow-hidden">
                @include('layouts.navigation')
                <div class="flex-1 overflow-y-auto p-8">
                    {{ $slot }}
                </div>
                <!-- Footer -->
                <footer class="text-center p-4 text-sm text-slate-500 border-t border-slate-200">
                    Copyright 2018 - {{ date('Y') }} &copy; <a href="https://kuzakizazi.com" target="_blank" class="hover:underline">Kuza Kizazi</a>. Version 1.0.0
                </footer>
            </main>

            <!-- Contact Form Modal -->
            <div x-show="contactModalOpen" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @click.self="contactModalOpen = false" x-cloak>
                <div class="bg-white rounded-2xl shadow-lg w-full max-w-lg p-8" @click.away="contactModalOpen = false">
                    <h2 class="text-xl font-bold text-slate-800 mb-2">Contact Support</h2>
                    <p class="text-sm text-slate-500 mb-1">Tel: +254745357116</p>
                    <p class="text-sm text-slate-500 mb-6">Email: info@kuzakizazi.com</p>
                    
                    <form action="{{ route('help.send') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <input type="hidden" name="name" value="{{ Auth::user()->name }}">
                            <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                            <div>
                                <label for="subject" class="block text-sm font-medium text-slate-700">Subject</label>
                                <input type="text" name="subject" id="subject" class="mt-1 block w-full rounded-md border-slate-300" required>
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-medium text-slate-700">Message</label>
                                <textarea name="message" id="message" rows="5" class="mt-1 block w-full rounded-md border-slate-300" required></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="contactModalOpen = false" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>