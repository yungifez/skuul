@extends('layouts.print')

@section('title', $feeInvoice->name)

@section('content')
    @livewire('show-fee-invoice', ['feeInvoice' => $feeInvoice])
@endsection

@section('style')
    <style>
        .parent-horizontal-align {
            position: relative;
            width: 100%;
        }
        .parent-horizontal-align::after {
            content: "";
            display: block;
            clear: both;
        }
        .horizontal-align{
            float: left;
            width: 50%;
        }
        .my-2{
            margin: 0.25rem 0;
        }
        .my-3{
            margin: 0.5rem 0;
        }
        h3{
            text-align: left;
        }
        p{
            padding: 2px;
            margin: 2px
        }
        .text-right{
            text-align: right
        }
        .status{
            font-size: large;
            text-align: center;
            color: white;
            padding: 1rem;
        }
        .w-full{
            width: 100%;
        }
        .bg-green-500{
            background-color: rgb(34 197 94 / 1);
        }
        .bg-yellow-500{
            background-color: rgb(234 179 8 / 1);
        }
        .bg-red-500{
            background-color: rgb(239 68 68 / 1);
        }
        .p-4{
            padding: 1rem;
        }
    </style>
@endSection