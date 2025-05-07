<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Run Scrape</title>
</head>
<body>
  <h1>Run Scrape</h1>

  @if($errors->any())
    <div style="color:red">
      <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <form action="{{ route('scraped-pages.store') }}" method="POST">
    @csrf

    <label>Site Key:</label>
    <select name="site_key">
      @foreach(config('scrapers.sites') as $key => $url)
        <option value="{{ $key }}" @selected(old('site_key')==$key)>{{ $key }}</option>
      @endforeach
    </select><br><br>

    <label>URL:</label>
    <input type="url" name="url" value="{{ old('url') }}" required style="width:400px"><br><br>

    <button type="submit">Run Scrape</button>
  </form>

  @isset($scrapedData)
    <h2>Results for {{ $scrapedUrl }}</h2>
    <pre>{{ json_encode($scrapedData, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
  @endisset
</body>
</html>
