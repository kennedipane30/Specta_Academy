# Midtrans SDK rules
-keep class com.midtrans.sdk.** { *; }
-keep class com.midtrans.sdk.uikit.** { *; }
-keep class com.midtrans.sdk.corekit.** { *; }

# Support libraries
-keep class androidx.appcompat.** { *; }
-keep class com.google.android.material.** { *; }

# Prevent obfuscation of Midtrans classes
-dontwarn com.midtrans.sdk.**