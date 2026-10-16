@extends('errors.layout')

@section('code', '410')
@section('title', 'No longer here')
@section('message', $exception->getMessage() ?: 'This item is no longer stored.')
