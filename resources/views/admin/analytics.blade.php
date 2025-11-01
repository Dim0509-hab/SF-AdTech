<!-- Выданные ссылки -->
<h4>Выданные ссылки</h4>
<table class="table table-striped">
    <thead>
        <tr>
            <th>URL</th>
            <th>Оффер</th>
            <th>Веб‑мастер</th>
            <th>Дата</th>
        </tr>
    </thead>
    <tbody>
        @foreach($links as $link)
            <tr>
                <td><code>{{ $link->url }}</code></td>
                <td>{{ $link->offer->name }}</td>
                <td>{{ $link->user->email }}</td>
                <td>{{ $link->created_at->format('d.m.Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Переходы -->
<h4>Переходы за последнюю неделю</h4
