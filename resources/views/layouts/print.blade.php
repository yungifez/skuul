<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <style>
        body{
            background-color: white;
        }
        .logo-wrapper{
            margin-left: auto;
            margin-right: auto;
            margin-top: 10px;
            margin-bottom: 10px;
            max-width: fit-content;
            text-align: center;
        }
        .logo{
            width: 100px;
            height: 80px;
           
        }
    </style>
</head>
<body>
    <div class=" my-2">
        <div class="logo-wrapper">
            <img src="{{asset(config('app.logo'))}}" alt="" class="logo" >
        </div>
        <div>
            <h1 class="text-capitalize text-center ">{{auth()->user()->school->name}}</h1>
            <h4 class="text-capitalize text-center ">{{auth()->user()->school->address}}</h4>
        </div>
    </div>
    @yield('content')
</body>
</html>