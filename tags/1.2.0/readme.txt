=== AI Featured Image Generator ===
Contributors: Amr267
Donate link: https://buymeacoffee.com/unclegold
Tags: featured image, ai, image generation, openai, gpt-4
Requires at least: 5.8
Tested up to: 6.8.3
Stable tag: 1.2.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate AI-powered featured images for your WordPress posts & pages using OpenAI Models.

== Description ==

AI Featured Image Generator is a WordPress plugin that adds a "Generate Featured Image" button to your post editor. When clicked, it uses AI image generation APIs to create a unique featured image based on your post title, selected style, and optional custom prompt.

= Features =

* Generate featured images using OpenAI Models (GPT Image 1 & GPT Image 1 Mini)
* Custom prompt input for fine-tuning image generation
* Multiple style presets (Realistic, Artistic, Cartoon, Sketch, Watercolor, 3D Render, Pixel Art, Cyberpunk, Fantasy, Anime, Minimalist, Technicolor)
* Configurable image sizes (Square, Portrait, Landscape)
* Output format selection (WebP, PNG, JPEG) with WebP as default
* Image quality control (1-100) for both global settings and individual posts/pages
* AI model selection (GPT Image 1 Standard or GPT Image 1 Mini for faster/cheaper generation)
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

Currently, the plugin only supports OpenAI Models. Support for other AI providers may be added in future versions.

= Is there a limit to how many images I can generate? =

The limit depends on your OpenAI API plan and quota. The plugin generates one image per request.

= What styles are available? =

The plugin supports 12 different artistic styles:
- None: No specific style applied
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

= What output formats are supported? =

The plugin supports three output formats:
- WebP (Default): Best compression and quality balance
- PNG: Lossless format, good for graphics with sharp edges
- JPEG: Widely compatible, good for photographs

= How does quality control work? =

You can control image quality from 1 (lowest) to 100 (highest):
- Global setting: Set default quality in Settings → AI Featured Image
- Per-post control: Override quality for individual post/pages in the metabox
- Higher quality produces larger file sizes but better image detail
- Default quality is set to 90 for optimal balance

= What AI models are available? =

The plugin supports two OpenAI models:
- GPT Image 1 (Standard): Default model with consistent, high-quality results
- GPT Image 1 Mini: Faster and more cost-effective, may produce slightly different results

== Screenshots ==

1. Settings page
2. Post/page editor meta box
3. Generated image preview

== Changelog ==

= 1.2.0 =
* Added output format selection (WebP, PNG, JPEG) with WebP as default
* Added image quality control (1-100) for both global settings and individual posts/pages
* Added AI model selection (GPT Image 1 Standard vs GPT Image 1 Mini)
* Updated image sizes to support current OpenAI API specifications
* Enhanced settings page with new quality and model controls
* Improved metabox with quality slider and real-time value display
* Added comprehensive CSS styling for quality controls
* Updated API integration to use quality and model parameters

= 1.1.0 =
* Improved UI for a smoother experience
* Fixed bug that forced text on image when the option is selected
* Replaced unfriendly error messages with user-friendly messages
* Updated plugin logo from PNG to SVG
* Added a "none" style option and set it as the default

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

= 1.2.0 =
Major update with new features: output format selection (WebP, PNG, JPEG), image quality control (1-100) for both global and per-post settings, AI model selection (GPT Image 1 vs GPT Image 1 Mini), updated image sizes, enhanced UI with quality sliders, and improved API integration

= 1.1.0 =
Improved UI for a smoother experience, fixed bug that forced text on image when the option is selected, replaced unfriendly error messages with user-friendly messages, updated plugin logo from PNG to SVG, and added a "none" style option and set it as the default

= 1.0.4 =
Added a loader spinner icon, improved UI styling, and activated the feature on the Pages post type

= 1.0.3 =
Added loader spinner icon and improved UI

= 1.0.0 =
Initial release 