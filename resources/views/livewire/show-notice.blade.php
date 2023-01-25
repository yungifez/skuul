<div class="card">
    <div class="card-header">
        <h4 class="card-title">View {{$notice->title}}</h4>
    </div>
    <div class="card-body">
        <div class="my-3">
            {{$notice->content}}
        </div>
        @isset($notice->attachment)
            <a class="bg-blue-600 py-2 px-4  text-white rounded" href="{{asset('storage/'.$notice->attachment)}}" download="{{$notice->title}}-notice">
                <i class="fas fa-download "></i>
                Download attachment
            </a>
        @endisset
    </div>
</div>
