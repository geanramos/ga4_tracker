# GA4 Tracker for YOURLS

This plugin integrates your YOURLS installation with Google Analytics 4 (GA4), allowing you to track every click on your shortened links as a custom event. Communication is handled securely and efficiently using Google's Measurement Protocol v2, without impacting the user's redirection speed.

**Plugin Version:** 1.0

**YOURLS Compatibility:** 1.7.x or higher

**Author:** [Gean Ramos](https://geanramos.com.br)

---

## Table of Contents

- [What Does This Plugin Do?](#what-does-this-plugin-do)
- [Why Use This Plugin?](#why-use-this-plugin)
- [How It Works](#how-it-works)
- [Installation and Configuration](#installation-and-configuration)
- [Verifying It Works](#verifying-it-works)
- [UTM Parameter Compatibility](#utm-parameter-compatibility)
- [Contributing](#contributing)
- [License](#license)

## What Does This Plugin Do?

* **Native GA4 Integration:** Sends data directly to your Google Analytics 4 property.
* **Event Tracking:** Each click on a shortened link is recorded as a custom event named `yourls_redirect`.
* **Uses the Measurement Protocol:** It uses the official and recommended method by Google for sending server-side data, ensuring speed and reliability.
* **Simple Configuration Panel:** Adds an easy-to-use admin page for you to securely enter your GA4 credentials.

## Why Use This Plugin?

1.  **Maximum Speed:** Unlike JavaScript-based methods, tracking via the Measurement Protocol happens on the server. This means your user's redirection experiences **no delay**.
2.  **Superior Reliability:** Server-to-server communication is more robust and is not affected by ad-blockers or the user's browser privacy settings, ensuring more accurate data collection.
3.  **Detailed Data for Analysis:** Having a dedicated `yourls_redirect` event allows you to create specific reports, funnels, and segments in GA4 to analyze the performance of your shortened links, which isn't possible with standard pageview tracking.
4.  **Security and Best Practices:** Your credentials (API Secret) are stored securely in the YOURLS database, and communication with Google is done via HTTPS.

## How It Works

The plugin uses a modern and efficient approach to tracking:

1.  **Redirection Hook:** The plugin uses the `pre_redirect` hook from YOURLS, which is a function triggered seconds before a link is redirected.
2.  **Data Collection:** At this moment, the plugin collects essential information: the destination long URL, the keyword (short URL), and anonymous user data, such as the IP address and User-Agent (browser information).
3.  **`client_id` Creation:** GA4 requires a `client_id` to identify a "user." The plugin creates a pseudo-anonymous identifier by combining and hashing (with SHA1) the IP and User-Agent, respecting privacy.
4.  **Sending via Measurement Protocol:** With all the data ready, the plugin builds a JSON request and sends it to Google Analytics' servers in the background, without waiting for a response, ensuring the user is redirected instantly.

## Installation and Configuration

Follow these steps to get the plugin running in minutes.

### 1. Plugin Installation

1.  Create a folder named `ga4-tracker` inside the `/user/plugins/` directory of your YOURLS installation.
2.  Create a file named `plugin.php` inside the `ga4-tracker` folder.
3.  Copy and paste the [plugin code](https://raw.githubusercontent.com/geanramos/ga4_tracker/refs/heads/main/plugin.php) into the `plugin.php` file you created.
4.  Go to your YOURLS admin page, click on **Manage Plugins**, and activate the **"GA4 Tracker"** plugin.

### 2. Configuration in Google Analytics 4

For the plugin to work, you need a **Measurement ID** and an **API Secret**.

1.  **Get the Measurement ID:**
    * Go to your GA4 property.
    * Navigate to **Admin** > **Data Streams**.
    * Click on your web data stream. Your Measurement ID (e.g., `G-XXXXXXXXXX`) will be visible. Copy it.

2.  **Create the API Secret:**
    * On the same "Data Streams" screen, click on **Measurement Protocol API secrets**.
    * Click **Create**, give it a nickname (e.g., `YOURLS Plugin`), and click **Create** again.
    * **Copy the "Secret value" immediately.** It will not be shown again.

For more details, refer to the [official Google documentation](https://developers.google.com/analytics/devguides/collection/protocol/v2/getting-started).

### 3. Plugin Configuration in YOURLS

1.  After activating the plugin, a new link named **"GA4 Tracker Settings"** will appear in the admin menu.
2.  Click on it, enter the **Measurement ID** and **API Secret** you obtained, and click **"Save Settings"**.

## Verifying It Works

The best way to verify that events are being sent is by using the **DebugView** in GA4:

1.  Click on one of your shortened links to generate an event.
2.  In GA4, go to **Admin** > **DebugView**.
3.  Within a few seconds, you should see the `yourls_redirect` event appear on the timeline, confirming that the integration is working.

## UTM Parameter Compatibility

This plugin is **100% compatible** with URLs containing UTM parameters (`utm_source`, `utm_medium`, etc.) and actually enhances tracking.

| Step | Action | Result |
| :--- | :--- | :--- |
| **1. Click** | User clicks `sho.rt/promo23`. | The process begins. |
| **2. Plugin** | Our plugin sends the `yourls_redirect` event to GA4 with the long URL and its UTMs. | You get real-time click data. |
| **3. YOURLS** | Redirects the user's browser to the long URL with UTMs. | The user reaches the correct destination. |
| **4. GA4** | The script on your destination site reads the UTMs from the URL and attributes the session to your campaign. | Your Traffic Acquisition reports are correct. |

You get the best of both worlds: standard GA4 campaign tracking working perfectly and a custom event to analyze clicks specifically.

## Contributing

Contributions are welcome! Feel free to open an *issue* to report bugs or suggest improvements, or submit a *pull request* with your changes.

## License

This plugin is distributed under the [MIT License](https://github.com/geanramos/ga4_tracker/blob/main/LICENSE).
