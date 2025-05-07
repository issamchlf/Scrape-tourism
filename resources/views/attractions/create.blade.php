<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Save Attraction</title>
</head>
<body>
    <h1>Save Attraction</h1>

    <form action="/attractions/store" method="POST">
        <!-- CSRF token would need to be handled differently in plain HTML -->
        <input type="hidden" name="website_url" value="">

        <div>
            <label>Name</label>
            <input type="text" name="name" value="" required>
        </div>

        <div>
            <label>Description</label>
            <textarea name="description"></textarea>
        </div>

        <div>
            <label>Address</label>
            <input type="text" name="address" value="">
        </div>

        <div>
            <label>Phone</label>
            <input type="text" name="phone" value="">
        </div>

        <div>
            <label>Hours</label>
            <input type="text" name="hours" value="">
        </div>

        <button type="submit">Save Attraction</button>
    </form>
</body>
</html>
