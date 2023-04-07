<x-profile :sharedData="$sharedData" doctitle="quem {{$sharedData['username']}} segue">
  @include('profile-following-only')
</x-profile>