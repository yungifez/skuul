<div class="container">
    {{-- written to support css 2.1 --}}
    <div class="row justify-content-center text-center">
        <img src="{{$user->profile_photo_url}}" class="rounded-circle" alt="user photo"  height="200px" width="200px" style="width: 175px;height: 175px; "/>
    </div>
    <div class="row my-2">
        <h2 class="text-center col-md-12">{{$user->name}}</h2>
        <h4>Personal information</h4>
        <table class="table col-12 table-bordered">
            <tbody class="">
                <tr>
                    <th scope="row">First Name:</th>
                    <td>{{$user->firstName()}}</td>
                </tr>
                <tr>
                    <th scope="row">Last Name:</th>
                    <td>{{$user->lastName()}}</td>
                </tr>
                <tr>
                    <th scope="row">Other Names:</th>
                    <td>{{$user->otherNames()}} </td>
                </tr>
                <tr>
                    <th scope="row">Email:</th>
                    <td>{{$user->email}} </td>
                </tr>
                <tr>
                    <th scope="row">Gender:</th>
                    <td>{{$user->gender}} </td>
                </tr>
                <tr>
                    <th scope="row">Birthday (Y/M/D):</th>
                    <td>{{$user->birthday}} </td>
                </tr>
                <tr>
                    <th scope="row">Nationality:</th>
                    <td>{{$user->nationality}} </td>
                </tr>
                <tr>
                    <th scope="row">State:</th>
                    <td>{{$user->state}} </td>
                </tr>
                <tr>
                    <th scope="row">City:</th>
                    <td>{{$user->city}} </td>
                </tr>
                <tr>
                    <th scope="row">Address:</th>
                    <td>{{$user->address}} </td>
                </tr>
                <tr>
                    <th scope="row">Religion:</th>
                    <td>{{$user->religion}} </td>
                </tr>
                <tr>
                    <th scope="row">Blood Group:</th>
                    <td>{{$user->blood_group}} </td>
                </tr>
                <tr>
                    <th scope="row">Phone:</th>
                    <td>{{$user->phone}} </td>
                </tr>
                <tr>
                    <th scope="row">School (Branch):</th>
                    <td>{{$user->school->name}} </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
