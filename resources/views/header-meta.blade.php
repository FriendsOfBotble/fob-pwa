<meta name="theme-color" content="{{ $themeColor }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="{{ $appName }}">
<link rel="manifest" href="{{ asset('pwa/manifest.json') }}">
<link rel="apple-touch-icon" href="{{ RvMedia::getImageUrl(setting('pwa_icon', theme_option('logo')), null, false, RvMedia::getDefaultImage()) }}">
