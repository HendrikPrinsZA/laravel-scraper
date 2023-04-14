# Roadmap
Collection of ideas as I'm experimenting and exploring with random ideas

## Fetching data bedind authenticated portals
This could be particularly useful with scraping data from Medium.com, as a lot of authors post their content behind the paywall. 

### Integrate with OpenAI to generate feedback on articles
The main goal about fetching blog posts is to generate some feedback from AI. Have it read and review the piece to provide some consructive criticism. Parsing the raw HTML to markdown is just the beginning, as it should enable the AI top interpret the content more logically. From this point the AI we could ask the engine to provide some feedback.

**Ideas**
- Evaluate the title and provide some alternatives
- Interpret the content and provide recommendations on how to improve it

**References**
- https://github.com/openai-php/laravel
- https://github.com/openai-php/client

### [Symfony BrowserKit Component](https://symfony.com/components/BrowserKit) 
_[Previously Goutte](https://github.com/FriendsOfPHP/Goutte)_

Fetching data behind a paywall is challenging and very limited. As some of these online services don't provide an API layer. One alternative is to replicate the user's normal authentication journey to get to the data.

**References**
- https://symfony.com/doc/current/components/browser_kit.html
- https://zubairidrisaweda.medium.com/introduction-to-web-scraping-with-laravel-a217e1444f7c

