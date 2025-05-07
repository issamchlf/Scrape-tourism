<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scrape a Tourism URL</title>
</head>
<body>
    <h1>Scrape a Tourism URL</h1>

    <form action="{{ route('scraped-pages.store') }}" method="POST">
        @csrf

        <div>
            <label for="site_key">Site Key</label>
            <select name="site_key" id="site_key" required>
                <option value="esmadrid">EsMadrid</option>
                <option value="marbella">turismo.marbella.es</option>
            </select>
        </div>
        <div>
            <label for="url">Page URL</label>
            <input type="url" name="url" id="url"
                         placeholder="https://www.esmadrid.com/..." required>
        </div>

        <button type="submit">Run Scrape</button>
    </form>
    @if(isset($scrapedData))
  <h2>Raw Scrape Result for {{ $scrapedUrl }}</h2>
  <pre>{{ json_encode($scrapedData, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
@endif

</body>
</html>