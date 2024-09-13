const utils = {
    date: {
        // Formats a JavaScript Date object into a readable string
        formatDate: function (
            date,
            options = { year: "numeric", month: "long", day: "numeric" }
        ) {
            const d = new Date(date);
            return d.toLocaleDateString(undefined, options);
        },

        // Returns the difference in days between two dates
        daysBetween: function (startDate, endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        },

        // Adds a specific number of days to a date
        addDays: function (date, days) {
            const result = new Date(date);
            result.setDate(result.getDate() + days);
            return result;
        },
    },

    image: {
        // Returns a random placeholder image URL
        placeholder: function (width = 300, height = 300) {
            return `https://via.placeholder.com/${width}x${height}`;
        },

        // Validates if a URL is a valid image
        isValidImageUrl: function (url) {
            return /\.(jpg|jpeg|png|webp|avif|gif|svg)$/.test(url);
        },

        // Preloads an image
        preloadImage: function (url, callback) {
            const img = new Image();
            img.src = url;
            img.onload = callback;
        },
    },
};

export default utils;
