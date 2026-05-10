package com.app.grubelige

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.os.Build
import android.util.Log
import androidx.core.app.NotificationCompat
import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage

class MyFirebaseMessagingService : FirebaseMessagingService() {

    override fun onMessageReceived(remoteMessage: RemoteMessage) {
        super.onMessageReceived(remoteMessage)
        Log.d("FCM", "From: ${remoteMessage.from}")

        // Handle notification payload
        remoteMessage.notification?.let {
            showNotification(it.title, it.body)
        }
        
        // Handle data payload (important for background/killed state in HTTP v1)
        if (remoteMessage.data.isNotEmpty()) {
            val title = remoteMessage.data["title"]
            val message = remoteMessage.data["body"]
            showNotification(title, message)
        }
    }

    override fun onNewToken(token: String) {
        super.onNewToken(token)
        Log.d("FCM", "New FCM Token: $token")
        
        // Save token locally
        val prefs = getSharedPreferences(WeatherUtils.PREFS_NAME, Context.MODE_PRIVATE)
        prefs.edit().putString("fcm_token", token).apply()
    }

    private fun showNotification(title: String?, message: String?) {
        val channelId = "urgent_alerts_channel"
        val intent = Intent(this, MainActivity::class.java).apply {
            addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
        }
        
        val pendingIntent = PendingIntent.getActivity(
            this, 0, intent,
            PendingIntent.FLAG_IMMUTABLE or PendingIntent.FLAG_UPDATE_CURRENT
        )

        val notificationBuilder = NotificationCompat.Builder(this, channelId)
            .setSmallIcon(android.R.drawable.ic_dialog_info)
            .setContentTitle(title ?: "Grubeli.Ge")
            .setContentText(message)
            .setAutoCancel(true)
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setDefaults(NotificationCompat.DEFAULT_ALL)
            .setContentIntent(pendingIntent)

        val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                channelId,
                "Urgent Alerts",
                NotificationManager.IMPORTANCE_HIGH
            ).apply {
                description = "Channel for high priority alerts"
                enableLights(true)
                enableVibration(true)
            }
            notificationManager.createNotificationChannel(channel)
        }

        notificationManager.notify(System.currentTimeMillis().toInt(), notificationBuilder.build())
    }
}
