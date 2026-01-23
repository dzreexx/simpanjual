<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="flex flex-col justify-center items-center h-screen">
        <h1 class="text-3xl mb-4">Login</h1>
        
        @if ($errors->any())
            <div role="alert" class="alert alert-error mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- @if (session('success'))
            <div role="alert" class="alert alert-success mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('success') }}
            </div>    
            @dd(session());
        @endif --}}

        <form action="{{url('/login')}}" method="POST" class="flex flex-col gap-2 w-80">
            @csrf
            <input type="text" name="email" placeholder="Email" class="input input-bordered w-full" value="{{ old('email') }}">
            <input type="password" name="password" placeholder="Password" class="input input-bordered w-full">
            <button type="submit" class="btn btn-primary w-full">Login</button>
        </form>
        <a href="{{url('/logcheck')}}">Cek Login</a>
        <a href="{{url('/logout')}}">Logout</a>
        <small>Belum punya akun? <a href="{{url('/daftar')}}">Daftar</a></small>
    </div>
</body>
</html>