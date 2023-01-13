<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <style>
        *{
            font-family: Helvetica, sans-serif;
        }
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
            padding: 0.45rem;
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
            width: 100%;
            vertical-align: middle;
            text-align: center;
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
    
    <header>
        <div class="logo-wrapper">
            <img src="{{public_path().'/'.config('app.logo')}}" alt="" class="logo" >
        </div>
        <div>
            <h1 class=" ">{{auth()->user()->school->name}}</h1>
            <h2 class="">{{auth()->user()->school->address}}</h2>
        </div>
    </header>
    @yield('content')
</body>
</html>