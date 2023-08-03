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
        header{
            display: table;
            width: 100%;
            margin-bottom:  1rem;
        }
        main{
            width: 100%;
        }
        .logo-wrapper{
            display: table-cell;
            vertical-align: middle;
            width: 5%;
        }
        .site-identity{
            display: table-cell;
            width: 95%;
        }
        .site-identity *{
            text-align: center;
        }
        .logo{
            width: 100px;
            height: 100px;
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
            font-size: 1.2rem;
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
            <img src="{{auth()->user()->school->logoURL ?? public_path().'/'.config('app.logo')}}" alt="" class="logo" >
        </div>
        <div class="site-identity">
            <h1 class=" ">{{auth()->user()->school->name}}</h1>
            <h2 class="">{{auth()->user()->school->address}}</h2>
        </div>
    </header>
    
    <main>
        @yield('content')
    </main>
</body>
</html>