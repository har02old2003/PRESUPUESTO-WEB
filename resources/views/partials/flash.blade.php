@if (session('success'))
    <div class="flash success">{{ session('success') }}</div>
@endif

@if (session('warning'))
    <div class="flash warning">{{ session('warning') }}</div>
@endif

@if ($errors->any())
    <div class="flash error">
        <ul style="margin:0; padding-left:1rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
