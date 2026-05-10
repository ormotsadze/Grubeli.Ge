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
import android.provider.Settings
import android.util.Log
import android.view.WindowManager
import android.webkit.*
import androidx.activity.ComponentActivity
import androidx.activity.OnBackPressedCallback
import androidx.activity.SystemBarStyle
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.layout.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Dashboard
import androidx.compose.material.icons.filled.Settings
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Color as ComposeColor
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.viewinterop.AndroidView
import androidx.core.content.ContextCompat
import androidx.core.splashscreen.SplashScreen.Companion.installSplashScreen
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.app.grubelige.ui.theme.GrubeliGeTheme
import com.google.android.gms.location.LocationServices
import com.google.firebase.messaging.FirebaseMessaging
import org.json.JSONObject
import java.net.URLEncoder
import kotlin.math.roundToInt

class MainActivity : ComponentActivity() {
    
    private var isWebViewLoaded = false

    inner class WebAppInterface {
        @JavascriptInterface
        fun updateWidgetLocation(cityName: String) {
            if (cityName.isEmpty() || cityName == "საქართველო") return
            val prefs = getSharedPreferences("WeatherPrefs", Context.MODE_PRIVATE)
            val cleanedCity = cityName.trim().replace("ამინდი ", "").replace("ში", "")
            prefs.edit().putString("last_viewed_city", cleanedCity).commit()
            // ვიჯეტის მომენტალური განახლება ქალაქის შეცვლისას
            updateWidget()
        }

        @JavascriptInterface
        fun saveWeatherData(jsonData: String) {
            try {
                val data = JSONObject(jsonData)
                val prefs = getSharedPreferences("WeatherPrefs", Context.MODE_PRIVATE)
                val editor = prefs.edit()
                
                val lat = data.optString("lat")
                val lon = data.optString("lon")
                val cityName = data.optString("city_name").trim().replace("ამინდი ", "").replace("ში", "")
                
                editor.putString("saved_lat", lat)
                editor.putString("saved_lon", lon)
                
                editor.putString("widget_city_name", cityName)
                val tempVal = data.opt("temp")?.toString()?.replace(",", ".")?.toDoubleOrNull()
                if (tempVal != null) {
                    editor.putInt("widget_temp", tempVal.roundToInt())
                    editor.putLong("widget_last_update", System.currentTimeMillis())
                }
                editor.putString("widget_desc", data.optString("desc"))
                editor.putInt("widget_code", data.optInt("code", 0))
                editor.putBoolean("widget_is_day", data.optBoolean("is_day", true))
                
                editor.commit()
                updateWidget()
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
                            findViewById<WebView>(R.id.webView)?.loadUrl(js)
                        }
                    }
                }
            }
        }
    }

    private fun updateWidget() {
        val manager = AppWidgetManager.getInstance(this)
        val ids = manager.getAppWidgetIds(ComponentName(this, WeatherWidget::class.java))
        if (ids.isNotEmpty()) {
            val intent = Intent(this, WeatherWidget::class.java).apply {
                action = AppWidgetManager.ACTION_APPWIDGET_UPDATE
                putExtra(AppWidgetManager.EXTRA_APPWIDGET_IDS, ids)
            }
            sendBroadcast(intent)
        }
    }

    @OptIn(ExperimentalMaterial3Api::class)
    override fun onCreate(savedInstanceState: Bundle?) {
        val splashScreen = installSplashScreen()
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        window.addFlags(WindowManager.LayoutParams.FLAG_HARDWARE_ACCELERATED)

        createNotificationChannel()
        FirebaseMessaging.getInstance().subscribeToTopic("weather_updates")
        splashScreen.setKeepOnScreenCondition { !isWebViewLoaded }

        setContent {
            GrubeliGeTheme {
                Scaffold(
                    modifier = Modifier.fillMaxSize(),
                    topBar = {
                        TopAppBar(
                            title = { Text("GRUBELI.GE", fontSize = 13.sp, fontWeight = FontWeight.Bold) },
                            actions = {
                                IconButton(onClick = { requestPinWidget() }) { Icon(Icons.Default.Dashboard, "Add") }
                                IconButton(onClick = { openSettings() }) { Icon(Icons.Default.Settings, "Settings") }
                            }
                        )
                    }
                ) { innerPadding ->
                    MainScreen(
                        modifier = Modifier.padding(innerPadding).fillMaxSize(),
                        onPageLoaded = { isWebViewLoaded = true },
                        webAppInterface = WebAppInterface()
                    )
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
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel("high_priority_channel", "Grubeli Notifications", NotificationManager.IMPORTANCE_HIGH)
            (getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager).createNotificationChannel(channel)
        }
    }
}

@SuppressLint("SetJavaScriptEnabled")
@Composable
fun MainScreen(modifier: Modifier = Modifier, onPageLoaded: () -> Unit, webAppInterface: Any? = null) {
    val context = LocalContext.current
    var webViewRef by remember { mutableStateOf<WebView?>(null) }

    AndroidView(
        modifier = modifier,
        factory = { ctx ->
            val swipeRefreshLayout = SwipeRefreshLayout(ctx)
            val webView = WebView(ctx).apply {
                id = R.id.webView
                settings.javaScriptEnabled = true
                settings.domStorageEnabled = true
                settings.userAgentString = "GrubeliApp/1.0"
                
                webViewClient = object : WebViewClient() {
                    override fun onPageFinished(view: WebView?, url: String?) {
                        swipeRefreshLayout.isRefreshing = false
                        onPageLoaded()
                    }

                    override fun onReceivedError(view: WebView?, request: WebResourceRequest?, error: WebResourceError?) {
                        if (request?.isForMainFrame == true) {
                            view?.loadUrl("file:///android_asset/errors/error_network.html")
                            onPageLoaded() // Splash Screen-ის გასაქრობად
                        }
                    }

                    @Deprecated("Deprecated in Java")
                    override fun onReceivedError(view: WebView?, errorCode: Int, description: String?, failingUrl: String?) {
                        view?.loadUrl("file:///android_asset/errors/error_network.html")
                        onPageLoaded()
                    }
                }
                
                if (webAppInterface != null) addJavascriptInterface(webAppInterface, "AndroidBridge")

                val prefs = ctx.getSharedPreferences("WeatherPrefs", Context.MODE_PRIVATE)
                val lat = prefs.getString("saved_lat", "")
                val lon = prefs.getString("saved_lon", "")
                val baseUrl = "https://mprof.ge/test/index.php"
                
                val finalUrl = if (!lat.isNullOrEmpty() && !lon.isNullOrEmpty()) {
                    "$baseUrl?lat=$lat&lon=$lon"
                } else {
                    baseUrl
                }
                
                loadUrl(finalUrl)
            }
            webViewRef = webView
            swipeRefreshLayout.addView(webView)
            swipeRefreshLayout.setOnRefreshListener { webView.reload() }
            swipeRefreshLayout
        }
    )
}
