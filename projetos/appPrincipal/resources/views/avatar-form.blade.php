<x-layout doctitle="gerenciando avatar">
    <div class="container container--narrow py-md-5">
        <h2 class="text-center mb-3">Upload New Avatar</h2>
        <form action="/manage-avatar" method="post" enctype="multipart/form-data"><!--pra mandar arquivos precisa desse enctype-->
            @csrf
            <div class="mb-3">
                <input type="file" name="avatar" >
                @error('avatar')
                    <p class="alert small alert-danger shadow-sm">{{$message}}</p>
                @enderror
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
    </div>
</x-layout>