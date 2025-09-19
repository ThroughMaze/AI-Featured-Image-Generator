=== AI Featured Image Generator ===
Contributors: Amr267
Tags: featured image, ai, image generation, openai, gpt-4
Requires at least: 5.8
Tested up to: 6.8
Stable tag: 1.0.4
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate AI-powered featured images for your WordPress posts using OpenAI's GPT-4 Vision (gpt-image-1).

== Description ==

AI Featured Image Generator is a WordPress plugin that adds a "Generate Featured Image" button to your post editor. When clicked, it uses AI image generation APIs to create a unique featured image based on your post title, selected style, and optional custom prompt.

= Features =

* Generate featured images using OpenAI's GPT-4 Vision (gpt-image-1) API
* Custom prompt input for fine-tuning image generation
* Multiple style presets (Realistic, Artistic, Cartoon, Sketch, Watercolor, 3D Render, Pixel Art, Cyberpunk, Fantasy, Anime, Minimalist, Technicolor)
* Configurable image sizes (Square, Portrait, Landscape, Wide)
* Works with both Gutenberg and Classic editors
* Multisite support
* Translation ready
* Option to allow or disallow text, captions, or words in generated images

= Requirements =

* WordPress 5.8 or higher
* PHP 7.4 or higher
* OpenAI API key

== External Services ==

This plugin connects to OpenAI's API to generate AI-powered images for your WordPress posts and pages. This service is required for the core functionality of generating featured images.

**What data is sent and when:**
- Your post title and content are sent to OpenAI's API when you generate a featured image
- Any custom prompt you provide is sent to the API
- Your OpenAI API key is used for authentication with each request
- Image generation requests are sent to OpenAI's servers when you click the "Generate Featured Image" button

**Service Provider:**
This service is provided by OpenAI: [Terms of Service](https://openai.com/policies/terms-of-use), [Privacy Policy](https://openai.com/policies/privacy-policy)

**Data Usage:**
- Your post content and prompts are processed by OpenAI's AI models to generate images
- Generated images are stored in your WordPress media library
- No personal data is stored by OpenAI beyond what's necessary for API processing
- You are responsible for ensuring your content complies with OpenAI's usage policies

== Installation ==

1. Upload the `ai-featured-image-generator` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings → AI Featured Image to configure your API key and other settings

== Frequently Asked Questions ==

= Do I need an API key? =

Yes, you need an OpenAI API key to use this plugin. You can get one by signing up at [OpenAI's website](https://platform.openai.com/).

= How much does it cost? =

The plugin itself is free, but you'll need to pay for API usage according to OpenAI's pricing. Check their website for current rates.

= Can I use a different AI image API? =

Currently, the plugin only supports OpenAI's GPT-4 Vision (gpt-image-1) API. Support for other APIs may be added in future versions.

= Is there a limit to how many images I can generate? =

The limit depends on your OpenAI API plan and quota. The plugin generates one image per request.

= What styles are available? =

The plugin supports 12 different artistic styles:
- Realistic: Photographic, lifelike images
- Artistic: Painterly, artistic interpretations
- Cartoon: Animated, cartoon-style images
- Sketch: Detailed sketch drawings
- Watercolor: Beautiful watercolor paintings
- 3D Render: High-quality 3D rendered images
- Pixel Art: Retro pixel art style
- Cyberpunk: Vibrant cyberpunk aesthetic
- Fantasy: Magical fantasy art style
- Anime: Detailed anime/manga style
- Minimalist: Clean, minimalist designs
- Technicolor: Vivid, colorful technicolor style

== Screenshots ==

1. Settings page
2. Post editor meta box
3. Generated image preview

== Changelog ==

= 1.0.4 =
* Added a loader spinner icon
* Improved UI styling
* Activated the feature on the Pages post type

= 1.0.3 =
* Added loader spinner icon
* Improved UI
* Activated feature on Pages post type

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.4 =
Added a loader spinner icon, improved UI styling, and activated the feature on the Pages post type

= 1.0.3 =
Added loader spinner icon and improved UI

= 1.0.0 =
Initial release 