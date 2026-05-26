@props(['url'])

<tr>
<td class="header">

<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">

    <div style="
        font-size: 32px;
        font-weight: 900;
        color: #1d4ed8;
        letter-spacing: -1px;
        font-family: Arial, sans-serif;
    ">
        {{ \App\Models\SiteSetting::first()?->site_name ?? config('app.name') }}
    </div>

</a>

</td>
</tr>
