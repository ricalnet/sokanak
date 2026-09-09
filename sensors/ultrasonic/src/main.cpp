#include <WiFi.h>
#include <PubSubClient.h>
#include <NewPing.h>
#include <ArduinoJson.h>

const char* WIFI_SSID = "your_ssid";                // sesuaikan
const char* WIFI_PASSWORD = "your_wifi_password";   // sesuaikan
const char* MQTT_BROKER = "ip_addr_or_domain";      // sesuaikan   
const int MQTT_PORT = 1883;
const char* MQTT_TOPIC = "iot/sensor_tinggi";       // sesuaikan   
const char* MQTT_USERNAME = "mqtt_username";        // sesuaikan 
const char* MQTT_PASSWORD = "mqtt_password";        // sesuaikan 

#define TRIGGER_PIN  23       // sesuaikan 
#define ECHO_PIN     22       // sesuaikan 
#define MAX_DISTANCE 400      // sesuaikan 
#define BASE_HEIGHT  120.0    // jarak sensor ke tanah

NewPing sonar(TRIGGER_PIN, ECHO_PIN, MAX_DISTANCE);
WiFiClient espClient;
PubSubClient client(espClient);

unsigned long lastMeasurement = 0;
const long measurementInterval = 2000;
float lastValidHeight = 0.0;
const float minHeight = 30.0;  
const float maxHeight = 200.0;

void setupWiFi() {
  Serial.print("Menghubungkan ke WiFi");
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  
  Serial.println("\nWiFi terhubung!");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
}

void reconnectMQTT() {
  while (!client.connected()) {
    Serial.print("Menghubungkan ke MQTT...");
    
    String clientId = "ESP32-Ultrasonic-";
    clientId += String(random(0xffff), HEX);
    
    if (client.connect(clientId.c_str(), MQTT_USERNAME, MQTT_PASSWORD)) {
      Serial.println("terhubung!");
    } else {
      Serial.print("gagal, rc=");
      Serial.print(client.state());
      Serial.println(" coba lagi dalam 5 detik...");
      delay(5000);
    }
  }
}

float measureHeight() {
  const int numReadings = 5;
  float totalDistance = 0.0;
  int validReadings = 0;
  
  for (int i = 0; i < numReadings; i++) {
    delay(30);
    
    float distance = sonar.ping_cm();
    
    if (distance > 0.0 && distance <= MAX_DISTANCE) {
      totalDistance += distance;
      validReadings++;
    }
  }
  
  if (validReadings == 0) {
    Serial.println("Pembacaan sensor tidak valid!");
    return -1.0;
  }
  
  float avgDistance = totalDistance / validReadings;
  
  float height = BASE_HEIGHT - avgDistance;
  
  if (height >= minHeight && height <= maxHeight) {
    lastValidHeight = height;
    return height;
  } else {
    Serial.println("Tinggi badan di luar rentang realistis!");
    return lastValidHeight;
  }
}

void setup() {
  Serial.begin(115200);
  
  setupWiFi();
  
  client.setServer(MQTT_BROKER, MQTT_PORT);
  
  Serial.println("Sistem pengukur tinggi badan siap!");
  Serial.println("================================");
}

void loop() {
  if (!client.connected()) {
    reconnectMQTT();
  }
  client.loop();
  
  if (millis() - lastMeasurement >= measurementInterval) {
    float height = measureHeight();
    
    if (height > 0.0) {
      char heightStr[15];
      char serialStr[15];
      
      dtostrf(height, 6, 2, serialStr);
      Serial.print("Tinggi badan: ");
      Serial.print(serialStr);
      Serial.println(" cm");
      
      dtostrf(height, 0, 2, heightStr); 
      
      if (client.publish(MQTT_TOPIC, heightStr)) {
        Serial.print("Data terkirim ke MQTT: ");
        Serial.println(heightStr);
      } else {
        Serial.println("Gagal mengirim ke MQTT!");
      }
    }
    
    lastMeasurement = millis();
  }
  
  delay(100);
}