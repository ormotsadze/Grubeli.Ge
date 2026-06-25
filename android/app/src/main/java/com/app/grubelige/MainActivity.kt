@file:OptIn(ExperimentalMaterial3Api::class)

package com.app.grubelige

import android.Manifest
import android.annotation.SuppressLint
import android.app.NotificationChannel
import android.app.NotificationManager
import android.appwidget.AppWidgetManager
import android.content.ComponentName
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.os.PowerManager
import android.provider.Settings
import android.webkit.WebChromeClient
import android.util.Log
import android.view.WindowManager
import android.webkit.*
import androidx.activity.ComponentActivity
import androidx.activity.compose.BackHandler
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Dashboard
import androidx.compose.material.icons.filled.Settings
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.PlatformTextStyle
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.viewinterop.AndroidView
import androidx.core.content.ContextCompat
import androidx.core.content.edit
import androidx.core.net.toUri
import androidx.core.splashscreen.SplashScreen.Companion.installSplashScreen
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.app.grubelige.ui.theme.GrubeliGeTheme
import com.google.android.gms.location.LocationServices
import com.google.firebase.messaging.FirebaseMessaging
import org.json.JSONObject
import kotlin.math.roundToInt

object WeatherUtils {
    const val PREFS_NAME = "WeatherPrefs"
    const val BASE_URL = "https://grubeli.ge"

    fun cleanCityName(name: String): String {
        return name.trim()
            .replace("ამინდი ", "")
            .replace("ში", "")
    }

    fun updateWidget(context: Context) {
        val manager = AppWidgetManager.getInstance(context)
        val componentName = ComponentName(context, WeatherWidget::class.java)
        val ids = manager.getAppWidgetIds(componentName)
        if (ids != null && ids.isNotEmpty()) {
            val intent = Intent(context, WeatherWidget::class.java).apply {
                action = AppWidgetManager.ACTION_APPWIDGET_UPDATE
                putExtra(AppWidgetManager.EXTRA_APPWIDGET_IDS, ids)
            }
            context.sendBroadcast(intent)
        }
    }
}

class MainActivity : ComponentActivity() {

    private var isWebViewLoaded by mutableStateOf(false)

    inner class WebAppInterface(private val webView: WebView?) {
        
        @JavascriptInterface
        @Suppress("unused")
        fun shareApp(text: String, url: String) {
            try {
                val shareContent = "$text\n$url"
                val sendIntent = Intent().apply {
                    action = Intent.ACTION_SEND
                    putExtra(Intent.EXTRA_TEXT, shareContent)
                    type = "text/plain"
                }
                val shareIntent = Intent.createChooser(sendIntent, "გაზიარება")
                startActivity(shareIntent)
            } catch (e: Exception) {
                Log.e("WebAppInterface", "shareApp error", e)
            }
        }

        @JavascriptInterface
        @Suppress("unused")
        fun share(text: String) {
            try {
                val sendIntent = Intent().apply {
                    action = Intent.ACTION_SEND
                    putExtra(Intent.EXTRA_TEXT, text)
                    type = "text/plain"
                }
                val shareIntent = Intent.createChooser(sendIntent, "გაზიარება")
                startActivity(shareIntent)
            } catch (e: Exception) {
                Log.e("WebAppInterface", "Share failed", e)
            }
        }

        @JavascriptInterface
        @Suppress("unused")
        fun updateWidgetLocation(cityName: String) {
            if (cityName.isBlank() || cityName == "საქართველო") return
            val prefs = getSharedPreferences(WeatherUtils.PREFS_NAME, MODE_PRIVATE)
            val cleanedCity = WeatherUtils.cleanCityName(cityName)
            prefs.edit(commit = true) { 
                putString("widget_city_name", cleanedCity)
                putString("last_viewed_city", cleanedCity) 
            }
            WeatherUtils.updateWidget(this@MainActivity)
        }

        @JavascriptInterface
        @Suppress("unused")
        fun saveWeatherData(jsonData: String) {
            try {
                val data = JSONObject(jsonData)
                val prefs = getSharedPreferences(WeatherUtils.PREFS_NAME, MODE_PRIVATE)

                val cityName = WeatherUtils.cleanCityName(data.optString("city_name"))
                val tempVal = data.opt("temp")?.toString()?.replace(",", ".")?.toDoubleOrNull()
                
                val isDayObj = data.opt("is_day")
                val isDay = when (isDayObj) {
                    is Boolean -> isDayObj
                    is Int -> isDayObj == 1
                    is String -> isDayObj == "true" || isDayObj == "1"
                    else -> true
                }

                prefs.edit(commit = true) {
                    if (cityName.isNotBlank() && cityName != "undefined") {
                        putString("widget_city_name", cityName)
                        putString("last_viewed_city", cityName)
                    }
                    if (tempVal != null) {
                        putInt("widget_temp", tempVal.roundToInt())
                        putLong("widget_last_update", System.currentTimeMillis())
                    }
                    putString("widget_desc", data.optString("desc"))
                    putInt("widget_code", data.optInt("code", 0))
                    putBoolean("widget_is_day", isDay)
                }
                WeatherUtils.updateWidget(this@MainActivity)
                Log.d("WeatherBridge", "Widget updated: $cityName, Temp: $tempVal, Day: $isDay")
            } catch (e: Exception) {
                Log.e("WeatherBridge", "JSON parse error", e)
            }
        }

        @JavascriptInterface
        @Suppress("unused")
        fun getNativeLocation() {
            if (ContextCompat.checkSelfPermission(this@MainActivity, Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED) {
                val fusedLocationClient = LocationServices.getFusedLocationProviderClient(this@MainActivity)
                fusedLocationClient.lastLocation.addOnSuccessListener { location ->
                    if (location != null) {
                        runOnUiThread {
                            val js = "javascript:if(window.setGPSLocation) window.setGPSLocation(${location.latitude}, ${location.longitude});"
                            webView?.loadUrl(js)
                        }
                    }
                }
            } else {
                runOnUiThread {
                    requestPermissionsLauncher.launch(arrayOf(
                        Manifest.permission.ACCESS_FINE_LOCATION,
                        Manifest.permission.ACCESS_COARSE_LOCATION
                    ))
                }
            }
        }

        @JavascriptInterface
        @Suppress("unused")
        fun getFCMToken() {
            FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
                if (task.isSuccessful) {
                    pushTokenToJS(task.result)
                }
            }
        }
    }

    private fun pushTokenToJS(token: String?) {
        if (token == null) return
        runOnUiThread {
            val webView = findViewById<WebView>(android.R.id.primary)
            val js = "javascript:if(window.setFCMToken) window.setFCMToken('$token');"
            webView?.loadUrl(js)
        }
    }

    private val requestPermissionsLauncher = registerForActivityResult(
        ActivityResultContracts.RequestMultiplePermissions()
    ) { permissions ->
        if (permissions[Manifest.permission.POST_NOTIFICATIONS] == true) {
            subscribeToUrgentAlerts()
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        val splashScreen = installSplashScreen()
        super.onCreate(savedInstanceState)

        CookieManager.getInstance().setAcceptCookie(true)
        enableEdgeToEdge()
        window.addFlags(WindowManager.LayoutParams.FLAG_HARDWARE_ACCELERATED)

        createNotificationChannel()
        subscribeToUrgentAlerts()
        checkBatteryOptimization()

        val permissionsToRequest = mutableListOf(
            Manifest.permission.ACCESS_FINE_LOCATION,
            Manifest.permission.ACCESS_COARSE_LOCATION
        )
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            permissionsToRequest.add(Manifest.permission.POST_NOTIFICATIONS)
        }
        requestPermissionsLauncher.launch(permissionsToRequest.toTypedArray())

        splashScreen.setKeepOnScreenCondition { !isWebViewLoaded }

        setContent {
            GrubeliGeTheme {
                Scaffold(
                    modifier = Modifier.fillMaxSize(),
                    topBar = {
                        TopAppBar(
                            title = {
                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    Text("GRUBELI.GE", fontSize = 13.sp, fontWeight = FontWeight.Bold)
                                    Spacer(modifier = Modifier.width(6.dp))
                                    ProBadge()
                                }
                            },
                            actions = {
                                IconButton(onClick = { requestPinWidget() }) { Icon(Icons.Default.Dashboard, contentDescription = "Add Widget") }
                                IconButton(onClick = { openSettings() }) { Icon(Icons.Default.Settings, contentDescription = "Settings") }
                            }
                        )
                    }
                ) { innerPadding ->
                    MainScreen(
                        modifier = Modifier.padding(innerPadding).fillMaxSize(),
                        onPageLoaded = { isWebViewLoaded = true },
                        activity = this
                    )
                }
            }
        }
    }

    private fun subscribeToUrgentAlerts() {
        FirebaseMessaging.getInstance().subscribeToTopic("urgent_alerts")
    }

    @SuppressLint("BatteryLife")
    private fun checkBatteryOptimization() {
        val pm = getSystemService(POWER_SERVICE) as PowerManager
        if (!pm.isIgnoringBatteryOptimizations(packageName)) {
            try {
                val intent = Intent(Settings.ACTION_REQUEST_IGNORE_BATTERY_OPTIMIZATIONS).apply {
                    data = "package:$packageName".toUri()
                }
                startActivity(intent)
            } catch (e: Exception) {}
        }
    }

    private fun requestPinWidget() {
        val mAppWidgetManager = getSystemService(AppWidgetManager::class.java)
        val myProvider = ComponentName(this, WeatherWidget::class.java)
        if (mAppWidgetManager?.isRequestPinAppWidgetSupported == true) {
            mAppWidgetManager.requestPinAppWidget(myProvider, null, null)
        }
    }

    private fun openSettings() {
        startActivity(Intent(Settings.ACTION_APPLICATION_DETAILS_SETTINGS).apply {
            data = Uri.fromParts("package", packageName, null)
        })
    }

    private fun createNotificationChannel() {
        val channel = NotificationChannel(
            "urgent_alerts_channel",
            "Urgent Alerts",
            NotificationManager.IMPORTANCE_HIGH
        ).apply {
            description = "Weather updates and alerts"
        }
        val manager = getSystemService(NOTIFICATION_SERVICE) as NotificationManager
        manager.createNotificationChannel(channel)
    }
}

@Composable
fun ProBadge() {
    Box(
        modifier = Modifier
            .offset(y = (-1).dp)
            .shadow(elevation = 2.dp, shape = RoundedCornerShape(4.dp))
            .background(
                brush = Brush.linearGradient(listOf(Color(0xFF4285F4), Color(0xFF9B51E0))),
                shape = RoundedCornerShape(4.dp)
            )
            .height(14.dp) // ფიქსირებული სიმაღლე ეხმარება ცენტრირებაში
            .padding(horizontal = 6.dp),
        contentAlignment = Alignment.Center
    ) {
        Text(
            text = "PRO",
            color = Color.White,
            fontSize = 8.sp,
            fontWeight = FontWeight.ExtraBold,
            letterSpacing = 1.sp,
            style = LocalTextStyle.current.copy(
                platformStyle = PlatformTextStyle(includeFontPadding = false),
                lineHeight = 8.sp
            )
        )
    }
}

@SuppressLint("SetJavaScriptEnabled")
@Composable
fun MainScreen(modifier: Modifier = Modifier, onPageLoaded: () -> Unit, activity: MainActivity) {
    var webViewInstance by remember { mutableStateOf<WebView?>(null) }
    val canGoBack = remember { mutableStateOf(false) }

    BackHandler(enabled = canGoBack.value) {
        if (webViewInstance?.canGoBack() == true) webViewInstance?.goBack()
    }

    AndroidView(
        modifier = modifier,
        factory = { ctx ->
            val swipeRefreshLayout = SwipeRefreshLayout(ctx)
            val webView = WebView(ctx).apply {
                id = android.R.id.primary
                webViewInstance = this
                CookieManager.getInstance().setAcceptCookie(true)

                settings.apply {
                    javaScriptEnabled = true
                    setGeolocationEnabled(true)
                    domStorageEnabled = true
                    cacheMode = WebSettings.LOAD_DEFAULT
                    userAgentString = "Mozilla/5.0 (Linux; Android 13; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Mobile Safari/537.36"
                    allowFileAccess = true
                    mixedContentMode = WebSettings.MIXED_CONTENT_ALWAYS_ALLOW
                }

                webChromeClient = object : WebChromeClient() {
                    override fun onGeolocationPermissionsShowPrompt(origin: String?, callback: GeolocationPermissions.Callback?) {
                        callback?.invoke(origin, true, false)
                    }
                }

                webViewClient = object : WebViewClient() {
                    override fun onPageFinished(view: WebView?, url: String?) {
                        swipeRefreshLayout.isRefreshing = false
                        onPageLoaded()
                        canGoBack.value = view?.canGoBack() ?: false
                    }
                }

                // ვამატებთ ინტერფეისს "Android" სახელით, რათა JS-მა დაინახოს shareApp
                val webInterface = activity.WebAppInterface(this)
                addJavascriptInterface(webInterface, "Android")
                addJavascriptInterface(webInterface, "AndroidBridge")
                
                val prefs = ctx.getSharedPreferences(WeatherUtils.PREFS_NAME, Context.MODE_PRIVATE)
                val lastCity = prefs.getString("last_viewed_city", "")
                val savedLang = prefs.getString("app_lang", "")

                var finalUrl = WeatherUtils.BASE_URL
                val params = mutableListOf<String>()
                if (!lastCity.isNullOrBlank()) params.add("city=$lastCity")
                if (!savedLang.isNullOrBlank()) params.add("lang=$savedLang")
                if (params.isNotEmpty()) finalUrl += "?" + params.joinToString("&")

                loadUrl(finalUrl)
            }
            swipeRefreshLayout.apply {
                addView(webView)
                setOnRefreshListener { webView.reload() }
            }
            swipeRefreshLayout
        }
    )
}
