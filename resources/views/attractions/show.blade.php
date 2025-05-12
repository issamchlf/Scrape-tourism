<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attraction Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-2">{{ attraction.name }}</h1>
        <p class="text-gray-700 mb-4">{{ attraction.description }}</p>
        {% if attraction.address %}
            <p><strong>Address:</strong> {{ attraction.address }}</p>
        {% endif %}
        {% if attraction.phone %}
            <p><strong>Phone:</strong> {{ attraction.phone }}</p>
        {% endif %}
        {% if attraction.hours %}
            <p><strong>Hours:</strong> {{ attraction.hours }}</p>
        {% endif %}
        {% if attraction.image %}
            <img src="{{ attraction.image }}" alt="" class="mt-4 rounded shadow">
        {% endif %}
        <p class="mt-6">
            <a href="{{ attraction.website_url }}" target="_blank" class="text-blue-600">Visit Website</a>
        </p>
    </div>
</body>
</html>
