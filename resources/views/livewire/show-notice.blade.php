<div class="card">
    <div class="card-header">
        <h4 class="card-title">View {{$notice->title}}</h4>
    </div>
    <div class="card-body">
        {{$notice->content}}
    </div>
    @isset($notice->attachment)
        <a class="btn btn-primary col-11 col-md-3 m-3 " href="{{asset('storage/'.$notice->attachment)}}" download="{{$notice->title}}-notice">
            <i class="fas fa-download "></i>
            Download attachment
        </a>
    @endisset
</div>
