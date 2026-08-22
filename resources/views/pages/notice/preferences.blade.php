@extends('layouts.app', ['breadcrumbs' => [['href' => route('dashboard'), 'text' => 'Dashboard'], ['text' => 'Notice delivery', 'active']]])
@section('title', 'Notice delivery')
@section('page_heading', 'Notice delivery')
@section('content')
<april:card><slot:title>Optional notice email</slot:title><slot:description>In-app notices stay available. Account and safety messages are not controlled here.</slot:description><slot:content><form method="POST" action="{{ route('notice-preferences.update') }}" class="flex flex-col gap-4">@csrf @method('PUT')<input type="hidden" name="email_enabled" value="0"><label class="flex items-start gap-2"><input type="checkbox" name="email_enabled" value="1" class="mt-1" {{ $preference->email_enabled ? 'checked' : '' }}><span>Email me optional notices from this school.</span></label><april:button class="w-fit">Save preference</april:button></form></slot:content></april:card>
@endsection
