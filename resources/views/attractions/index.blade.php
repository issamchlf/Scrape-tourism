<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Malaga Tourism Attractions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/fallback.js') }}"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .attraction-card {
            transition: all 0.3s ease;
        }
        .attraction-card:hover {
            transform: translateY(-5px);
        }
        .image-container {
            position: relative;
            overflow: hidden;
        }
        .image-container img {
            transition: transform 0.5s ease;
        }
        .attraction-card:hover .image-container img {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center text-white hover:text-blue-100 transition">
                        <i class="fas fa-landmark text-2xl mr-2"></i>
                        <span class="text-xl font-bold">Malaga Tourism</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-gradient-to-b from-blue-800 to-blue-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Discover Malaga</h1>
            <p class="text-xl text-blue-100">Explore the best attractions and places to visit in Malaga</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($attractions as $attraction)
            <div class="attraction-card bg-white rounded-lg shadow-md overflow-hidden">
                <div class="image-container aspect-w-16 aspect-h-9">
                    @if($attraction->image_url && filter_var($attraction->images, FILTER_VALIDATE_URL))
                        <img src="{{ $attraction->images }}" 
                             alt="{{ $attraction->title ?? 'Attraction Image' }}"
                             class="w-full h-48 object-cover"
                             data-fallback="https://via.placeholder.com/400x300?text=No+Image+Available">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-image text-4xl text-gray-400"></i>
                        </div>
                    @endif
                </div>

                @if($attraction->categories->isNotEmpty())
                <div class="absolute top-2 right-2 flex flex-wrap gap-1">
                    @foreach($attraction->categories as $category)
                    <span class="px-2 py-1 text-xs font-semibold bg-blue-500 text-white rounded-full">
                        {{ $category->name }}
                    </span>
                    @endforeach
                </div>
                @endif

                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2 line-clamp-2">
                        {{ $attraction->title }}
                    </h3>
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        {{ $attraction->description }}
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-gray-500">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span class="text-sm">{{ $attraction->location ?? 'Malaga' }}</span>
                        </div>
                        <a href="{{ $attraction->website_url }}" 
                           target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            Visit
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $attractions->links() }}
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400">© {{ date('Y') }} Malaga Tourism. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
