@extends('errors.layout')

@section('code', '403')
@section('title', 'Not open to you')
@section('message', $exception->getMessage() !== '' && $exception->getMessage() !== 'This action is unauthorized.' ? $exception->getMessage() : 'Your account cannot open this page. Ask your school administrator if you need it.')
