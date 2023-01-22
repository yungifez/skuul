<div class="card">
   <div class="card-body">
      <div class=" profile-image-wrapper" >
         <img src="{{asset($user->profile_photo_url)}}" alt="user photo"  height="200px" class="m-auto rounded-full profile-image" width="200px" style="width: 175px;height: 175px;"/>  
      </div>
      <h2 class="text-center text-3xl m-4">{{$user->name}}</h2>
      <div class="w-full md:w-8/12 m-auto">
         <h4 class="text-left text-xl my-2">Personal information</h4>
         <x-show-table :body="[
               ['First Name' , $user->first_name],
               ['Last Name' , $user->last_name],
               ['Other Name' , $user->other_names],
               ['Email' , $user->email],
               ['Gender' , $user->gender],
               ['Birthday' , $user->birthday],
               ['nationality' , $user->nationality],
               ['state' , $user->state],
               ['City' , $user->city],
               ['Address' , $user->address],
               ['Religion' , $user->religion],
               ['Blood Group' , $user->blood_group],
               ['Phone' , $user->phone],

         ]"/>
      </div>
   </div>
</div>
