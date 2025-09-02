=== Comments Plus Plus ===
=== Comments Plus Plus ===
Contributors:      bhargavbhandari90, biliplugins
Donate link:       https://www.paypal.me/BnB90/20
Tags:              comments, auto-suggestion, comments++
Tags:              comments, auto-suggestion, comments++
Requires at least: 6.6
Tested up to:      6.8
Tested up to:      6.8
Stable tag:        1.0.0
License:           GPL-2.0-or-later
Requires PHP:      8.0
Requires PHP:      8.0
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

A different experience of commenting.

== Description ==

A gmail like auto-complete suggestion while adding comments.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/comments-plus-plus` directory, or install the plugin through the WordPress plugins screen directly.
1. Upload the plugin files to the `/wp-content/plugins/comments-plus-plus` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

== Screenshots ==

1. Auto-suggestion while adding comment.
2. Plugin Setting.

== External Services ==

This plugin connects to the following AI services to provide smart comment suggestions and AI-powered responses.

=== Gemini ===
When you add comments, the plugin sends:
- Your entered comment

This data is sent to:
- Google Generative AI API (Gemini)

Base URLs:
- https://generativelanguage.googleapis.com/
- https://aiplatform.googleapis.com/
- https://vertexai.googleapis.com/

The data is transmitted over HTTPS and used only to generate AI-powered responses. No personal identifying data is sent unless you explicitly include it in your comment.

You must provide your own Gemini API key for the plugin to function. Without it, no data will be sent.

*Service provider:* Google LLC  
*Terms of Service:* https://ai.google.dev/terms  
*Privacy Policy:* https://policies.google.com/privacy

=== Ollama ===
When using Ollama, prompts are processed locally via the Ollama runtime on your own server or machine.  
No data is sent to external services unless configured to do so.  
Refer to the Ollama documentation for setup.

*Service provider:* Ollama (Self-hosted)  
*Website:* https://ollama.ai/

=== OpenAI ===
When using OpenAI, the plugin sends:
- Your entered comment.

Base URL:
- https://api.openai.com/

Data is transmitted over HTTPS and used only to generate AI-powered responses. No personal identifying data is sent unless you explicitly include it in your prompt.

You must provide your own OpenAI API key for the plugin to function. Without it, no data will be sent.

*Service provider:* OpenAI, L.L.C.  
*Terms of Service:* https://openai.com/policies/terms-of-use  
*Privacy Policy:* https://openai.com/policies/privacy-policy

== GitHub Repo ==
[https://github.com/BhargavBhandari90/comments-plus-plus](https://github.com/BhargavBhandari90/comments-plus-plus)

== Frequently Asked Questions ==

= Do I need an API key to use this plugin? =
Yes. You must provide your own API key for Gemini or OpenAI. Ollama works locally and does not require a remote API key.

= Is my data stored anywhere? =
No. The plugin only sends the prompt and visible page content to the selected AI provider in real-time. It does not store data on external servers unless required by the provider.

= Can I use this plugin without an internet connection? =
Yes, if you use Ollama. Since Ollama runs locally, no internet connection is required (except for initial installation of Ollama and model downloads).

= Which AI providers are supported? =
Currently, the plugin supports:
- Google Generative AI (Gemini)
- Ollama (Local)
- OpenAI (ChatGPT models)

= Does the plugin send personal information? =
No personal data is sent unless you include it in your prompt or page content. You are responsible for the content you send to the AI providers.

= Can I switch between AI providers? =
Yes. You can select your preferred AI provider in the plugin settings at any time.
[https://github.com/BhargavBhandari90/comments-plus-plus](https://github.com/BhargavBhandari90/comments-plus-plus)

== Frequently Asked Questions ==

= Do I need an API key to use this plugin? =
Yes. You must provide your own API key for Gemini or OpenAI. Ollama works locally and does not require a remote API key.

= Is my data stored anywhere? =
No. The plugin only sends the prompt and visible page content to the selected AI provider in real-time. It does not store data on external servers unless required by the provider.

= Can I use this plugin without an internet connection? =
Yes, if you use Ollama. Since Ollama runs locally, no internet connection is required (except for initial installation of Ollama and model downloads).

= Which AI providers are supported? =
Currently, the plugin supports:
- Google Generative AI (Gemini)
- Ollama (Local)
- OpenAI (ChatGPT models)

= Does the plugin send personal information? =
No personal data is sent unless you include it in your prompt or page content. You are responsible for the content you send to the AI providers.

= Can I switch between AI providers? =
Yes. You can select your preferred AI provider in the plugin settings at any time.

== Changelog ==

= 1.0.0 =
* Inital Release
