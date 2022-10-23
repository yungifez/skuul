<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
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
            border-radius: 50px;
        }
        p{
            padding: 0.5rem;
        }
        h1, h2, h3, h4, h5, h6 {
            text-align: center;
        }
        h1{
            font-size: 2rem;
        }
        h2{
            font-size: 1.5rem;
        }
        table,th,td {
            border: 1px solid rgba(46, 45, 45, 0.854);
            border-collapse: collapse;
        }
        table {
            width: 100%
        }
        th{
            font-weight: 700;
        }
        td, th {
            padding: 0.75rem;
        }
    </style>
    @yield('style')
</head>
<body>
    
    <div class=" my-2">
        <div class="logo-wrapper">
            <img src="{{public_path().'/'.config('app.logo')}}" alt="" class="logo" >
        </div>
        <div>
            <h1 class="text-capitalize text-center ">{{auth()->user()->school->name}}</h1>
            <h2 class="text-capitalize text-center ">{{auth()->user()->school->address}}</h2>
        </div>
    </div>
    @yield('content')
</body>
</html>