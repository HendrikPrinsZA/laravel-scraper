# Laravel Scraper
<p align="center">
<img src="resources/images/cowboy-in-clouds.png" width="80%" alt="Laravel Logo">
</p>

Refer to the article published on <a href="https://hendrikprinsza.medium.com/">Medium</a>, see <a href="https://hendrikprinsza.medium.com/practical-guide-how-to-build-a-web-scraper-with-laravel-actions-63575c33df71">Practical Guide: How to Build a Web Scraper with Laravel Actions</a>.

## Getting started
To get going you will need <a href="https://www.docker.com/">Docker</a> and <a href="https://getcomposer.org/">Composer</a>.
```sh
# Clone repository 
git clone git@github.com:HendrikPrinsZA/laravel-scraper.git

# Navigate to directory
cd laravel-scraper

# Install Composer dependencies
composer install 

# Copy the example env config
cp .env.example .env

# Spin up environment 
./vendor/bin/sail up -d

# Run migrations and seeds
./vendor/bin/sail artisan migrate --seed
```

## Commands
- Fetch top posts from Reddit
  - `sail artisan scraper:fetch-reddit-posts`
- Fetch top posts from Twitter
  - `sail artisan scraper:fetch-twitter-posts`
- Fetch bicycles impounded in The Netherlands
  - `sail artisan scraper:fetch-bicycles`

---
## Related articles
- <a href="https://hendrikprinsza.medium.com/practical-guide-how-to-build-a-web-scraper-with-laravel-actions-63575c33df71">Practical Guide: How to Build a Web Scraper with Laravel Actions</a>
- <a href="https://hendrikprinsza.medium.com/trending-posts-related-to-php-and-laravel-in-march-2023-f640e5c97436">Trending Posts Related to PHP and Laravel in March 2023</a>
