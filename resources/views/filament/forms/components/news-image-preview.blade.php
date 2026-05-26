@if ($getRecord()?->image)

    <div class="mb-4">

        <img
            src="{{ asset('storage/' . $getRecord()->image) }}"
            class="w-64 rounded-xl border"
        >

    </div>

@endif