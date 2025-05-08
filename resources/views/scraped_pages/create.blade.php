<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Run Scrape</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 antialiased">
  <div class="max-w-3xl mx-auto p-6 bg-white shadow-md rounded-lg mt-10">
    <h1 class="text-2xl font-semibold mb-6">Run Scrape</h1>

    @if($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul class="list-disc list-inside">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('scraped-pages.store') }}" method="POST" class="space-y-4">
      @csrf

      <div>
        <label for="site_key" class="block text-sm font-medium mb-1">Site Key</label>
        <select name="site_key" id="site_key" 
                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
          @foreach(config('scrapers.sites') as $key => $url)
            <option value="{{ $key }}" @selected(old('site_key') == $key)>{{ $key }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label for="url" class="block text-sm font-medium mb-1">URL</label>
        <input type="url" name="url" id="url" placeholder="https://example.com/page"
               value="{{ old('url') }}" required
               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
      </div>

      <button type="submit" 
              class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow-sm transition-colors">
        Run Scrape
      </button>
    </form>

    @isset($scrapedData)
      <div class="mt-8">
        <h2 class="text-xl font-semibold mb-4">Results for <span class="text-blue-600">{{ $scrapedUrl }}</span></h2>
        <pre class="bg-gray-800 text-gray-100 p-4 rounded-md overflow-auto text-sm">
{{ json_encode($scrapedData, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}
        </pre>
      </div>
    @endisset
  </div>
</body>
</html>
