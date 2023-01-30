<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{$syllabus->name}}</h2>
    </div>
    <div class="card-body">
        <p class="my-3">
            {{$syllabus->description}}
        </p>
        <a class="bg-blue-600 py-2 px-4 text-white rounded" href="{{asset('storage/'.$syllabus->file)}}" download>
            <i class="fas fa-download"></i>
            Download
        </a> 
    </div>
</div>
