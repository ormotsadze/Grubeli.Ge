package com.app.grubelige

import android.app.PendingIntent
import android.appwidget.AppWidgetManager
import android.appwidget.AppWidgetProvider
import android.content.ComponentName
import android.content.Context
import android.content.Intent
import android.util.Log
import android.widget.RemoteViews
import java.util.Calendar

class WeatherWidget : AppWidgetProvider() {

    override fun onUpdate(context: Context, appWidgetManager: AppWidgetManager, appWidgetIds: IntArray) {
        for (appWidgetId in appWidgetIds) {
            updateAppWidget(context, appWidgetManager, appWidgetId)
        }
    }

    override fun onReceive(context: Context, intent: Intent) {
        super.onReceive(context, intent)
        // გარედან მოსული განახლების სიგნალის დამუშავება
        if (intent.action == AppWidgetManager.ACTION_APPWIDGET_UPDATE || 
            intent.action == "com.app.grubelige.UPDATE_WIDGET") {
            val appWidgetManager = AppWidgetManager.getInstance(context)
            val componentName = ComponentName(context, WeatherWidget::class.java)
            val appWidgetIds = appWidgetManager.getAppWidgetIds(componentName)

            for (id in appWidgetIds) {
                updateAppWidget(context, appWidgetManager, id)
            }
        }
    }

    private fun updateAppWidget(context: Context, appWidgetManager: AppWidgetManager, appWidgetId: Int) {
        val views = RemoteViews(context.packageName, R.layout.weather_widget_layout)
        val prefs = context.getSharedPreferences(WeatherUtils.PREFS_NAME, Context.MODE_PRIVATE)

        // აპლიკაციის გახსნა ვიჯეტზე დაჭერით
        val intent = Intent(context, MainActivity::class.java)
        val pendingIntent = PendingIntent.getActivity(
            context, 0, intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )
        views.setOnClickPendingIntent(R.id.widget_root, pendingIntent)

        // მონაცემების წამოღება
        val cityName = prefs.getString("widget_city_name", null) ?: 
                      prefs.getString("last_viewed_city", "საქართველო") ?: "საქართველო"
        
        val temp = prefs.getInt("widget_temp", 0)
        val desc = prefs.getString("widget_desc", "ამინდის ინფორმაცია...") ?: "ამინდის ინფორმაცია..."
        val code = prefs.getInt("widget_code", 0)
        val lastUpdate = prefs.getLong("widget_last_update", 0L)
        
        // დღე/ღამის განსაზღვრა
        val hour = Calendar.getInstance().get(Calendar.HOUR_OF_DAY)
        val isNightFallback = hour >= 20 || hour < 7
        val isDay = prefs.getBoolean("widget_is_day", !isNightFallback)

        Log.d("WeatherWidget", "Updating Widget: $cityName, Temp: $temp, Day: $isDay, Code: $code")

        // UI განახლება
        views.setTextViewText(R.id.widget_city, cityName)
        
        if (lastUpdate != 0L) {
            views.setTextViewText(R.id.widget_temp, "$temp°")
            views.setTextViewText(R.id.widget_desc, desc)
        } else {
            views.setTextViewText(R.id.widget_temp, "--°")
            views.setTextViewText(R.id.widget_desc, "გახსენით აპლიკაცია")
        }

        // ხატულას და ფონის დაყენება
        views.setImageViewResource(R.id.widget_icon, getWeatherIcon(code, isDay))
        val backgroundRes = if (isDay) R.drawable.widget_bg_day_image else R.drawable.widget_bg_night_image
        views.setImageViewResource(R.id.widget_background_image, backgroundRes)

        appWidgetManager.updateAppWidget(appWidgetId, views)
    }

    private fun getWeatherIcon(code: Int, isDay: Boolean): Int {
        return when (code) {
            0 -> if (isDay) R.drawable.clear_day else R.drawable.clear_night
            1, 2, 3 -> if (isDay) R.drawable.partly_cloudy_day else R.drawable.partly_cloudy_night
            45, 48 -> R.drawable.fog
            51, 53, 55 -> R.drawable.drizzle
            61, 63, 65, 80, 81, 82 -> R.drawable.rain
            71, 73, 75 -> R.drawable.snow
            95, 96, 99 -> R.drawable.thunderstorms
            else -> R.drawable.not_available
        }
    }
}
