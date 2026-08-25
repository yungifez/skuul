@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['href' => route('organizations.edit', $organization), 'text' => 'Settings', 'active'],
]])

@section('title', __('Organization settings'))

@section('page_heading', __('Organization settings'))

@section('content')
    <april:card>
        <slot:title>{{ $organization->name }}</slot:title>
        <slot:content>
            <form method="POST" action="{{ route('organizations.update', $organization) }}" class="space-y-4 md:max-w-xl">
                @csrf
                @method('PUT')
                <x-display-validation-errors />
                <april:input-group name="name" id="name" label="Organization name *" value="{{ old('name', $organization->name) }}" required />
                <april:input-group name="code" id="code" label="Organization code *" value="{{ old('code', $organization->code) }}" required />
                <april:input-group name="address" id="address" type="text" label="Address" value="{{ old('address', $organization->address) }}" />
                <april:input-group name="email" id="email" type="email" label="Email" value="{{ old('email', $organization->email) }}" />
                <april:input-group name="phone" id="phone" type="tel" label="Phone" value="{{ old('phone', $organization->phone) }}" />
                <april:button type="submit">Save organization</april:button>
            </form>
        </slot:content>
    </april:card>
@endsection
