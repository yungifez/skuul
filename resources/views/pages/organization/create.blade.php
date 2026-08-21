@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.create'), 'text' => 'Create', 'active'],
]])

@section('title', __('Create organization'))

@section('page_heading', __('Create organization'))

@section('content')
    <april:card>
        <slot:title>Create organization</slot:title>
        <slot:description>Use one organization for a school group, district, or independent school. A campus can be added afterwards.</slot:description>
        <slot:content>
            <form method="POST" action="{{ route('organizations.store') }}" class="space-y-4 md:max-w-xl">
                @csrf
                <x-display-validation-errors />
                <april:input-group name="name" id="name" label="Organization name *" value="{{ old('name') }}" required />
                <april:input-group name="code" id="code" label="Organization code" value="{{ old('code') }}" />
                <div class="flex flex-col gap-2"><april:label for="address">Address</april:label><april:textarea name="address" id="address">{{ old('address') }}</april:textarea></div>
                <april:input-group name="email" id="email" type="email" label="Email" value="{{ old('email') }}" />
                <april:input-group name="phone" id="phone" type="tel" label="Phone" value="{{ old('phone') }}" />
                <april:button type="submit">Create organization</april:button>
            </form>
        </slot:content>
    </april:card>
@endsection
