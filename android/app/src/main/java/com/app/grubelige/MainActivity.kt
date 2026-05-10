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
import android.util.Log
import android.view.WindowManager
import android.webkit.*
import androidx.activity.ComponentActivity
import androidx.activity.compose.BackHandler
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.layout.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Dashboard
import androidx.compose.material.icons.filled.Settings
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.sp
import androidx.compose.ui.viewinterop.AndroidView
import androidx.core.content.ContextCompat
import androidx.core.splashscreen.SplashScreen.Companion.installSplashScreen
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.app.grubelige.ui.theme.GrubeliGeTheme
import com.google.android.gms.location.LocationServices
import com.google.firebase.messaging.FirebaseMessaging
import org.json.JSONObject
import kotlin.math.roundToInt

object WeatherUtils {
    const val PREFS_NAME = "WeatherPrefs"
    const val BASE_URL = "https://mprof.ge/test/index.php"

    fun cleanCityName(name: String): String {
        return name.trim()
            .replace("ამინდი ", "")
            .replace("ში", "")
    }

    fun updateWidget(context: Context) {
        val manager = AppWidgetManager.getInstance(context)
        val componentName = ComponentName(context, WeatherWidget::class.java)
        val ids = manager.getAppWidgetIds(componentName)
        if (ids.isNotEmpty()) {
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
        fun updateWidgetLocation(cityName: String) {
            if (cityName.isBlank() || cityName == "საქართველო") return
            val prefs = getSharedPreferences(WeatherUtils.PREFS_NAME, Context.MODE_PRIVATE)
            val cleanedCity = WeatherUtils.cleanCityName(cityName)
            prefs.edit().putString("last_viewed_city", cleanedCity).apply()
            WeatherUtils.updateWidget(this@MainActivity)
        }

        @JavascriptInterface
        fun saveWeatherData(jsonData: String) {
            try {
                val data = JSONObject(jsonData)
                val prefs = getSharedPreferences(WeatherUtils.PREFS_NAME, Context.MODE_PRIVATE)
                
                val cityName = WeatherUtils.cleanCityName(data.optString("city_name"))
                val tempVal = data.opt("temp")?.toString()?.replace(",", ".")?.toDoubleOrNull()

                prefs.edit().apply {
                    putString("saved_lat", data.optString("lat"))
                    putString("saved_lon", data.optString("lon"))
                    putString("widget_city_name", cityName)
                    if (tempVal != null) {
                        putInt("widget_temp", tempVal.roundToInt())
                        putLong("widget_last_update", System.currentTimeMillis())
                    }
                    putString("widget_desc", data.optString("desc"))
                    putInt("widget_code", data.optInt("code", 0))
                    putBoolean("widget_is_day", data.optBoolean("is_day", true))
                    apply()
                }
                WeatherUtils.updateWidget(this@MainActivity)
            } catch (e: Exception) {
                Log.e("WeatherBridge", "JSON error", e)
            }
        }

        @JavascriptInterface
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
            }
        }

        @JavascriptInterface
        fun getFCMToken() {
            FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
                if (task.isSuccessful) {
                    val token = task.result
                    Log.d("FCM", "FCM Token obtained: $token")
                    pushTokenToJS(token)
                }
            }
        }
    }

    private fun pushTokenToJS(token: String?) {
        if (token == null) return
        runOnUiThread {
            findViewById<WebView>(android.R.id.primary)?.let { webView ->
                val js = "javascript:if(window.setFCMToken) window.setFCMToken('$token');"
                webView.loadUrl(js)
            }
        }
    }

    private val requestPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { isGranted: Boolean ->
        if (isGranted) subscribeToUrgentAlerts()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        val splashScreen = installSplashScreen()
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        window.addFlags(WindowManager.LayoutParams.FLAG_HARDWARE_ACCELERATED)

        createNotificationChannel()
        subscribeToUrgentAlerts()
        checkBatteryOptimization()
        
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED) {
                requestPermissionLauncher.launch(Manifest.permission.POST_NOTIFICATIONS)
            }
        }

        splashScreen.setKeepOnScreenCondition { !isWebViewLoaded }

        setContent {
            GrubeliGeTheme {
                Scaffold(
                    modifier = Modifier.fillMaxSize(),
                    topBar = {
                        TopAppBar(
                            title = { Text("GRUBELI.GE", fontSize = 13.sp, fontWeight = FontWeight.Bold) },
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
            .addOnCompleteListener { task ->
                if (task.isSuccessful) {
                    Log.d("FCM", "Successfully subscribed to topic: urgent_alerts")
                } else {
                    Log.e("FCM", "Subscription to urgent_alerts failed", task.exception)
                }
            }
    }

    @SuppressLint("BatteryLife")
    private fun checkBatteryOptimization() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            val pm = getSystemService(Context.POWER_SERVICE) as PowerManager
            if (!pm.isIgnoringBatteryOptimizations(packageName)) {
                try {
                    val intent = Intent(Settings.ACTION_REQUEST_IGNORE_BATTERY_OPTIMIZATIONS).apply {
                        data = Uri.parse("package:$packageName")
                    }
                    startActivity(intent)
                } catch (e: Exception) {
                    Log.e("FCM", "Battery optimization prompt failed", e)
                }
            }
        }
    }

    private fun requestPinWidget() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val mAppWidgetManager = getSystemService(AppWidgetManager::class.java)
            val myProvider = ComponentName(this, WeatherWidget::class.java)
            if (mAppWidgetManager?.isRequestPinAppWidgetSupported == true) {
                mAppWidgetManager.requestPinAppWidget(myProvider, null, null)
            }
        }
    }

    private fun openSettings() {
        startActivity(Intent(Settings.ACTION_APPLICATION_DETAILS_SETTINGS).apply {
            data = Uri.fromParts("package", packageName, null)
        })
    }

    private fun createNotificationChannel() {
        val channelId = "urgent_alerts_channel"
        val channel = NotificationChannel(
            channelId, 
            "Urgent Alerts", 
            NotificationManager.IMPORTANCE_HIGH
        ).apply {
            description = "Weather updates and alerts"
            enableLights(true)
            enableVibration(true)
        }
        val manager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        manager.createNotificationChannel(channel)
    }
}

@SuppressLint("SetJavaScriptEnabled")
@Composable
fun MainScreen(modifier: Modifier = Modifier, onPageLoaded: () -> Unit, activity: MainActivity) {
    var webViewInstance by remember { mutableStateOf<WebView?>(null) }
    var canGoBack by remember { mutableStateOf(false) }

    // მართავს სისტემურ "უკან" ღილაკს
    BackHandler(enabled = canGoBack) {
        webViewInstance?.goBack()
    }

    AndroidView(
        modifier = modifier,
        factory = { ctx ->
            val swipeRefreshLayout = SwipeRefreshLayout(ctx)
            val webView = WebView(ctx).apply {
                id = android.R.id.primary
                webViewInstance = this
                
                settings.apply {
                    javaScriptEnabled = true
                    domStorageEnabled = true
                    @Suppress("DEPRECATION")
                    databaseEnabled = true
                    cacheMode = WebSettings.LOAD_DEFAULT
                    userAgentString = "GrubeliApp/1.0"
                    allowFileAccess = true
                    allowContentAccess = false
                }
                
                webViewClient = object : WebViewClient() {
                    override fun onPageFinished(view: WebView?, url: String?) {
                        swipeRefreshLayout.isRefreshing = false
                        onPageLoaded()
                        // ანახლებს მდგომარეობას, შეუძლია თუ არა უკან დაბრუნება
                        canGoBack = view?.canGoBack() ?: false
                        
                        // Auto-send token on every load
                        FirebaseMessaging.getInstance().token.addOnSuccessListener { token ->
                            val js = "javascript:if(window.setFCMToken) window.setFCMToken('$token');"
                            view?.loadUrl(js)
                        }
                    }

                    override fun doUpdateVisitedHistory(view: WebView?, url: String?, isReload: Boolean) {
                        super.doUpdateVisitedHistory(view, url, isReload)
                        // ანახლებს მდგომარეობას ისტორიის ყოველი ცვლილებისას
                        canGoBack = view?.canGoBack() ?: false
                    }

                    override fun onReceivedError(view: WebView?, request: WebResourceRequest?, error: WebResourceError?) {
                        if (request?.isForMainFrame == true) {
                            view?.loadUrl("file:///android_asset/errors/error_network.html")
                            onPageLoaded()
                            canGoBack = view?.canGoBack() ?: false
                        }
                    }
                }
                
                addJavascriptInterface(activity.WebAppInterface(this), "AndroidBridge")

                val prefs = ctx.getSharedPreferences(WeatherUtils.PREFS_NAME, Context.MODE_PRIVATE)
                val lastCity = prefs.getString("last_viewed_city", "")
                val url = if (lastCity.isNullOrBlank()) WeatherUtils.BASE_URL else "${WeatherUtils.BASE_URL}?city=$lastCity"
                loadUrl(url)
            }

            swipeRefreshLayout.apply {
                addView(webView)
                setOnRefreshListener {
                    webView.reload()
                }
            }
            swipeRefreshLayout
        }
    )
}
